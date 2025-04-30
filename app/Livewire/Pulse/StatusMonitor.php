<?php

namespace App\Livewire\Pulse;

use App\Models\StatusMetric;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class StatusMonitor extends Card
{
    // These properties can be set when the component is used
    public string $source = 'blogs';  // Default source to monitor
    public string $key = 'count';  // Default key to monitor
    public string $title = 'Status Monitor';
    public int $warningThreshold = 0;  // Value that triggers warning state

    /**
     * Render the component.
     */
    public function render()
    {
        // Get the latest metric
        \Log::info("StatusMonitor: Looking for source={$this->source}, key={$this->key}");
        $latestMetric = StatusMetric::where('source', $this->source)
            ->where('key', $this->key)
            ->latest()
            ->first();

        // Log what we found
        if ($latestMetric) {
            \Log::info("StatusMonitor: Found metric! ID={$latestMetric->id}, value={$latestMetric->value}, status={$latestMetric->status}");
        } else {
            \Log::warning("StatusMonitor: No metric found for source={$this->source}, key={$this->key}");
        }

        // Get historical data (last 24 entries)
        $history = StatusMetric::where('source', $this->source)
            ->where('key', $this->key)
            ->latest()
            ->take(24)
            ->get();

        // Calculate if we should show alert
        $currentValue = $latestMetric?->value ?? '0';
        $showAlert = !$latestMetric ||
                    $latestMetric?->status === 'critical' ||
                    $latestMetric?->status === 'warning' ||
                    (is_numeric($currentValue) && (int)$currentValue <= $this->warningThreshold);

        // Get additional metrics if available
        $additionalMetrics = StatusMetric::where('source', $this->source)
            ->where('key', '!=', $this->key)
            ->latest()
            ->groupBy('key')
            ->selectRaw('MAX(id) as id')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return StatusMetric::find($item->id);
            });

        return view('livewire.pulse.status-monitor', [
            'currentValue' => $currentValue,
            'status' => $latestMetric?->status ?? 'unknown',
            'lastUpdated' => $latestMetric?->created_at,
            'history' => $history,
            'showAlert' => $showAlert,
            'additionalMetrics' => $additionalMetrics,
            'source' => $this->source,
            'key' => $this->key,
            'title' => $this->title,
        ]);
    }
}
