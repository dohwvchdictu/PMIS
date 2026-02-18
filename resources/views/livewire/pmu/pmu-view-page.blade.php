<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">PMU - Notice of Award Details</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Procurements under NOA:
                    <span class="font-semibold">{{ $noticeOfAwardNumber }}</span>
                </p>
            </div>
            <button wire:click="back"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </button>
        </div>
    </div>

    <!-- Summary Card -->
    @if ($postProcurement)
        <div
            class="mb-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Award Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Notice of Award Number
                        </label>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $postProcurement->notice_of_award_number }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Notice of Award Date
                        </label>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $postProcurement->notice_of_award ? \Carbon\Carbon::parse($postProcurement->notice_of_award)->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Resolution Award Number
                        </label>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $postProcurement->resolution_award_number ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Resolution Award Date
                        </label>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $postProcurement->resolution_award_date ? \Carbon\Carbon::parse($postProcurement->resolution_award_date)->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Total ABC
                        </label>
                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                            ₱ {{ number_format($totalAbc, 2) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Total PRs
                        </label>
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            {{ $procurements->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Procurements Table -->
    <div
        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Purchase Requests</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            PR Number
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Division
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Category
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            ABC
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Date Received
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Current Stage
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                    @foreach ($procurements as $procurement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('procurements.view', $procurement->procID) }}"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $procurement->pr_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $procurement->division->divisions ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $procurement->category->category ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                                ₱ {{ number_format($procurement->abc, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $procurement->date_receipt ? \Carbon\Carbon::parse($procurement->date_receipt)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $latestStage =
                                        $procurement->prLotPrstages->sortByDesc('stage_history')->first() ??
                                        $procurement->prItemPrstages->sortByDesc('stage_history')->first();
                                @endphp
                                @if ($latestStage)
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        {{ $latestStage->procurementStage->stage_description ?? ($latestStage->stage->stage_description ?? 'N/A') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
