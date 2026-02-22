<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Tandon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        $alerts = Alert::with('tandon')
            ->orderBy('triggered_at', 'desc')
            ->paginate(20);

        $activeAlerts = Alert::with('tandon')
            ->whereNull('resolved_at')
            ->orderBy('triggered_at', 'desc')
            ->get();

        return view('alerts.index', [
            'alerts' => $alerts,
            'activeAlerts' => $activeAlerts,
        ]);
    }

    public function show(Alert $alert): View
    {
        $alert->load('tandon');

        return view('alerts.show', [
            'alert' => $alert,
        ]);
    }

    public function resolve(Alert $alert): RedirectResponse
    {
        if ($alert->resolved_at) {
            return redirect()->back()->with('error', 'Alert already resolved.');
        }

        $alert->update([
            'resolved_at' => now('Asia/Jakarta'),
        ]);

        return redirect()->back()->with('success', 'Alert marked as resolved.');
    }

    public function unresolve(Alert $alert): RedirectResponse
    {
        $alert->update([
            'resolved_at' => null,
        ]);

        return redirect()->back()->with('success', 'Alert marked as unresolved.');
    }

    public function destroy(Alert $alert): RedirectResponse
    {
        $alert->delete();

        return redirect()->route('alerts.index')->with('success', 'Alert deleted successfully.');
    }

    public function resolveAll(): RedirectResponse
    {
        $count = Alert::whereNull('resolved_at')->update([
            'resolved_at' => now('Asia/Jakarta'),
        ]);

        return redirect()->back()->with('success', "Resolved {$count} active alert(s).");
    }
}
