<?php

namespace App\Http\Controllers;

use App\Models\Tandon;
use App\Models\TandonReading;
use App\Models\WaterUsage;
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
        $todayUsage = WaterUsage::whereDate('usage_date', now()->toDateString())->sum('volume_used');
        
        // Get recent water usages
        $recentUsages = WaterUsage::with('tandon')
            ->orderBy('usage_date', 'desc')
            ->limit(10)
            ->get();
        
        // Get usage by tandon for the last 30 days
        $last30DaysUsage = WaterUsage::with('tandon')
            ->where('usage_date', '>=', now()->subDays(30)->toDateString())
            ->orderBy('tandon_id')
            ->orderBy('usage_date', 'desc')
            ->get()
            ->groupBy('tandon_id');
        
        // Calculate tandon-wise usage
        $tandonUsage = [];
        foreach ($tandons as $tandon) {
            $total = WaterUsage::where('tandon_id', $tandon->id)->sum('volume_used');
            $thisMonth = WaterUsage::where('tandon_id', $tandon->id)
                ->whereMonth('usage_date', now()->month)
                ->whereYear('usage_date', now()->year)
                ->sum('volume_used');
            
            $tandonUsage[] = [
                'tandon' => $tandon,
                'total_usage' => $total,
                'this_month' => $thisMonth,
            ];
        }
        
        return view('dashboard', [
            'tandons' => $tandons,
            'totalUsage' => $totalUsage,
            'todayUsage' => $todayUsage,
            'recentUsages' => $recentUsages,
            'tandonUsage' => $tandonUsage,
        ]);
    }

    /**
     * Calculate today's water usage based on tandon readings
     * Handles scenarios where water may be refilled during the day
     * If today's usage doesn't exist, create it. Otherwise, update it.
     */
    private function calculateTodayUsage($tandons): void
    {
        $today = now()->toDateString();

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
