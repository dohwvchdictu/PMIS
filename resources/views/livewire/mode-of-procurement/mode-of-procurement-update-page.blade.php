<div class="space-y-2">

    <div class="relative bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

        <ul class="flex items-center w-full max-w-7xl pt-2 p-2 bg-white dark:bg-neutral-700 dark:border-neutral-700 mx-auto"
            data-hs-stepper='{"isCompleted": true}'>

            <li class="flex items-center gap-x-2 flex-1 group"
                data-hs-stepper-nav-item='{"index": 1, "isCompleted": {{ $activeTab > 1 ? 'true' : 'false' }} }'>

                <button type="button" wire:click="setStep(1)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105
                {{ $activeTab == 1
                    ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
                    : ($activeTab > 1
                        ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                        : 'bg-gray-100 text-gray-800 hover:bg-gray-200') }}">
                    1
                </button>

                <span
                    class="text-sm font-medium whitespace-nowrap {{ $activeTab >= 1 ? 'text-black dark:text-white' : 'text-gray-500' }}">
                    Details
                </span>

                <div
                    class="h-px grow transition-colors duration-300 {{ $activeTab > 1 ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                </div>
            </li>

            <li class="flex items-center gap-x-2 group"
                data-hs-stepper-nav-item='{"index": 2, "isCompleted": {{ $activeTab > 2 ? 'true' : 'false' }} }'>

                <button type="button" wire:click="setStep(2)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105
                {{ $activeTab == 2
                    ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
                    : ($activeTab > 2
                        ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                        : 'bg-gray-100 text-neutral-400 cursor-not-allowed') }}">
                    2
                </button>

                <span
                    class="text-sm font-medium whitespace-nowrap {{ $activeTab >= 2 ? 'text-gray-800 dark:text-white' : 'text-neutral-400 dark:text-neutral-500' }}">
                    Post
                </span>
            </li>

        </ul>
    </div>
    {{-- <hr class=" border-gray-200 dark:border-neutral-600"> --}}

    <div>
        @if ($activeTab == 1)
            <div class="flex flex-col gap-2 pt-2">
                <div
                    class="bg-white p-4 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                    <!-- Grid for PR No. + Program/Project -->
                    <div class="grid grid-cols-2 md:grid-cols-10 gap-4">
                        <!-- PR Number -->
                        <div class="col-span-1">
                            <x-forms.input id="pr_number" label="PR No." model="form.pr_number" :form="$form"
                                :required="true" textAlign="right" :readonly="true" />
                        </div>

                        <!-- Procurement Program / Project -->
                        <div class="col-span-9">
                            <x-forms.textarea id="procurement_program_project" label="Procurement Program / Project"
                                model="form.procurement_program_project" :required="true" :readonly="true"
                                :rows="$textareaRows" />
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl p-2 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 mt-4">

                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">

                                <tr>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white w-20 border-b border-gray-300 dark:border-neutral-600">
                                    </th>

                                    {{-- Show Item columns only for perItem type --}}

                                    @if ($form['procurement_type'] === 'perItem')
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                            No.</th>

                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Description</th>
                                    @else
                                    @endif

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Mode of Procurement</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                        Bidding #</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        IB No.</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Proc Conference</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Ads/Post IB</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Bid Conference</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Eligibility Check</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Sub/Open of Bids</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Date</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Result</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        NTF No.</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        RFQ No.</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Canvass Date</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Returned of Canvass</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Abstract of Canvass</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution Number</th>



                                </tr>

                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">

                                @forelse (array_reverse($form['items'] ?? [], true) as $itemIndex => $item)
                                    @php
                                        $modeId = $item['mode_of_procurement_id'] ?? null;
                                        $rowUid = $item['uid'] ?? 'temp_' . $itemIndex;

                                        $isSavedRecord =
                                            $form['procurement_type'] === 'perItem'
                                                ? isset($item['prItemID'])
                                                : isset($item['id']) && is_numeric($item['id']);

                                        $isHistory = !$loop->first;

                                        // Check if a schedule is saved
                                        $hasSchedule =
                                            !empty($item['bidding_number']) ||
                                            !empty($item['ntf_no']) ||
                                            !empty($item['ntf_bidding_date']) ||
                                            !empty($item['rfq_no']) ||
                                            !empty($item['canvass_date']);

                                        // --- LOGIC UPDATES ---

                                        // 1. SELECT DROPDOWN: Disable if:
                                        //    a) History row
                                        //    b) Schedule exists
                                        //    c) It is specifically the initial Public Bidding record (MOP-1-1)
                                        $disableSelect = $isHistory || $hasSchedule || $rowUid === 'MOP-1-1';

                                        // 2. INPUT FIELDS: Disable ONLY if History
                                        $disableInputs = $isHistory;

                                        $showFields = $isSavedRecord;
                                        $isVisible = $loop->first || $showHistory;
                                    @endphp


                                    {{-- Apply the hidden class conditionally --}}
                                    <tr wire:key="row-{{ $rowUid }}"
                                        class="{{ $isVisible ? '' : 'hidden' }} hover:bg-emerald-100 dark:hover:bg-neutral-800">

                                        {{-- Action Column --}}
                                        <td class="px-2 py-2 align-middle">
                                            <div class="flex items-center justify-center gap-1">

                                                @if ($loop->first)
                                                    {{-- CURRENT ITEM --}}

                                                    {{-- 1. Toggle Button (History) --}}
                                                    {{-- CONDITION: Hide if UID is specifically 'MOP-1-1' --}}
                                                    @if ($rowUid !== 'MOP-1-1')
                                                        <button type="button" wire:click="toggleHistory"
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 transition-colors text-gray-500 dark:text-gray-400"
                                                            title="{{ $showHistory ? 'Hide History' : 'Show History' }}">
                                                            @if ($showHistory)
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-4 w-4 text-emerald-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-4 w-4 text-emerald-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                                </svg>
                                                            @endif
                                                        </button>
                                                    @else
                                                        {{-- Spacer to maintain layout alignment if button is hidden --}}
                                                        <div class="w-7 h-7"></div>
                                                    @endif

                                                    {{-- 2. Add Button (Conditional) --}}
                                                    @php
                                                        $hasResult =
                                                            !empty($item['bidding_result']) ||
                                                            !empty($item['ntf_bidding_result']);
                                                        $isPublicBidding = $modeId == 1;
                                                        $canAddRebid = $hasResult || $isPublicBidding;
                                                    @endphp

                                                    @if ($canAddRebid)
                                                        <button wire:click.prevent="addItem"
                                                            class="inline-flex items-center justify-center w-7 h-7 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                                            title="Add New Row">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <div class="w-7 h-7"></div>
                                                    @endif
                                                @else
                                                    {{-- HISTORY ITEMS: Lock Icon --}}
                                                    <span
                                                        class="inline-flex items-center justify-center w-7 h-7 text-gray-300 dark:text-neutral-600 cursor-not-allowed"
                                                        title="History Record">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                                        </svg>
                                                    </span>
                                                @endif

                                            </div>
                                        </td>

                                        {{-- Show Item columns only for perItem type --}}
                                        @if ($form['procurement_type'] === 'perItem')
                                            <td class="px-2 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap"
                                                @disabled($shouldDisable)>
                                                {{ $item['item_no'] }}
                                            </td>
                                            <td class="px-2 py-2 text-gray-900 dark:text-gray-100"
                                                @disabled($shouldDisable)>
                                                {{ $item['description'] }}
                                            </td>
                                        @endif

                                        {{-- Mode of Procurement --}}
                                        <td class="px-2 py-2">
                                            <select wire:key="select-mode-{{ $rowUid }}"
                                                wire:model.live="form.items.{{ $itemIndex }}.mode_of_procurement_id"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableSelect)>

                                                <option value="">Select Mode...</option>
                                                @foreach ($modeOfProcurements ?? [] as $modeOption)
                                                    <option value="{{ $modeOption->id }}">
                                                        {{ $modeOption->modeofprocurements }}
                                                    </option>
                                                @endforeach>
                                            </select>
                                        </td>

                                        {{-- Bidding # --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [5, 1]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="bid-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_number"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    maxlength="2" value="1" @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- IB No. through Sub/Open of Bids --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [5, 1]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="ib-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ib_number"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    placeholder="IB-2025-002" @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-proc-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_proc_conference"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ads-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ads_post_ib"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-bid-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_bid_conf"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="elig-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.eligibility_check"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="sub-open-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.sub_open_bids"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Bidding Date / NTF Bidding Date --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [4, 5, 1]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="bid-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>
                                        @elseif ($showFields && $modeId == 4)
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ntf-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ntf_bidding_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Bidding Result / NTF Bidding Result --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [4, 5, 1]))
                                            <td class="px-2 py-2">
                                                <select wire:key="res-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_result"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                                    <option value="">Select...</option>
                                                    <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                    <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                                </select>
                                            </td>
                                        @elseif ($showFields && $modeId == 4)
                                            <td class="px-2 py-2">
                                                <select wire:key="ntf-res-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ntf_bidding_result"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                                    <option value="">Select...</option>
                                                    <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                    <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                                </select>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- NTF No. --}}
                                        @if ($showFields && $modeId == 4)
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="ntf-no-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ntf_no"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    placeholder="NTF-2025-001" @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- RFQ No. --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <div class="relative">
                                                    <input type="text" wire:key="rfq-{{ $rowUid }}"
                                                        wire:model.defer="form.items.{{ $itemIndex }}.rfq_no"
                                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white
                {{ $errors->has('form.items.' . $itemIndex . '.rfq_no')
                    ? 'border-red-500 focus:ring-red-500'
                    : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                        placeholder="RFQ-2025-001" @disabled($disableInputs)>

                                                    {{-- Optional: Tooltip for error --}}
                                                    @error('form.items.' . $itemIndex . '.rfq_no')
                                                        <div
                                                            class="absolute top-0 right-0 -mt-8 p-1 text-xs text-white bg-red-500 rounded shadow-lg z-50 whitespace-nowrap">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Canvass Date --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="can-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.canvass_date"
                                                    class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.canvass_date')
                ? 'border-red-500 focus:ring-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Returned of Canvass --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ret-can-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.date_returned_of_canvass"
                                                    class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.date_returned_of_canvass')
                ? 'border-red-500 focus:ring-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Abstract of Canvass --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="abs-can-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.abstract_of_canvass_date"
                                                    class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.abstract_of_canvass_date')
                ? 'border-red-500 focus:ring-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Resolution Number --}}
                                        @if ($showFields && $modeId == 5)
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="res-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.resolution_number"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.resolution_number')
                ? 'border-red-500 focus:ring-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="RES-2025-001" @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif

                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="20"
                                            class="px-2 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No items available
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
            {{-- Loop through Modes --}}
            <div class="flex flex-col items-center p-2">

                <div class="flex justify-center p-2">
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 inline-block dark:bg-neutral-700 dark:border-neutral-700">


                    </div>
                </div>


            </div>
        @endif

        @if ($activeTab == 3)
            <div class="flex flex-col items-center gap-6 p-6">

                {{-- Award Information Block --}}
                <div
                    class="w-full max-w-7xl bg-white p-6 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                </div>

                {{-- PhilGEPS & Supplier Information Block --}}
                <div
                    class="w-full max-w-7xl bg-white p-6 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                </div>


            </div>
        @endif
    </div>


    <div
        class="fixed bottom-5 right-0 left-0 lg:ml-[13.75rem] flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-x-2">

            {{-- REMOVED wire:navigate here to force a full reload and persist Dark Mode state --}}
            <a href="{{ route('mode-of-procurement.index') }}"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300 dark:hover:bg-neutral-700">
                Cancel
            </a>

            <button wire:click="save" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50">
                <div wire:loading wire:target="save"
                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-white">
                </div>
                Save
            </button>
        </div>
    </div>
</div>
