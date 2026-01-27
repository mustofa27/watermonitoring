<?php

namespace App\Http\Controllers;

use App\Models\Tandon;
use App\Models\TandonReading;
use Illuminate\Http\Request;

class TandonReadingController extends Controller
{
    public function index(Tandon $tandon)
    {
        $readings = $tandon->readings()
            ->orderBy('recorded_at', 'desc')
            ->paginate(15);

        return view('tandon-readings.index', compact('tandon', 'readings'));
    }

    public function create(Tandon $tandon)
    {
        return view('tandon-readings.create', compact('tandon'));
    }

    public function store(Request $request, Tandon $tandon)
    {
        $validated = $request->validate([
            'water_height' => 'required|numeric|min:0',
            'water_volume' => 'required|numeric|min:0',
            'recorded_at' => 'required|date_format:Y-m-d H:i',
        ]);

        $validated['recorded_at'] = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['recorded_at']);
        $tandon->readings()->create($validated);

        return redirect()->route('tandon-readings.index', $tandon)
            ->with('success', 'Reading recorded successfully!');
    }

    public function show(Tandon $tandon, TandonReading $reading)
    {
        if ($reading->tandon_id !== $tandon->id) {
            abort(404);
        }

        return view('tandon-readings.show', compact('tandon', 'reading'));
    }

    public function destroy(Tandon $tandon, TandonReading $reading)
    {
        if ($reading->tandon_id !== $tandon->id) {
            abort(404);
        }

        $reading->delete();

        return redirect()->route('tandon-readings.index', $tandon)
            ->with('success', 'Reading deleted successfully!');
    }

    public function bulkDestroy(Request $request, Tandon $tandon)
    {
        $validated = $request->validate([
            'reading_ids' => 'required|array',
            'reading_ids.*' => 'exists:tandon_readings,id',
        ]);

        $deletedCount = TandonReading::whereIn('id', $validated['reading_ids'])
            ->where('tandon_id', $tandon->id)
            ->delete();

        return redirect()->route('tandon-readings.index', $tandon)
            ->with('success', "$deletedCount reading(s) deleted successfully!");
    }
}
