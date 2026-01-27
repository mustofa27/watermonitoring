<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tandon;
use Illuminate\Http\JsonResponse;

class TandonApiController extends Controller
{
    /**
     * Get tandon details by name
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
            ],
        ]);
    }
}
