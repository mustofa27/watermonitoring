<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tandon;
use Illuminate\Http\JsonResponse;

class TandonApiController extends Controller
{
    /**
     * Get tandon config by name for edge devices.
     *
     * Device computes: water_height = height_max - (measured_distance - sensor_distance_full).
     * sensor_distance_full is null until it has been set on the tank.
     *
     * @param string $name
     * @return JsonResponse
     */
    public function show(string $name): JsonResponse
    {
        $tandon = Tandon::where('name', $name)->first();

        if (!$tandon) {
            return response()->json([
                'success' => false,
                'message' => 'Tandon not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $tandon->name,
                'height_max' => (float) $tandon->height_max,
                'height_warning' => (float) $tandon->height_warning,
                'height_min' => (float) $tandon->height_min,
                'sensor_distance_full' => $tandon->sensor_distance_full !== null ? (float) $tandon->sensor_distance_full : null,
            ],
        ]);
    }
}
