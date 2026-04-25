<div>

    <!-- ===== Completed Procurement Activities Card ===== -->
    <div
        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col mb-4">

        <!-- ===== Header ===== -->
        <div
            class="sticky top-0 z-40 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 w-full">

            <!-- Title Row -->
            <div class="px-6 py-3 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-emerald-600 dark:text-emerald-400 shrink-0">
                                <path fill-rule="evenodd"
                                    d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm4.5 7.5a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0v-2.25a.75.75 0 0 1 .75-.75Zm3.75-1.5a.75.75 0 0 0-1.5 0v4.5a.75.75 0 0 0 1.5 0V12Zm2.25-3a.75.75 0 0 1 .75.75v6.75a.75.75 0 0 1-1.5 0V9.75A.75.75 0 0 1 13.5 9Zm3.75-1.5a.75.75 0 0 0-1.5 0v9a.75.75 0 0 0 1.5 0v-9Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Procurement Status Report</h2>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 pl-8">
                            As of {{ \App\Livewire\Reports\ProcurementStatusPage::quarterName($quarter) }} Quarter
                            {{ $year }} &mdash;
                            DEPARTMENT OF HEALTH &ndash;WESTERN VISAYAS CENTER FOR HEALTH DEVELOPMENT
                        </p>
                    </div>

                    <!-- Export -->
                    <button type="button" wire:click="exportToExcel"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-150 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                        wire:loading.attr="disabled">
                        <svg wire:loading.remove wire:target="exportToExcel" class="w-4 h-4" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20ZM10.5 15.5L9 14L7.5 15.5L6.5 14.5L8 13L6.5 11.5L7.5 10.5L9 12L10.5 10.5L11.5 11.5L10 13L11.5 14.5L10.5 15.5ZM13 13.5H17V15H13V13.5ZM13 11H17V12.5H13V11Z" />
                        </svg>
                        <svg wire:loading wire:target="exportToExcel" class="animate-spin w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <!-- Toolbar: filters -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-900/50 border-b border-gray-100 dark:border-neutral-700">
                <div class="flex items-end gap-2 flex-wrap">

                    <!-- Search -->
                    <div class="relative flex-1 min-w-[180px]">
                        <span
                            class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Search</span>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="PR Number or Program/Project..."
                                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                        </div>
                    </div>

                    <!-- Year -->
                    <div class="shrink-0">
                        <span
                            class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Year</span>
                        <select wire:model.live="year"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            @for ($y = now()->year + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Quarter -->
                    <div class="shrink-0">
                        <span
                            class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Quarter</span>
                        <select wire:model.live="quarter"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            <option value="1">1st Quarter (Jan – Mar)</option>
                            <option value="2">2nd Quarter (Apr – Jun)</option>
                            <option value="3">3rd Quarter (Jul – Sep)</option>
                            <option value="4">4th Quarter (Oct – Dec)</option>
                        </select>
                    </div>

                    <!-- Toggle Advanced Filters -->
                    <div class="shrink-0">
                        <button type="button" wire:click="$toggle('showAdvancedFilters')"
                            class="inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 border border-emerald-300 dark:border-emerald-600 rounded-lg bg-white hover:bg-emerald-50 dark:bg-neutral-800 dark:hover:bg-neutral-700 transition-colors duration-150"
                            title="{{ $showAdvancedFilters ? 'Hide Filters' : 'Show Filters' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </button>
                    </div>

                </div>

                <!-- Advanced Filters (hidden by default) -->
                @if ($showAdvancedFilters)
                    <div
                        class="flex items-end gap-2 flex-wrap mt-3 pt-3 border-t border-gray-200 dark:border-neutral-700">

                        <!-- PMO/End-User Filter -->
                        <div class="relative flex-1 min-w-[180px]">
                            <span
                                class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">PMO/End-User</span>
                            <div class="relative">
                                <select wire:model.live="pmoEndUserFilter"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                                    <option value="">All PMO/End-Users</option>
                                    @foreach ($pmoEndUserOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Source of Funds Filter -->
                        <div class="relative flex-1 min-w-[180px]">
                            <span
                                class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Source
                                of Funds</span>
                            <div class="relative">
                                <select wire:model.live="sourceOfFundsFilter"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                                    <option value="">All Sources</option>
                                    @foreach ($sourceOfFundsOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="relative flex-1 min-w-[180px]">
                            <span
                                class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Category</span>
                            <div class="relative">
                                <select wire:model.live="categoryFilter"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                                    <option value="">All Categories</option>
                                    @foreach ($categoryOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>

        <!-- ===== Table ===== -->
        <div class="overflow-x-auto">
            <div wire:loading.class="opacity-60 pointer-events-none" wire:target="search,year,quarter,perPage">
                <table class="w-full text-xs border-collapse min-w-[1800px]">
                    <thead>
                        <!-- Group header row -->
                        <tr class="bg-emerald-700 text-white">
                            <th rowspan="2"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600 whitespace-nowrap min-w-[90px]">
                                Code (PAP)
                            </th>
                            <th rowspan="2"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600 min-w-[180px]">
                                Procurement Project
                            </th>
                            <th rowspan="2"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900  bg-emerald-600 min-w-[120px]">
                                PMO/End-User
                            </th>
                            <th rowspan="2"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600 min-w-[130px]">
                                Mode of Procurement
                            </th>
                            <!-- Actual Procurement Activity (spans 12) -->
                            <th colspan="12"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600">
                                Actual Procurement Activity
                            </th>
                            <th rowspan="2"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600 min-w-[100px]">
                                Source of Funds
                            </th>
                            <!-- ABC -->
                            <th colspan="3"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600">
                                ABC (PhP)
                            </th>
                            <!-- Contract Cost -->
                            <th colspan="3"
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 bg-emerald-600">
                                Contract Cost (PhP)
                            </th>
                        </tr>
                        <!-- Sub-header row -->
                        <tr class="bg-emerald-600 text-white">
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Pre-Proc Conference</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Ads/Post of IB</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[80px]">
                                Pre-bid Conf</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Eligibility Check</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Sub/Open of Bids</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Bid Evaluation</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[80px]">
                                Post Qual</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Notice of Award</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Contract Signing</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[90px]">
                                Notice to Proceed</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[100px]">
                                Delivery/Completion</th>
                            <th
                                class="px-2 py-2 text-center font-semibold border border-emerald-900 whitespace-nowrap min-w-[110px]">
                                Inspection &amp; Acceptance</th>
                            <!-- ABC sub-headers -->
                            <th class="px-2 py-2 text-center font-semibold border border-emerald-900 min-w-[90px]">
                                Total
                            </th>
                            <th class="px-2 py-2 text-center font-semibold border border-emerald-900 min-w-[90px]">MOOE
                            </th>
                            <th class="px-2 py-2 text-center font-semibold border border-emerald-900 min-w-[90px]">CO
                            </th>
                            <!-- Contract Cost sub-headers -->
                            <th class="px-2 py-2 text-center font-semibold border border-emerald-900 min-w-[90px]">
                                Total</th>
                            <th class="px-2 py-2 text-center font-semibold border border-emerald-900 min-w-[90px]">MOOE
                            </th>
                            <th class="px-2 py-2 text-center font-semibold border border-emerald-900 min-w-[90px]">CO
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-700">
                        @forelse ($rows as $row)
                            @if (isset($row['_section_header']))
                                <tr>
                                    <td colspan="23"
                                        class="px-3 py-2 text-xs font-bold tracking-widest uppercase text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-700">
                                        {{ $row['_section_header'] }}
                                    </td>
                                </tr>
                            @else
                                <tr class="hover:bg-emerald-50/40 dark:hover:bg-emerald-900/10 transition-colors">
                                    <td
                                        class="px-2 py-2 text-center font-mono text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @can('view_procurement')
                                            <a href="{{ route('procurements.view', ['procurement' => $row['proc_id']]) }}"
                                                target="_blank"
                                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-400 transition-colors text-emerald-700 dark:text-emerald-300">
                                                {{ $row['code_pap'] }}
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs text-emerald-700 dark:text-emerald-300">
                                                {{ $row['code_pap'] }}
                                            </span>
                                        @endcan
                                    </td>
                                    <td
                                        class="px-2 py-2 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700">
                                        {{ $row['project'] }}
                                    </td>
                                    <td
                                        class="px-2 py-2 text-center text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        {{ $row['end_user'] }}
                                    </td>
                                    <td
                                        class="px-2 py-2 text-center text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700">
                                        {{ $row['mode'] }}
                                    </td>
                                    <!-- Activity dates -->
                                    @foreach (['pre_proc_conf', 'ads_post_ib', 'pre_bid_conf', 'eligibility', 'sub_open', 'bid_eval', 'post_qual', 'notice_of_award', 'contract_signing', 'notice_to_proceed', 'delivery_completion', 'inspection_acceptance'] as $col)
                                        <td
                                            class="px-2 py-2 text-center border border-gray-200 dark:border-neutral-700 whitespace-nowrap {{ $row[$col] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-neutral-600' }}">
                                            {{ $row[$col] ?: '—' }}
                                        </td>
                                    @endforeach
                                    <!-- Source of Funds -->
                                    <td
                                        class="px-2 py-2 text-center text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        {{ $row['fund_source'] }}
                                    </td>
                                    <!-- ABC -->
                                    <td
                                        class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @if ($row['abc_total'] !== '')
                                            {{ number_format((float) $row['abc_total'], 2) }}
                                        @else
                                            <span class="text-gray-300 dark:text-neutral-600">—</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @if ($row['abc_mooe'] !== '' && $row['abc_mooe'] != 0)
                                            {{ number_format((float) $row['abc_mooe'], 2) }}
                                        @else
                                            <span class="text-gray-300 dark:text-neutral-600">—</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @if ($row['abc_co'] !== '' && $row['abc_co'] != 0)
                                            {{ number_format((float) $row['abc_co'], 2) }}
                                        @else
                                            <span class="text-gray-300 dark:text-neutral-600">—</span>
                                        @endif
                                    </td>
                                    <!-- Contract Cost -->
                                    <td
                                        class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @if ($row['contract_total'] !== '' && $row['contract_total'] !== null)
                                            {{ number_format((float) $row['contract_total'], 2) }}
                                        @else
                                            <span class="text-gray-300 dark:text-neutral-600">—</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @if (!empty($row['contract_mooe']) && $row['contract_mooe'] != 0)
                                            {{ number_format((float) $row['contract_mooe'], 2) }}
                                        @else
                                            <span class="text-gray-300 dark:text-neutral-600">—</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                        @if (!empty($row['contract_co']) && $row['contract_co'] != 0)
                                            {{ number_format((float) $row['contract_co'], 2) }}
                                        @else
                                            <span class="text-gray-300 dark:text-neutral-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="23"
                                    class="px-6 py-12 text-center text-gray-400 dark:text-neutral-500 text-sm">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-10 opacity-30">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span>No procurement records found for
                                            {{ \App\Livewire\Reports\ProcurementStatusPage::quarterName($quarter) }}
                                            Quarter {{ $year }}.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== Completed Footer / Pagination ===== -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">
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
            <div class="flex flex-col items-center justify-center gap-3 flex-1">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                    Showing <span
                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->firstItem() ?? 0 }}</span>
                    to
                    <span
                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->lastItem() ?? 0 }}</span>
                    of
                    <span
                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $completedRowCount }}</span>
                    items
                </div>
                <div class="flex justify-center">
                    {{ $procurements->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>

        {{-- ===== Completed Totals Strip ===== --}}
        <div class="border-t border-emerald-100 dark:border-emerald-900/30 bg-gradient-to-r from-emerald-50/60 to-white dark:from-emerald-900/10 dark:to-neutral-800 px-5 py-3 flex flex-wrap items-center justify-end gap-x-6 gap-y-1.5">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Total Allotted Budget:
                <strong class="font-mono text-emerald-700 dark:text-emerald-300 ml-1">₱ {{ number_format($completedAbcTotal, 2) }}</strong>
            </span>
            <span class="w-px h-4 bg-gray-200 dark:bg-neutral-600 hidden sm:block"></span>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Contract Price:
                <strong class="font-mono text-blue-700 dark:text-blue-300 ml-1">₱ {{ number_format($completedContractTotal, 2) }}</strong>
            </span>
            <span class="w-px h-4 bg-gray-200 dark:bg-neutral-600 hidden sm:block"></span>
            <span class="text-xs {{ $completedSavingsTotal >= 0 ? 'text-gray-500 dark:text-gray-400' : 'text-gray-500 dark:text-gray-400' }}">
                Total Savings:
                <strong class="font-mono ml-1 {{ $completedSavingsTotal >= 0 ? 'text-teal-700 dark:text-teal-300' : 'text-red-600 dark:text-red-400' }}">₱ {{ number_format($completedSavingsTotal, 2) }}</strong>
            </span>
        </div>

    </div>{{-- end completed card --}}


    <!-- ===== On-Going Procurement Activities Card ===== -->
    <div
        class="bg-white border border-amber-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-amber-700 flex flex-col">



        <!-- ===== On-Going Table ===== -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse min-w-[1800px]">
                <thead>
                    <!-- Group header row -->
                    <tr class="bg-amber-600 text-white">
                        <th rowspan="2"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Code (PAP)
                        </th>
                        <th rowspan="2"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[180px]">
                            Procurement Project
                        </th>
                        <th rowspan="2"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[120px]">
                            PMO/End-User (Cluster)
                        </th>
                        <th rowspan="2"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[130px]">
                            Mode of Procurement
                        </th>
                        <th colspan="12"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 bg-amber-500">
                            Actual Procurement Activity
                        </th>
                        <th rowspan="2"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[100px]">
                            Source of Funds
                        </th>
                        <th colspan="3"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 bg-amber-500">
                            ABC (PhP)
                        </th>
                        <th colspan="3"
                            class="px-2 py-2 text-center font-semibold border border-amber-900 bg-amber-500">
                            Contract Cost (PhP)
                        </th>
                    </tr>
                    <tr class="bg-amber-500 text-white">
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Pre-Proc Conference</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Ads/Post of IB</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[80px]">
                            Pre-bid Conf</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Eligibility Check</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Sub/Open of Bids</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Bid Evaluation</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[80px]">
                            Post Qual</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Notice of Award</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Contract Signing</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[90px]">
                            Notice to Proceed</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[100px]">
                            Delivery/Completion</th>
                        <th
                            class="px-2 py-2 text-center font-semibold border border-amber-900 whitespace-nowrap min-w-[110px]">
                            Inspection &amp; Acceptance</th>
                        <th class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[90px]">Total</th>
                        <th class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[90px]">MOOE</th>
                        <th class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[90px]">CO</th>
                        <th class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[90px]">Total</th>
                        <th class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[90px]">MOOE</th>
                        <th class="px-2 py-2 text-center font-semibold border border-amber-900 min-w-[90px]">CO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-700">
                    @forelse ($ongoingRows as $row)
                        @if (isset($row['_section_header']))
                            <tr>
                                <td colspan="23"
                                    class="px-3 py-2 text-xs font-bold tracking-widest uppercase text-amber-800 dark:text-amber-200 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700">
                                    {{ $row['_section_header'] }}
                                </td>
                            </tr>
                        @else
                            <tr class="hover:bg-amber-50/40 dark:hover:bg-amber-900/10 transition-colors">
                                <td
                                    class="px-2 py-2 text-center font-mono text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @can('view_procurement')
                                        <a href="{{ route('procurements.view', ['procurement' => $row['proc_id']]) }}"
                                            target="_blank"
                                            class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-400 transition-colors text-emerald-700 dark:text-emerald-300">
                                            {{ $row['code_pap'] }}
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs text-emerald-700 dark:text-emerald-300">
                                            {{ $row['code_pap'] }}
                                        </span>
                                    @endcan
                                </td>
                                <td
                                    class="px-2 py-2 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700">
                                    {{ $row['project'] }}
                                </td>
                                <td
                                    class="px-2 py-2 text-center text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    {{ $row['end_user'] }}
                                </td>
                                <td
                                    class="px-2 py-2 text-center text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700">
                                    {{ $row['mode'] }}
                                </td>
                                @foreach (['pre_proc_conf', 'ads_post_ib', 'pre_bid_conf', 'eligibility', 'sub_open', 'bid_eval', 'post_qual', 'notice_of_award', 'contract_signing', 'notice_to_proceed', 'delivery_completion', 'inspection_acceptance'] as $col)
                                    <td
                                        class="px-2 py-2 text-center border border-gray-200 dark:border-neutral-700 whitespace-nowrap {{ $row[$col] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-neutral-600' }}">
                                        {{ $row[$col] ?: '—' }}
                                    </td>
                                @endforeach
                                <td
                                    class="px-2 py-2 text-center text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    {{ $row['fund_source'] }}
                                </td>
                                <td
                                    class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @if ($row['abc_total'] !== '')
                                        {{ number_format((float) $row['abc_total'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-neutral-600">—</span>
                                    @endif
                                </td>
                                <td
                                    class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @if ($row['abc_mooe'] !== '' && $row['abc_mooe'] != 0)
                                        {{ number_format((float) $row['abc_mooe'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-neutral-600">—</span>
                                    @endif
                                </td>
                                <td
                                    class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @if ($row['abc_co'] !== '' && $row['abc_co'] != 0)
                                        {{ number_format((float) $row['abc_co'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-neutral-600">—</span>
                                    @endif
                                </td>
                                <td
                                    class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @if ($row['contract_total'] !== '' && $row['contract_total'] !== null)
                                        {{ number_format((float) $row['contract_total'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-neutral-600">—</span>
                                    @endif
                                </td>
                                <td
                                    class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @if (!empty($row['contract_mooe']) && $row['contract_mooe'] != 0)
                                        {{ number_format((float) $row['contract_mooe'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-neutral-600">—</span>
                                    @endif
                                </td>
                                <td
                                    class="px-2 py-2 text-right text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-neutral-700 whitespace-nowrap">
                                    @if (!empty($row['contract_co']) && $row['contract_co'] != 0)
                                        {{ number_format((float) $row['contract_co'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-neutral-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="23"
                                class="px-6 py-12 text-center text-gray-400 dark:text-neutral-500 text-sm">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-10 opacity-30">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span>No on-going procurement records found for
                                        {{ \App\Livewire\Reports\ProcurementStatusPage::quarterName($quarter) }}
                                        Quarter {{ $year }}.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ===== On-Going Footer / Pagination ===== -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-amber-200 dark:border-amber-700 gap-3 bg-gradient-to-r from-amber-50 to-white dark:from-neutral-900 dark:to-neutral-800">

            <!-- Left: Per-page selector -->
            <div class="flex items-center gap-x-2">
                <label for="ongoingPerPage" class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
                <select id="ongoingPerPage" wire:model.live="ongoingPerPage"
                    class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
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
                        class="text-amber-600 dark:text-amber-400 font-semibold">{{ $ongoingProcurements->firstItem() ?? 0 }}</span>
                    to
                    <span
                        class="text-amber-600 dark:text-amber-400 font-semibold">{{ $ongoingProcurements->lastItem() ?? 0 }}</span>
                    of
                    <span
                        class="text-amber-600 dark:text-amber-400 font-semibold">{{ $ongoingRowCount }}</span>
                    items
                </div>
                <div class="flex justify-center">
                    {{ $ongoingProcurements->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>

        {{-- ===== On-Going Totals Strip ===== --}}
        <div class="border-t border-amber-100 dark:border-amber-900/30 bg-gradient-to-r from-amber-50/60 to-white dark:from-amber-900/10 dark:to-neutral-800 px-5 py-3 flex items-center justify-end">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Total Allotted Budget of On-Going Procurement Activities:
                <strong class="font-mono text-amber-700 dark:text-amber-300 ml-1">₱ {{ number_format($ongoingAbcTotal, 2) }}</strong>
            </span>
        </div>

    </div>{{-- end on-going card --}}

    {{-- ===== Summary Card ===== --}}
    <div class="mt-6 mb-6 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-5 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0">
                <path fill-rule="evenodd"
                    d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.04 16.5.5-1.5h6.42l.5 1.5H8.29Zm7.46-12a.75.75 0 0 0-1.5 0v6a.75.75 0 0 0 1.5 0v-6Zm-3 2.25a.75.75 0 0 0-1.5 0v3.75a.75.75 0 0 0 1.5 0V9Zm-3 3a.75.75 0 0 0-1.5 0v.75a.75.75 0 0 0 1.5 0V12Z"
                    clip-rule="evenodd" />
            </svg>
            <h3 class="text-sm font-bold text-gray-800 dark:text-white">Procurement Summary</h3>
            <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">— ABC breakdown by status</span>
        </div>

        {{-- Stacked proportion bar --}}
        <div class="flex w-full h-2.5 overflow-hidden">
            <div class="bg-emerald-500 h-full transition-all duration-500"
                style="width: {{ number_format($summaryPctCompleted, 2) }}%"></div>
            <div class="bg-amber-400 h-full transition-all duration-500"
                style="width: {{ number_format($summaryPctOngoing, 2) }}%"></div>
        </div>

        {{-- Completed row --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-neutral-700">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Completed</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">({{ $completedRowCount }} items)</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono text-gray-600 dark:text-gray-300">₱ {{ number_format($summaryAbcCompleted, 2) }}</span>
                    <span class="text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400 w-14 text-right">{{ number_format($summaryPctCompleted, 2) }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 dark:bg-neutral-700 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                    style="width: {{ number_format($summaryPctCompleted, 2) }}%"></div>
            </div>
        </div>

        {{-- On-Going row --}}
        <div class="px-5 py-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">On-Going</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">({{ $ongoingRowCount }} items)</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono text-gray-600 dark:text-gray-300">₱ {{ number_format($summaryAbcOngoing, 2) }}</span>
                    <span class="text-xs font-bold font-mono text-amber-500 dark:text-amber-400 w-14 text-right">{{ number_format($summaryPctOngoing, 2) }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-100 dark:bg-neutral-700 rounded-full h-2 overflow-hidden">
                <div class="bg-amber-400 h-2 rounded-full transition-all duration-500"
                    style="width: {{ number_format($summaryPctOngoing, 2) }}%"></div>
            </div>
        </div>

        {{-- Total footer --}}
        <div class="border-t-2 border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="size-4 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <path fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 0 1 8.25-8.25.75.75 0 0 1 .75.75v6.75H18a.75.75 0 0 1 .75.75 8.25 8.25 0 0 1-16.5 0Z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M12.75 3a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3Z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Total ABC (PhP)</span>
                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $completedRowCount + $ongoingRowCount }} items)</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold font-mono text-emerald-700 dark:text-emerald-300">₱ {{ number_format($summaryAbcTotal, 2) }}</span>
                <span class="text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400 w-14 text-right">{{ number_format($summaryPctTotal, 2) }}%</span>
            </div>
        </div>

    </div>

</div>{{-- end wrapper --}}
