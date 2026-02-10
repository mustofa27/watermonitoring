<?php

namespace App\Http\Controllers;

use App\Models\Tandon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TandonController extends Controller
{
    /**
     * Remove all readings and usages for a given Tandon (water tank).
     *
     * @param  Tandon  $tandon
     * @return RedirectResponse
     */
    public function truncateData(Tandon $tandon): RedirectResponse
    {
        // Delete all readings
        $tandon->readings()->delete();

        // Delete all usages
        \App\Models\WaterUsage::where('tandon_id', $tandon->id)->delete();

        return redirect()->route('tandons.show', $tandon)
            ->with('success', 'All readings and usages for this tank have been deleted.');
    }
    public function index(): View
    {
        $tandons = Tandon::with('parent')->paginate(10);

        return view('tandons.index', compact('tandons'));
    }

    public function create(): View
    {
        $parents = Tandon::whereNull('parent_id')->get();

        return view('tandons.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:tandons,id',
            'cross_section_area' => 'required|numeric|min:0',
            'height_max' => 'required|numeric|min:0',
            'height_min' => 'required|numeric|min:0',
            'height_warning' => 'required|numeric|min:0',
        ]);

        Tandon::create($validated);

        return redirect()->route('tandons.index')->with('success', 'Tandon created successfully.');
    }

    public function show(Tandon $tandon): View
    {
        $tandon->load(['parent', 'children', 'readings']);

        return view('tandons.show', compact('tandon'));
    }

    public function edit(Tandon $tandon): View
    {
        $parents = Tandon::whereNull('parent_id')->where('id', '!=', $tandon->id)->get();

        return view('tandons.edit', compact('tandon', 'parents'));
    }

    public function update(Request $request, Tandon $tandon): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:tandons,id',
            'cross_section_area' => 'required|numeric|min:0',
            'height_max' => 'required|numeric|min:0',
            'height_min' => 'required|numeric|min:0',
            'height_warning' => 'required|numeric|min:0',
        ]);

        $tandon->update($validated);

        return redirect()->route('tandons.index')->with('success', 'Tandon updated successfully.');
    }

    public function destroy(Tandon $tandon): RedirectResponse
    {
        $tandon->delete();

        return redirect()->route('tandons.index')->with('success', 'Tandon deleted successfully.');
    }
}