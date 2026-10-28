<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Tandon;
use App\Models\TandonReading;
use App\Models\WaterUsage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tandons = Tandon::all();
        
        // Calculate and save today's water usage if not yet calculated
        $this->calculateTodayUsage($tandons);
        
        // Get water usage statistics
        $totalUsage = WaterUsage::sum('volume_used');
        $todayUsage = WaterUsage::whereDate('usage_date', now('Asia/Jakarta')->toDateString())->sum('volume_used');
        
        // Get recent water usages
        $recentUsages = WaterUsage::with('tandon')
            ->orderBy('usage_date', 'desc')
            ->limit(10)
            ->get();
        
        // Calculate tandon-wise usage
        $tandonUsage = [];
        foreach ($tandons as $tandon) {
            $total = WaterUsage::where('tandon_id', $tandon->id)->sum('volume_used');
            $thisMonth = WaterUsage::where('tandon_id', $tandon->id)
                ->whereMonth('usage_date', now('Asia/Jakarta')->month)
                ->whereYear('usage_date', now('Asia/Jakarta')->year)
                ->sum('volume_used');

            // Get latest reading for water height
            $latestReading = TandonReading::where('tandon_id', $tandon->id)
                ->orderBy('recorded_at', 'desc')
                ->first();
            $heightPercent = null;
            if ($latestReading && $tandon->height_max > 0) {
                $heightPercent = round(($latestReading->water_height / $tandon->height_max) * 100);
            }

            $tandonUsage[] = [
                'tandon' => $tandon,
                'total_usage' => $total,
                'this_month' => $thisMonth,
                'height_percent' => $heightPercent,
                'water_height' => $latestReading ? $latestReading->water_height : null,
            ];
        }

        $nodeStatuses = collect($tandonUsage)->map(function (array $usage): array {
            /** @var Tandon $tandon */
            $tandon = $usage['tandon'];
            $latestReading = TandonReading::where('tandon_id', $tandon->id)
                ->orderByDesc('recorded_at')
                ->first();

            $status = 'offline';
            $statusLabel = 'Offline';

            if ($latestReading) {
                $isOffline = $latestReading->recorded_at->lt(now('Asia/Jakarta')->subHours(2));

                if ($isOffline) {
                    $status = 'offline';
                    $statusLabel = 'Offline';
                } elseif ($latestReading->water_height <= $tandon->height_min || $latestReading->water_height >= $tandon->height_max) {
                    $status = 'critical';
                    $statusLabel = 'Critical';
                } elseif ($latestReading->water_height <= $tandon->height_warning) {
                    $status = 'warning';
                    $statusLabel = 'Warning';
                } else {
                    $status = 'online';
                    $statusLabel = 'Online';
                }
            }

            return [
                'id' => $tandon->id,
                'name' => $tandon->name,
                'building' => $tandon->building_name,
                'type' => $tandon->type,
                'status' => $status,
                'status_label' => $statusLabel,
                'height_percent' => $usage['height_percent'],
                'water_height' => $usage['water_height'],
                'last_seen' => $latestReading?->recorded_at?->toIso8601String(),
            ];
        });

        $onlineCount = $nodeStatuses->where('status', 'online')->count();
        $warningCount = $nodeStatuses->where('status', 'warning')->count();
        $criticalCount = $nodeStatuses->where('status', 'critical')->count();
        $offlineCount = $nodeStatuses->where('status', 'offline')->count();

        $avgLevel = round((float) collect($tandonUsage)
            ->pluck('height_percent')
            ->filter(fn ($value) => $value !== null)
            ->avg(), 1);

        $activeAlertsCount = Alert::whereNull('resolved_at')->count();

        $readingsLast90Minutes = TandonReading::with('tandon:id,height_max')
            ->where('recorded_at', '>=', now('Asia/Jakarta')->subMinutes(90))
            ->orderBy('tandon_id')
            ->orderBy('recorded_at')
            ->get()
            ->groupBy('tandon_id');

        $flowLitersPerMinute = 0.0;
        foreach ($readingsLast90Minutes as $tankReadings) {
            /** @var Collection<int, TandonReading> $tankReadings */
            for ($i = 0; $i < $tankReadings->count() - 1; $i++) {
                $current = $tankReadings[$i];
                $next = $tankReadings[$i + 1];
                $minutes = max(1, (int) $current->recorded_at->diffInMinutes($next->recorded_at));
                $deltaVolume = (float) $current->water_volume - (float) $next->water_volume;

                if ($deltaVolume > 0) {
                    $flowLitersPerMinute += ($deltaVolume * 1000) / $minutes;
                }
            }
        }

        $telemetryReadings = TandonReading::with('tandon:id,name,height_max')
            ->where('recorded_at', '>=', now('Asia/Jakarta')->subDays(30))
            ->orderBy('recorded_at')
            ->get();

        $telemetryBuckets = [];
        foreach ($telemetryReadings as $reading) {
            $bucketKey = Carbon::parse($reading->recorded_at, 'Asia/Jakarta')->startOfHour()->toIso8601String();
            if (!isset($telemetryBuckets[$bucketKey])) {
                $telemetryBuckets[$bucketKey] = [
                    'timestamp' => $bucketKey,
                    'level_sum' => 0.0,
                    'volume_sum_l' => 0.0,
                    'count' => 0,
                ];
            }

            $maxHeight = (float) ($reading->tandon->height_max ?: 0);
            $heightPercent = $maxHeight > 0 ? min(100, max(0, ((float) $reading->water_height / $maxHeight) * 100)) : 0;

            $telemetryBuckets[$bucketKey]['level_sum'] += $heightPercent;
            $telemetryBuckets[$bucketKey]['volume_sum_l'] += ((float) $reading->water_volume * 1000);
            $telemetryBuckets[$bucketKey]['count']++;
        }

        $telemetrySeries = collect($telemetryBuckets)
            ->map(function (array $bucket): array {
                $count = max(1, $bucket['count']);

                return [
                    'timestamp' => $bucket['timestamp'],
                    'avg_level' => round($bucket['level_sum'] / $count, 2),
                    'avg_volume_l' => round($bucket['volume_sum_l'] / $count, 2),
                ];
            })
            ->sortBy('timestamp')
            ->values();

        $readingLogs = TandonReading::with('tandon:id,name,building_name,height_min,height_warning,height_max')
            ->orderByDesc('recorded_at')
            ->limit(250)
            ->get();

        $kpi = [
            'avg_level' => $avgLevel,
            'flow_lpm' => round($flowLitersPerMinute, 1),
            'active_alerts' => $activeAlertsCount,
            'connectivity' => [
                'online' => $onlineCount,
                'warning' => $warningCount,
                'critical' => $criticalCount,
                'offline' => $offlineCount,
                'total' => $nodeStatuses->count(),
            ],
            'water_quality' => [
                'available' => 0,
                'total' => $nodeStatuses->count(),
            ],
        ];
        
        return view('dashboard', [
            'tandons' => $tandons,
            'totalUsage' => $totalUsage,
            'todayUsage' => $todayUsage,
            'recentUsages' => $recentUsages,
            'tandonUsage' => $tandonUsage,
            'nodeStatuses' => $nodeStatuses,
            'telemetrySeries' => $telemetrySeries,
            'readingLogs' => $readingLogs,
            'kpi' => $kpi,
        ]);
    }

    /**
     * Calculate today's water usage based on tandon readings
     * Handles scenarios where water may be refilled during the day
     * If today's usage doesn't exist, create it. Otherwise, update it.
     */
    private function calculateTodayUsage($tandons): void
    {
        $today = now('Asia/Jakarta')->toDateString();

        foreach ($tandons as $tandon) {
            // Get readings for today sorted by time
            $todayReadings = TandonReading::where('tandon_id', $tandon->id)
                ->whereDate('recorded_at', $today)
                ->orderBy('recorded_at')
                ->get();

            if ($todayReadings->count() < 2) {
                // Need at least 2 readings to calculate usage
                continue;
            }

            // Calculate total volume usage for today by tracking all decreases
            // This accommodates refilling scenarios where volume increases
            $totalUsage = 0;
            
            for ($i = 0; $i < $todayReadings->count() - 1; $i++) {
                $currentReading = $todayReadings[$i];
                $nextReading = $todayReadings[$i + 1];
                
                $currentVolume = (float) $currentReading->water_volume;
                $nextVolume = (float) $nextReading->water_volume;
                
                // Only count volume decreases as usage
                // Volume increases indicate refilling, so we skip them
                if ($currentVolume > $nextVolume) {
                    $usage = $currentVolume - $nextVolume;
                    $totalUsage += $usage;
                }
                // If volume increases, it means refilling occurred - skip this pair
            }

            // Create or update usage record
            WaterUsage::updateOrCreate(
                [
                    'tandon_id' => $tandon->id,
                    'usage_date' => $today,
                ],
                [
                    'volume_used' => $totalUsage,
                ]
            );
        }
    }
}
