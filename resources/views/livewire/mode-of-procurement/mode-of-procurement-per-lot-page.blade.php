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
                                :rows="$textareaRows" :autoResize="true" />
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
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                        Mode of Procurement
                                    </th>

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
                                        $isSavedRecord = isset($item['id']) && is_numeric($item['id']);
                                        $isHistory = !$loop->first;

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

                                        $disableSelect = $isHistory || $hasSchedule || $rowUid === 'MOP-1-1';
                                        $disableInputs = $isHistory;
                                        $showFields = $isSavedRecord;
                                        $isVisible = $loop->first;
                                    @endphp

                                    <tr wire:key="row-{{ $rowUid }}"
                                        class="{{ $isVisible ? '' : 'hidden' }} hover:bg-emerald-100 dark:hover:bg-neutral-800">

                                        <td class="px-2 py-2 align-middle">
                                            <div class="flex items-center justify-center gap-1">

                                                @if ($loop->first)
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
                                                        <div class="w-7 h-7"></div>
                                                    @endif

                                                    @php
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
                                                                24,
                                                            ])
                                                        ) {
                                                            $canAddRebid = false;
                                                        } else {
                                                            $bidResult = $item['bidding_result'] ?? '';

                                                            $hasBiddingData =
                                                                !empty($item['ib_number']) &&
                                                                !empty($item['bidding_number']) &&
                                                                !empty($item['bidding_date']);

                                                            // Check if Pre-Proc Conference is filled
                                                            $hasPreProcConference =
                                                                !empty($item['pre_proc_conference']) &&
                                                                trim($item['pre_proc_conference']) !== '';

                                                            $canAddRebid =
                                                                $modeId == 1 ||
                                                                (($hasBiddingData || $hasPreProcConference) &&
                                                                    $bidResult === 'UNSUCCESSFUL' &&
                                                                    !$this->isPostAvailable);
                                                        }
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

                                        <td class="px-2 py-2">
                                            <select wire:key="select-mode-{{ $rowUid }}"
                                                wire:model.live="form.items.{{ $itemIndex }}.mode_of_procurement_id"
                                                class="w-full max-w-xs px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableSelect)>

                                                <option value="">Select Mode...</option>
                                                @foreach ($modeOfProcurements ?? [] as $modeOption)
                                                    <option value="{{ $modeOption->id }}"
                                                        title="{{ $modeOption->modeofprocurements }}">
                                                        {{ Str::limit($modeOption->modeofprocurements, 35, '...') }}
                                                    </option>
                                                @endforeach>
                                            </select>
                                        </td>

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

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="bid-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                @php
                                                    $biddingResult = $item['bidding_result'] ?? '';

                                                    $hasPostData = \App\Models\PostProcurement::where(
                                                        'ref_id',
                                                        $this->procID,
                                                    )->exists();

                                                    $shouldDisableBiddingResult =
                                                        $disableInputs ||
                                                        ($biddingResult === 'SUCCESSFUL' && $hasPostData);
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
                                        @endif

                                        @if ($showFields && in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="rfq-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.rfq_no"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white
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
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif

                                    </tr>

                                    @if ($loop->first && $showHistory)
                                        <tr
                                            class="bg-gray-50 dark:bg-neutral-800/30 border-t-2 border-emerald-500 dark:border-emerald-900">
                                            <td colspan="20" class="px-0 py-0">
                                                <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                                                    <table class="w-full text-xs min-w-max">
                                                        <thead
                                                            class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                                            <tr>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white w-20 border-b border-gray-300 dark:border-neutral-600">
                                                                </th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
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
                                                                    Bidding Date</th>
                                                                <th
                                                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                                    Bidding Result</th>
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
                                                                    Resolution Number</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody
                                                            class="divide-y divide-gray-200 dark:divide-neutral-800">
                                                            @forelse (array_reverse($form['items'] ?? [], true) as $historyIndex => $historyItem)
                                                                @if (!$loop->first)
                                                                    @php
                                                                        $historyModeId =
                                                                            $historyItem['mode_of_procurement_id'] ??
                                                                            null;
                                                                    @endphp

                                                                    <tr
                                                                        class="hover:bg-gray-100 dark:hover:bg-neutral-700 border-b border-gray-200 dark:border-neutral-700">
                                                                        @can('edit_mode::of::procurement')
                                                                            @if ($historyModeId == 1)
                                                                                <td class="px-2 py-2 align-middle">

                                                                                </td>
                                                                            @else
                                                                                <td class="px-2 py-2 align-middle">
                                                                                    <button type="button"
                                                                                        wire:click="editHistoryItem({{ $historyIndex }})"
                                                                                        class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                                                                        title="Edit History Record">
                                                                                        <x-heroicon-o-pencil
                                                                                            class="w-4 h-4" />
                                                                                    </button>
                                                                                </td>
                                                                            @endif
                                                                        @endcan
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            @php
                                                                                $mode = (
                                                                                    $modeOfProcurements ?? collect()
                                                                                )->firstWhere('id', $historyModeId);
                                                                            @endphp
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
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['bidding_date'] ?? '-' }}
                                                                        </td>
                                                                        <td
                                                                            class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $historyItem['bidding_result'] ?? '-' }}
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
                                                                @endif
                                                            @empty
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

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
            <div class="flex flex-col gap-2 pt-2">
                <div
                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 ">

                    <div class="overflow-x-auto overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white w-20 border-b border-gray-300 dark:border-neutral-600">
                                    </th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution #</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bid Evaluation Date</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Post Qual Date</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Recommending For Award</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Notice of Award</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Awarded Amount</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        PhilGEPS Reference #</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Award Notice #</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Posting of Award|PhilGEPS</th>

                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-72">
                                        Supplier</th>
                                </tr>

                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                <tr>
                                    <td class="px-2 py-2 align-top"></td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="text" wire:model.defer="resolutionNumber"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="RES-YYYY-NNN">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="bidEvaluationDate"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="postQualDate"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="recommendingForAward"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="noticeOfAward"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="number" step="0.01" wire:model.defer="awardedAmount"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="text" wire:model.defer="philgepsReferenceNo"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="PHL-YYYY-NNN">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="text" wire:model.defer="awardNoticeNumber"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="AN-YYYY-NNN">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="dateOfPostingOfAwardOnPhilGEPS"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <select wire:model.defer="supplier_id"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <option value="">Select Supplier...</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>


    <div
        class="fixed bottom-5 right-0 left-0 lg:ml-[13.75rem] flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-3">
            <button wire:click="cancel"
                class="flex items-center gap-2 px-2 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600">
                Cancel
            </button>
            <button wire:click="save"
                class="flex items-center gap-2 px-2 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save
            </button>
        </div>
    </div>

    {{-- Edit History Modal --}}
    <x-forms.modal wire:model="showModal" title="Edit" size="max-w-6xl">
        @if ($editingItem)
            <div class="px-4 py-3">

                @php
                    $editModeId = $editingItem['mode_of_procurement_id'] ?? null;
                @endphp

                {{-- Table Layout --}}
                <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                    <table class="w-full text-xs min-w-max">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-700 z-10">
                            <tr>
                                <th
                                    class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                    Mode of Procurement
                                </th>
                                @if ($editModeId && !in_array($editModeId, [1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                        Bidding #
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        IB No.
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Proc Conference
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Ads/Post IB
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Bid Conference
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Eligibility Check
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Sub/Open of Bids
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Date
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Result
                                    </th>
                                @endif
                                @if ($editModeId && in_array($editModeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        RFQ No.
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Canvass Date
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Returned of Canvass
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Abstract of Canvass
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution Number
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white dark:bg-neutral-800">
                                <td class="px-2 py-2 ">
                                    <select wire:model.live="editingItem.mode_of_procurement_id"
                                        class="w-full px-2 py-1 text-xs border-0 bg-gray-100 dark:bg-neutral-700 dark:text-white rounded cursor-not-allowed"
                                        disabled>
                                        <option value="">Select Mode...</option>
                                        @foreach ($modeOfProcurements ?? [] as $modeOption)
                                            <option value="{{ $modeOption->id }}">
                                                {{ $modeOption->modeofprocurements }}
                                            </option>
                                        @endforeach>
                                    </select>
                                </td>
                                @if ($editModeId && !in_array($editModeId, [1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <td class="px-2 py-2 text-left font-semibold text-black dark:text-white w-20">
                                        <input type="text" wire:model.defer="editingItem.bidding_number"
                                            maxlength="2"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.ib_number"
                                            placeholder="IB-2025-002"
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
                                        <input type="date" wire:model.defer="editingItem.bidding_date"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <select wire:model.defer="editingItem.bidding_result"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white cursor-not-allowed"
                                            disabled>
                                            <option value="">Select...</option>
                                            <option value="SUCCESSFUL">SUCCESSFUL</option>
                                            <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                        </select>
                                    </td>
                                @endif
                                @if ($editModeId && in_array($editModeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.rfq_no"
                                            placeholder="RFQ-2025-001"
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
                                        <input type="text" wire:model.defer="editingItem.resolution_number"
                                            placeholder="RES-2025-001"
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
