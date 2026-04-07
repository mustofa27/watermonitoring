<?php

namespace App\Http\Controllers;

use App\Models\Tandon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Throwable;

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

    public function pumpOn(Tandon $tandon): RedirectResponse
    {
        $config = config('services.mqtt');
        $baseTopic = (string) ($config['topic'] ?? '');
        $projectCode = explode('/', $baseTopic)[0] ?? '';

        if ($projectCode === '') {
            return back()->with('error', 'MQTT topic configuration is invalid.');
        }

        $pumpTopic = $projectCode.'/'.$tandon->name.'/water_pump';
        $targetStatus = ((int) $tandon->pump_status) === 1 ? 0 : 1;

        $client = new MqttClient(
            $config['host'],
            (int) $config['port'],
            ($config['client_id'] ?? 'web_pump_control').'_web_toggle'
        );

        $settings = (new ConnectionSettings())
            ->setUsername($config['username'] ?? null)
            ->setPassword($config['password'] ?? null)
            ->setKeepAliveInterval(30)
            ->setUseTls((bool) ($config['use_tls'] ?? false));

        try {
            $client->connect($settings, true);
            $client->publish($pumpTopic, (string) $targetStatus, 0);
            $client->disconnect();

            $tandon->update(['pump_status' => $targetStatus]);

            $statusText = $targetStatus === 1 ? 'ON' : 'OFF';

            return back()->with('success', "Pump {$statusText} command sent for {$tandon->name}.");
        } catch (Throwable $e) {
            Log::error('mqtt.manual_pump_toggle_failed', [
                'tandon_id' => $tandon->id,
                'topic' => $pumpTopic,
                'target_status' => $targetStatus,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send pump command. Please try again.');
        }
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