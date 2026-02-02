<div class="space-y-4">
    <!-- Bulk Edit Form -->
    <div
        class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">
        <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-500"></div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-max">
                    <thead class="bg-gray-200 dark:bg-neutral-800">
                        <tr>
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
                        <tr class="bg-white dark:bg-neutral-700">
                            <!-- Mode of Procurement -->
                            <td class="px-2 py-2">
                                <select wire:model="bulkEdit.mode_of_procurement_id"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                                    <option value="">Select Mode...</option>
                                    @foreach ($modeOfProcurements as $mode)
                                        <option value="{{ $mode->id }}">{{ $mode->modeofprocurements }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Bidding # -->
                            <td class="px-2 py-2">
                                <input type="text" wire:model="bulkEdit.bidding_number"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- IB No. -->
                            <td class="px-2 py-2">
                                <input type="text" wire:model="bulkEdit.ib_number"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- PhilGEPS Posting Ref # -->
                            <td class="px-2 py-2">
                                <input type="text" wire:model="bulkEdit.philgeps_posting_ref_no"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Ads/Post IB -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.ads_post_ib"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Pre-Proc Conference -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.pre_proc_conference"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- List of Invited Observers -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.list_invited_observers"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Observers (Pre-Bid) -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.obsrvr_prebid_conf"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Observers (Eligibility) -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.obsrvr_eligibility"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Observers (Sub/Open) -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.obsrvr_sub_open_of_bid"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Observers (Bid) -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.obsrvr_bid"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Observers (Post Qual) -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.obsrvr_post_qual"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Pre-Bid Conference -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.pre_bid_conf"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Eligibility Check -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.eligibility_check"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Sub/Open of Bids -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.sub_open_bids"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Bid Evaluation Date -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.bid_evaluation_date"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Post Qualification Date -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.post_qualification_date"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Bidding Result -->
                            <td class="px-2 py-2">
                                <select wire:model="bulkEdit.bidding_result"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                                    <option value="">Select...</option>
                                    <option value="SUCCESSFUL">SUCCESSFUL</option>
                                    <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                </select>
                            </td>
                            <!-- Resolution # (MOP) -->
                            <td class="px-2 py-2">
                                <input type="text" wire:model="bulkEdit.resolution_number_mop"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- RFQ No. -->
                            <td class="px-2 py-2">
                                <input type="text" wire:model="bulkEdit.rfq_no"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Canvass Date -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.canvass_date"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Returned of Canvass -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.date_returned_of_canvass"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                            <!-- Abstract of Canvass -->
                            <td class="px-2 py-2">
                                <input type="date" wire:model="bulkEdit.abstract_of_canvass_date"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="bg-white rounded-xl p-2 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="w-full text-xs min-w-max">
                    <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                        <tr>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                PR Number
                            </th>
                            <th
                                class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                PR Title / Item Description
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

                        @foreach ($items as $index => $item)
                            @php
                                $modeId = $item['mode_of_procurement_id'];
                                $showBiddingFields = in_array($modeId, [2, 3, 4, 5, 6]);
                                $showSvpFields = in_array($modeId, [
                                    7,
                                    8,
                                    9,
                                    10,
                                    11,
                                    12,
                                    13,
                                    14,
                                    15,
                                    16,
                                    17,
                                    18,
                                    19,
                                    20,
                                    21,
                                    22,
                                    23,
                                    24,
                                ]);

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
                                class="hover:bg-gray-50 dark:hover:bg-neutral-700 {{ !$isHead ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                <!-- PR Number -->
                                <td class="px-2 py-2 text-xs font-medium text-gray-900 dark:text-white">
                                    @if ($isHead)
                                        <div class="flex items-center gap-1">
                                            <!-- History Toggle Button -->
                                            <button type="button" wire:click="toggleHistory('{{ $itemKey }}')"
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
                                            <span
                                                class="inline-flex items-center px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded text-emerald-700 dark:text-emerald-300 font-mono">
                                                {{ $item['pr_number'] }}
                                            </span>
                                        </div>
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
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">History</span>
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
                                @endif

                                <!-- IB No. -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $item['ib_number'] ?: '-' }}
                                        </span>
                                @endif

                                <!-- PhilGEPS Posting Ref # -->
                                @if ($showBiddingFields || $showSvpFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $item['philgeps_posting_ref_no'] ?: '-' }}
                                        </span>
                                    @else
                                    <td class="px-2 py-2"></td>
                                @endif

                                <!-- Ads/Post IB -->
                                @if ($showBiddingFields || $showSvpFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['ads_post_ib']) }}
                                        </span>
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
                                @endif

                                <!-- List of Invited Observers -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['list_invited_observers']) }}
                                        </span>
                                @endif

                                <!-- Observer 1 (Pre-Bid) -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['obsrvr_prebid_conf']) }}
                                        </span>
                                @endif

                                <!-- Observer 2 (Eligibility) -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['obsrvr_eligibility']) }}
                                        </span>
                                @endif

                                <!-- Observer 3 (Sub/Open) -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['obsrvr_sub_open_of_bid']) }}
                                        </span>
                                @endif

                                <!-- Observer 4 (Bid) -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['obsrvr_bid']) }}
                                        </span>
                                @endif

                                <!-- Observer 5 (Post Qual) -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['obsrvr_post_qual']) }}
                                        </span>
                                @endif

                                <!-- Pre-Bid Conference -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['pre_bid_conf']) }}
                                        </span>
                                @endif

                                <!-- Eligibility Check -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['eligibility_check']) }}
                                        </span>
                                @endif

                                <!-- Sub/Open of Bids -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['sub_open_bids']) }}
                                        </span>
                                @endif

                                <!-- Bid Evaluation -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['bid_evaluation_date']) }}
                                        </span>
                                @endif

                                <!-- Post Qualification -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['post_qualification_date']) }}
                                        </span>
                                @endif

                                <!-- Bidding Result -->
                                @if ($showBiddingFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $item['bidding_result'] ?: '-' }}
                                        </span>
                                @endif

                                <!-- Resolution # (MOP) -->
                                @if ($showBiddingFields || $showSvpFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $item['resolution_number_mop'] ?: '-' }}
                                        </span>
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
                                @endif

                                <!-- Canvass Date -->
                                @if ($showSvpFields)
                                    <td class="px-2 py-2">
                                        <span
                                            class="text-xs {{ $isHead ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $this->formatDate($item['canvass_date']) }}
                                        </span>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div
        class="fixed bottom-5 right-0 left-0 lg:ml-[13.75rem] flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-x-2">
            <button wire:click="cancel" type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                Cancel
            </button>

            <button wire:click="save" wire:loading.attr="disabled" type="button"
                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>
</div>
