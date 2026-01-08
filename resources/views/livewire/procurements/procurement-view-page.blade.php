<div>
    <div class="space-y-4">

        {{-- Stepper --}}
        <div
            class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">
            <ul class="flex items-center w-full px-4 py-3">
                {{-- Step 1: Basic Details --}}
                <li class="flex items-center gap-x-2 flex-1 group">
                    <button type="button" wire:click="setStep(1)"
                        class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105 {{ $activeTab == 1 ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                        1
                    </button>
                    <span
                        class="text-sm font-medium whitespace-nowrap {{ $activeTab >= 1 ? 'text-black dark:text-white' : 'text-gray-500' }}">
                        Details
                    </span>
                    <div
                        class="h-px grow transition-colors duration-300 {{ $activeTab > 1 || $this->hasMopData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                    </div>
                </li>

                {{-- Step 2: Mode of Procurement --}}
                <li class="flex items-center gap-x-2 flex-1 group">
                    <button type="button" wire:click="setStep(2)" @if (!$this->hasMopData) disabled @endif
                        class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105 {{ $activeTab == 2 ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600' : ($this->hasMopData ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed') }}">
                        2
                    </button>
                    <span
                        class="text-sm font-medium whitespace-nowrap {{ $activeTab >= 2 || $this->hasMopData ? 'text-black dark:text-white' : 'text-gray-400' }}">
                        Mode of Procurement
                    </span>
                    <div
                        class="h-px grow transition-colors duration-300 {{ $activeTab > 2 || $this->hasPostData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                    </div>
                </li>

                {{-- Step 3: Post Procurement --}}
                <li class="flex items-center gap-x-2 group">
                    <button type="button" wire:click="setStep(3)" @if (!$this->hasPostData) disabled @endif
                        class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition hover:scale-105 {{ $activeTab == 3 ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600' : ($this->hasPostData ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed') }}">
                        3
                    </button>
                    <span
                        class="text-sm font-medium whitespace-nowrap {{ $activeTab >= 3 || $this->hasPostData ? 'text-black dark:text-white' : 'text-gray-400' }}">
                        Post Procurement
                    </span>
                </li>
            </ul>
        </div>

        {{-- Tab Content --}}
        <div>
            @if ($activeTab == 1)
                {{-- Basic Details Tab --}}
                <div class="space-y-4">
                    <div
                        class="bg-white p-4 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 relative">

                        {{-- Document Button - Top Right Corner --}}
                        @can('view_procurement')
                            @if (!empty($procurement->bacApprovedPr?->filepath))
                                <a href="{{ $procurement->bacApprovedPr->filepath }}" target="_blank"
                                    rel="noopener noreferrer"
                                    class="absolute top-0 right-0 inline-flex items-center justify-center p-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white rounded-tr-xl rounded-bl-xl transition-all duration-150 shadow-sm hover:shadow-md group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor"
                                        class="size-5 group-hover:scale-110 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                </a>
                            @endif
                        @endcan

                        {{-- PR Number and Program/Project --}}
                        <div class="grid grid-cols-2 md:grid-cols-10 gap-4">
                            <div class="col-span-1">
                                <x-forms.readonly-input id="pr_number" label="PR No." model="form.pr_number"
                                    :form="$form" :required="true" :colspan="1" textAlign="right"
                                    :viewOnly="true" class="flex-1" />
                            </div>
                            <div class="col-span-9">
                                <x-forms.textarea id="procurement_program_project" label="Procurement Program / Project"
                                    model="form.procurement_program_project" :form="$form" :required="true"
                                    :maxlength="500" :rows="$textareaRows" colspan="col-span-9" :viewOnly="true"
                                    :autoResize="true" />
                            </div>
                        </div>

                        {{-- Per Lot / Per Item Toggle + Table --}}
                        <div class="mt-6 flex flex-col md:flex-row md:items-start md:space-x-6">
                            <div class="flex items-center gap-x-3">
                                <x-forms.prType id="procurement-toggle" model="form.procurement_type" :form="$form"
                                    :viewOnly="true" />
                            </div>

                            @if ($form['procurement_type'] === 'perItem')
                                <div class="mt-4 md:mt-0 w-full md:max-w-3xl">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex items-center gap-x-2">
                                            <button type="button" wire:click="$toggle('showTable')"
                                                class="transition p-1 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-600">
                                                @if (!$showTable)
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-emerald-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-emerald-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </div>
                                    </div>

                                    @if ($showTable)
                                        <div
                                            class="bg-white p-4 rounded-xl shadow border border-gray-200 overflow-x-auto w-full dark:bg-neutral-700">
                                            <h3 class="font-semibold text-gray-700 dark:text-white mb-3">Item List</h3>
                                            @if (data_get($form, 'procurement_type') === 'perItem')
                                                <x-forms.prItems-table :form="$form" model="form.items"
                                                    :page="$page" :per-page="$perPage" :viewOnly="true" />
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Category and Division Details --}}
                    <div
                        class="bg-white p-4 rounded-xl shadow border border-gray-200
                dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="grid grid-cols-2 md:grid-cols-8 gap-4">
                            <x-forms.date id="date_receipt" label="Date Receipt" model="form.date_receipt"
                                :form="$form" :required="false" :viewOnly="true" colspan="col-span-1" />
                            <x-forms.select id="category_id" label="Category" model="form.category_id"
                                :form="$form" :options="$categories" optionValue="id" optionLabel="category"
                                :required="true" :viewOnly="true" colspan="col-span-2" />
                            <x-forms.readonly-input id="category_type" label="Category Type" model="form.category_type"
                                :form="$form" :required="false" :viewOnly="true" :colspan="1" />
                            <x-forms.readonly-input id="rbac_sbac" label="RBAC / SBAC" model="form.rbac_sbac"
                                :form="$form" :required="false" :viewOnly="true" :colspan="1" />
                            <x-forms.input id="dtrack_no" label="DTRACK #" model="form.dtrack_no" :form="$form"
                                :required="true" :viewOnly="true" colspan="col-span-1" />
                            <x-forms.input id="unicode" label="UniCode" model="form.unicode" :form="$form"
                                :viewOnly="true" :required="false" colspan="col-span-2" />
                            <x-forms.select id="divisions_id" label="Division" model="form.divisions_id"
                                :form="$form" :options="$divisions" optionValue="id" optionLabel="divisions"
                                :required="true" :viewOnly="true" colspan="col-span-4" />
                            <x-forms.select id="cluster_committees_id" label="Cluster / Committee"
                                model="form.cluster_committees_id" :form="$form" :options="$clusterCommittees"
                                optionValue="id" optionLabel="clustercommittee" :required="true" :viewOnly="true"
                                colspan="col-span-2" />
                        </div>
                    </div>

                    {{-- Venue Details --}}
                    <div
                        class="bg-white p-4 rounded-xl shadow border border-gray-200
                dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="grid grid-cols-4 gap-4">
                            <x-forms.select id="venue_specific_id" label="Venue|Specific"
                                model="form.venue_specific_id" :form="$form" :options="$venueSpecifics" optionValue="id"
                                optionLabel="name" :required="false" :viewOnly="true" colspan="col-span-2" />
                            <x-forms.select id="venue_province_huc_id" label="Venue|Province/HUC"
                                model="form.venue_province_huc_id" :form="$form" :options="$venueProvinces"
                                optionValue="id" optionLabel="province_huc" :viewOnly="true" :required="false"
                                colspan="col-span-2" />
                            <x-forms.readonly-input id="category_venue" label="Category / Venue"
                                model="form.category_venue" :form="$form" :required="false" :viewOnly="true"
                                colspan="col-span-4" />
                            <div class="flex flex-col col-span-2">
                                <x-forms.approved-ppmp :form="$form" model="form.approved_ppmp"
                                    :viewOnly="true" />
                            </div>
                            <div class="flex flex-col col-span-2">
                                <x-forms.app-updated :form="$form" model="form.app_updated" :viewOnly="true" />
                            </div>
                        </div>
                    </div>

                    {{-- Date Needed and End User --}}
                    <div
                        class="bg-white p-4 rounded-xl shadow border border-gray-200
                dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-3 flex gap-4">
                                <div class="flex-1">
                                    <x-forms.textarea id="immediate_date_needed" label="Immediate Date Needed"
                                        model="form.immediate_date_needed" :form="$form" :viewOnly="true"
                                        :maxlength="500" rows="4" :autoResize="true" />
                                </div>
                                <div class="flex-1">
                                    <x-forms.textarea id="date_needed" label="Date Needed" model="form.date_needed"
                                        :form="$form" :required="false" :maxlength="500" :viewOnly="true"
                                        rows="4" :autoResize="true" />
                                </div>
                            </div>
                            <div class="col-span-1 flex flex-col gap-4">
                                <div>
                                    <x-forms.select id="end_users_id" label="PMO/End-User" model="form.end_users_id"
                                        :form="$form" :options="$endUsers" optionValue="id" optionLabel="endusers"
                                        :viewOnly="true" :required="false" />
                                </div>
                                <div>
                                    <x-forms.early-procurement model="form.early_procurement" :form="$form"
                                        :viewOnly="true" :clickable="false" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fund Source and ABC --}}
                    <div class="flex justify-center gap-4">
                        <div
                            class="bg-white p-4 rounded-xl shadow border border-gray-200
                dark:bg-neutral-700 dark:border-neutral-700">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1">
                                    <x-forms.select id="fund_source_id" label="Source of Funds"
                                        model="form.fund_source_id" :form="$form" :options="$fundSources"
                                        optionValue="id" optionLabel="fundsources" :viewOnly="true"
                                        :required="true" />
                                </div>
                                <div class="col-span-1">
                                    <x-forms.input id="expense_class" label="Expense Class"
                                        model="form.expense_class" :form="$form" :required="false"
                                        :viewOnly="true" textAlign="right" />
                                </div>
                                <x-forms.currency-input id="abc" label="ABC Amount" model="form.abc"
                                    :form="$form" :required="true" colspan="col-span-1" :viewOnly="true" />
                                <div class="col-span-1">
                                    <x-forms.abc50k id="abc_50k" label="ABC ⇔ 50k" model="form.abc_50k"
                                        :viewOnly="true" :form="$form" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            @if ($activeTab == 2)
                {{-- Mode of Procurement Tab --}}
                <div
                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                    @if (!empty($form['mop_items']))
                        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                            <table class="w-full text-xs min-w-max">
                                <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                    <tr>
                                        <th
                                            class="px-2 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-10">
                                            #</th>
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
                                    @foreach ($form['mop_items'] as $item)
                                        <tr class="hover:bg-emerald-100 dark:hover:bg-neutral-800">
                                            <td class="px-2 py-2 text-center text-gray-700 dark:text-gray-200">
                                                {{ $item['mode_order'] }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['mode_of_procurement_name'] }}</td>
                                            <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                {{ $item['bidding_number'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['ib_number'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['pre_proc_conference'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['ads_post_ib'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['pre_bid_conf'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['eligibility_check'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['sub_open_bids'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['bidding_date'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                @if ($item['bidding_result'])
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $item['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                        {{ $item['bidding_result'] }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                {{ $item['rfq_no'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['canvass_date'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['date_returned_of_canvass'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                                {{ $item['abstract_of_canvass_date'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                                {{ $item['resolution_number'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No mode of procurement data
                                available</p>
                        </div>
                    @endif
                </div>
            @endif

            @if ($activeTab == 3)
                {{-- Post Procurement Tab --}}
                <div
                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                    @if ($this->hasPostData)
                        <div class="overflow-x-auto overflow-y-auto">
                            <table class="w-full text-xs min-w-max">
                                <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">
                                    <tr>
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
                                    <tr class="bg-white dark:bg-neutral-800">
                                        <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                            {{ $resolutionNumber ?? '-' }}</td>
                                        <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                            {{ $bidEvaluationDate ?? '-' }}</td>
                                        <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                            {{ $postQualDate ?? '-' }}</td>
                                        <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                            {{ $recommendingForAward ?? '-' }}</td>
                                        <td class="px-2 py-2 text-gray-700 dark:text-gray-200">
                                            {{ $noticeOfAward ?? '-' }}</td>
                                        <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-200">
                                            @if ($awardedAmount)
                                                ₱{{ number_format($awardedAmount, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No post-procurement data available
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
