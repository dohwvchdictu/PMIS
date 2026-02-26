<div
    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col mb-6">

    <!-- Enhanced Header with Filters -->
    <div class="sticky top-0 z-40 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 w-full">
        <!-- Title Row -->
        <div class="px-6 py-3 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="size-6 text-emerald-600 dark:text-emerald-400">
                    <path
                        d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z" />
                    <path
                        d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                </svg>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">PR's Received (Category A) Report</h2>
            </div>
        </div>

        <!-- Search and Filters Row -->
        <div class="px-6 py-3 bg-gray-50 dark:bg-neutral-900/50">
            <div class="flex items-end justify-between gap-3">
                <div class="flex items-end gap-3">
                    <!-- Period From -->
                    <div>
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-1.5">Period
                            From</label>
                        <input type="date" wire:model.live="startDate"
                            class="w-36 px-2.5 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-neutral-800 dark:text-white dark:border-neutral-600" />
                    </div>

                    <!-- Period To -->
                    <div>
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-1.5">Period
                            To</label>
                        <input type="date" wire:model.live="endDate"
                            class="w-36 px-2.5 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-neutral-800 dark:text-white dark:border-neutral-600" />
                    </div>

                    <!-- Current Mode -->
                    <div class="relative z-50 w-56">
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-1.5">Current
                            Mode</label>
                        <x-forms.searchable-select wire:model.live="currentModeFilter" :options="$modes" labelKey="name"
                            valueKey="id" placeholder="All Modes" />
                    </div>
                </div>

                <!-- Export Button -->
                <div class="flex-shrink-0">
                    <label class="text-xs font-semibold text-transparent block mb-1.5">Export</label>
                    <button type="button" wire:click="exportToExcel" title="Export to Excel"
                        class="inline-flex items-center justify-center w-10 h-10 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md dark:bg-emerald-600 dark:hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"
                            wire:loading.remove wire:target="exportToExcel">
                            <path fill-rule="evenodd"
                                d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875Zm5.845 17.03a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V12a.75.75 0 0 0-1.5 0v4.19l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3Z"
                                clip-rule="evenodd" />
                            <path
                                d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
                        </svg>
                        <svg wire:loading wire:target="exportToExcel" class="animate-spin h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="overflow-auto flex-1">
        <table class="table-auto w-full min-w-[2400px] divide-y divide-gray-200 dark:divide-neutral-700">
            <thead
                class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-30">
                <tr>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-52">
                        PR Number
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">
                        IB No
                    </th>
                    <th
                        class="px-3 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-72">
                        Procurement Program / Project
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">
                        Date Received
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">
                        DTrack No
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Division
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Unit / Cluster
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-40">
                        Category
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        End-User
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Category / Venue
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Immediate Date Needed
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Date Needed
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Fund Source
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">
                        ABC Amount
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">
                        Approved PPMP
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-20">
                        EPA
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Procurement Stage
                    </th>
                    <th
                        class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-44">
                        Current Mode
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800">
                @forelse ($procurements as $procurement)
                    <tr
                        class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-gray-50/50 dark:bg-neutral-900/50' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20 transition-all duration-200">
                        <!-- PR Number -->
                        <td class="px-3 py-4 text-center text-sm font-bold text-emerald-700 dark:text-emerald-300">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs whitespace-nowrap">
                                {{ $procurement->pr_number }}
                            </span>
                        </td>

                        <!-- IB No -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200 whitespace-nowrap">
                            @if ($procurement->currentIbNo)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-md font-mono text-xs text-amber-700 dark:text-amber-300">
                                    {{ $procurement->currentIbNo }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">N/A</span>
                            @endif
                        </td>

                        <!-- Procurement Program / Project -->
                        <td class="px-3 py-4 text-left text-xs text-gray-900 dark:text-gray-100">
                            <div class="font-medium break-words whitespace-normal"
                                title="{{ $procurement->procurement_program_project }}">
                                {{ $procurement->procurement_program_project }}
                            </div>
                        </td>

                        <!-- Date Received -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->date_receipt)
                                <span class="text-xs">
                                    {{ \Carbon\Carbon::parse($procurement->date_receipt)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">N/A</span>
                            @endif
                        </td>

                        <!-- DTrack No -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200 whitespace-nowrap">
                            @if ($procurement->dtrack_no)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 bg-sky-50 dark:bg-sky-900/30 border border-sky-200 dark:border-sky-700 rounded-md font-mono text-xs text-sky-700 dark:text-sky-300">
                                    {{ $procurement->dtrack_no }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">N/A</span>
                            @endif
                        </td>

                        <!-- Division -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            <div class="truncate" title="{{ $procurement->division?->divisions }}">
                                {{ $procurement->division?->divisions ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- Unit / Cluster -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            <div class="truncate" title="{{ $procurement->clusterCommittee?->clustercommittee }}">
                                {{ $procurement->clusterCommittee?->clustercommittee ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            <div class="truncate" title="{{ $procurement->category?->category }}">
                                {{ $procurement->category?->category ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- End-User -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            <div class="truncate" title="{{ $procurement->endUser?->endusers }}">
                                {{ $procurement->endUser?->endusers ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- Category / Venue -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            <div class="truncate" title="{{ $procurement->category_venue }}">
                                {{ $procurement->category_venue ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- Immediate Date Needed -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->immediate_date_needed)
                                <span class="text-xs">
                                    @php
                                        try {
                                            echo \Carbon\Carbon::parse($procurement->immediate_date_needed)->format(
                                                'M d, Y',
                                            );
                                        } catch (\Exception $e) {
                                            echo $procurement->immediate_date_needed;
                                        }
                                    @endphp
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">N/A</span>
                            @endif
                        </td>

                        <!-- Date Needed -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->date_needed)
                                <span class="text-xs">{{ $procurement->date_needed }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">N/A</span>
                            @endif
                        </td>

                        <!-- Fund Source -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            <div class="truncate" title="{{ $procurement->fundSource?->fundsources }}">
                                {{ $procurement->fundSource?->fundsources ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- ABC Amount -->
                        <td class="px-3 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                            <div class="inline-flex items-baseline gap-0.5">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                <span
                                    class="text-emerald-700 dark:text-emerald-400">{{ number_format($procurement->abc ?? 0, 2) }}</span>
                            </div>
                        </td>

                        <!-- Approved PPMP -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->approved_ppmp !== null && $procurement->approved_ppmp !== '')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $procurement->approved_ppmp == '1' || strtolower($procurement->approved_ppmp) === 'yes' ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">
                                    {{ $procurement->approved_ppmp == '1' || strtolower((string) $procurement->approved_ppmp) === 'yes' ? 'Yes' : $procurement->approved_ppmp }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">N/A</span>
                            @endif
                        </td>

                        <!-- EPA (Early Procurement Activity) -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->early_procurement)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700">
                                    Yes
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600">
                                    No
                                </span>
                            @endif
                        </td>

                        <!-- Procurement Stage -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->currentPrStage)
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 dark:from-blue-900/40 dark:to-blue-800/40 dark:text-blue-200 dark:border-blue-700 shadow-sm">
                                    {{ $procurement->currentPrStage->procurementStage?->procurementstage ?? 'N/A' }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">No Stage</span>
                            @endif
                        </td>

                        <!-- Current Mode -->
                        <td class="px-3 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                            @if ($procurement->currentMode)
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300 dark:from-purple-900/40 dark:to-purple-800/40 dark:text-purple-200 dark:border-purple-700 shadow-sm">
                                    {{ $procurement->currentMode->modeofprocurements }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">No Mode</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="18" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-sm font-medium">No PRs received found</div>
                                <div class="text-xs text-gray-400">Try adjusting your search or filters</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Enhanced Footer Pagination -->
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

        <!-- Left: Per-page selector -->
        <div class="flex items-center gap-x-2">
            <label for="perPage" class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
            <select id="perPage" wire:model.live="perPage"
                class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
        </div>

        <!-- Center: Summary + Pagination -->
        <div class="flex flex-col items-center justify-center gap-3 flex-1">
            <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                Showing <span
                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->firstItem() }}</span>
                to
                <span
                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->lastItem() }}</span>
                of
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->total() }}</span>
                items
            </div>
            <div class="flex justify-center">
                {{ $procurements->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>
