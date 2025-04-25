<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header name="Queue Alerts">
        <x-slot:icon>
            @if($hasAlerts)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            @endif
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid gap-3 p-4">
            @if($hasAlerts)
                <div class="flex items-center rounded-lg bg-red-50 p-3 text-red-700 dark:bg-red-950 dark:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Queue alerts detected!</span>
                </div>
            @else
                <div class="flex items-center rounded-lg bg-green-50 p-3 text-green-700 dark:bg-green-950 dark:text-green-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">All queues operating normally</span>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-md border p-3 {{ $isQueueBacklogged ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-950' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Queued Jobs</div>
                    <div class="text-lg font-bold {{ $isQueueBacklogged ? 'text-red-700 dark:text-red-300' : '' }}">{{ $queuedJobs }}</div>
                </div>

                <div class="rounded-md border p-3 {{ $hasTooManyProcessing ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-950' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Processing Jobs</div>
                    <div class="text-lg font-bold {{ $hasTooManyProcessing ? 'text-red-700 dark:text-red-300' : '' }}">{{ $processingJobs }}</div>
                </div>

                <div class="rounded-md border p-3 {{ $hasLongRunningJobs ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-950' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Long Running Jobs</div>
                    <div class="text-lg font-bold {{ $hasLongRunningJobs ? 'text-red-700 dark:text-red-300' : '' }}">{{ $longRunningJobs }}</div>
                </div>

                <div class="rounded-md border p-3 {{ $hasRecentFailures ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-950' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Failed Jobs (Last Hour)</div>
                    <div class="text-lg font-bold {{ $hasRecentFailures ? 'text-red-700 dark:text-red-300' : '' }}">{{ $failedJobsLastHour }}</div>
                </div>
            </div>

            @if($hasAlerts)
                <div class="mt-2 text-sm font-medium">Alert Details:</div>

                @if($isQueueBacklogged)
                    <div class="rounded-md bg-red-50 p-2 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                        Queue backlog detected: {{ $queuedJobs }} jobs waiting to be processed
                    </div>
                @endif

                @if($hasLongRunningJobs)
                    <div class="rounded-md bg-red-50 p-2 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                        Long-running jobs detected: {{ $longRunningJobs }} jobs running longer than 60 seconds
                    </div>

                    @if(count($longRunningJobDetails) > 0)
                        <div class="mt-2 text-xs font-medium">Top Long-Running Jobs:</div>
                        <div class="mt-1 max-h-32 overflow-y-auto text-xs">
                            <table class="w-full text-left">
                                <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="p-1">Job</th>
                                    <th class="p-1">Runtime</th>
                                    <th class="p-1">Queue</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($longRunningJobDetails as $job)
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="p-1 font-medium">{{ \Illuminate\Support\Str::afterLast($job->name, '\\') }}</td>
                                        <td class="p-1">{{ $job->runtime }}s</td>
                                        <td class="p-1">{{ $job->queue }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif

                @if($hasRecentFailures)
                    <div class="rounded-md bg-red-50 p-2 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                        Failed jobs detected: {{ $failedJobsLastHour }} jobs failed in the last hour
                    </div>
                @endif
            @endif
        </div>
    </x-pulse::scroll>
</x-pulse::card>
