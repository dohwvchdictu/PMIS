<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col mb-6">

    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 w-full">

        <!-- Title Row -->
        <div class="px-6 py-3 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="size-6 text-blue-600 dark:text-blue-400">
                        <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z" />
                        <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                    </svg>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Procurement Monitoring Report (CAT B)</h2>
                </div>
                <!-- Export to Excel -->
                <button type="button" wire:click="exportToExcel"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-150 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <svg wire:loading.remove wire:target="exportToExcel" class="w-4 h-4" viewBox="0 0 24 24"
                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2ZM18 20H6V4H13V9H18V20ZM10.5 15.5L9 14L7.5 15.5L6.5 14.5L8 13L6.5 11.5L7.5 10.5L9 12L10.5 10.5L11.5 11.5L10 13L11.5 14.5L10.5 15.5ZM13 13.5H17V15H13V13.5ZM13 11H17V12.5H13V11Z" />
                    </svg>
                    <svg wire:loading wire:target="exportToExcel" class="animate-spin w-4 h-4"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-900/50 border-b border-gray-100 dark:border-neutral-700">
            <div class="flex items-end gap-2 flex-wrap">

                <!-- Search -->
                <div class="relative flex-1 min-w-[200px]">
                    <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Search</span>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search PR Number or Program/Project..."
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                    </div>
                </div>

                <!-- Year -->
                <div class="shrink-0">
                    <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Year</span>
                    <input type="number" wire:model.live="year" min="2000" max="2099"
                        class="w-24 px-2.5 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-neutral-800 dark:text-white dark:border-neutral-600" />
                </div>

                <div class="w-px h-8 bg-gray-300 dark:bg-neutral-600 shrink-0 self-end"></div>

                <!-- Filters Toggle -->
                <div class="shrink-0">
                    <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Filters</span>
                    <button type="button" wire:click="$toggle('showFilters')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border transition-colors duration-150
                            {{ $showFilters || $clusterFilter || $fundSourceFilter || $currentModeFilter
                                ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700'
                                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        Filters
                    </button>
                </div>

                <!-- Clear -->
                @if ($search || $clusterFilter || $fundSourceFilter || $currentModeFilter)
                    <div class="shrink-0">
                        <span class="text-[10px] font-semibold text-transparent block mb-1">‎</span>
                        <button type="button" wire:click="clearFilters"
                            class="inline-flex items-center gap-1 px-2.5 py-2 text-xs font-semibold text-red-500 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors duration-150 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear
                        </button>
                    </div>
                @endif

            </div>
        </div>

        <!-- Filter Panel -->
        @if ($showFilters || $clusterFilter || $fundSourceFilter || $currentModeFilter)
            <div class="px-4 py-3 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

                    <div class="relative z-50">
                        <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Unit / Cluster</label>
                        <x-forms.searchable-select wire:model.live="clusterFilter" :options="$clusterOptions" labelKey="name" valueKey="id" placeholder="All" />
                    </div>

                    <div class="relative z-50">
                        <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Mode of Procurement</label>
                        <x-forms.searchable-select wire:model.live="currentModeFilter" :options="$modes" labelKey="name" valueKey="id" placeholder="All" />
                    </div>

                    <div class="relative z-40">
                        <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide block mb-1">Fund Source</label>
                        <x-forms.searchable-select wire:model.live="fundSourceFilter" :options="$fundSources" labelKey="name" valueKey="id" placeholder="All" />
                    </div>

                </div>
            </div>
        @endif

    </div>

    <!-- Table -->
    <div class="overflow-auto flex-1">
        <table class="table-auto w-full min-w-[12000px] divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-30">
                <tr>
                    {{-- 1 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">PR Number</th>
                    {{-- 2 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">IB Number</th>
                    {{-- 3 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-24">NP No.</th>
                    {{-- 4 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-96">Procurement Program / Project</th>
                    {{-- 5 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Date Receipt</th>
                    {{-- 6 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-24">RBAC/SBAC</th>
                    {{-- 7 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">DTRACK #</th>
                    {{-- 8 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">UniCode</th>
                    {{-- 9 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-40">Division</th>
                    {{-- 10 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Cluster / Committee</th>
                    {{-- 11 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">Category</th>
                    {{-- 12 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Venue (Specific)</th>
                    {{-- 13 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Venue (Province/HUC)</th>
                    {{-- 14 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">Category / Venue</th>
                    {{-- 15 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-24">w/ Approved PPMP</th>
                    {{-- 16 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-24">APP (Updated)</th>
                    {{-- 17 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Immediate Date Needed</th>
                    {{-- 18 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Date Needed</th>
                    {{-- 19 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-40">PMO / End-User</th>
                    {{-- 20 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-20">EPA</th>
                    {{-- 21 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-40">Source of Funds</th>
                    {{-- 22 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Expense Class</th>
                    {{-- 23 --}} <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">ABC</th>
                    {{-- 24 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">Mode of Procurement</th>
                    {{-- 25 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-24">ABC &lt;=&gt; 50k</th>
                    {{-- 26 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Pre-Proc Conference</th>
                    {{-- 27 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Ads/Post of IB</th>
                    {{-- 28 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Pre-bid Conf</th>
                    {{-- 29 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Eligibility Check</th>
                    {{-- 30 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Sub/Open of Bids</th>
                    {{-- 31 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">1st Bidding Date</th>
                    {{-- 32 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">1st Bidding Result</th>
                    {{-- 33 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">2nd Bidding Date</th>
                    {{-- 34 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">2nd Bidding Result</th>
                    {{-- 35 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Bid Evaluation Date</th>
                    {{-- 36 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Post Qual Date</th>
                    {{-- 37 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Resolution No. (Recom. Mode)</th>
                    {{-- 38 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">NTF No.</th>
                    {{-- 39 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">NTF Bidding Date</th>
                    {{-- 40 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">NTF Bidding Result</th>
                    {{-- 41 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">RFQ No.</th>
                    {{-- 42 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Canvass Date</th>
                    {{-- 43 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Date Returned of Canvass</th>
                    {{-- 44 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Abstract of Canvass Date</th>
                    {{-- 45 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Date of BAC Resolution (Award)</th>
                    {{-- 46 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Notice of Award Date</th>
                    {{-- 47 --}} <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Awarded Amount</th>
                    {{-- 48 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">Award Notice Number</th>
                    {{-- 49 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Date of Posting of Award (PhilGEPS)</th>
                    {{-- 50 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">Supplier</th>
                    {{-- 51 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">Procurement Stage</th>
                    {{-- 52 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Remarks (Status)</th>
                    {{-- 53 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">Remarks (Notes)</th>
                    {{-- 54 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">Reschedule / Cancellation Letter</th>
                    {{-- 55 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Date Forwarded to PMU</th>
                    {{-- 56 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">PhilGEPS Posting Ref No.</th>
                    {{-- 57 --}} <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Contract Amount</th>
                    {{-- 58 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-36">PO / Contract Number</th>
                    {{-- 59 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Contract Signing / PO</th>
                    {{-- 60 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Notice to Proceed</th>
                    {{-- 61 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-28">Delivery / Completion</th>
                    {{-- 62 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap w-32">Inspection &amp; Acceptance</th>
                    {{-- 63 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">List of Invited Observers</th>
                    {{-- 64 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Observers (Pre-bid Conf)</th>
                    {{-- 65 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Observers (Eligibility)</th>
                    {{-- 66 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Observers (Sub/Open)</th>
                    {{-- 67 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Observers (Bid Eval)</th>
                    {{-- 68 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">Observers (Post Qual)</th>
                    {{-- 69 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">Delivery/Completion/Acceptance (if applicable)</th>
                    {{-- 70 --}} <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">Remarks (Changes from APP)</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800">

                @forelse ($procurements as $procurement)
                    @php
                        $fmt = function($d) {
                            if (!$d) return '';
                            try { return \Carbon\Carbon::parse($d)->format('M d, Y'); } catch (\Exception $e) { return $d; }
                        };
                        $isPerLot = $procurement->procurement_type === 'perLot';
                    @endphp

                    @if ($isPerLot)
                        @php
                            $latestMop = $procurement->mopLots->sortByDesc('mode_order')->first();
                            $modeId    = $latestMop?->mode_of_procurement_id;
                            $isBidding = in_array($modeId, [2, 3, 4, 5, 6]);
                            $isSvp     = in_array($modeId, range(7, 24));
                            $key       = $procurement->procID . '_' . ($latestMop?->uid ?? '');

                            $biddingGroup = $lotBidScheduleMap->get($key);
                            $bidding1     = $biddingGroup?->get(1);
                            $bidding2     = $biddingGroup?->get(2);
                            $mainBid      = $bidding1 ?? $biddingGroup?->first();

                            $prSvp    = $lotPrSvpMap->get($key);
                            $pmuPo    = $lotPmuPoMap->get($procurement->procID);
                            $supply   = $supplyMap->get($pmuPo?->po_contract_number);
                            $supplyPo = $supply?->supplyPos->first();

                            $post = $procurement->postProcurement;
                            $pmu  = $post?->pmu;

                            $remarkText = $procurement->currentLotRemark?->remark?->remarks ?? '';
                            $r = strtolower($remarkText);
                            $remarkBadge = match (true) {
                                str_contains($r, 'award') || str_contains($r, 'complet') || str_contains($r, 'approved') || str_contains($r, 'done')
                                    => 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/40 dark:text-green-300 dark:border-green-700',
                                str_contains($r, 'cancel') || str_contains($r, 'terminat') || str_contains($r, 'reject') || str_contains($r, 'disapprov') || str_contains($r, 'failed') || str_contains($r, 'lapsed')
                                    => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/40 dark:text-red-300 dark:border-red-700',
                                str_contains($r, 'hold') || str_contains($r, 'suspend') || str_contains($r, 'defer') || str_contains($r, 'return') || str_contains($r, 'revert')
                                    => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-700',
                                str_contains($r, 'ongoing') || str_contains($r, 'in progress') || str_contains($r, 'active') || str_contains($r, 'proceed') || str_contains($r, 'posted') || str_contains($r, 'publish')
                                    => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
                                str_contains($r, 'pending') || str_contains($r, 'for eval') || str_contains($r, 'for review') || str_contains($r, 'for approval') || str_contains($r, 'waiting') || str_contains($r, 'endors')
                                    => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700',
                                str_contains($r, 'rebid') || str_contains($r, 're-bid') || str_contains($r, 'repeat')
                                    => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300 dark:border-purple-700',
                                default => 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600',
                            };
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-gray-50/50 dark:bg-neutral-900/50' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-blue-50 hover:to-sky-50 dark:hover:from-blue-900/20 dark:hover:to-sky-900/20 transition-all duration-200">
                            {{-- 1 --}} <td class="px-3 py-2 text-center text-xs font-bold text-blue-700 dark:text-blue-300 whitespace-nowrap"><span class="inline-flex items-center px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-md font-mono text-xs whitespace-nowrap">{{ $procurement->pr_number }}</span></td>
                            {{-- 2 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? ($mainBid?->ib_number ?? '') : '' }}</td>
                            {{-- 3 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            {{-- 4 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2" title="{{ $procurement->procurement_program_project }}">{{ $procurement->procurement_program_project }}</div></td>
                            {{-- 5 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($procurement->date_receipt) }}</td>
                            {{-- 6 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $procurement->category?->bacType?->abbreviation ?? '' }}</td>
                            {{-- 7 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $procurement->dtrack_no }}</td>
                            {{-- 8 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $procurement->unicode }}</td>
                            {{-- 9 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->division?->divisions ?? '' }}</div></td>
                            {{-- 10 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->clusterCommittee?->clustercommittee ?? '' }}</div></td>
                            {{-- 11 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->category?->category ?? '' }}</div></td>
                            {{-- 12 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->venueSpecific?->name ?? '' }}</div></td>
                            {{-- 13 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->venueProvincesHUC?->province_huc ?? '' }}</div></td>
                            {{-- 14 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->category_venue }}</div></td>
                            {{-- 15 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $procurement->approved_ppmp ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">{{ $procurement->approved_ppmp ? 'Yes' : 'No' }}</span></td>
                            {{-- 16 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $procurement->app_updated ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">{{ $procurement->app_updated ? 'Yes' : 'No' }}</span></td>
                            {{-- 17 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($procurement->immediate_date_needed) }}</td>
                            {{-- 18 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($procurement->date_needed) }}</td>
                            {{-- 19 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->endUser?->endusers ?? '' }}</div></td>
                            {{-- 20 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $procurement->early_procurement ? 'bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">{{ $procurement->early_procurement ? 'Yes' : 'No' }}</span></td>
                            {{-- 21 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->fundSource?->fundsources ?? '' }}</div></td>
                            {{-- 22 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $procurement->expense_class }}</td>
                            {{-- 23 ABC --}} <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">@if ($procurement->abc !== null)<div class="inline-flex items-baseline gap-0.5"><span class="text-gray-500 dark:text-gray-400 font-normal">₱</span><span class="text-blue-700 dark:text-blue-400">{{ number_format($procurement->abc, 2) }}</span></div>@endif</td>
                            {{-- 24 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $latestMop?->modeOfProcurement?->modeofprocurements ?? '' }}</div></td>
                            {{-- 25 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $procurement->abc_50k }}</td>
                            {{-- 26 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->pre_proc_conference) : '' }}</td>
                            {{-- 27 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->ads_post_ib) : ($isSvp ? $fmt($prSvp?->ads_post_ib) : '') }}</td>
                            {{-- 28 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->pre_bid_conf) : '' }}</td>
                            {{-- 29 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->eligibility_check) : '' }}</td>
                            {{-- 30 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->sub_open_bids) : '' }}</td>
                            {{-- 31 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($bidding1?->sub_open_bids) : '' }}</td>
                            {{-- 32 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($bidding1?->bidding_result ?? '') : '' }}</td>
                            {{-- 33 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($bidding2?->sub_open_bids) : '' }}</td>
                            {{-- 34 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($bidding2?->bidding_result ?? '') : '' }}</td>
                            {{-- 35 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->bid_evaluation_date) : '' }}</td>
                            {{-- 36 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->post_qualification_date) : '' }}</td>
                            {{-- 37 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($mainBid?->resolution_number_mop ?? '') : ($isSvp ? ($prSvp?->resolution_number_mop ?? '') : '') }}</td>
                            {{-- 38 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            {{-- 39 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            {{-- 40 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            {{-- 41 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isSvp ? ($prSvp?->rfq_no ?? '') : '' }}</td>
                            {{-- 42 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isSvp ? $fmt($prSvp?->canvass_date) : '' }}</td>
                            {{-- 43 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isSvp ? $fmt($prSvp?->date_returned_of_canvass) : '' }}</td>
                            {{-- 44 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isSvp ? $fmt($prSvp?->abstract_of_canvass_date) : '' }}</td>
                            {{-- 45 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($post?->resolution_award_date) }}</td>
                            {{-- 46 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($post?->notice_of_award) }}</td>
                            {{-- 47 Awarded Amount --}} <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">@if ($post?->awarded_amount !== null)<div class="inline-flex items-baseline gap-0.5"><span class="text-gray-500 dark:text-gray-400 font-normal">₱</span><span class="text-blue-700 dark:text-blue-400">{{ number_format($post->awarded_amount, 2) }}</span></div>@endif</td>
                            {{-- 48 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $post?->notice_of_award_number ?? '' }}</td>
                            {{-- 49 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($post?->philgeps_posting_of_award) }}</td>
                            {{-- 50 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $post?->supplier?->name ?? '' }}</div></td>
                            {{-- 51 Stage --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">@if ($procurement->currentPrStage?->procurementStage?->procurementstage)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 dark:from-blue-900/40 dark:to-blue-800/40 dark:text-blue-200 dark:border-blue-700 break-words whitespace-normal text-center">{{ $procurement->currentPrStage->procurementStage->procurementstage }}</span>@endif</td>
                            {{-- 52 Remarks Status --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">@if ($remarkText)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border break-words whitespace-normal text-center {{ $remarkBadge }}" title="{{ $remarkText }}">{{ $remarkText }}</span>@endif</td>
                            {{-- 53 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->currentLotRemark?->notes ?? '' }}</div></td>
                            {{-- 54 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            {{-- 55 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($pmu?->date_forwarded) }}</td>
                            {{-- 56 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($mainBid?->philgeps_posting_ref_no ?? '') : ($isSvp ? ($prSvp?->philgeps_posting_ref_no ?? '') : '') }}</td>
                            {{-- 57 Contract Amount --}} <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">@if ($pmuPo?->contract_amount !== null)<div class="inline-flex items-baseline gap-0.5"><span class="text-gray-500 dark:text-gray-400 font-normal">₱</span><span class="text-blue-700 dark:text-blue-400">{{ number_format($pmuPo->contract_amount, 2) }}</span></div>@endif</td>
                            {{-- 58 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $pmuPo?->po_contract_number ?? '' }}</td>
                            {{-- 59 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($pmuPo?->contract_signing_date) }}</td>
                            {{-- 60 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($pmuPo?->notice_to_proceed_date) }}</td>
                            {{-- 61 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($supplyPo?->delivery_completion) }}</td>
                            {{-- 62 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($supplyPo?->date_of_acceptance) }}</td>
                            {{-- 63 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->list_invited_observers ?? '') : '' }}</div></td>
                            {{-- 64 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_prebid_conf ?? '') : '' }}</div></td>
                            {{-- 65 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_eligibility ?? '') : '' }}</div></td>
                            {{-- 66 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_sub_open_of_bid ?? '') : '' }}</div></td>
                            {{-- 67 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_bid ?? '') : '' }}</div></td>
                            {{-- 68 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_post_qual ?? '') : '' }}</div></td>
                            {{-- 69 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            {{-- 70 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                        </tr>

                    @else
                        {{-- perItem: one row per item --}}
                        @foreach ($procurement->pr_items as $item)
                            @php
                                $latestMop = $item->mopItems->sortByDesc('mode_order')->first();
                                $modeId    = $latestMop?->mode_of_procurement_id;
                                $isBidding = in_array($modeId, [2, 3, 4, 5, 6]);
                                $isSvp     = in_array($modeId, range(7, 24));
                                $key       = $item->prItemID . '_' . ($latestMop?->uid ?? '');

                                $biddingGroup = $itemBidScheduleMap->get($key);
                                $bidding1     = $biddingGroup?->get(1);
                                $bidding2     = $biddingGroup?->get(2);
                                $mainBid      = $bidding1 ?? $biddingGroup?->first();

                                $prSvp    = $itemPrSvpMap->get($key);
                                $pmuPo    = $itemPmuPoMap->get($item->prItemID);
                                $supply   = $supplyMap->get($pmuPo?->po_contract_number);
                                $supplyPo = $supply?->supplyPos->first();

                                $post = $item->postProcurement;
                                $pmu  = $post?->pmu;

                                $remarkText = $item->currentItemRemark?->remark?->remarks ?? '';
                                $r = strtolower($remarkText);
                                $remarkBadge = match (true) {
                                    str_contains($r, 'award') || str_contains($r, 'complet') || str_contains($r, 'approved') || str_contains($r, 'done')
                                        => 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/40 dark:text-green-300 dark:border-green-700',
                                    str_contains($r, 'cancel') || str_contains($r, 'terminat') || str_contains($r, 'reject') || str_contains($r, 'disapprov') || str_contains($r, 'failed') || str_contains($r, 'lapsed')
                                        => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/40 dark:text-red-300 dark:border-red-700',
                                    str_contains($r, 'hold') || str_contains($r, 'suspend') || str_contains($r, 'defer') || str_contains($r, 'return') || str_contains($r, 'revert')
                                        => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-700',
                                    str_contains($r, 'ongoing') || str_contains($r, 'in progress') || str_contains($r, 'active') || str_contains($r, 'proceed') || str_contains($r, 'posted') || str_contains($r, 'publish')
                                        => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
                                    str_contains($r, 'pending') || str_contains($r, 'for eval') || str_contains($r, 'for review') || str_contains($r, 'for approval') || str_contains($r, 'waiting') || str_contains($r, 'endors')
                                        => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700',
                                    str_contains($r, 'rebid') || str_contains($r, 're-bid') || str_contains($r, 'repeat')
                                        => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300 dark:border-purple-700',
                                    default => 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600',
                                };
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-sky-50/30 dark:bg-sky-900/10' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-blue-50 hover:to-sky-50 dark:hover:from-blue-900/20 dark:hover:to-sky-900/20 transition-all duration-200">
                                {{-- 1 --}} <td class="px-3 py-2 text-center text-xs font-bold text-blue-700 dark:text-blue-300 whitespace-nowrap"><span class="inline-flex items-center px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-md font-mono text-xs whitespace-nowrap">{{ $procurement->pr_number }}</span></td>
                                {{-- 2 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? ($mainBid?->ib_number ?? '') : '' }}</td>
                                {{-- 3 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 4 item description --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2" title="{{ $item->description ?? $procurement->procurement_program_project }}">{{ $item->description ?? $procurement->procurement_program_project }}</div></td>
                                {{-- 5 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($procurement->date_receipt) }}</td>
                                {{-- 6 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $procurement->category?->bacType?->abbreviation ?? '' }}</td>
                                {{-- 7 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $procurement->dtrack_no }}</td>
                                {{-- 8 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $procurement->unicode }}</td>
                                {{-- 9 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->division?->divisions ?? '' }}</div></td>
                                {{-- 10 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->clusterCommittee?->clustercommittee ?? '' }}</div></td>
                                {{-- 11 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->category?->category ?? '' }}</div></td>
                                {{-- 12 venue N/A for perItem --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 13 venue N/A for perItem --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 14 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->category_venue }}</div></td>
                                {{-- 15 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $procurement->approved_ppmp ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">{{ $procurement->approved_ppmp ? 'Yes' : 'No' }}</span></td>
                                {{-- 16 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $procurement->app_updated ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">{{ $procurement->app_updated ? 'Yes' : 'No' }}</span></td>
                                {{-- 17 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($procurement->immediate_date_needed) }}</td>
                                {{-- 18 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($procurement->date_needed) }}</td>
                                {{-- 19 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->endUser?->endusers ?? '' }}</div></td>
                                {{-- 20 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $procurement->early_procurement ? 'bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700' : 'bg-gray-100 text-gray-600 border border-gray-300 dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600' }}">{{ $procurement->early_procurement ? 'Yes' : 'No' }}</span></td>
                                {{-- 21 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $procurement->fundSource?->fundsources ?? '' }}</div></td>
                                {{-- 22 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $procurement->expense_class }}</td>
                                {{-- 23 ABC = item amount --}} <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">@if ($item->amount !== null)<div class="inline-flex items-baseline gap-0.5"><span class="text-gray-500 dark:text-gray-400 font-normal">₱</span><span class="text-blue-700 dark:text-blue-400">{{ number_format($item->amount, 2) }}</span></div>@endif</td>
                                {{-- 24 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $latestMop?->modeOfProcurement?->modeofprocurements ?? '' }}</div></td>
                                {{-- 25 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $procurement->abc_50k }}</td>
                                {{-- 26 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->pre_proc_conference) : '' }}</td>
                                {{-- 27 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->ads_post_ib) : ($isSvp ? $fmt($prSvp?->ads_post_ib) : '') }}</td>
                                {{-- 28 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->pre_bid_conf) : '' }}</td>
                                {{-- 29 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->eligibility_check) : '' }}</td>
                                {{-- 30 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->sub_open_bids) : '' }}</td>
                                {{-- 31 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($bidding1?->sub_open_bids) : '' }}</td>
                                {{-- 32 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($bidding1?->bidding_result ?? '') : '' }}</td>
                                {{-- 33 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($bidding2?->sub_open_bids) : '' }}</td>
                                {{-- 34 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($bidding2?->bidding_result ?? '') : '' }}</td>
                                {{-- 35 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->bid_evaluation_date) : '' }}</td>
                                {{-- 36 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isBidding ? $fmt($mainBid?->post_qualification_date) : '' }}</td>
                                {{-- 37 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($mainBid?->resolution_number_mop ?? '') : ($isSvp ? ($prSvp?->resolution_number_mop ?? '') : '') }}</td>
                                {{-- 38 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 39 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 40 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 41 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isSvp ? ($prSvp?->rfq_no ?? '') : '' }}</td>
                                {{-- 42 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isSvp ? $fmt($prSvp?->canvass_date) : '' }}</td>
                                {{-- 43 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isSvp ? $fmt($prSvp?->date_returned_of_canvass) : '' }}</td>
                                {{-- 44 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $isSvp ? $fmt($prSvp?->abstract_of_canvass_date) : '' }}</td>
                                {{-- 45 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($post?->resolution_award_date) }}</td>
                                {{-- 46 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($post?->notice_of_award) }}</td>
                                {{-- 47 Awarded Amount --}} <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">@if ($post?->awarded_amount !== null)<div class="inline-flex items-baseline gap-0.5"><span class="text-gray-500 dark:text-gray-400 font-normal">₱</span><span class="text-blue-700 dark:text-blue-400">{{ number_format($post->awarded_amount, 2) }}</span></div>@endif</td>
                                {{-- 48 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $post?->notice_of_award_number ?? '' }}</td>
                                {{-- 49 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($post?->philgeps_posting_of_award) }}</td>
                                {{-- 50 --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $post?->supplier?->name ?? '' }}</div></td>
                                {{-- 51 item stage --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">@if ($item->prstage?->stage?->procurementstage)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 dark:from-blue-900/40 dark:to-blue-800/40 dark:text-blue-200 dark:border-blue-700 break-words whitespace-normal text-center">{{ $item->prstage->stage->procurementstage }}</span>@endif</td>
                                {{-- 52 item remark status --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">@if ($remarkText)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border break-words whitespace-normal text-center {{ $remarkBadge }}" title="{{ $remarkText }}">{{ $remarkText }}</span>@endif</td>
                                {{-- 53 item remark notes --}} <td class="px-3 py-2 text-xs text-left text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2">{{ $item->currentItemRemark?->notes ?? '' }}</div></td>
                                {{-- 54 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 55 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($pmu?->date_forwarded) }}</td>
                                {{-- 56 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $isBidding ? ($mainBid?->philgeps_posting_ref_no ?? '') : ($isSvp ? ($prSvp?->philgeps_posting_ref_no ?? '') : '') }}</td>
                                {{-- 57 Contract Amount --}} <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">@if ($pmuPo?->contract_amount !== null)<div class="inline-flex items-baseline gap-0.5"><span class="text-gray-500 dark:text-gray-400 font-normal">₱</span><span class="text-blue-700 dark:text-blue-400">{{ number_format($pmuPo->contract_amount, 2) }}</span></div>@endif</td>
                                {{-- 58 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300">{{ $pmuPo?->po_contract_number ?? '' }}</td>
                                {{-- 59 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($pmuPo?->contract_signing_date) }}</td>
                                {{-- 60 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($pmuPo?->notice_to_proceed_date) }}</td>
                                {{-- 61 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($supplyPo?->delivery_completion) }}</td>
                                {{-- 62 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $fmt($supplyPo?->date_of_acceptance) }}</td>
                                {{-- 63 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->list_invited_observers ?? '') : '' }}</div></td>
                                {{-- 64 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_prebid_conf ?? '') : '' }}</div></td>
                                {{-- 65 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_eligibility ?? '') : '' }}</div></td>
                                {{-- 66 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_sub_open_of_bid ?? '') : '' }}</div></td>
                                {{-- 67 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_bid ?? '') : '' }}</div></td>
                                {{-- 68 --}} <td class="px-3 py-2 text-xs text-center text-gray-700 dark:text-gray-300"><div class="break-words whitespace-normal line-clamp-2 text-center">{{ $isBidding ? ($mainBid?->obsrvr_post_qual ?? '') : '' }}</div></td>
                                {{-- 69 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                                {{-- 70 --}} <td class="px-3 py-2 text-xs text-gray-300 dark:text-gray-600"></td>
                            </tr>
                        @endforeach
                    @endif

                @empty
                    <tr>
                        <td colspan="70" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="text-sm font-medium">No records found for {{ $year }}</div>
                                <div class="text-xs text-gray-400">Try adjusting your search or filters</div>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

        <div class="flex items-center gap-x-2">
            <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
            <select wire:model.live="perPage"
                class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
        </div>

        <div class="flex flex-col items-center justify-center gap-3 flex-1">
            @if ($procurements->total() > 0)
                <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                    Showing <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $procurements->firstItem() }}</span>
                    to <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $procurements->lastItem() }}</span>
                    of <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $procurements->total() }}</span> records
                </div>
            @endif
            <div class="flex justify-center">
                {{ $procurements->links('vendor.pagination.tailwind') }}
            </div>
        </div>

    </div>

</div>
