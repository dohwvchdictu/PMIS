<div class="space-y-2">

    <div
        class="relative bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

        <ul class="flex items-center w-full max-w-7xl px-4 py-3 bg-white dark:bg-neutral-700 mx-auto"
            data-hs-stepper='{"isCompleted": true}'>

            {{-- STEP 1: DETAILS --}}
            <li class="flex items-center gap-x-2 flex-1 group"
                data-hs-stepper-nav-item='{"index": 1, "isCompleted": {{ $activeTab > 1 ? 'true' : 'false' }} }'>

                <button type="button" wire:click="setStep(1)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105
            {{ $activeTab == 1
                ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
                : ($activeTab > 1 || $this->isPostAvailable
                    ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200') }}">
                    1
                </button>

                <span
                    class="text-sm font-medium whitespace-nowrap {{ $activeTab >= 1 ? 'text-black dark:text-white' : 'text-gray-500' }}">
                    Details
                </span>

                <div
                    class="h-px grow transition-colors duration-300
            {{ $activeTab > 1 || $this->isPostAvailable ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                </div>
            </li>

            {{-- STEP 2: POST --}}
            <li class="flex items-center gap-x-2 group"
                data-hs-stepper-nav-item='{"index": 2, "isCompleted": {{ $activeTab > 2 ? 'true' : 'false' }} }'>

                <button type="button" @if (!$this->isPostAvailable && $activeTab < 2) disabled @else wire:click="setStep(2)" @endif
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105
            {{ $activeTab == 2
                ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
                : ($activeTab > 2 || $this->isPostAvailable
                    ? 'bg-emerald-600 text-white hover:bg-emerald-700 cursor-pointer'
                    : 'bg-gray-100 text-neutral-400 cursor-not-allowed') }}">
                    2
                </button>

                <span
                    class="text-sm font-medium whitespace-nowrap
            {{ $activeTab >= 2 || $this->isPostAvailable ? 'text-gray-800 dark:text-white' : 'text-neutral-400 dark:text-neutral-500' }}">
                    Post
                </span>
            </li>

        </ul>
    </div>

    <div>
        @if ($activeTab == 1)
            <div class="flex flex-col gap-2 pt-2">
                <div
                    class="bg-white p-4 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                    <div class="grid grid-cols-2 md:grid-cols-10 gap-4">
                        <div class="col-span-1">
                            <x-forms.input id="pr_number" label="PR No." model="form.pr_number" :form="$form"
                                :required="true" textAlign="right" :readonly="true" />
                        </div>

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
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                        No.</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                        Description</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
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

                                    <!-- NEW: Add these two columns -->
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bid Evaluation Date</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Post Qualification Date</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Date</th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Result</th>

                                    <!-- NEW: Add Resolution Number column -->
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution # (MOP)</th>

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

                                    <!-- RENAMED: Resolution Number to Resolution # -->
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution #</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">

                                @forelse ($form['items'] ?? [] as $itemIndex => $item)
                                    @php
                                        $modeId = $item['mode_of_procurement_id'] ?? null;
                                        $rowUid = $item['uid'] ?? 'temp_' . $itemIndex;
                                        $currentPrID = $item['prItemID'] ?? null;
                                        $prevPrID = $form['items'][$itemIndex - 1]['prItemID'] ?? null;
                                        $isHead = $itemIndex === 0 || $currentPrID !== $prevPrID;
                                        $isHistoryRow = !$isHead;
                                    @endphp

                                    @if ($isHistoryRow)
                                        @continue
                                    @endif

                                    @php
                                        $historyTargetUid = $rowUid;
                                        $isSavedRecord = isset($item['id']) && is_numeric($item['id']);

                                        $nextItemPrID = $form['items'][$itemIndex + 1]['prItemID'] ?? null;
                                        $hasHistory = $nextItemPrID === $currentPrID;

                                        $hasSchedule =
                                            // Bidding fields
                                            !empty($item['bidding_number']) ||
                                            !empty($item['ib_number']) ||
                                            !empty($item['pre_proc_conference']) ||
                                            !empty($item['ads_post_ib']) ||
                                            !empty($item['pre_bid_conf']) ||
                                            !empty($item['eligibility_check']) ||
                                            !empty($item['sub_open_bids']) ||
                                            !empty($item['bidding_date']) ||
                                            !empty($item['bidding_result']) ||
                                            // SVP/Canvass fields
                                            !empty($item['rfq_no']) ||
                                            !empty($item['canvass_date']) ||
                                            !empty($item['date_returned_of_canvass']) ||
                                            !empty($item['abstract_of_canvass_date']) ||
                                            !empty($item['resolution_number']);

                                        $hasPostData = $this->hasPostDataForItem($itemIndex);
                                        $canEditMop = auth()->user()->can('edit_mode::of::procurement');

                                        $showFields = !empty($modeId) && $isSavedRecord;
                                        $disableInputs = $isHead && $hasPostData && !$canEditMop;
                                        $disableSelect = $hasSchedule || $modeId == 1;

                                        $canAddNewMode = false;

                                        if (
                                            in_array($modeId, [
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
                                            ])
                                        ) {
                                            $canAddNewMode = false;
                                        } else {
                                            $bidResult = $item['bidding_result'] ?? '';
                                            $hasBiddingData =
                                                !empty($item['ib_number']) &&
                                                !empty($item['bidding_number']) &&
                                                !empty($item['bidding_date']);
                                            $hasPreProcConference =
                                                !empty($item['pre_proc_conference']) &&
                                                trim($item['pre_proc_conference']) !== '';

                                            $canAddNewMode =
                                                $modeId == 1 ||
                                                (($hasBiddingData || $hasPreProcConference) &&
                                                    $bidResult === 'UNSUCCESSFUL' &&
                                                    !$this->isPostAvailable);
                                        }
                                    @endphp

                                    <tr wire:key="row-{{ $currentPrID }}"
                                        class="hover:bg-emerald-50 dark:hover:bg-neutral-800">

                                        {{-- Action Buttons (History & Add) --}}
                                        <td class="px-2 py-2 align-middle">
                                            <div class="flex items-center justify-center gap-1">
                                                @if ($isHead)
                                                    @if ($hasHistory)
                                                        <button type="button"
                                                            wire:click="toggleHistory('{{ $currentPrID }}')"
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 transition-colors text-gray-500 dark:text-gray-400"
                                                            title="{{ $this->showHistory && $this->historyForPrItemId === $currentPrID ? 'Hide History' : 'Show History' }}">
                                                            @if ($this->showHistory && $this->historyForPrItemId === $currentPrID)
                                                                {{-- Down Arrow (Hide) --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-4 w-4 text-emerald-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                                </svg>
                                                            @else
                                                                {{-- Right Arrow (Show) --}}
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
                                                        <div class="w-7 h-7"></div>
                                                    @endif

                                                    @if ($canAddNewMode)
                                                        <button wire:click.prevent="addItem({{ $itemIndex }})"
                                                            class="inline-flex items-center justify-center w-7 h-7 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                                            title="Add New Mode">
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
                                                    <div class="w-7 h-7 flex items-center justify-center text-gray-300">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Item No --}}
                                        <td class="px-2 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap w-16"
                                            @disabled($disableInputs)>
                                            {{ $item['item_no'] }}
                                        </td>

                                        {{-- Description --}}
                                        <td class="px-2 py-2 text-gray-900 dark:text-gray-100 w-64 truncate"
                                            @disabled($disableInputs)>
                                            {{ $item['description'] }}
                                        </td>

                                        {{-- Mode Select --}}
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
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- Bidding Number --}}
                                        @if (
                                            $showFields &&
                                                $modeId &&
                                                !in_array($modeId, [1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="bid-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_number"
                                                    maxlength="2"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.bidding_number')
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="ib-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ib_number"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.ib_number')
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="IB-2025-002" @disabled($disableInputs)>
                                            </td>

                                            {{-- Pre-Proc Conference --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-proc-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_proc_conference"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- Ads/Post IB --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ads-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ads_post_ib"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- Pre-Bid Conference --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-bid-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_bid_conf"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- Eligibility Check --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="elig-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.eligibility_check"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- Sub/Open of Bids --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="sub-open-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.sub_open_bids"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <!-- Bid Evaluation Date -->
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="bid-eval-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bid_evaluation_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <!-- Post Qualification Date -->
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="post-qual-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.post_qualification_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <!-- Keep existing Bidding Date field -->
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="bid-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                @php
                                                    $biddingResult = $item['bidding_result'] ?? '';
                                                    $hasPostData = $this->hasPostDataForItem($itemIndex);
                                                    $canEditMop = auth()->user()->can('edit_mode::of::procurement');
                                                    $shouldDisableBiddingResult =
                                                        $disableInputs ||
                                                        ($biddingResult === 'SUCCESSFUL' &&
                                                            $hasPostData &&
                                                            !$canEditMop);
                                                @endphp

                                                <select wire:key="res-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_result"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @if ($shouldDisableBiddingResult) disabled @endif>
                                                    <option value="">Select...</option>
                                                    <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                    <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                                </select>
                                            </td>

                                            @if ($showFields && $modeId && !in_array($modeId, [1]))
                                                <td class="px-2 py-2">
                                                    @if (in_array($modeId, [3, 4, 5, 6]))
                                                        <input type="text" wire:key="res-mop-{{ $rowUid }}"
                                                            wire:model.defer="form.items.{{ $itemIndex }}.resolution_number_mop"
                                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.resolution_number_mop')
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                            placeholder="RES-2025-001" @disabled($disableInputs)
                                                            title="Required for this procurement mode">
                                                    @else
                                                        {{-- Mode 2: Show N/A or leave empty --}}
                                                        <div class="flex items-center justify-center h-full">
                                                            <span
                                                                class="text-gray-400 dark:text-gray-500 text-xs"></span>
                                                        </div>
                                                    @endif
                                                </td>
                                            @else
                                                <td class="px-2 py-2"></td>
                                            @endif

                                            >
                                        @else
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- RFQ No. --}}
                                        @if ($showFields && in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="rfq-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.rfq_no"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
{{ $errors->has('form.items.' . $itemIndex . '.rfq_no')
    ? 'border-red-500 focus:ring-red-500'
    : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="RFQ-2025-001" @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="can-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.canvass_date"
                                                    class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.canvass_date')
        ? 'border-red-500 focus:ring-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ret-can-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.date_returned_of_canvass"
                                                    class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.date_returned_of_canvass')
        ? 'border-red-500 focus:ring-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="abs-can-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.abstract_of_canvass_date"
                                                    class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.abstract_of_canvass_date')
        ? 'border-red-500 focus:ring-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- CHANGED: Resolution Number (MOP) for SVP modes --}}
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="res-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.resolution_number_mop"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.resolution_number_mop')
        ? 'border-red-500 focus:ring-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="RES-2025-001" @disabled($disableInputs)>
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif


                                    </tr>

                                    @if ($isHead && $showHistory && $historyForPrItemId === $currentPrID)
                                        <tr
                                            class="bg-gray-50 dark:bg-neutral-800/30 border-t-2 border-emerald-500 dark:border-emerald-900">
                                            <td colspan="19" class="px-0 py-0">
                                                <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                                                    <table class="w-full text-xs min-w-max">
                                                        <thead
                                                            class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                                            <tr>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white w-20 border-b border-gray-300 dark:border-neutral-600">
                                                                </th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                                                </th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                                                </th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                                                    Mode of Procurement</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                                                    Bidding #</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    IB No.</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Pre-Proc Conference</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Ads/Post IB</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Pre-Bid Conference</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Eligibility Check</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Sub/Open of Bids</th>

                                                                <!-- NEW: Add these two columns -->
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Bid Evaluation Date</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Post Qualification Date</th>

                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Bidding Date</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Bidding Result</th>

                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Resolution # (MOP)</th>

                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    RFQ No.</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Canvass Date</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Returned of Canvass</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Abstract of Canvass</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Resolution #</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody
                                                            class="divide-y divide-gray-200 dark:divide-neutral-800">
                                                            @if ($historyItems->count() > 0)
                                                                @foreach ($historyItems as $histIndex => $historyItem)
                                                                    @php
                                                                        $actualIndex = collect($form['items'])->search(
                                                                            fn($item) => $item['uid'] ===
                                                                                $historyItem['uid'],
                                                                        );
                                                                        $historyModeId =
                                                                            $historyItem['mode_of_procurement_id'] ??
                                                                            null;
                                                                    @endphp
                                                                    <tr
                                                                        class="hover:bg-gray-100 dark:hover:bg-neutral-700 border-b border-gray-200 dark:border-neutral-700">
                                                                        @if ($historyModeId == 1)
                                                                            <td class="px-2 py-2 align-middle"></td>
                                                                        @else
                                                                            <td class="px-2 py-2 align-middle">
                                                                                @can('edit_mode::of::procurement')
                                                                                    <button type="button"
                                                                                        wire:click="editHistoryItem({{ $actualIndex }})"
                                                                                        class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                                                                        title="Edit History Record">
                                                                                        <x-heroicon-o-pencil
                                                                                            class="w-4 h-4" />
                                                                                    </button>
                                                                                @endcan
                                                                            </td>
                                                                        @endif

                                                                        <td class="px-2 py-2"></td>
                                                                        <td class="px-2 py-2"></td>

                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            @php $mode = $modeOfProcurements->firstWhere('id', $historyModeId); @endphp
                                                                            {{ $mode?->modeofprocurements ?? 'N/A' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['bidding_number'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['ib_number'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['pre_proc_conference'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['ads_post_ib'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['pre_bid_conf'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['eligibility_check'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['sub_open_bids'] ?? '-' }}
                                                                        </td>

                                                                        <!-- NEW: Add these two cells -->
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['bid_evaluation_date'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['post_qualification_date'] ?? '-' }}
                                                                        </td>

                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['bidding_date'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['bidding_result'] ?? '-' }}
                                                                        </td>

                                                                        <!-- NEW: Display resolution_number for modes 3-6 -->
                                                                        <td
                                                                            class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                                            @if (in_array($historyModeId, [3, 4, 5, 6]))
                                                                                {{ $historyItem['resolution_number'] ?? '-' }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>

                                                                        <td
                                                                            class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['rfq_no'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['canvass_date'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['date_returned_of_canvass'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['abstract_of_canvass_date'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['resolution_number'] ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="21"
                                                                        class="px-2 py-4 text-center text-gray-500">
                                                                        No history available for this item
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="18" class="px-2 py-4 text-center text-gray-500">
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
            <div class="flex flex-col gap-2 pt-2">
                @php
                    // Filter items that meet post-procurement criteria
                    $postAvailableItems = [];
                    foreach ($form['items'] ?? [] as $index => $item) {
                        $modeId = $item['mode_of_procurement_id'] ?? null;

                        if (in_array($modeId, [2, 3, 4, 5, 6])) {
                            $bidResult = $item['bidding_result'] ?? '';
                            $ntfResult = $item['ntf_bidding_result'] ?? '';

                            if ($bidResult === 'SUCCESSFUL' || $ntfResult === 'SUCCESSFUL') {
                                $postAvailableItems[$index] = $item;
                                continue;
                            }
                        }

                        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                            if (
                                !empty($item['rfq_no']) &&
                                !empty($item['canvass_date']) &&
                                !empty($item['date_returned_of_canvass']) &&
                                !empty($item['abstract_of_canvass_date']) &&
                                !empty($item['resolution_number'])
                            ) {
                                $postAvailableItems[$index] = $item;
                                continue;
                            }
                        }
                    }
                @endphp

                @if (count($postAvailableItems) > 0)
                    <div
                        class="bg-white rounded-xl p-2 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                            <table class="w-full text-xs min-w-max">
                                <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                    <tr>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white w-20 border-b border-gray-300 dark:border-neutral-600">
                                        </th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                        </th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                        </th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                            Mode of Procurement</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                            Bidding #</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            IB No.</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Pre-Proc Conference</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Ads/Post IB</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Pre-Bid Conference</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Eligibility Check</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Sub/Open of Bids</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Bid Evaluation Date</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Post Qualification Date</th>

                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Bidding Date</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Bidding Result</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Resolution # (MOP)</th>

                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            RFQ No.</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Canvass Date</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Returned of Canvass</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Abstract of Canvass</th>
                                        <th
                                            class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Resolution #</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                    @if ($historyItems->count() > 0)
                                        @foreach ($historyItems as $histIndex => $historyItem)
                                            @php
                                                $actualIndex = collect($form['items'])->search(
                                                    fn($item) => $item['uid'] === $historyItem['uid'],
                                                );
                                                $historyModeId = $historyItem['mode_of_procurement_id'] ?? null;
                                            @endphp
                                            <tr
                                                class="hover:bg-gray-100 dark:hover:bg-neutral-700 border-b border-gray-200 dark:border-neutral-700">
                                                @if ($historyModeId == 1)
                                                    <td class="px-2 py-2 align-middle"></td>
                                                @else
                                                    <td class="px-2 py-2 align-middle">
                                                        @can('edit_mode::of::procurement')
                                                            <button type="button"
                                                                wire:click="editHistoryItem({{ $actualIndex }})"
                                                                class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                                                title="Edit History Record">
                                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                                            </button>
                                                        @endcan
                                                    </td>
                                                @endif

                                                <td class="px-2 py-2"></td>
                                                <td class="px-2 py-2"></td>

                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    @php $mode = $modeOfProcurements->firstWhere('id', $historyModeId); @endphp
                                                    {{ $mode?->modeofprocurements ?? 'N/A' }}
                                                </td>
                                                <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['bidding_number'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['ib_number'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['pre_proc_conference'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['ads_post_ib'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['pre_bid_conf'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['eligibility_check'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['sub_open_bids'] ?? '-' }}
                                                </td>

                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['bid_evaluation_date'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['post_qualification_date'] ?? '-' }}
                                                </td>

                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['bidding_date'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['bidding_result'] ?? '-' }}
                                                </td>

                                                <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                    @if (in_array($historyModeId, [3, 4, 5, 6]))
                                                        {{ $historyItem['resolution_number_mop'] ?? '-' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['rfq_no'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['canvass_date'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['date_returned_of_canvass'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['abstract_of_canvass_date'] ?? '-' }}
                                                </td>
                                                <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                    {{ $historyItem['resolution_number_mop'] ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="21" class="px-2 py-4 text-center text-gray-500">
                                                No history available for this item
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-white rounded-xl p-8 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 text-center">
                        <p class="text-gray-500 dark:text-gray-400">
                            No items available for post-procurement. Complete procurement details in Tab 1 first.
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>


    <div
        class="fixed bottom-5 right-0 left-0 lg:ml-[13.75rem] flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-x-2">

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
    {{-- Edit History Modal --}}
    <x-forms.modal title="Edit History Record" size="max-w-6xl" wire:model="showModal">
        @if ($editingItem)
            <div class="px-4 py-3">

                @php
                    $editModeId = $editingItem['mode_of_procurement_id'] ?? null;
                @endphp

                <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                    <table class="w-full text-xs min-w-max">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-700 z-10">
                            <tr>
                                <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300 w-44">
                                    Mode of Procurement
                                </th>
                                {{-- Bidding Columns --}}
                                @if ($editModeId && !in_array($editModeId, [1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300 w-20">
                                        Bidding #</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        IB No.</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Pre-Proc</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Ads/Post IB</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Pre-Bid</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Eligibility</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Sub/Open</th>

                                    <!-- NEW: Add these two columns -->
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Bid Evaluation</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Post Qual.</th>

                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Bidding Date</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Result</th>

                                    <!-- NEW: Add Resolution # (MOP) for modes 3-6 -->
                                    @if (in_array($editModeId, [3, 4, 5, 6]))
                                        <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                            Resolution # <span class="text-red-500">*</span></th>
                                    @endif
                                @endif

                                @if ($editModeId && in_array($editModeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        RFQ No.</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Canvass Date</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Returned</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Abstract</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Resolution #</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white dark:bg-neutral-800">
                                {{-- Mode (Read Only) --}}
                                <td class="px-2 py-2">
                                    <select wire:model.live="editingItem.mode_of_procurement_id" disabled
                                        class="w-full px-2 py-1 text-xs border-0 bg-gray-100 dark:bg-neutral-700 dark:text-white rounded cursor-not-allowed">
                                        <option value="">Select...</option>
                                        @foreach ($modeOfProcurements ?? [] as $modeOption)
                                            <option value="{{ $modeOption->id }}">
                                                {{ $modeOption->modeofprocurements }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Bidding Fields --}}
                                @if ($editModeId && !in_array($editModeId, [1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.bidding_number"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.ib_number"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.pre_proc_conference"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.ads_post_ib"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.pre_bid_conf"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.eligibility_check"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.sub_open_bids"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.bid_evaluation_date"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.post_qualification_date"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.bidding_date"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        @php
                                            $editBiddingResult = $editingItem['bidding_result'] ?? '';
                                            $editIndex = $this->editingIndex ?? null;
                                            $editHasPostData =
                                                $editIndex !== null ? $this->hasPostDataForItem($editIndex) : false;
                                            $editCanEditMop = auth()->user()->can('edit_mode::of::procurement');
                                            $disableEditBiddingResult =
                                                $editBiddingResult === 'SUCCESSFUL' &&
                                                $editHasPostData &&
                                                !$editCanEditMop;
                                        @endphp

                                        <select wire:model.defer="editingItem.bidding_result"
                                            @if ($disableEditBiddingResult) disabled @endif
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white
    {{ $disableEditBiddingResult ? 'cursor-not-allowed' : '' }}">
                                            <option value="">Select...</option>
                                            <option value="SUCCESSFUL">SUCCESSFUL</option>
                                            <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                        </select>
                                    </td>

                                    @if (in_array($editModeId, [3, 4, 5, 6]))
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model.defer="editingItem.resolution_number_mop"
                                                placeholder="RES-2025-001"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                        </td>
                                    @endif
                                @endif

                                {{-- SVP/NTF Fields --}}
                                @if ($editModeId && in_array($editModeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.rfq_no"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.canvass_date"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.date_returned_of_canvass"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.abstract_of_canvass_date"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.resolution_number_mop"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer Actions --}}
                <div
                    class="border-t border-gray-200 dark:border-neutral-700 pt-4 mt-4 flex items-center justify-end gap-2">
                    <button type="button" wire:click="closeEditModal"
                        class="px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-600 border border-gray-300 dark:border-neutral-500 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-500 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="updateHistoryItem"
                        class="flex items-center gap-2 px-2 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Save
                    </button>
                </div>

            </div>
        @endif
    </x-forms.modal>
</div>
