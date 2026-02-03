<div class="space-y-4">
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
                    class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500' }}">
                    Bulk Edit Mode of Procurement
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
            <div class="flex flex-col">

                @if ($disableInputs)
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
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Fields Locked</p>
                                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                                    All selected procurements have SUCCESSFUL bidding results with post-procurement
                                    data. Editing requires the 'Edit Mode of Procurement' permission.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($disableModeSelect && !$disableInputs)
                    <div
                        class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Mode Selection Locked
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    Cannot change Mode of Procurement because selected items already have schedule data.
                                    You can edit existing fields only.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div
                    class="bg-white rounded-xl p-2 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                    <!-- Bulk Edit Section Header -->
                    <div class="px-2 py-3 bg-emerald-50 dark:bg-emerald-900/20 border-b-2 border-emerald-500 mb-2">
                        <h3 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                            📝 Bulk Edit Form - Changes will apply to all selected PRs
                        </h3>
                    </div>

                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
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
                                <tr class="bg-white dark:bg-neutral-700">
                                    <!-- Actions -->
                                    <td class="px-2 py-2 align-middle">
                                        <div class="flex items-center justify-center gap-1">
                                            @if($showAddModeButton)
                                                <button wire:click.prevent="addItem"
                                                    class="inline-flex items-center justify-center w-7 h-7 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                                    title="Add Mode"
                                                    @disabled($disableInputs)>
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
                                            class="w-full max-w-xs px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                            @disabled($disableInputs || $disableModeSelect || $showAddModeButton)>
                                            <option value="">Select Mode...</option>
                                            @foreach ($modeOfProcurements as $mode)
                                                <option value="{{ $mode->id }}"
                                                    title="{{ $mode->modeofprocurements }}">
                                                    {{ Str::limit($mode->modeofprocurements, 35, '...') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    @if ($showBiddingFields)
                                        <!-- Bidding # -->
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model="bulkEdit.bidding_number" maxlength="2"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- IB No. -->
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model="bulkEdit.ib_number"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                placeholder="IB-2025-002" @disabled($disableInputs)>
                                        </td>
                                        <!-- PhilGEPS Posting Ref # -->
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model="bulkEdit.philgeps_posting_ref_no"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                placeholder="PHL-2025-001" @disabled($disableInputs)>
                                        </td>
                                        <!-- Ads/Post IB -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.ads_post_ib"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Pre-Proc Conference -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.pre_proc_conference"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- List of Invited Observers -->
                                        <!-- List of Invited Observers -->
                                        <td class="px-2 py-2">
                                            <textarea wire:model="bulkEdit.list_invited_observers" rows="1"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                placeholder="List observers..." @disabled($disableInputs)></textarea>
                                        </td>
                                        <!-- Observers (Pre-Bid) -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.obsrvr_prebid_conf"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Observers (Eligibility) -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.obsrvr_eligibility"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Observers (Sub/Open) -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.obsrvr_sub_open_of_bid"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Observers (Bid) -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.obsrvr_bid"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Observers (Post Qual) -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.obsrvr_post_qual"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Pre-Bid Conference -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.pre_bid_conf"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Eligibility Check -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.eligibility_check"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Sub/Open of Bids -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.sub_open_bids"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Bid Evaluation Date -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.bid_evaluation_date"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Post Qualification Date -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.post_qualification_date"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Bidding Result -->
                                        <td class="px-2 py-2">
                                            <select wire:model="bulkEdit.bidding_result"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                                <option value="">Select...</option>
                                                <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                            </select>
                                        </td>
                                        <!-- Resolution # (MOP) -->
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model="bulkEdit.resolution_number_mop"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                placeholder="RES-2025-001" @disabled($disableInputs)>
                                        </td>

                                    @elseif ($showSvpFields)
                                        {{-- Empty cells for bidding-only fields (17 fields) --}}
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

                                        <!-- Resolution # (MOP) -->
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model="bulkEdit.resolution_number_mop"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                placeholder="RES-2025-001" @disabled($disableInputs)>
                                        </td>
                                        <!-- RFQ No. -->
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model="bulkEdit.rfq_no"
                                                class="w-full px-2 py-1 text-xs text-right border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                placeholder="RFQ-2025-001" @disabled($disableInputs)>
                                        </td>
                                        <!-- Canvass Date -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.canvass_date"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Returned of Canvass -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.date_returned_of_canvass"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                        <!-- Abstract of Canvass -->
                                        <td class="px-2 py-2">
                                            <input type="date" wire:model="bulkEdit.abstract_of_canvass_date"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($disableInputs)>
                                        </td>
                                    
                                    @else
                                        {{-- MODE 1 OR NO MODE SELECTED - ALL EMPTY (22 fields total) --}}
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
                            </tbody>
                        </table>

                        <!-- Divider and PR List Header -->
                        <div class="my-4">
                            <div class="h-px bg-gray-300 dark:bg-neutral-600"></div>
                            <div class="px-2 py-3 bg-blue-50 dark:bg-blue-900/20 border-b-2 border-blue-500 mt-4 mb-2">
                                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                                    📋 Selected PRs - Current Mode of Procurement Data ({{ $abcThresholdCategory }})
                                </h3>
                            </div>
                        </div>

                        <!-- PR List Table -->
                        <table class="w-full text-xs min-w-max">
                            <thead class="bg-gray-200 dark:bg-neutral-800">
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
                                                    <button type="button"
                                                        wire:click="toggleHistory('{{ $itemKey }}')"
                                                        class="p-1 text-xs rounded hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors
                                                    {{ $showHistory && $historyForKey === $itemKey ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                                                        title="Toggle History">
                                                        @if ($showHistory && $historyForKey === $itemKey)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
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
                                                <span
                                                    class="text-xs text-gray-400 dark:text-gray-500 italic">History</span>
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
        @endif

        @if ($activeTab == 2)
            <div class="flex flex-col gap-2 pt-2">
                <div
                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Post Procurement section will be available here</p>
                    </div>
                </div>
            </div>
        @endif
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
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>
</div>
