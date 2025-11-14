<div class="space-y-2">
    <div class="relative bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
        <div
            class="absolute top-0 left-0 bg-emerald-600 text-white text-xs font-semibold px-2 py-0.5 rounded-tl-xl rounded-br-xl">
            {{ $procurementType === 'perLot' ? 'Per Lot' : 'Per Item' }}
        </div>

        <ul class="flex items-center w-full max-w-7xl pt-2 p-2 bg-white dark:bg-neutral-700 dark:border-neutral-700 mx-auto"
            data-hs-stepper='{"isCompleted": true}'>

            <li class="flex items-center gap-x-2 flex-1 group"
                data-hs-stepper-nav-item='{"index": 1, "isCompleted": {{ $activeTab > 1 || $mopGroupId ? 'true' : 'false' }} }'>
                <button type="button" wire:click="setStep(1)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition
       hover:scale-105
       {{ $activeTab == 1
           ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
           : ($activeTab > 1 || $mopGroupId
               ? 'bg-emerald-600 text-white hover:bg-emerald-700'
               : 'bg-gray-100 text-gray-800 hover:bg-gray-200') }}">
                    1
                </button>
                <span class="text-sm font-medium text-black dark:text-white whitespace-nowrap">
                    Details
                </span>
                <div
                    class="h-px grow transition-colors duration-300
            {{ $activeTab > 1 || $mopGroupId ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                </div>
            </li>

            <li class="flex items-center gap-x-2 flex-1 group"
                data-hs-stepper-nav-item='{"index": 2, "isCompleted": {{ $activeTab > 2 || $mopGroupId ? 'true' : 'false' }} }'>
                <button type="button" wire:click="setStep(2)" @if (!$mopGroupId) disabled @endif
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition
       hover:scale-105
       {{ $activeTab == 2
           ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
           : ($activeTab > 2 || $mopGroupId
               ? 'bg-emerald-600 text-white hover:bg-emerald-700'
               : 'bg-gray-100 text-neutral-400 cursor-not-allowed') }}">
                    2
                </button>
                <span
                    class="text-sm font-medium whitespace-nowrap
            {{ $activeTab > 2 || $mopGroupId ? 'text-gray-800 dark:text-white' : 'text-neutral-400 dark:text-neutral-500' }}">
                    Mode of Procurement
                </span>

                <!-- changed: include || $mopGroupId so this line behaves like the one after tab 1 -->
                <div
                    class="h-px grow transition-colors duration-300
            {{ $activeTab > 2 || $mopGroupId ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                </div>
            </li>

            <li class="flex items-center gap-x-2 group"
                data-hs-stepper-nav-item='{"index": 3, "isCompleted": {{ $activeTab > 3 || $mopGroupId ? 'true' : 'false' }} }'>
                <button type="button" wire:click="setStep(3)" @if (!$mopGroupId) disabled @endif
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition
       hover:scale-105
       {{ $activeTab == 3
           ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
           : ($activeTab > 3 || $mopGroupId
               ? 'bg-emerald-600 text-white hover:bg-emerald-700'
               : 'bg-gray-100 text-neutral-400 cursor-not-allowed') }}">
                    3
                </button>
                <span
                    class="text-sm font-medium whitespace-nowrap
            {{ $activeTab > 3 || $mopGroupId ? 'text-gray-800 dark:text-white' : 'text-neutral-400 dark:text-neutral-500' }}">
                    Post
                </span>

                <!-- invisible spacer to keep spacing identical -->
                <div class="h-px grow invisible"></div>
            </li>
        </ul>

    </div>
    {{-- <hr class=" border-gray-200 dark:border-neutral-600"> --}}

    <div>
        @if ($activeTab == 1)
            <div>
                <button wire:click="openSelectionModal"
                    class="px-2 py-1 inline-flex items-center text-sm font-medium rounded-lg border border-transparent bg-emerald-600 text-white hover:bg-emerald-700">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Select
                </button>

                @if (!empty($selectedProcurements))
                    <div class="mt-2 space-y-6">
                        <div
                            class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-700 dark:border-neutral-700">

                            <h3
                                class="text-sm font-semibold text-gray-800 dark:text-white bg-white dark:bg-neutral-700 p-2 border-b border-gray-200 dark:border-neutral-700">
                                @if ($procurementType === 'perLot')
                                    Selected PR
                                @else
                                    Selected Items
                                @endif
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">

                                    <thead class="sticky bg-gray-200 dark:bg-neutral-800">
                                        <tr>
                                            <th
                                                class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-20">
                                                PR No.</th>
                                            <th
                                                class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                                @if ($procurementType === 'perLot')
                                                    Procurement Program / Project
                                                @else
                                                    Item Description
                                                @endif
                                            </th>
                                            <th
                                                class="px-2 py-1 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                                Amount</th>
                                            <th
                                                class="px-2 py-1 text-center font-semibold text-black dark:text-white w-12 border-b border-gray-300 dark:border-neutral-600">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                        @foreach ($this->SelectedPR as $pr)
                                            <tr wire:key="selected-pr-{{ $pr['unique_key'] ?? $pr['id'] }}">
                                                <td
                                                    class="px-2 py-1 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                                    {{ $pr['pr_number'] }}
                                                </td>
                                                <td class="px-2 py-1 text-gray-900 dark:text-gray-100">
                                                    {{ $pr['description'] ?? $pr['procurement_program_project'] }}
                                                </td>
                                                <td
                                                    class="px-2 py-1 text-right text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                                    <span class="text-gray-500">₱</span>
                                                    <span>{{ number_format($pr['amount'] ?? ($pr['abc'] ?? 0), 2) }}</span>
                                                </td>
                                                <td class="px-2 py-1 text-center">
                                                    <button
                                                        wire:click.prevent="removeSelectedPR('{{ $pr['unique_key'] }}')"
                                                        class="font-medium text-red-500 hover:text-red-700 text-base">×</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($this->SelectedPR->isNotEmpty() && $this->SelectedPR->hasPages())
                                <div
                                    class="flex-shrink-0 border-t border-gray-200 dark:border-neutral-700 px-4 py-2 bg-white dark:bg-neutral-900 grid grid-cols-3 items-center">

                                    <div class="text-xs text-gray-500 text-left">
                                        Showing {{ $this->SelectedPR->firstItem() }} to
                                        {{ $this->SelectedPR->lastItem() }} of
                                        {{ $this->SelectedPR->total() }} items
                                    </div>

                                    <nav role="navigation" aria-label="Pagination Navigation"
                                        class="flex justify-center items-center gap-3">

                                        <button wire:click.prevent="previousCustomPage('selectedPRPage')"
                                            @disabled($this->SelectedPR->onFirstPage())
                                            class="inline-flex items-center justify-center w-5 h-5 text-gray-600 hover:text-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:text-emerald-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd"
                                                    d="M10.72 11.47a.75.75 0 0 0 0 1.06l7.5 7.5a.75.75 0 1 0 1.06-1.06L12.31 12l6.97-6.97a.75.75 0 0 0-1.06-1.06l-7.5 7.5Z"
                                                    clip-rule="evenodd" />
                                                <path fill-rule="evenodd"
                                                    d="M4.72 11.47a.75.75 0 0 0 0 1.06l7.5 7.5a.75.75 0 1 0 1.06-1.06L6.31 12l6.97-6.97a.75.75 0 0 0-1.06-1.06l-7.5 7.5Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $this->SelectedPR->currentPage() }} of
                                            {{ $this->SelectedPR->lastPage() }}
                                        </span>

                                        <button wire:click.prevent="nextCustomPage('selectedPRPage')"
                                            @disabled(!$this->SelectedPR->hasMorePages())
                                            class="inline-flex items-center justify-center w-5 h-5 text-gray-600 hover:text-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:text-emerald-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd"
                                                    d="M13.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L11.69 12 4.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                                                    clip-rule="evenodd" />
                                                <path fill-rule="evenodd"
                                                    d="M19.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06L17.69 12l-6.97-6.97a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </nav>
                                    <div></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if ($activeTab == 2)
            {{-- Add Mode Button --}}
            @php
                $hasDefaultMode = collect($form['modes'])->contains('mode_of_procurement_id', 1);
                $hasPendingOrEmptySchedule = collect($form['modes'])->contains(function ($mode) {
                    $schedules = collect($mode['bid_schedules'] ?? []);
                    return $schedules->isEmpty() ||
                        $schedules->contains(fn($s) => empty($s['bidding_result']) && empty($s['ntf_bidding_result']));
                });
            @endphp

            @if (!$viewOnlyTab2 && $this->showAddModeButton)
                <div class="flex justify-center p-2">
                    <button type="button" wire:click.prevent="addMode"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl font-medium shadow">
                        + Mode
                    </button>
                </div>
            @endif

            {{-- Loop through Modes --}}
            <div class="flex flex-col items-center p-2">
                @php
                    $modes = collect($form['modes'] ?? []);

                    if ($modes->count() === 1) {
                        $displayModes = $modes;
                    } else {
                        $displayModes = $modes->reject(fn($mode) => ($mode['mode_of_procurement_id'] ?? null) == 1);
                    }
                @endphp

                @foreach ($displayModes as $modeIndex => $mode)
                    <div class="flex justify-center p-2">
                        <div
                            class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 inline-block dark:bg-neutral-700 dark:border-neutral-700">

                            <x-forms.select id="mode_of_procurement_{{ $modeIndex }}" label="Mode of Procurement"
                                model="form.modes.{{ $modeIndex }}.mode_of_procurement_id" :form="$form"
                                :options="$modeOfProcurements" optionValue="id" optionLabel="modeofprocurements" :required="false"
                                wireModifier="defer" />
                        </div>
                    </div>

                    @if (!in_array($mode['mode_of_procurement_id'], [null, '', 1]))
                        @php
                            // Get the mode UID - use database UID if available
                            $modeUid = $mode['uid'] ?? null;

                            // Get bid schedules
                            $bidSchedules = $mode['bid_schedules'] ?? [];

                            // If empty and non-default mode, create display placeholder
                            if (empty($bidSchedules)) {
                                $bidSchedules = [
                                    [
                                        'bidding_number' => 1,
                                        'ib_number' => '',
                                        'pre_proc_conference' => null,
                                        'ads_post_ib' => null,
                                        'pre_bid_conf' => null,
                                        'eligibility_check' => null,
                                        'sub_open_bids' => null,
                                        'bidding_date' => null,
                                        'bidding_result' => '',
                                        'ntf_no' => '',
                                        'ntf_bidding_date' => null,
                                        'ntf_bidding_result' => '',
                                        'rfq_no' => '',
                                        'canvass_date' => null,
                                        'date_returned_of_canvass' => null,
                                        'abstract_of_canvass_date' => null,
                                        'resolution_number' => '',
                                    ],
                                ];
                            }
                        @endphp

                        @if ($this->canShowAddBidButton($modeUid, $mode['mode_of_procurement_id']))
                            <div class="flex justify-center p-2">
                                <button type="button" wire:click.prevent="addBidSchedule({{ $modeIndex }})"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-xl font-medium shadow">
                                    + Bid
                                </button>
                            </div>
                        @endif

                        <div class="space-y-6 mt-2">
                            @foreach ($bidSchedules as $bidIndex => $schedule)
                                <div
                                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 mb-6">
                                    <div class="grid grid-cols-9 gap-4">
                                        @if ($mode['mode_of_procurement_id'] != 5)
                                            <div class="w-full md:w-20">
                                                <x-forms.input
                                                    id="bidding_number_{{ $modeIndex }}_{{ $bidIndex }}"
                                                    label="Bidding #"
                                                    model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.bidding_number"
                                                    :form="$form" textAlign="right" maxlength="2"
                                                    :required="true" :disabled="true" :readonly="true" />
                                            </div>

                                            <x-forms.input id="ib_number_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="IB No."
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.ib_number"
                                                :form="$form" :required="true" textAlign="right" />

                                            <x-forms.date
                                                id="pre_proc_conference_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Pre-Proc Conference"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.pre_proc_conference"
                                                :form="$form" />

                                            <x-forms.date id="ads_post_ib_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Ads/Post IB"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.ads_post_ib"
                                                :form="$form" />

                                            <x-forms.date id="pre_bid_conf_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Pre-Bid Conference"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.pre_bid_conf"
                                                :form="$form" />

                                            <x-forms.date
                                                id="eligibility_check_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Eligibility Check"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.eligibility_check"
                                                :form="$form" />

                                            <x-forms.date id="sub_open_bids_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Sub/Open of Bids"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.sub_open_bids"
                                                :form="$form" />
                                        @endif

                                        @if (!in_array($mode['mode_of_procurement_id'], [4, 5]))
                                            <x-forms.date id="bidding_date_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Bidding Date"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.bidding_date"
                                                :form="$form" />

                                            <x-forms.select
                                                id="bidding_result_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Bidding Result" :options="[
                                                    'SUCCESSFUL' => 'SUCCESSFUL',
                                                    'UNSUCCESSFUL' => 'UNSUCCESSFUL',
                                                ]"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.bidding_result"
                                                :form="$form" wireModifier="defer" />
                                        @endif

                                        @if ($mode['mode_of_procurement_id'] == 4)
                                            <x-forms.date
                                                id="ntf_bidding_date_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="NTF Bidding Date"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.ntf_bidding_date"
                                                :form="$form" />

                                            <x-forms.select
                                                id="ntf_bidding_result_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Bidding Result" :options="[
                                                    'SUCCESSFUL' => 'SUCCESSFUL',
                                                    'UNSUCCESSFUL' => 'UNSUCCESSFUL',
                                                ]"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.ntf_bidding_result"
                                                :form="$form" wireModifier="defer" />

                                            <div class="col-span-2"></div>

                                            <x-forms.input id="ntf_no_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="NTF No."
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.ntf_no"
                                                :form="$form" textAlign="right" />

                                            <x-forms.input id="rfq_no_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="RFQ No."
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.rfq_no"
                                                :form="$form" textAlign="right" />

                                            <x-forms.date id="canvass_date_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Canvass Date"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.canvass_date"
                                                :form="$form" />

                                            <x-forms.date
                                                id="date_returned_of_canvass_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Returned of Canvass"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.date_returned_of_canvass"
                                                :form="$form" />

                                            <x-forms.date
                                                id="abstract_of_canvass_date_{{ $modeIndex }}_{{ $bidIndex }}"
                                                label="Abstract of Canvass"
                                                model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.abstract_of_canvass_date"
                                                :form="$form" />
                                        @endif

                                        @if ($mode['mode_of_procurement_id'] == 5)
                                            <div class="col-span-9 flex flex-wrap justify-center gap-4">
                                                <x-forms.input
                                                    id="resolution_number_{{ $modeIndex }}_{{ $bidIndex }}"
                                                    label="Resolution Number"
                                                    model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.resolution_number"
                                                    :form="$form" :required="true" textAlign="center" />

                                                <x-forms.input id="rfq_no_{{ $modeIndex }}_{{ $bidIndex }}"
                                                    label="RFQ No."
                                                    model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.rfq_no"
                                                    :form="$form" :required="true" textAlign="center" />

                                                <x-forms.date
                                                    id="canvass_date_{{ $modeIndex }}_{{ $bidIndex }}"
                                                    label="Canvass Date"
                                                    model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.canvass_date"
                                                    :form="$form" :required="true" class="text-center" />

                                                <x-forms.date
                                                    id="date_returned_of_canvass_{{ $modeIndex }}_{{ $bidIndex }}"
                                                    label="Returned of Canvass"
                                                    model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.date_returned_of_canvass"
                                                    :form="$form" :required="true" class="text-center" />

                                                <x-forms.date
                                                    id="abstract_of_canvass_date_{{ $modeIndex }}_{{ $bidIndex }}"
                                                    label="Abstract of Canvass"
                                                    model="form.modes.{{ $modeIndex }}.bid_schedules.{{ $bidIndex }}.abstract_of_canvass_date"
                                                    :form="$form" :required="true" class="text-center" />
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach

            </div>
        @endif

        @if ($activeTab == 3)
            <div class="flex flex-col items-center gap-6 p-6">

                {{-- Award Information Block --}}
                <div
                    class="w-full max-w-7xl bg-white p-6 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                    <div class="grid grid-cols-6 gap-4">
                        {{-- Resolution Number --}}
                        <x-forms.input id="resolutionNumber" label="Resolution Number" model="form.resolutionNumber"
                            :form="$form" :required="false" :viewOnly="$viewOnlyTab3" colspan="col-span-1" />

                        {{-- Bid Evaluation Date --}}
                        <x-forms.date id="bidEvaluationDate" label="Bid Evaluation Date"
                            model="form.bidEvaluationDate" :form="$form" :viewOnly="$viewOnlyTab3" :required="false"
                            colspan="col-span-1" />

                        {{-- Post Qual Date --}}
                        <x-forms.date id="postQualDate" label="Post Qual Date" model="form.postQualDate"
                            :form="$form" :viewOnly="$viewOnlyTab3" :required="false" colspan="col-span-1" />

                        {{-- Recommending for Award --}}
                        <x-forms.date id="recommendingForAward" label="Recommending for Award"
                            model="form.recommendingForAward" :form="$form" :viewOnly="$viewOnlyTab3" :required="false"
                            colspan="col-span-1" />

                        {{-- Notice of Award --}}
                        <x-forms.date id="noticeOfAward" label="Notice of Award" model="form.noticeOfAward"
                            :form="$form" :viewOnly="$viewOnlyTab3" :required="false" colspan="col-span-1" />

                        {{-- Awarded Amount --}}
                        <x-forms.currency-input id="awardedAmount" label="Awarded Amount" model="form.awardedAmount"
                            :form="$form" :required="false" :viewOnly="$viewOnlyTab3" colspan="col-span-1" />
                    </div>
                </div>

                {{-- PhilGEPS & Supplier Information Block --}}
                <div
                    class="w-full max-w-7xl bg-white p-6 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                    <div class="grid grid-cols-6 gap-4">
                        {{-- PhilGEPS Reference No --}}
                        <x-forms.input id="philgepsReferenceNo" label="PhilGEPS Reference #"
                            model="form.philgepsReferenceNo" :form="$form" :required="false" :viewOnly="$viewOnlyTab3"
                            colspan="col-span-1" />

                        {{-- Award Notice Number --}}
                        <x-forms.input id="awardNoticeNumber" label="Award Notice Number"
                            model="form.awardNoticeNumber" :form="$form" :required="false" :viewOnly="$viewOnlyTab3"
                            colspan="col-span-1" />

                        {{-- Posting of Award on PhilGEPS --}}
                        <x-forms.date id="dateOfPostingOfAwardOnPhilGEPS" label="Posting of Award|PhilGEPS"
                            model="form.dateOfPostingOfAwardOnPhilGEPS" :form="$form" :viewOnly="$viewOnlyTab3"
                            :required="false" colspan="col-span-1" />

                        {{-- Supplier --}}
                        <x-forms.select id="supplier_id" label="Supplier" model="form.supplier_id" :form="$form"
                            :options="$suppliers" optionValue="id" optionLabel="name" :required="false"
                            :viewOnly="$viewOnlyTab3" colspan="col-span-3" />
                    </div>
                </div>


            </div>
        @endif
    </div>

    <livewire:mode-of-procurement.select-modal :procurementType="$procurementType" :existing-lot-ids="$existingLotIds" :existing-item-ids="$existingItemIds" />

    <div
        class="fixed bottom-5 right-0 left-0 lg:ml-[13.75rem] flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-x-2">
            <a href="{{ route('mode-of-procurement.index') }}" wire:navigate
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
