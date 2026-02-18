<div class="space-y-6">
    <div
        class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden relative">
        <!-- PR Number Badge - Top Left Corner -->
        <div class="absolute top-0 left-0 z-10">
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-tl-xl rounded-br-xl text-s font-semibold bg-emerald-600 text-white shadow-md">
                PR #{{ $form['pr_number'] ?? 'N/A' }}
            </span>
        </div>

        <!-- ABC Badge - Top Right Corner -->
        <div class="absolute top-0 right-0 z-10">
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-tr-xl rounded-bl-xl text-s font-semibold bg-blue-600 text-white shadow-md">
                ABC: ₱{{ $abc ? number_format($abc, 2) : 'N/A' }}
            </span>
        </div>

        <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-500"></div>
        <div class="p-6 pt-8">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Procurement Program / Project</p>
                    <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                        {{ $form['procurement_program_project'] ?? 'No project description available' }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <div
        class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

        <ul class="flex items-center w-full max-w-7xl px-4 py-3 bg-white dark:bg-neutral-700 mx-auto"
            data-hs-stepper='{"isCompleted": true}'>

            {{-- STEP 1: DETAILS --}}
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
                    class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500' }}">
                    Mode of Procurement
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
            <div class="flex flex-col">

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
                                    Mode of Procurement Details
                                </h3>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Manage procurement methods and bidding information
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <caption class="sr-only">Mode of Procurement Details - Per Lot View</caption>
                            <thead class="bg-gray-200 dark:bg-neutral-800">

                                <tr>

                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white w-12 border-b border-gray-300 dark:border-neutral-600">
                                    </th>

                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white w-16 border-b border-gray-300 dark:border-neutral-600">
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
                                        PhilGEPS Posting Ref #
                                    </th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Ads/Post IB</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Proc Conference</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        List of Invited Observers</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Pre-Bid)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Eligibility)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Sub/Open)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Bid)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Post Qual)</th>

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
                                        Bid Evaluation Date
                                    </th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Post Qualification Date
                                    </th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bidding Result</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution # (MOP)
                                    </th>

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

                                </tr>

                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">

                                @forelse (array_reverse($form['items'] ?? [], true) as $itemIndex => $item)
                                    @php
                                        $modeId = $item['mode_of_procurement_id'] ?? null;
                                        $rowUid = $item['uid'] ?? 'temp_' . $itemIndex;
                                        $isSavedRecord = isset($item['id']) && is_numeric($item['id']);
                                        $isHistory = !$loop->first;

                                        // Use component method for consistent empty checking
                                        $hasSchedule = $this->itemHasSchedule($item);

                                        $hasPostData = \App\Models\PostProcurement::where(
                                            'ref_id',
                                            $this->procID,
                                        )->exists();
                                        $canEditMop = auth()->user()->can('edit_mode::of::procurement');
                                        $isCurrentRow = $loop->first;

                                        $disableSelect = $isHistory || $hasSchedule || $rowUid === 'MOP-1-1';
                                        $disableInputs = $isHistory || ($isCurrentRow && $hasPostData && !$canEditMop);
                                        $showFields = $isSavedRecord;

                                        // Show current row always, history rows only when $showHistory is true
                                        if ($isHistory && !$showHistory) {
                                            continue;
                                        }
                                    @endphp

                                    <tr wire:key="row-{{ $rowUid }}"
                                        class="hover:bg-gray-50 dark:hover:bg-neutral-700 {{ $isHistory ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">

                                        <!-- History Button Column -->
                                        <td class="px-2 py-2 text-center">
                                            @if ($loop->first && $rowUid !== 'MOP-1-1')
                                                <button type="button" wire:click="toggleHistory"
                                                    class="p-1 text-xs rounded hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors
                                                    {{ $showHistory ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                                                    title="Toggle History">
                                                    @if ($showHistory)
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
                                            @elseif ($isHistory)
                                                <span
                                                    class="text-xs text-gray-400 dark:text-gray-500 italic">History</span>
                                            @endif
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-2 py-2 text-center">
                                            @if ($loop->first)
                                                @php
                                                    $canAddRebid = $this->canAddRebidForItem($item, $modeId);
                                                @endphp

                                                @if ($canAddRebid)
                                                    <button wire:key="add-item-btn-{{ $rowUid }}"
                                                        wire:click.prevent="addItem" wire:loading.attr="disabled"
                                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                                        class="inline-flex items-center justify-center w-7 h-7 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                        title="Add New Row" aria-label="Add new procurement row">
                                                        <svg wire:loading.remove class="w-4 h-4" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 4.5v15m7.5-7.5h-15" />
                                                        </svg>
                                                        <svg wire:loading class="animate-spin w-4 h-4"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12"
                                                                r="10" stroke="currentColor" stroke-width="4">
                                                            </circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            @elseif ($isHistory && $modeId != 1)
                                                @can('edit_mode::of::procurement')
                                                    <button type="button"
                                                        wire:click="editHistoryItem({{ $itemIndex }})"
                                                        class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-800 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                                        title="Edit History Record">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                @endcan
                                            @endif
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

                                        @if ($showFields && $this->isCompetitiveBidding($modeId))
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
                                                <input type="text" wire:key="philgeps-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.philgeps_posting_ref_no"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.philgeps_posting_ref_no')
                ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="PHL-2025-001" @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ads-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ads_post_ib"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-proc-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_proc_conference"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <textarea wire:key="list-observers-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.list_invited_observers" rows="1"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    placeholder="List observers..." @disabled($disableInputs)></textarea>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="obs-prebid-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.obsrvr_prebid_conf"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="obs-elig-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.obsrvr_eligibility"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="obs-subopen-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.obsrvr_sub_open_of_bid"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="obs-bid-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.obsrvr_bid"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="obs-postqual-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.obsrvr_post_qual"
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
                                                <input type="date" wire:key="bid-eval-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bid_evaluation_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="post-qual-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.post_qualification_date"
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

                                            {{-- Resolution # (MOP) for competitive bidding modes 2-6 --}}
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="res-mop-comp-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.resolution_number_mop"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.resolution_number_mop')
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="RES-2025-001" @disabled($disableInputs)>
                                            </td>

                                            {{-- Empty SVP columns for competitive bidding --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>

                                            {{-- SVP/ALTERNATIVE MODES (7-24) --}}
                                        @elseif ($showFields && in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                            {{-- Empty cells for Bidding # and IB No. --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>

                                            {{-- PhilGEPS Posting Ref # for SVP modes --}}
                                            @if ($abc >= 200000)
                                                <td class="px-2 py-2">
                                                    <input type="text" wire:key="philgeps-svp-{{ $rowUid }}"
                                                        wire:model.defer="form.items.{{ $itemIndex }}.philgeps_posting_ref_no"
                                                        class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.philgeps_posting_ref_no')
                ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                        placeholder="PHL-2025-001" @disabled($disableInputs)
                                                        title="PhilGEPS Posting Reference Number">
                                                </td>
                                            @else
                                                <td class="px-2 py-2"></td>
                                            @endif

                                            {{-- Ads/Post IB for SVP modes --}}
                                            @if ($abc >= 200000)
                                                <td class="px-2 py-2">
                                                    <input type="date" wire:key="ads-svp-{{ $rowUid }}"
                                                        wire:model.defer="form.items.{{ $itemIndex }}.ads_post_ib"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                        @disabled($disableInputs)
                                                        title="Advertisement/Post IB Date">
                                                </td>
                                            @else
                                                <td class="px-2 py-2"></td>
                                            @endif

                                            {{-- Empty cell for Pre-Proc Conference --}}
                                            <td class="px-2 py-2"></td>

                                            {{-- Empty cells for observer fields (not needed for SVP modes 7-24) --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>

                                            {{-- Empty cells for remaining bidding-specific fields (6 fields: Pre-Bid, Eligibility, Sub/Open, Bid Eval, Post Qual, Bidding Result) --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>

                                            {{-- SVP-specific fields --}}
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="res-mop-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.resolution_number_mop"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
            {{ $errors->has('form.items.' . $itemIndex . '.resolution_number_mop')
                ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="RES-2025-001" @disabled($disableInputs)>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="rfq-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.rfq_no"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white  disabled:opacity-60 disabled:cursor-not-allowed
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

                                            {{-- MODE 1 OR UNSAVED RECORDS - ALL EMPTY --}}
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

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="26"
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
                                    Post-Procurement Data
                                </h3>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Award information and supplier details
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead class="bg-gray-200 dark:bg-neutral-800">
                                <tr>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution Award Number</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution Award Date</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Notice of Award Number
                                    </th>

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
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-72">
                                        Supplier</th>
                                </tr>

                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                <tr>

                                    <td class="px-2 py-2 align-top">
                                        <input type="text" wire:model.defer="resolutionAwardNumber"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="RES-YYYY-NNN">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="resolutionAwardDate"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="text" wire:model.defer="noticeOfAwardNumber"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="NOAYYYY-NNNN">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="noticeOfAward"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <div class="relative">
                                            <span
                                                class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-600 dark:text-gray-400 pointer-events-none">₱</span>
                                            <input type="text" wire:model.defer="awardedAmount" x-data
                                                x-mask:dynamic="$money($input, '.', ',', 2)"
                                                class="w-full pl-6 pr-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                placeholder="0.00">
                                        </div>
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="text" wire:model.defer="philgepsNoticeOfAwardNo"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                            placeholder="PHL-NOA-YYYY-NNN">
                                    </td>

                                    <td class="px-2 py-2 align-top">
                                        <input type="date" wire:model.defer="philgepsPostingOfAward"
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

                {{-- Forward to PMU Action Section --}}
                @if ($this->canForwardToPmu)
                    <div
                        class="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-emerald-600 dark:bg-emerald-700 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">
                                        Ready to Forward
                                    </h4>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                        All post-procurement details are complete. You can now forward this to PMU.
                                    </p>
                                </div>
                            </div>
                            <button wire:click="openForwardModal"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-md hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                Forward to PMU
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>


    <div
        class="fixed bottom-4 right-0 left-0 lg:left-48  flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-700 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-3">
            <button wire:click="cancel"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600">
                Cancel
            </button>
            <button wire:click="save"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save
            </button>
        </div>
    </div>

    {{-- Edit History Modal --}}
    <x-forms.modal wire:model="showModal" title="Edit History Record" size="max-w-6xl" model="showModal"
        closeMethod="closeModal">
        @if ($editingItem)
            <div class="px-4 py-3">

                @php
                    $editModeId = $editingItem['mode_of_procurement_id'] ?? null;
                    $editModeName =
                        $modeOfProcurements->firstWhere('id', $editModeId)?->modeofprocurements ?? 'Unknown Mode';
                @endphp

                {{-- Mode Display Section - IMPROVED --}}
                <div
                    class="mb-4 p-3 bg-gray-50 dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Mode of Procurement:</span>
                            <span
                                class="ml-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $editModeName }}</span>
                        </div>
                    </div>
                </div>

                {{-- Warning Banner for Locked Fields --}}
                @php
                    $editBiddingResult = $editingItem['bidding_result'] ?? '';
                    $editHasPostData = \App\Models\PostProcurement::where('ref_id', $this->procID)->exists();
                    $editCanEditMop = auth()->user()->can('edit_mode::of::procurement');
                    $editShouldDisableBiddingResult =
                        $editBiddingResult === 'SUCCESSFUL' && $editHasPostData && !$editCanEditMop;
                @endphp

                @if ($editShouldDisableBiddingResult)
                    <div
                        class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Bidding Result Locked
                                </p>
                                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                                    Cannot change SUCCESSFUL result because post-procurement data exists. Special
                                    permission required.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Table Layout --}}
                <div
                    class="overflow-x-auto max-h-[60vh] overflow-y-auto border border-gray-200 dark:border-neutral-600 rounded-lg">
                    <table class="w-full text-xs min-w-max">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-700 z-10">
                            <tr>
                                @if ($editModeId && in_array($editModeId, [2, 3, 4, 5, 6]))
                                    {{-- COMPETITIVE BIDDING HEADERS (Modes 2-6) --}}
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
                                        PhilGEPS Ref #
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Ads/Post IB
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Proc Conf.
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        List of Invited Observers</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Pre-Bid)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Eligibility)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Sub/Open)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Bid)</th>

                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Observers (Post Qual)</th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Pre-Bid Conf.
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Eligibility
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Sub/Open Bids
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Bid Evaluation
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Post Qual.
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Result
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution # (MOP)
                                    </th>
                                @endif

                                @if ($editModeId && in_array($editModeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    {{-- SVP/ALTERNATIVE MODE HEADERS (Modes 7-24) --}}
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Resolution # (MOP)
                                    </th>
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
                                        Returned
                                    </th>
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                        Abstract
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white dark:bg-neutral-800">
                                @if ($editModeId && in_array($editModeId, [2, 3, 4, 5, 6]))
                                    {{-- COMPETITIVE BIDDING FIELDS --}}
                                    <td class="px-2 py-2">
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
                                        <input type="text" wire:model.defer="editingItem.philgeps_posting_ref_no"
                                            placeholder="PHL-2025-001"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.ads_post_ib"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.pre_proc_conference"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <textarea wire:model.defer="editingItem.list_invited_observers" rows="1"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white"
                                            placeholder="List observers..."></textarea>
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.obsrvr_prebid_conf"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.obsrvr_eligibility"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.obsrvr_sub_open_of_bid"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.obsrvr_bid"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>

                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="editingItem.obsrvr_post_qual"
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
                                        <select wire:model.defer="editingItem.bidding_result"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-700 dark:text-white
                                            {{ $editShouldDisableBiddingResult
                                                ? 'border-gray-300 dark:border-neutral-600 bg-gray-100 dark:bg-neutral-800 cursor-not-allowed opacity-60'
                                                : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @if ($editShouldDisableBiddingResult) disabled @endif>
                                            <option value="">Select...</option>
                                            <option value="SUCCESSFUL">SUCCESSFUL</option>
                                            <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.resolution_number_mop"
                                            placeholder="RES-2025-001"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                @endif

                                @if ($editModeId && in_array($editModeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]))
                                    {{-- SVP/ALTERNATIVE MODE FIELDS --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.resolution_number_mop"
                                            placeholder="RES-2025-001"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
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
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer Actions --}}
                <div class="border-t border-gray-200 dark:border-neutral-700 pt-4 mt-4 flex items-center justify-end">
                    <div class="flex gap-2">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-600 border border-gray-300 dark:border-neutral-500 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-500 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="updateHistoryItem"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save
                        </button>
                    </div>
                </div>

            </div>
        @endif
    </x-forms.modal>

    {{-- Forward to PMU Modal --}}
    <x-forms.modal wire:model="showForwardModal" title="Forward to PMU" size="max-w-lg" model="showForwardModal"
        closeMethod="closeForwardModal">
        <div class="px-4 py-3">
            {{-- Information Section --}}
            <div
                class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-1">
                            Forward Procurement to PMU
                        </h4>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300">
                            This will mark the procurement as forwarded to the Procurement Management Unit (Stage 7).
                            Please enter the actual date when this procurement was forwarded.
                        </p>
                    </div>
                </div>
            </div>

            {{-- PR Number Display --}}
            <div
                class="mb-4 p-3 bg-gray-50 dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-600">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">PR Number:</span>
                        <span
                            class="ml-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $form['pr_number'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Date Input Field --}}
            <div class="mb-4">
                <label for="actualDateForwarded"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Actual Date Forwarded <span class="text-red-500">*</span>
                </label>
                <input type="date" id="actualDateForwarded" wire:model.defer="actualDateForwarded"
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
