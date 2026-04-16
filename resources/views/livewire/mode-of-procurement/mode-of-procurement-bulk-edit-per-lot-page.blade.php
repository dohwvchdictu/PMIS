<div class="space-y-6">
    <!-- Stepper Tab -->
    <div
        class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

        <ul class="flex items-center w-full max-w-7xl px-4 py-3 bg-white dark:bg-neutral-700 mx-auto"
            data-hs-stepper='{"isCompleted": true}'>

            {{-- STEP 1: BULK EDIT --}}
            <li class="flex items-center flex-1">
                <button type="button" wire:click="setStep(1)"
                    class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 hover:scale-105 shadow-md
            {{ $activeTab == 1
                ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400'
                : ($activeTab > 1 || $this->isPostAvailable
                    ? 'bg-emerald-500 text-white hover:bg-emerald-600'
                    : 'bg-emerald-600 text-white') }}">
                    1
                </button>

                <span
                    class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500' }}">Mode
                    of Procurement
                </span>

                <div
                    class="h-px flex-1 mx-3 transition-all duration-300
            {{ $activeTab > 1 || $this->isPostAvailable ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-600' }}">
                </div>
            </li>

            {{-- STEP 2: POST --}}
            <li class="flex items-center">
                <button type="button" @if (!$this->isPostAvailable && $activeTab < 2) disabled @else wire:click="setStep(2)" @endif
                    class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 shadow-md
            {{ $activeTab == 2
                ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400 hover:scale-105'
                : ($activeTab > 2 || $this->isPostAvailable
                    ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-105'
                    : 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-neutral-600') }}">
                    2
                </button>

                <span
                    class="ml-2 text-sm font-semibold whitespace-nowrap
            {{ $activeTab >= 2 || $this->isPostAvailable ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                    Post Procurement
                </span>
            </li>

        </ul>
    </div>

    <div>
        @if ($activeTab == 1)
            <!-- Combined Bulk Edit Form and PR Table -->
            <div class="flex flex-col gap-6">

                <!-- Bulk Edit Button Section (shown when items are selected) -->
                @if (count($selectedItems) > 0)
                    <div
                        class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 dark:border-emerald-600 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                                        {{ count($selectedItems) }} procurement(s) selected for bulk edit
                                    </span>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                        Click Bulk Edit to update mode of procurement data for all selected PRs
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="clearSelections" type="button"
                                    class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                    Clear Selection
                                </button>
                                <button wire:click="openBulkEditModal" type="button"
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>Edit
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- PR Table Section -->
                <div
                    class="bg-white rounded-xl shadow-md border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

                    <!-- Header Section -->
                    <div
                        class="px-4 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 border-b-2 border-emerald-500 dark:border-emerald-600">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-emerald-600 dark:bg-emerald-700 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-100">
                                    Selected Purchase Requests
                                </h3>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Select PRs and use Bulk Edit to update multiple records
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- PR List Table -->
                    <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <caption class="sr-only">Selected Purchase Requests for Bulk Editing</caption>
                            <thead class="bg-gray-200 dark:bg-neutral-800">
                                <tr>
                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-12">
                                        <input type="checkbox" wire:model.live="selectAll" id="select-all-procurements"
                                            aria-label="Select all procurements on this page"
                                            class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </th>
                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                        PR Number
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                        Procurement Program / Project
                                    </th>
                                    <th
                                        class="px-2 py-3 text-right font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                        ABC Amount
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                        Mode of Procurement
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                        Bidding #
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        IB No.
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        PhilGEPS Posting Ref #
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Ads/Post IB
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Proc Conference
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        List of Invited Observers
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Pre-Bid)
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Eligibility)
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Sub/Open)
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Bid)
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Post Qual)
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Bid Conference
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Eligibility Check
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Sub/Open of Bids
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bid Evaluation Date
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Post Qualification Date
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Result
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution # (MOP)
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        RFQ No.
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Canvass Date
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Returned of Canvass
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Abstract of Canvass
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                @php
                                    $currentProcKey = null;
                                @endphp

                                @forelse ($items as $index => $item)
                                    @php
                                        $modeId = $item['mode_of_procurement_id'];
                                        $showBiddingFields = $this->isCompetitiveBidding($modeId);
                                        $showSvpFields = $this->isSvpMode($modeId);

                                        // Determine if this is the head row for this procurement
                                        $itemKey =
                                            $item['procurement_type'] === 'perLot'
                                                ? 'lot_' . $item['procID']
                                                : 'item_' . $item['prItemID'];
                                        $isHead = $currentProcKey !== $itemKey;
                                        if ($isHead) {
                                            $currentProcKey = $itemKey;
                                        }

                                        $disableInputs = !$isHead; // Disable inputs for history rows

                                        // Skip history rows unless they're actively being shown
                                        if (!$isHead && (!$showHistory || $historyForKey !== $itemKey)) {
                                            continue;
                                        }
                                    @endphp

                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-neutral-700 {{ !$isHead ? 'bg-amber-50 dark:bg-amber-900/10' : '' }} {{ in_array($item['procID'], $selectedItems ?? []) && $isHead ? 'bg-emerald-100 dark:bg-emerald-900/30 border-2 border-emerald-400' : '' }}">
                                        <!-- Checkbox -->
                                        <td class="px-2 py-2 text-center">
                                            @if ($isHead)
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="checkbox" wire:model.live="selectedItems"
                                                        value="{{ $item['procID'] }}"
                                                        id="procurement-checkbox-{{ $item['procID'] }}"
                                                        aria-label="Select PR #{{ $item['pr_number'] }}"
                                                        class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">

                                                </div>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-2 py-2 text-center">
                                            @if ($isHead)
                                                <!-- History Toggle Button -->
                                                <button type="button"
                                                    wire:click="toggleHistory('{{ $itemKey }}')"
                                                    class="p-1 text-xs rounded hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors
                                                {{ $showHistory && $historyForKey === $itemKey ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                                                    title="Toggle History">
                                                    @if ($showHistory && $historyForKey === $itemKey)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endif
                                        </td>
                                        <!-- PR Number -->
                                        <td class="px-2 py-2 text-xs font-medium text-gray-900 dark:text-white">
                                            @if ($isHead)
                                                @can('view_procurement')
                                                    <a href="{{ route('procurements.view', ['procurement' => $item['procID']]) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded text-emerald-700 dark:text-emerald-300 font-mono hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-400 transition-colors">
                                                        {{ $item['pr_number'] }}
                                                    </a>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded text-emerald-700 dark:text-emerald-300 font-mono">
                                                        {{ $item['pr_number'] }}
                                                    </span>
                                                @endcan
                                            @endif
                                        </td>

                                        <!-- PR Title -->
                                        <td class="px-2 py-2 text-xs text-gray-700 dark:text-gray-300">
                                            @if ($isHead)
                                                <div class="max-w-xs truncate"
                                                    title="{{ $item['procurement_program_project'] }}">
                                                    {{ $item['procurement_program_project'] }}
                                                </div>
                                            @else
                                                <span
                                                    class="text-xs text-gray-400 dark:text-gray-500 italic">History</span>
                                            @endif
                                        </td>

                                        <!-- ABC Amount -->
                                        <td class="px-2 py-2 text-right">
                                            @if ($isHead)
                                                <span class="text-xs text-gray-900 dark:text-white font-medium">
                                                    ₱{{ number_format($item['abc'] ?? 0, 2) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                                    ₱{{ number_format($item['abc'] ?? 0, 2) }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Mode of Procurement -->
                                        <td class="px-2 py-2">
                                            <span
                                                class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $modeOfProcurements->firstWhere('id', $item['mode_of_procurement_id'])?->modeofprocurements ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <!-- Bidding # -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $item['bidding_number'] ?: '-' }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- IB No. -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $item['ib_number'] ?: '-' }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- PhilGEPS Posting Ref # -->
                                        @if ($showBiddingFields || ($showSvpFields && $abcThresholdCategory === '₱200,000.00 and Above'))
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $item['philgeps_posting_ref_no'] ?: '-' }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Ads/Post IB -->
                                        @if ($showBiddingFields || ($showSvpFields && $abcThresholdCategory === '₱200,000.00 and Above'))
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['ads_post_ib']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Pre-Proc Conference -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['pre_proc_conference']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- List of Invited Observers -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['list_invited_observers']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Observer 1 (Pre-Bid) -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['obsrvr_prebid_conf']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Observer 2 (Eligibility) -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['obsrvr_eligibility']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Observer 3 (Sub/Open) -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['obsrvr_sub_open_of_bid']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Observer 4 (Bid) -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['obsrvr_bid']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Observer 5 (Post Qual) -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['obsrvr_post_qual']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Pre-Bid Conference -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['pre_bid_conf']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Eligibility Check -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['eligibility_check']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Sub/Open of Bids -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['sub_open_bids']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Bid Evaluation -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['bid_evaluation_date']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Post Qualification -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['post_qualification_date']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Bidding Result -->
                                        @if ($showBiddingFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $item['bidding_result'] ?: '-' }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Resolution # (MOP) -->
                                        @if ($showBiddingFields || $showSvpFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $item['resolution_number_mop'] ?: '-' }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- RFQ No. -->
                                        @if ($showSvpFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $item['rfq_no'] ?: '-' }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Canvass Date -->
                                        @if ($showSvpFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['canvass_date']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Returned of Canvass -->
                                        @if ($showSvpFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['date_returned_of_canvass']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        <!-- Abstract of Canvass -->
                                        @if ($showSvpFields)
                                            <td class="px-2 py-2">
                                                <span
                                                    class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                                    {{ $this->formatDate($item['abstract_of_canvass_date']) }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="28" class="px-4 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No
                                                    procurements selected</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please select
                                                    procurements from the index page to bulk edit.</p>
                                                <div class="mt-6">
                                                    <a href="{{ route('mode-of-procurement.index') }}"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                                        </svg>
                                                        Back to Index
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab == 2)
            <div class="flex flex-col gap-6">

                <!-- Bulk Edit Button Section (shown when items are selected) -->
                @if (count($selectedPostItems) > 0)
                    <div
                        class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 dark:border-emerald-600 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                                        {{ count($selectedPostItems) }} procurement(s) selected for bulk edit
                                    </span>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                        Click Edit to update post-procurement data for all selected PRs
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="clearPostSelections" type="button"
                                    class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                    Clear Selection
                                </button>
                                <button wire:click="openPostBulkEditModal" type="button"
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </span>
                                </button>
                                @if ($this->canForwardToPmu)
                                    <button wire:click="openForwardModal" type="button"
                                        class="px-5 py-2 text-sm font-semibold rounded-lg shadow-lg focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform
                                        bg-blue-600 hover:bg-blue-700 text-white hover:shadow-xl hover:scale-105 focus:ring-blue-500">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                            </svg>
                                            Forward to PMU
                                            @if ($forwardedToPmuSummary['forwarded'] > 0)
                                                <span class="ml-1 px-1.5 py-0.5 text-xs bg-white/20 rounded-full">
                                                    {{ $forwardedToPmuSummary['forwarded'] }} done
                                                </span>
                                            @endif
                                        </span>
                                    </button>
                                @else
                                    {{-- Disabled button: visible so users know the action exists, hover tooltip explains what's needed --}}
                                    <div x-data="{ showTip: false, tipX: 0, tipY: 0 }"
                                        @mouseenter="
                                            const r = $el.getBoundingClientRect();
                                            tipX = r.right;
                                            tipY = r.bottom + 6;
                                            showTip = true
                                        "
                                        @mouseleave="showTip = false">
                                        <button type="button" disabled
                                            class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200 bg-gray-100 dark:bg-neutral-700 text-gray-400 dark:text-neutral-400 border border-gray-300 dark:border-neutral-600 cursor-not-allowed select-none">
                                            <span class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                                Forward to PMU
                                            </span>
                                        </button>
                                        <template x-teleport="body">
                                            <div x-show="showTip" x-cloak
                                                :style="`position:fixed; top:${tipY}px; left:${tipX}px; transform: translateX(-100%);`"
                                                class="z-[9999] w-72 rounded-lg bg-gray-800 dark:bg-neutral-900 text-white text-xs px-3 py-2 shadow-xl pointer-events-none leading-relaxed">
                                                <p class="font-semibold mb-1">Select items and complete all required
                                                    fields to forward:</p>
                                                <ul class="list-disc list-inside space-y-0.5 text-gray-300">
                                                    <li>Resolution Award Number</li>
                                                    <li>Resolution Award Date</li>
                                                    <li>Notice of Award Number</li>
                                                    <li>Notice of Award Date</li>
                                                    <li>Awarded Amount</li>
                                                    <li>Supplier</li>
                                                    <li>Date Receipt of Supplier (NOA)</li>
                                                </ul>
                                            </div>
                                        </template>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Post Procurement Table Card -->
                <div
                    class="bg-white rounded-xl shadow-md border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

                    <!-- Header Section -->
                    <div
                        class="px-4 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 border-b-2 border-emerald-500 dark:border-emerald-600">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-emerald-600 dark:bg-emerald-700 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-100">
                                    Post-Procurement Data - Eligible PRs
                                </h3>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Select PRs with SUCCESSFUL bidding or complete SVP data to bulk edit
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Post Procurement Table -->
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead class="bg-gray-200 dark:bg-neutral-800">
                                <tr>
                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-12">
                                        <input type="checkbox" wire:model.live="selectAllPost"
                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-700">
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                        PR Number</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                        Procurement Program / Project</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution Award Number</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution Award Date</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Notice of Award Number</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Notice of Award Date</th>
                                    <th
                                        class="px-2 py-3 text-right font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Awarded Amount</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        PhilGEPS Notice of Award No.</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        PhilGEPS Posting of Award</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Supplier</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Date Receipt of Supplier (NOA)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                @php
                                    $displayedPRs = [];

                                    // Pre-load all post-procurement, supplier, and forwarded data in batch queries
                                    $allPostRefIds = collect($items)->pluck('procID')->unique()->values()->toArray();
                                    $preloadedPostData = \App\Models\PostProcurement::whereIn('ref_id', $allPostRefIds)
                                        ->get()
                                        ->keyBy('ref_id');
                                    $preloadedSupplierIds = $preloadedPostData
                                        ->whereNotNull('supplier_id')
                                        ->pluck('supplier_id')
                                        ->unique()
                                        ->values()
                                        ->toArray();
                                    $preloadedSuppliers = \App\Models\Supplier::whereIn('id', $preloadedSupplierIds)
                                        ->get()
                                        ->keyBy('id');
                                    $preloadedForwardedStages = \App\Models\PrLotPrstage::whereIn(
                                        'procID',
                                        $allPostRefIds,
                                    )
                                        ->where('pr_stage_id', 7)
                                        ->whereNotNull('actual_date_forwarded')
                                        ->orderBy('created_at', 'desc')
                                        ->get()
                                        ->groupBy('procID');
                                @endphp

                                @foreach ($items as $item)
                                    @php
                                        // Only show each PR once (the current/latest mode)
                                        $procKey =
                                            $item['procurement_type'] === 'perLot'
                                                ? 'lot_' . $item['procID']
                                                : 'item_' . $item['prItemID'];

                                        if (in_array($procKey, $displayedPRs)) {
                                            continue;
                                        }
                                        $displayedPRs[] = $procKey;

                                        // Check if this item is eligible based on CURRENT mode requirements
                                        // Only show items that meet the post-procurement criteria
                                        if (!$this->isItemEligibleForPost($item)) {
                                            continue;
                                        }

                                        // Get post-procurement data from pre-loaded collections (no N+1 queries)
                                        $refId = $item['procID'];
                                        $postData = $preloadedPostData->get($refId);
                                        $supplier =
                                            $postData && $postData->supplier_id
                                                ? $preloadedSuppliers->get($postData->supplier_id)
                                                : null;

                                        $isSelected = in_array($refId, $selectedPostItems);
                                        $forwardedStages = $preloadedForwardedStages->get($refId);
                                        $isForwarded = $forwardedStages && $forwardedStages->isNotEmpty();
                                        $forwardedDate = $isForwarded
                                            ? $forwardedStages->first()?->actual_date_forwarded
                                            : null;
                                    @endphp

                                    <tr wire:key="post-item-{{ $refId }}"
                                        class="hover:bg-gray-50 dark:hover:bg-neutral-700
                                                {{ $isForwarded ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}
                                                {{ $isSelected ? 'border-2 border-emerald-400 ' . ($isForwarded ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-emerald-100 dark:bg-emerald-900/30') : '' }}">
                                        <td class="px-2 py-2 text-center">
                                            @if ($isForwarded)
                                                <div class="relative flex justify-center items-center"
                                                    x-data="{ open: false }" @click.outside="open = false">
                                                    <button type="button" @click="open = !open"
                                                        @mouseenter="open = true" @mouseleave="open = false"
                                                        class="p-1 rounded-full bg-blue-100 dark:bg-blue-900/40 hover:bg-blue-200 dark:hover:bg-blue-800/60 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400"
                                                        title="Forwarded to PMU">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open"
                                                        x-transition:enter="transition ease-out duration-150"
                                                        x-transition:enter-start="opacity-0 scale-95"
                                                        x-transition:enter-end="opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-100"
                                                        x-transition:leave-start="opacity-100 scale-100"
                                                        x-transition:leave-end="opacity-0 scale-95"
                                                        class="absolute left-8 top-1/2 -translate-y-1/2 z-50 w-52 bg-white dark:bg-neutral-800 border border-blue-200 dark:border-blue-700 rounded-lg shadow-lg p-3 text-left pointer-events-none"
                                                        style="display: none;">
                                                        <div class="flex items-center gap-1.5 mb-1.5">
                                                            <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                            </svg>
                                                            <span
                                                                class="text-xs font-semibold text-blue-700 dark:text-blue-300">Forwarded
                                                                to PMU</span>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            <span
                                                                class="font-medium text-gray-700 dark:text-gray-300">Date:</span>
                                                            {{ $forwardedDate ? \Carbon\Carbon::parse($forwardedDate)->setTimezone('Asia/Manila')->format('F d, Y g:i A') : 'N/A' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <input type="checkbox" wire:model.live="selectedPostItems"
                                                    value="{{ $refId }}"
                                                    class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-xs font-medium text-gray-900 dark:text-white">
                                            <div class="flex flex-col gap-0.5">
                                                @can('view_procurement')
                                                    <a href="{{ route('procurements.view', ['procurement' => $item['procID']]) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded text-emerald-700 dark:text-emerald-300 font-mono hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-400 transition-colors">
                                                        {{ $item['pr_number'] }}
                                                    </a>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded text-emerald-700 dark:text-emerald-300 font-mono">
                                                        {{ $item['pr_number'] }}
                                                    </span>
                                                @endcan
                                            </div>
                                        </td>
                                        <td class="px-2 py-2 text-xs text-gray-700 dark:text-gray-300">
                                            <div class="max-w-xs truncate"
                                                title="{{ $item['procurement_program_project'] }}">
                                                {{ $item['procurement_program_project'] }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData?->resolution_award_number ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData && $postData->resolution_award_date ? $this->formatDate($postData->resolution_award_date) : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData?->notice_of_award_number ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData && $postData->notice_of_award ? $this->formatDate($postData->notice_of_award) : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2 text-right">
                                            <span class="text-xs text-gray-900 dark:text-white font-medium">
                                                {{ $postData && $postData->awarded_amount ? '₱' . number_format($postData->awarded_amount, 2) : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData?->philgeps_notice_of_award_no ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData && $postData->philgeps_posting_of_award ? $this->formatDate($postData->philgeps_posting_of_award) : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $supplier ? $supplier->name : '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                                {{ $postData && $postData->date_receipt_of_supplier_noa ? $this->formatDate($postData->date_receipt_of_supplier_noa) : '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- Fixed Action Buttons -->
    <div
        class="fixed bottom-4 right-0 left-0 lg:left-48 flex justify-end px-4 py-3 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-700 shadow-lg z-30">
        <div class="w-full max-w-[110rem] mx-auto flex justify-end gap-3">
            <button type="button" wire:click="cancel"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600 transition-colors shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </button>
        </div>
    </div>

    <!-- Bottom Spacer to prevent content hiding under fixed footer and overall footer -->
    <div class="h-32"></div>

    <!-- Bulk Edit Modal -->
    <x-forms.modal :model="'showBulkEditModal'" :closeMethod="'closeBulkEditModal'" :title="'Bulk Edit Mode of Procurement'" size="max-w-screen-2xl">
        <div class="px-4 py-3">
            {{-- Summary Section --}}
            <div
                class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm text-blue-900 dark:text-blue-100">
                    <span class="font-semibold">{{ count($selectedItems) }}</span> procurement(s) selected.
                </p>
            </div>

            {{-- Validation Errors Section --}}
            @if (!empty($scheduleValidationErrors))
                <div
                    class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-600 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">
                                Validation Errors
                            </h4>
                            <ul class="space-y-1">
                                @foreach ($scheduleValidationErrors as $error)
                                    <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Bulk Edit Form Table --}}
            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                @php
                    $modeId = $bulkEdit['mode_of_procurement_id'] ?? null;
                @endphp

                <table class="w-full text-xs min-w-max">
                    <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                        <tr>
                            <th
                                class="px-2 py-3 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                Actions
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                Mode of Procurement
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                Bidding #
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                IB No.
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                PhilGEPS Posting Ref #
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Ads/Post IB
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Pre-Proc Conference
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                List of Invited Observers
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Observers (Pre-Bid)
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Observers (Eligibility)
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Observers (Sub/Open)
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Observers (Bid)
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Observers (Post Qual)
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Pre-Bid Conference
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Eligibility Check
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Sub/Open of Bids
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Bid Evaluation Date
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Post Qualification Date
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Bidding Result
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Resolution # (MOP)
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                RFQ No.
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Canvass Date
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Returned of Canvass
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                Abstract of Canvass
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white dark:bg-neutral-700 hover:bg-emerald-50 dark:hover:bg-neutral-800">
                            <!-- Actions -->
                            <td class="px-2 py-2 align-middle">
                                <div class="flex items-center justify-center gap-1">
                                    @if ($this->showAddModeButton)
                                        <button type="button" wire:click="addItem"
                                            class="inline-flex items-center justify-center w-7 h-7 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                            title="Add new mode to all selected procurements after applying changes"
                                            @disabled($this->disableInputs)>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    @else
                                        <div class="w-7 h-7"></div>
                                    @endif
                                </div>
                            </td>

                            <!-- Mode of Procurement -->
                            <td class="px-2 py-2">
                                <select wire:model.live="bulkEdit.mode_of_procurement_id"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                    @disabled($this->disableInputs || $disableModeSelect || ($this->showAddModeButton && !$this->showAddForm))>
                                    <option value="">Select Mode...</option>
                                    @foreach ($modeOfProcurements as $mode)
                                        <option value="{{ $mode->id }}">
                                            {{ $mode->modeofprocurements }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            @if ($this->showBiddingFields)
                                <!-- Bidding Fields -->
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="bulkEdit.bidding_number" maxlength="2"
                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.bidding_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="bulkEdit.ib_number"
                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.ib_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        placeholder="IB-2025-002" @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="bulkEdit.philgeps_posting_ref_no"
                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.philgeps_posting_ref_no') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        placeholder="PHL-2025-001" @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.ads_post_ib"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.ads_post_ib') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.pre_proc_conference"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.pre_proc_conference') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <textarea wire:model.defer="bulkEdit.list_invited_observers" rows="1"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.list_invited_observers') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        placeholder="List observers..." @disabled($this->disableInputs)></textarea>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.obsrvr_prebid_conf"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.obsrvr_prebid_conf') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.obsrvr_eligibility"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.obsrvr_eligibility') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.obsrvr_sub_open_of_bid"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.obsrvr_sub_open_of_bid') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.obsrvr_bid"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.obsrvr_bid') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.obsrvr_post_qual"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.obsrvr_post_qual') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.pre_bid_conf"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.pre_bid_conf') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.eligibility_check"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.eligibility_check') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.sub_open_bids"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.sub_open_bids') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.bid_evaluation_date"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.bid_evaluation_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.post_qualification_date"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.post_qualification_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <select wire:model.defer="bulkEdit.bidding_result"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.bidding_result') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                        <option value="">Select...</option>
                                        <option value="SUCCESSFUL">SUCCESSFUL</option>
                                        <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="bulkEdit.resolution_number_mop"
                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.resolution_number_mop') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        placeholder="RES-2025-001" @disabled($this->disableInputs)>
                                </td>
                                {{-- Empty SVP fields --}}
                                <td class="px-2 py-2"></td>
                                <td class="px-2 py-2"></td>
                                <td class="px-2 py-2"></td>
                                <td class="px-2 py-2"></td>
                            @elseif ($this->showSvpFields)
                                {{-- Empty cells for bidding-only fields --}}
                                <td class="px-2 py-2"></td>
                                <td class="px-2 py-2"></td>

                                {{-- PhilGEPS and Ads/Post IB: Show for ABC >= 200k --}}
                                @if ($abcThresholdCategory === '₱200,000.00 and Above')
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEdit.philgeps_posting_ref_no"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                        {{ $errors->has('bulkEdit.philgeps_posting_ref_no') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="PHL-2025-001" @disabled($this->disableInputs)>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEdit.ads_post_ib"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                        {{ $errors->has('bulkEdit.ads_post_ib') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableInputs)>
                                    </td>
                                @else
                                    <td class="px-2 py-2"></td>
                                    <td class="px-2 py-2"></td>
                                @endif

                                {{-- Empty bidding-specific fields --}}
                                @for ($i = 0; $i < 13; $i++)
                                    <td class="px-2 py-2"></td>
                                @endfor

                                <!-- Resolution # (MOP) -->
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="bulkEdit.resolution_number_mop"
                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.resolution_number_mop') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        placeholder="RES-2025-001" @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="bulkEdit.rfq_no"
                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.rfq_no') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        placeholder="RFQ-2025-001" @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.canvass_date"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.canvass_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.date_returned_of_canvass"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.date_returned_of_canvass') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="bulkEdit.abstract_of_canvass_date"
                                        class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $errors->has('bulkEdit.abstract_of_canvass_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                        @disabled($this->disableInputs)>
                                </td>
                            @else
                                {{-- MODE 1 OR NO MODE SELECTED - ALL EMPTY (23 fields) --}}
                                @for ($i = 0; $i < 23; $i++)
                                    <td class="px-2 py-2"></td>
                                @endfor
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div
                class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 flex justify-end gap-3 border-t border-gray-200 dark:border-neutral-600">
                <button type="button" wire:click="closeBulkEditModal"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </button>
                <button type="button" onclick="confirmBulkEditSave()"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Save
                </button>
            </div>
        </div>
    </x-forms.modal>

    <!-- Post Procurement Bulk Edit Modal -->
    <x-forms.modal :model="'showPostBulkEditModal'" :closeMethod="'closePostBulkEditModal'" :title="'Bulk Edit Post Procurement'" size="max-w-screen-2xl">
        @if (!empty($postBulkEditData))
            <div class="px-4 py-3">
                {{-- Summary Section --}}
                <div
                    class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-900 dark:text-blue-100">
                        <span class="font-semibold">{{ count($postBulkEditData['selected_items'] ?? []) }}</span>
                        procurement(s) selected for post-procurement bulk edit
                    </p>
                </div>

                {{-- Validation Errors Section --}}
                @if (!empty($postBulkEditErrors))
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-600 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">
                                    Validation Errors
                                </h4>
                                <ul class="space-y-1">
                                    @foreach ($postBulkEditErrors as $error)
                                        <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Post Procurement Bulk Edit Form Table --}}
                <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-xs min-w-max">
                        <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                            <tr>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Resolution Award Number
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Resolution Award Date
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Notice of Award Number
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Notice of Award Date
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Awarded Amount
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    PhilGEPS Notice of Award No.
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    PhilGEPS Posting Date
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Supplier
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Date Receipt of Supplier (NOA)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white dark:bg-neutral-700 hover:bg-emerald-50 dark:hover:bg-neutral-800">
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="postBulkEditData.resolutionAwardNumber"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                        placeholder="Enter resolution award number">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.resolutionAwardDate"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="postBulkEditData.noticeOfAwardNumber"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                        placeholder="Enter notice of award number">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.noticeOfAward"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                                <td class="px-2 py-2">
                                    <div class="relative">
                                        <span
                                            class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-600 dark:text-gray-400 pointer-events-none">₱</span>
                                        <input type="text" wire:model.defer="postBulkEditData.awardedAmount" x-data
                                            x-mask:dynamic="$money($input, '.', ',', 2)"
                                            class="w-full pl-6 pr-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="0.00">
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="postBulkEditData.philgepsNoticeOfAwardNo"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                        placeholder="PhilGEPS award number">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.philgepsPostingOfAward"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                                <td class="px-2 py-2">
                                    <select wire:model.defer="postBulkEditData.supplier_id"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                        <option value="">Select Supplier...</option>
                                        @foreach ($suppliers ?? [] as $supplier)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.dateReceiptOfSupplierNoa"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Footer -->
                <div
                    class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 flex justify-end gap-3 border-t border-gray-200 dark:border-neutral-600">
                    <button type="button" wire:click="closePostBulkEditModal"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="button" onclick="confirmPostBulkEditSave()"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Save
                    </button>
                </div>
        @endif
    </x-forms.modal>

    @once
        <style>
            /* Ensure toast notifications appear above modals */
            .swal2-container {
                z-index: 9999999 !important;
            }

            .swal2-popup {
                z-index: 9999999 !important;
            }
        </style>

        <script>
            function confirmBulkEditSave() {
                Swal.fire({
                    title: 'Apply Changes?',
                    text: 'Apply changes to selected items?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Apply',
                    cancelButtonText: 'No, Cancel',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('save').then(() => {
                            // Modal will stay open, backend will handle success notification
                        });
                    }
                });
            }

            function confirmPostBulkEditSave() {
                Swal.fire({
                    title: 'Apply Changes?',
                    text: 'Apply changes to selected items?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Apply',
                    cancelButtonText: 'No, Cancel',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('savePostBulkEdit').then(() => {
                            // Modal will stay open, backend will handle success notification
                        });
                    }
                });
            }
        </script>
    @endonce

    {{-- Forward to PMU Modal --}}
    <x-forms.modal wire:model="showForwardModal" title="Forward to PMU" size="max-w-lg" model="showForwardModal"
        closeMethod="closeForwardModal">
        <div class="px-4 py-3">
            {{-- Summary Pills --}}
            @if ($forwardedToPmuSummary['forwarded'] > 0 || $forwardedToPmuSummary['pending'] > 0)
                <div class="mb-4 flex flex-wrap gap-2">
                    @if ($forwardedToPmuSummary['forwarded'] > 0)
                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-xs font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $forwardedToPmuSummary['forwarded'] }} already forwarded
                        </div>
                    @endif
                    @if ($forwardedToPmuSummary['pending'] > 0)
                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-lg text-xs font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $forwardedToPmuSummary['pending'] }} pending
                        </div>
                    @endif
                </div>
            @endif

            {{-- Already Forwarded Note --}}
            @if ($forwardedToPmuSummary['forwarded'] > 0)
                <div
                    class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg text-xs text-yellow-800 dark:text-yellow-300">
                    <strong>Note:</strong> {{ $forwardedToPmuSummary['forwarded'] }} PR(s) already forwarded —
                    their date will be updated to the date you enter below.
                </div>
            @endif

            {{-- Date Input Field --}}
            <div class="mb-4">
                <label for="actualDateForwarded"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Actual Date &amp; Time Forwarded <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" id="actualDateForwarded" wire:model.defer="actualDateForwarded"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                    required>
                @error('actualDateForwarded')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Modal Footer Actions --}}
            <div class="border-t border-gray-200 dark:border-neutral-700 pt-4 mt-4 flex items-center justify-end">
                <div class="flex gap-2">
                    <button type="button" wire:click="closeForwardModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-600 border border-gray-300 dark:border-neutral-500 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-500 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="forwardToPmu"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Confirm Forward
                    </button>
                </div>
            </div>
        </div>
    </x-forms.modal>

</div>
