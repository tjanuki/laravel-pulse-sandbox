<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header :name="$title">
        <x-slot:icon>
            @if($showAlert)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            @endif
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid gap-3 p-4">
            @if($showAlert)
                <div class="flex items-center rounded-lg bg-red-50 p-3 text-red-700 dark:bg-red-950 dark:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Alert: {{ ucfirst($source) }} {{ $key }} is in alert state!</span>
                </div>
            @else
                <div class="flex items-center rounded-lg bg-green-50 p-3 text-green-700 dark:bg-green-950 dark:text-green-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ ucfirst($source) }} status is normal</span>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-md border p-3 {{ $showAlert ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-950' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($key) }}</div>
                    <div class="text-lg font-bold {{ $showAlert ? 'text-red-700 dark:text-red-300' : '' }}">{{ $currentValue }}</div>
                </div>

                <div class="rounded-md border p-3 border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Status</div>
                    <div class="text-lg font-bold 
                        @if($status === 'critical') text-red-600 dark:text-red-400 
                        @elseif($status === 'warning') text-amber-600 dark:text-amber-400 
                        @elseif($status === 'ok') text-green-600 dark:text-green-400 
                        @else text-gray-600 dark:text-gray-400 @endif">
                        {{ ucfirst($status) }}
                    </div>
                </div>
            </div>

            @if($additionalMetrics->isNotEmpty())
                <div class="rounded-md border p-3 border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Additional Metrics</div>
                    <div class="space-y-2">
                        @foreach($additionalMetrics as $metric)
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($metric->key) }}</div>
                                <div class="text-sm font-medium">{{ $metric->value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($history->isNotEmpty())
                <div class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                    <div class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Recent History</div>
                    <div class="space-y-2">
                        @foreach($history as $entry)
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $entry->created_at->diffForHumans() }}
                                </div>
                                <div class="text-sm font-medium 
                                    @if($entry->status === 'critical') text-red-600 dark:text-red-400 
                                    @elseif($entry->status === 'warning') text-amber-600 dark:text-amber-400 
                                    @elseif($entry->value == 0) text-red-600 dark:text-red-400
                                    @else text-gray-700 dark:text-gray-300 @endif">
                                    {{ $entry->value }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($showAlert)
                <div class="mt-3 rounded-md bg-amber-50 p-3 text-sm text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                    <div class="font-medium">Recommendations:</div>
                    <ul class="ml-4 mt-1 list-disc">
                        <li>Check if {{ $source }} services are working properly</li>
                        <li>Verify database connectivity</li>
                        <li>Review any recent deployment changes</li>
                    </ul>
                </div>
            @endif
            
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-right">
                @if($lastUpdated)
                    Last updated: {{ $lastUpdated->diffForHumans() }}
                @else
                    No data available
                @endif
            </div>
        </div>
    </x-pulse::scroll>
</x-pulse::card>