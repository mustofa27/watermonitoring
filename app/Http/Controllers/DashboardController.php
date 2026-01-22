<?php

namespace App\Http\Controllers;

use App\Models\Tandon;
use App\Models\WaterUsage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tandons = Tandon::all();
        
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
}
