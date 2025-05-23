<x-pulse>
    <livewire:pulse.servers cols="full" />

    <!-- Blog Activity Monitor -->
    <livewire:pulse.status-monitor
        cols="4"
        rows="2"
        source="blogs"
        key="count"
        title="Blog Activity Monitor"
        :warningThreshold="0"
    />

    <!-- External Service Monitor -->
    <livewire:pulse.status-monitor
        cols="4"
        rows="2"
        source="external-service"
        key="count"
        title="External Service Health"
        :warningThreshold="50"
    />

    <livewire:pulse.usage cols="4" rows="2" />

    <livewire:pulse.queue-alert cols="4" rows="2" />

</x-pulse>
