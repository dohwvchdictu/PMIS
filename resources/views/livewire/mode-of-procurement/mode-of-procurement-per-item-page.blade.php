<div class="space-y-6">
    <div
        class="bg-white rounded-xl shadow-md border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden relative">
        <!-- PR Number Badge - Top Left Corner -->
        <div class="absolute top-0 left-0 z-10">
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-tl-xl rounded-br-xl text-sm font-semibold bg-emerald-600 text-white shadow-md">
                PR #{{ $form['pr_number'] ?? 'N/A' }}
            </span>
        </div>

        <!-- ABC Badge - Top Right Corner -->
        <div class="absolute top-0 right-0 z-10">
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-tr-xl rounded-bl-xl text-sm font-semibold bg-blue-600 text-white shadow-md">
                ABC: ₱{{ number_format($procurement->abc ?? 0, 2) }}
            </span>
        </div>

        <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-500"></div>
        <div class="px-6 py-5 pt-10">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Procurement Program / Project
                    </p>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $form['procurement_program_project'] ?? 'No project description available' }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <div
        class="bg-white rounded-xl shadow-md border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

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
                                        {{ count($selectedItems) }} item(s) selected for bulk edit
                                    </span>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                        Click Bulk Edit to update mode of procurement data for all selected items
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="deselectAll" type="button"
                                    class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                    Clear Selection
                                </button>
                                <button wire:click="openBulkEditModal" type="button"
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Bulk Edit
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Items Table Section -->
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
                                    Mode of Procurement Items
                                </h3>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                    Select items and use Bulk Edit to update multiple records
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Items List Table -->
                    <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <caption class="sr-only">Mode of Procurement Details - Per Item View</caption>
                            <thead class="bg-gray-200 dark:bg-neutral-800">
                                <tr>
                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-12">
                                        <input type="checkbox" id="select-all-checkbox"
                                            class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            title="Select all" onclick="toggleAllCheckboxes(this)">
                                        @push('scripts')
                                            <script>
                                                function toggleAllCheckboxes(source) {
                                                    const checkboxes = document.querySelectorAll('input[type=checkbox][data-row-checkbox]');
                                                    checkboxes.forEach(cb => {
                                                        if (!cb.disabled) {
                                                            cb.checked = source.checked;
                                                            cb.dispatchEvent(new Event('click', {
                                                                bubbles: true
                                                            }));
                                                        }
                                                    });
                                                }

                                                function updateSelectAllCheckbox() {
                                                    const selectAllCheckbox = document.getElementById('select-all-checkbox');
                                                    if (!selectAllCheckbox) return; // Exit if checkbox doesn't exist

                                                    const checkboxes = document.querySelectorAll('input[type=checkbox][data-row-checkbox]:not(:disabled)');
                                                    const checkedCheckboxes = document.querySelectorAll(
                                                        'input[type=checkbox][data-row-checkbox]:not(:disabled):checked');

                                                    if (checkboxes.length === 0) {
                                                        selectAllCheckbox.checked = false;
                                                        return;
                                                    }

                                                    // Only check if ALL are selected, otherwise uncheck
                                                    if (checkedCheckboxes.length === checkboxes.length && checkboxes.length > 0) {
                                                        selectAllCheckbox.checked = true;
                                                    } else {
                                                        selectAllCheckbox.checked = false;
                                                    }
                                                }

                                                // Listen for Livewire updates
                                                document.addEventListener('livewire:init', () => {
                                                    Livewire.hook('morph.updated', () => {
                                                        updateSelectAllCheckbox();
                                                    });
                                                });

                                                // Update on page load
                                                document.addEventListener('DOMContentLoaded', updateSelectAllCheckbox);

                                                // Post Procurement checkbox synchronization
                                                function updatePostSelectAllCheckbox() {
                                                    const selectAllCheckbox = document.getElementById('select-all-post-checkbox');
                                                    if (!selectAllCheckbox) return; // Exit if checkbox doesn't exist

                                                    const checkboxes = document.querySelectorAll(
                                                        'input[type=checkbox][data-post-row-checkbox]:not(:disabled)');
                                                    const checkedCheckboxes = document.querySelectorAll(
                                                        'input[type=checkbox][data-post-row-checkbox]:not(:disabled):checked');

                                                    if (checkboxes.length === 0) {
                                                        selectAllCheckbox.checked = false;
                                                        return;
                                                    }

                                                    // Only check if ALL are selected, otherwise uncheck
                                                    if (checkedCheckboxes.length === checkboxes.length && checkboxes.length > 0) {
                                                        selectAllCheckbox.checked = true;
                                                    } else {
                                                        selectAllCheckbox.checked = false;
                                                    }
                                                }

                                                // Update post checkboxes on Livewire updates
                                                document.addEventListener('livewire:init', () => {
                                                    Livewire.hook('morph.updated', () => {
                                                        updatePostSelectAllCheckbox();
                                                    });
                                                });

                                                // Update on page load
                                                document.addEventListener('DOMContentLoaded', updatePostSelectAllCheckbox);

                                                // Toggle all post checkboxes function
                                                function toggleAllPostCheckboxes(source) {
                                                    const checkboxes = document.querySelectorAll('input[type=checkbox][data-post-row-checkbox]');
                                                    checkboxes.forEach(cb => {
                                                        if (!cb.disabled) {
                                                            cb.checked = source.checked;
                                                            cb.dispatchEvent(new Event('click', {
                                                                bubbles: true
                                                            }));
                                                        }
                                                    });
                                                }
                                            </script>
                                        @endpush
                                    </th>
                                    <th
                                        class="px-2 py-3 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                        Item No.
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-64">
                                        Item Description
                                    </th>
                                    <th
                                        class="px-2 py-3 text-right font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                        ABC Amount
                                    </th>
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
                                        Bidding Result</th>
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">

                                @forelse ($form['items'] ?? [] as $itemIndex => $item)
                                    @php
                                        // ===== BASIC ROW IDENTIFIERS =====
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
                                        // ===== MODE TYPE CHECKS (Using Helper Methods - Eliminates Magic Numbers) =====
                                        $isCompetitiveBidding = $this->isCompetitiveBidding($modeId);
                                        $isSvpMode = $this->isSvpMode($modeId);
                                        $isPending = $this->isPendingMode($modeId);
                                        $itemAmount = (float) ($item['amount'] ?? 0);
                                        $meetsAbcThreshold = $this->meetsAbcThreshold($itemAmount);

                                        // ===== ROW METADATA =====
                                        $historyTargetUid = $rowUid;
                                        $isSavedRecord = isset($item['id']) && is_numeric($item['id']);
                                        $nextItemPrID = $form['items'][$itemIndex + 1]['prItemID'] ?? null;
                                        $hasHistory = $nextItemPrID === $currentPrID;

                                        // ===== SCHEDULE DATA CHECK (Reduced Nesting) =====
                                        $hasSchedule =
                                            // Bidding fields
                                            !empty($item['bidding_number']) ||
                                            !empty($item['ib_number']) ||
                                            !empty($item['pre_proc_conference']) ||
                                            !empty($item['ads_post_ib']) ||
                                            !empty($item['pre_bid_conf']) ||
                                            !empty($item['eligibility_check']) ||
                                            !empty($item['sub_open_bids']) ||
                                            !empty($item['bidding_result']) ||
                                            // SVP/Canvass fields
                                            !empty($item['rfq_no']) ||
                                            !empty($item['canvass_date']) ||
                                            !empty($item['date_returned_of_canvass']) ||
                                            !empty($item['abstract_of_canvass_date']) ||
                                            !empty($item['resolution_number']);

                                        // ===== PERMISSIONS & STATE =====
                                        $hasPostData = $this->hasPostDataForItem($itemIndex);
                                        $canEditMop = auth()->user()->can('edit_mode::of::procurement');
                                        $showFields = !empty($modeId) && $isSavedRecord;
                                        $disableInputs = $isHead && $hasPostData && !$canEditMop;
                                        $disableSelect = $hasSchedule || $isPending;
                                        $canAddNewMode = $this->canAddNewModeForItem($item, $modeId);
                                        $isSelectedItem = in_array($itemIndex, $selectedItems ?? []) && $isHead;
                                    @endphp

                                    <tr wire:key="row-{{ $currentPrID }}"
                                        class="hover:bg-emerald-50 dark:hover:bg-neutral-800 {{ $isSelectedItem ? 'bg-emerald-100 dark:bg-emerald-900/30' : '' }}"
                                        style="{{ $isSelectedItem ? 'box-shadow: inset 4px 0 0 0 rgb(52 211 153);' : '' }}">
                                        @if ($isHead)
                                            <td
                                                class="px-2 py-2 text-center align-middle {{ $isSelectedItem ? 'border-l-4 border-emerald-400' : '' }}">
                                                <input type="checkbox"
                                                    wire:click="toggleItemSelection({{ $itemIndex }})"
                                                    @checked(in_array($itemIndex, $selectedItems))
                                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-700"
                                                    data-row-checkbox
                                                    onclick="setTimeout(updateSelectAllCheckbox, 100)">
                                            </td>
                                        @else
                                            <td class="px-2 py-2"></td>
                                        @endif
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
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                                </svg>
                                                            @else
                                                                {{-- Right Arrow (Show) --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-4 w-4 text-emerald-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
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
                                                    <div
                                                        class="w-7 h-7 flex items-center justify-center text-gray-300">
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

                                        {{-- Amount --}}
                                        <td class="px-2 py-2 align-middle">
                                            <div
                                                class="text-xs font-semibold text-center text-gray-900 dark:text-white">
                                                ₱{{ number_format((float) ($item['amount'] ?? 0), 2) }}
                                            </div>
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

                                        {{-- COMPETITIVE BIDDING MODES (2-6) --}}
                                        @if ($showFields && in_array($modeId, [2, 3, 4, 5, 6]))
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

                                            {{-- Ads/Post IB --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ads-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ads_post_ib"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- Pre-Proc Conference --}}
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-proc-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_proc_conference"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                    @disabled($disableInputs)>
                                            </td>

                                            {{-- SVP/ALTERNATIVE MODES --}}
                                        @elseif ($showFields && $isSvpMode)
                                            {{-- Empty cells for Bidding # and IB No. --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>

                                            {{-- PhilGEPS Posting Ref # for SVP modes (only if meets ABC threshold) --}}
                                            @if ($meetsAbcThreshold)
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

                                            {{-- Ads/Post IB for SVP modes (only if meets ABC threshold) --}}
                                            @if ($meetsAbcThreshold)
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

                                            {{-- MODE 1 OR UNSAVED RECORDS - ALL EMPTY --}}
                                        @else
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Continue with List of Invited Observers and observer fields for COMPETITIVE BIDDING --}}
                                        @if ($showFields && $isCompetitiveBidding)
                                            {{-- List of Invited Observers --}}
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

                                            {{-- SVP/ALTERNATIVE MODES - Empty cells for list and observers --}}
                                        @elseif ($showFields && $isSvpMode)
                                            {{-- Empty cells for list of invited observers and observer fields (not needed for SVP modes 7-24) --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>

                                            {{-- MODE 1 OR UNSAVED RECORDS --}}
                                        @else
                                            {{-- Empty cells for list of invited observers and observer fields --}}
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Pre-Bid Conference through Bidding Result - Show only for competitive bidding --}}
                                        @if ($showFields && $isCompetitiveBidding)
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

                                            {{-- Empty cells for remaining bidding fields for other modes --}}
                                        @else
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif

                                        {{-- Resolution # (MOP) for competitive bidding modes --}}
                                        @if ($showFields && $isCompetitiveBidding)
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

                                            {{-- SVP/ALTERNATIVE MODES - Resolution # (MOP) comes FIRST to match per-lot --}}
                                        @elseif ($showFields && $isSvpMode)
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="res-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.resolution_number_mop"
                                                    class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
    {{ $errors->has('form.items.' . $itemIndex . '.resolution_number_mop')
        ? 'border-red-500 focus:ring-red-500'
        : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                    placeholder="RES-2025-001" @disabled($disableInputs)>
                                            </td>

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
                                        @else
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2"></td>
                                        @endif


                                    </tr>

                                    @if ($isHead && $showHistory && $historyForPrItemId === $currentPrID)
                                        @if ($this->historyItems->count() > 0)
                                            @foreach ($this->historyItems as $histIndex => $historyItem)
                                                @php
                                                    $actualIndex = collect($form['items'])->search(
                                                        fn($item) => $item['uid'] === $historyItem['uid'],
                                                    );
                                                    $historyModeId = $historyItem['mode_of_procurement_id'] ?? null;
                                                @endphp
                                                <tr
                                                    class="bg-amber-50 dark:bg-amber-900/10 hover:bg-amber-100 dark:hover:bg-amber-900/20">
                                                    {{-- Checkbox Column --}}
                                                    <td class="px-2 py-2 text-center"></td>

                                                    {{-- Actions Column --}}
                                                    @if ($historyModeId == 1)
                                                        <td class="px-2 py-2 align-middle"></td>
                                                    @else
                                                        <td class="px-2 py-2 align-middle">
                                                            @can('edit_mode::of::procurement')
                                                                <button type="button"
                                                                    wire:click="editHistoryItem({{ $actualIndex }})"
                                                                    class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-lg transition-colors"
                                                                    title="Edit History Record">
                                                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                                                </button>
                                                            @endcan
                                                        </td>
                                                    @endif

                                                    {{-- Item No Column --}}
                                                    <td class="px-2 py-2"></td>

                                                    {{-- Description with History Label --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">
                                                            History
                                                        </span>
                                                    </td>

                                                    {{-- Amount Column --}}
                                                    <td class="px-2 py-2 text-right">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ isset($historyItem['amount']) ? '₱' . number_format($historyItem['amount'], 2) : '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Mode of Procurement --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            @php $mode = $modeOfProcurements->firstWhere('id', $historyModeId); @endphp
                                                            {{ $mode?->modeofprocurements ?? 'N/A' }}
                                                        </span>
                                                    </td>

                                                    {{-- Bidding # --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['bidding_number'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- IB No. --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['ib_number'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- PhilGEPS Ref # --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['philgeps_posting_ref_no'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Ads/Post IB --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['ads_post_ib'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Pre-Proc Conference --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['pre_proc_conference'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- List of Invited Observers --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['list_invited_observers'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Observers (Pre-Bid) --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['obsrvr_prebid_conf'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Observers (Eligibility) --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['obsrvr_eligibility'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Observers (Sub/Open) --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['obsrvr_sub_open_of_bid'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Observers (Bid) --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['obsrvr_bid'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Observers (Post Qual) --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['obsrvr_post_qual'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Pre-Bid Conference --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['pre_bid_conf'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Eligibility Check --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['eligibility_check'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Sub/Open of Bids --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['sub_open_bids'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Bid Evaluation Date --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['bid_evaluation_date'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Post Qualification Date --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['post_qualification_date'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Bidding Result --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['bidding_result'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Resolution # (MOP) --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['resolution_number_mop'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- RFQ No. --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['rfq_no'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Canvass Date --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['canvass_date'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Returned of Canvass --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['date_returned_of_canvass'] ?? '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Abstract of Canvass --}}
                                                    <td class="px-2 py-2">
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            {{ $historyItem['abstract_of_canvass_date'] ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="25" class="px-2 py-4 text-center text-gray-500">
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
                                        {{ count($selectedPostItems) }} item(s) selected for bulk edit
                                    </span>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                        Click Bulk Edit to update post procurement data for all selected items
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="deselectAllPostItems" type="button"
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
                                        Bulk Edit
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (count($this->postAvailableItems) > 0)
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
                                        Post Procurement Details
                                    </h3>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                                        {{ count($this->postAvailableItems) }}
                                        item{{ count($this->postAvailableItems) > 1 ? 's' : '' }}
                                        available for post-procurement
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Post Procurement Table -->
                        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                            <table class="w-full text-xs min-w-max">
                                <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                    <tr>
                                        <th
                                            class="px-2 py-3 text-center font-semibold text-black dark:text-white w-10 border-b border-gray-300 dark:border-neutral-600">
                                            <input type="checkbox" id="select-all-post-checkbox"
                                                class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                title="Select all" onclick="toggleAllPostCheckboxes(this)">
                                        </th>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white w-16 border-b border-gray-300 dark:border-neutral-600">
                                            No.
                                        </th>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Description
                                        </th>
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
                                            Notice of Award
                                        </th>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            Awarded Amount
                                        </th>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            PhilGEPS| Notice of Award No.
                                        </th>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                            PhilGEPS| Posting of Award
                                        </th>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-72">
                                            Supplier
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                    @forelse ($this->postAvailableItems as $itemIndex => $item)
                                        @php
                                            $prItemID = $item['prItemID'] ?? null;
                                            $isSelectedPost = in_array($prItemID, $selectedPostItems ?? []);
                                        @endphp

                                        <tr wire:key="post-row-{{ $prItemID }}"
                                            class="hover:bg-emerald-50 dark:hover:bg-neutral-800 {{ $isSelectedPost ? 'bg-emerald-100 dark:bg-emerald-900/30' : '' }}"
                                            style="{{ $isSelectedPost ? 'box-shadow: inset 4px 0 0 0 rgb(52 211 153);' : '' }}">
                                            <td
                                                class="px-2 py-2 text-center align-middle {{ $isSelectedPost ? 'border-l-4 border-emerald-400' : '' }}">
                                                <input type="checkbox"
                                                    wire:click="togglePostItemSelection('{{ $prItemID }}')"
                                                    @checked(in_array($prItemID, $selectedPostItems))
                                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-700"
                                                    data-post-row-checkbox
                                                    onclick="setTimeout(updatePostSelectAllCheckbox, 100)">
                                            </td>
                                            <td class="px-2 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                                {{ $item['item_no'] }}
                                            </td>
                                            <td class="px-2 py-2 text-gray-900 dark:text-gray-100">
                                                {{ $item['description'] }}
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text"
                                                    wire:model.defer="postItems.{{ $prItemID }}.resolutionAwardNumber"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white
                                                {{ $errors->has('postItems.' . $prItemID . '.resolutionAwardNumber') ? 'border-red-500 focus:ring-red-500' : '' }}"
                                                    placeholder="RES-YYYY-NNN">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="date"
                                                    wire:model.defer="postItems.{{ $prItemID }}.resolutionAwardDate"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text"
                                                    wire:model.defer="postItems.{{ $prItemID }}.noticeOfAwardNumber"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="NOA-YYYY-NNN">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="date"
                                                    wire:model.defer="postItems.{{ $prItemID }}.noticeOfAward"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="number" step="0.01"
                                                    wire:model.defer="postItems.{{ $prItemID }}.awardedAmount"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="0.00">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text"
                                                    wire:model.defer="postItems.{{ $prItemID }}.philgepsNoticeOfAwardNo"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="PHL-NOA-YYYY-NNN">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="date"
                                                    wire:model.defer="postItems.{{ $prItemID }}.philgepsPostingOfAward"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                            <td class="px-2 py-2">
                                                <select wire:model.defer="postItems.{{ $prItemID }}.supplier_id"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                                    <option value="">Select Supplier...</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">
                                                            {{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="30" class="px-4 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                                        No items found</h3>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This
                                                        procurement request has no items to display.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>


    <!-- Fixed Action Buttons -->
    <div
        class="fixed bottom-4 right-0 left-0 lg:left-48 flex justify-end px-4 py-3 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-700 shadow-lg z-30">
        <div class="w-full max-w-[110rem] mx-auto flex justify-end gap-3">
            <button wire:click="cancel"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600 transition-colors shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </button>
            <button wire:click="save"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Save
            </button>
        </div>
    </div>

    <!-- Bottom Spacer to prevent content hiding under fixed footer and overall footer -->
    <div class="h-32"></div>

    {{-- Edit History Modal --}}
    <x-forms.modal title="Edit History Record" size="max-w-6xl" wire:model="showModal" model="showModal"
        closeMethod="closeEditModal">
        @if ($editingItem)
            <div class="px-4 py-3">
                {{-- Validation Errors Section --}}
                @if (!empty($scheduleValidationErrors))
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
                                    @foreach ($scheduleValidationErrors as $error)
                                        <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $editModeId = $editingItem['mode_of_procurement_id'] ?? null;
                    $editIsCompetitiveBidding = $this->isCompetitiveBidding($editModeId);
                    $editIsSvpMode = $this->isSvpMode($editModeId);
                @endphp
                <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                    <table class="w-full text-xs min-w-max">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-700 z-10">
                            <tr>
                                <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300 w-44">
                                    Mode of Procurement
                                </th>
                                {{-- Bidding Columns --}}
                                @if ($editIsCompetitiveBidding)
                                    <th
                                        class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300 w-20">
                                        Bidding #</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        IB No.</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        PhilGEPS Ref
                                    </th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Ads/Post IB</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Pre-Proc</th>
                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        List Observers
                                    </th>

                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Obs (Pre-Bid)
                                    </th>

                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Obs (Eligibility)
                                    </th>

                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Obs (Sub/Open)
                                    </th>

                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Obs (Bid)
                                    </th>

                                    <th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">
                                        Obs (Post Qual)
                                    </th>
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
                                        Result</th>

                                    <!-- NEW: Add Resolution # (MOP) for modes 2-6 -->
                                    @if (in_array($editModeId, [2, 3, 4, 5, 6]))
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
                                @if ($editIsCompetitiveBidding)
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.bidding_number"
                                            class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="editingItem.ib_number"
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

                                    @if (in_array($editModeId, [2, 3, 4, 5, 6]))
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model.defer="editingItem.resolution_number_mop"
                                                placeholder="RES-2025-001"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white">
                                        </td>
                                    @endif
                                @endif

                                {{-- SVP/NTF Fields --}}
                                @if ($editIsSvpMode)
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

    <x-forms.modal title="Bulk Edit Items" size="max-w-screen-2xl" wire:model="showBulkEditModal"
        model="showBulkEditModal" closeMethod="closeBulkEditModal">
        @if ($bulkEditData)
            <div class="px-4 py-3">
                {{-- Summary Section --}}
                <div
                    class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-900 dark:text-blue-100">
                        <span class="font-semibold">{{ $bulkEditData['items_count'] ?? 0 }}</span> items selected
                    </p>
                </div>

                {{-- Validation Errors Section --}}
                @if (!empty($bulkEditErrors))
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
                                    @foreach ($bulkEditErrors as $error)
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
                        $modeId = $bulkEditData['mode_of_procurement_id'] ?? null;
                        // Use helper methods instead of magic numbers (Better maintainability)
                        $showBiddingFields = $this->isCompetitiveBidding($modeId);
                        $showSvpFields = $this->isSvpMode($modeId);
                        $abcThreshold = $bulkEditData['amount_threshold'] ?? null;
                        $showPhilgepsForSvp = $showSvpFields && $abcThreshold === '>=200k';

                        // Determine if add button should be enabled
                        $canAddBulkMode =
                            $this->isPendingMode($modeId) ||
                            ($this->isCompetitiveBidding($modeId) &&
                                ($bulkEditData['bidding_result'] ?? '') === 'UNSUCCESSFUL');
                        $isModeSvp = $this->isSvpMode($modeId);
                    @endphp

                    <table class="w-full text-xs min-w-max">
                        <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                            <tr>
                                {{-- Action Column --}}
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white w-20 border-b border-gray-300 dark:border-neutral-600">
                                </th>

                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-44">
                                    Mode of Procurement
                                </th>

                                {{-- COMPETITIVE BIDDING COLUMNS (2-6) --}}
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

                                {{-- SVP/ALTERNATIVE COLUMNS (7-24) --}}
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
                                {{-- Action Column with Add Button --}}
                                <td class="px-2 py-2 align-middle">
                                    <div class="flex items-center justify-center gap-1">
                                        @if ($this->showAddModeButton)
                                            <button type="button" wire:click="bulkAddMode"
                                                class="inline-flex items-center justify-center w-7 h-7 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                                title="Add new mode to all selected items after applying changes"
                                                @disabled($this->disableBulkInputs)>
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

                                {{-- Mode of Procurement (Editable in bulk edit) --}}
                                <td class="px-2 py-2">
                                    <select wire:model.live="bulkEditData.mode_of_procurement_id"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                        @disabled($this->disableBulkInputs || $this->disableModeSelect || ($this->showAddModeButton && !$this->showAddForm))>
                                        <option value="">Select Mode...</option>
                                        @foreach ($modeOfProcurements as $mode)
                                            <option value="{{ $mode->id }}">
                                                {{ $mode->modeofprocurements }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- COMPETITIVE BIDDING FIELDS (2-6) --}}
                                @if ($showBiddingFields)
                                    {{-- Bidding # --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEditData.bidding_number"
                                            maxlength="2"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.bidding_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- IB No. --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEditData.ib_number"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.ib_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="IB-2025-002" @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- PhilGEPS Posting Ref # --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEditData.philgeps_posting_ref_no"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.philgeps_posting_ref_no') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="PHL-2025-001" @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Ads/Post IB --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.ads_post_ib"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.ads_post_ib') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Pre-Proc Conference --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.pre_proc_conference"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.pre_proc_conference') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- List of Invited Observers --}}
                                    <td class="px-2 py-2">
                                        <textarea wire:model.defer="bulkEditData.list_invited_observers" rows="1"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.list_invited_observers') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="List observers..." @disabled($this->disableBulkInputs)></textarea>
                                    </td>

                                    {{-- Observers (Pre-Bid) --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.obsrvr_prebid_conf"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.obsrvr_prebid_conf') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Observers (Eligibility) --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.obsrvr_eligibility"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.obsrvr_eligibility') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Observers (Sub/Open) --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.obsrvr_sub_open_of_bid"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.obsrvr_sub_open_of_bid') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Observers (Bid) --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.obsrvr_bid"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.obsrvr_bid') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Observers (Post Qual) --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.obsrvr_post_qual"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.obsrvr_post_qual') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Pre-Bid Conference --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.pre_bid_conf"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.pre_bid_conf') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Eligibility Check --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.eligibility_check"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.eligibility_check') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Sub/Open of Bids --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.sub_open_bids"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.sub_open_bids') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Bid Evaluation Date --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.bid_evaluation_date"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.bid_evaluation_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Post Qualification Date --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.post_qualification_date"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.post_qualification_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Bidding Result --}}
                                    <td class="px-2 py-2">
                                        <select wire:model.defer="bulkEditData.bidding_result"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.bidding_result') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                            <option value="">Select...</option>
                                            <option value="SUCCESSFUL">SUCCESSFUL</option>
                                            <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                        </select>
                                    </td>

                                    {{-- Resolution # (MOP) --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEditData.resolution_number_mop"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.resolution_number_mop') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="RES-2025-001" @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Empty SVP cells (4 fields) --}}
                                    @for ($i = 0; $i < 4; $i++)
                                        <td class="px-2 py-2"></td>
                                    @endfor

                                    {{-- SVP/ALTERNATIVE FIELDS (7-24) --}}
                                @elseif ($showSvpFields)
                                    {{-- Empty bidding-only cells (2 fields) --}}
                                    @for ($i = 0; $i < 2; $i++)
                                        <td class="px-2 py-2"></td>
                                    @endfor

                                    {{-- PhilGEPS Posting Ref # (only if >= 200k) --}}
                                    @if ($showPhilgepsForSvp)
                                        <td class="px-2 py-2">
                                            <input type="text"
                                                wire:model.defer="bulkEditData.philgeps_posting_ref_no"
                                                class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                                {{ $errors->has('bulkEditData.philgeps_posting_ref_no') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                placeholder="PHL-2025-001"
                                                title="Required for items with ABC ≥ ₱200,000"
                                                @disabled($this->disableBulkInputs)>
                                        </td>
                                    @else
                                        <td class="px-2 py-2"></td>
                                    @endif

                                    {{-- Ads/Post IB (only if >= 200k) --}}
                                    @if ($showPhilgepsForSvp)
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model.defer="bulkEditData.ads_post_ib"
                                                class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                                {{ $errors->has('bulkEditData.ads_post_ib') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                                title="Required for items with ABC ≥ ₱200,000"
                                                @disabled($this->disableBulkInputs)>
                                        </td>
                                    @else
                                        <td class="px-2 py-2"></td>
                                    @endif

                                    {{-- Empty competitive bidding cells (13 fields) --}}
                                    @for ($i = 0; $i < 13; $i++)
                                        <td class="px-2 py-2"></td>
                                    @endfor

                                    {{-- Resolution # (MOP) --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEditData.resolution_number_mop"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.resolution_number_mop') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="RES-2025-001" @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- RFQ No. --}}
                                    <td class="px-2 py-2">
                                        <input type="text" wire:model.defer="bulkEditData.rfq_no"
                                            class="w-full px-2 py-1 text-xs text-right border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.rfq_no') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            placeholder="RFQ-2025-001" @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Canvass Date --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.canvass_date"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.canvass_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Returned of Canvass --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.date_returned_of_canvass"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.date_returned_of_canvass') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- Abstract of Canvass --}}
                                    <td class="px-2 py-2">
                                        <input type="date" wire:model.defer="bulkEditData.abstract_of_canvass_date"
                                            class="w-full px-2 py-1 text-xs border rounded focus:ring-2 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed
                                            {{ $errors->has('bulkEditData.abstract_of_canvass_date') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-neutral-600 focus:ring-emerald-500' }}"
                                            @disabled($this->disableBulkInputs)>
                                    </td>

                                    {{-- NO MODE SELECTED OR MODE 1 - ALL EMPTY (23 fields) --}}
                                @else
                                    @for ($i = 0; $i < 23; $i++)
                                        <td class="px-2 py-2"></td>
                                    @endfor
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer Actions --}}
                <div
                    class="border-t border-gray-200 dark:border-neutral-700 pt-4 mt-6 flex items-center justify-end gap-2">
                    <button type="button" wire:click="closeBulkEditModal"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-600 border border-gray-300 dark:border-neutral-500 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="button" onclick="confirmBulkEditSave()"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save</span>
                    </button>
                </div>
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
                        @this.call('applyBulkEdit').then(() => {
                            // Modal will stay open, backend will handle success notification
                        });
                    }
                });
            }

            function confirmPostBulkEditSave() {
                Swal.fire({
                    title: 'Apply Changes?',
                    text: 'Apply post-procurement changes to selected items?',
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
                        @this.call('applyPostBulkEdit').then(() => {
                            // Modal will stay open, backend will handle success notification
                        });
                    }
                });
            }

            // Listen for bulk edit modal close event to uncheck all checkboxes
            document.addEventListener('livewire:init', () => {
                Livewire.on('bulk-edit-closed', () => {
                    // Uncheck all row checkboxes
                    document.querySelectorAll('input[data-row-checkbox]').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Also uncheck the "select all" checkbox if it exists
                    const selectAllCheckbox = document.getElementById('select-all-checkbox');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                });

                Livewire.on('post-bulk-edit-closed', () => {
                    // Uncheck all post row checkboxes
                    document.querySelectorAll('input[data-post-row-checkbox]').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Also uncheck the "select all" checkbox for post items if it exists
                    const selectAllCheckbox = document.getElementById('select-all-post-checkbox');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                });
            });
        </script>
    @endonce

    <x-forms.modal title="Bulk Edit Post Procurement" size="max-w-screen-2xl" wire:model="showPostBulkEditModal"
        model="showPostBulkEditModal" closeMethod="closePostBulkEditModal">
        @if ($postBulkEditData)
            <div class="px-4 py-3">
                {{-- Summary Section --}}
                <div
                    class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-900 dark:text-blue-100">
                        <span class="font-semibold">{{ $postBulkEditData['items_count'] ?? 0 }}</span> post
                        procurement items selected
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
                                    Notice of Award
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
                                    PhilGEPS Posting of Award
                                </th>
                                <th
                                    class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-72">
                                    Supplier
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr class="bg-white dark:bg-neutral-700 hover:bg-emerald-50 dark:hover:bg-neutral-800">

                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="postBulkEditData.resolutionAwardNumber"
                                        class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white
                                            {{ $errors->has('postBulkEditData.resolutionAwardNumber') ? 'border-red-500 focus:ring-red-500' : '' }}"
                                        placeholder="RES-YYYY-NNN">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.resolutionAwardDate"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="postBulkEditData.noticeOfAwardNumber"
                                        class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                        placeholder="NOA-YYYY-NNN">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.noticeOfAward"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" step="0.01"
                                        wire:model.defer="postBulkEditData.awardedAmount"
                                        class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                        placeholder="0.00">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" wire:model.defer="postBulkEditData.philgepsNoticeOfAwardNo"
                                        class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                        placeholder="PHL-NOA-YYYY-NNN">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="date" wire:model.defer="postBulkEditData.philgepsPostingOfAward"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                </td>
                                <td class="px-2 py-2">
                                    <select wire:model.defer="postBulkEditData.supplier_id"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                        <option value="">Select Supplier...</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer Actions --}}
                <div
                    class="border-t border-gray-200 dark:border-neutral-700 pt-4 mt-6 flex items-center justify-end gap-2">
                    <button type="button" wire:click="closePostBulkEditModal"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-600 border border-gray-300 dark:border-neutral-500 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="button" wire:click="applyPostBulkEdit"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                        <div wire:loading wire:target="applyPostBulkEdit"
                            class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove
                            wire:target="applyPostBulkEdit">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove wire:target="applyPostBulkEdit">Save</span>
                        <span wire:loading wire:target="applyPostBulkEdit">Saving...</span>
                    </button>
                </div>
            </div>
        @endif
    </x-forms.modal>


</div>
