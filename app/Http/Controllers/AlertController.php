<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AlertController extends Controller
{
    private const ALLOWED_PER_PAGE = [10, 25, 50, 100];

    public function index(): View
    {
        $perPage = (int) request('per_page', 20);
        if (!in_array($perPage, self::ALLOWED_PER_PAGE, true)) {
            $perPage = 20;
        }

        $alerts = Alert::with('tandon')
            ->orderBy('triggered_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $activeAlerts = Alert::with('tandon')
            ->whereNull('resolved_at')
            ->orderBy('triggered_at', 'desc')
            ->get();

        return view('alerts.index', [
            'alerts' => $alerts,
            'activeAlerts' => $activeAlerts,
        ]);
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:alerts,id',
            'action' => 'required|in:resolve,unresolve,delete',
        ]);

        $query = Alert::whereIn('id', $validated['ids']);

        if ($validated['action'] === 'resolve') {
            $count = $query->update(['resolved_at' => now('Asia/Jakarta')]);
            $message = "{$count} alert(s) marked as resolved.";
        } elseif ($validated['action'] === 'unresolve') {
            $count = $query->update(['resolved_at' => null]);
            $message = "{$count} alert(s) marked as unresolved.";
        } else {
            $count = $query->delete();
            $message = "{$count} alert(s) deleted successfully.";
        }

        return redirect()
            ->route('alerts.index', $request->only('per_page', 'page'))
            ->with('success', $message);
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
