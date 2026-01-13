<div>
    <div class="space-y-4">
        <div
            class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-md text-sm font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                PR #{{ $form['pr_number'] ?? 'N/A' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Procurement Program / Project</p>
                        <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                            {{ $form['procurement_program_project'] ?? 'No project description available' }}
                        </h1>
                    </div>
                    @can('view_procurement')
                        @if (!empty($procurement->bacApprovedPr?->filepath))
                            <a href="{{ $procurement->bacApprovedPr->filepath }}" target="_blank" rel="noopener noreferrer"
                                class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 group-hover:scale-110 transition-transform">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <div
            class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">
            <ul class="flex items-center w-full px-4 py-3">
                {{-- Step 1: Basic Details --}}
                <li class="flex items-center flex-1">
                    <button type="button" wire:click="setStep(1)"
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 hover:scale-105 shadow-md {{ $activeTab == 1 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400' : ($this->hasMopData ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-emerald-600 text-white') }}">
                        1
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500' }}">
                        Details
                    </span>
                    <div
                        class="h-px flex-1 mx-3 transition-all duration-300 {{ $activeTab > 1 || $this->hasMopData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-600' }}">
                    </div>
                </li>

                {{-- Step 2: Mode of Procurement --}}
                <li class="flex items-center flex-1">
                    <button type="button" wire:click="setStep(2)" @if (!$this->hasMopData) disabled @endif
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 shadow-md {{ $activeTab == 2 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400 hover:scale-105' : ($this->hasMopData ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-105' : 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-neutral-600') }}">
                        2
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 2 || $this->hasMopData ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                        Mode of Procurement
                    </span>
                    <div
                        class="h-px flex-1 mx-3 transition-all duration-300 {{ $activeTab > 2 || $this->hasPostData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-600' }}">
                    </div>
                </li>

                {{-- Step 3: Post Procurement --}}
                <li class="flex items-center">
                    <button type="button" wire:click="setStep(3)" @if (!$this->hasPostData) disabled @endif
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 shadow-md {{ $activeTab == 3 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400 hover:scale-105' : ($this->hasPostData ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-105' : 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-neutral-600') }}">
                        3
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 3 || $this->hasPostData ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
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

                    @if ($form['procurement_type'] === 'perItem')
                        <div
                            class="bg-white p-4 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                            {{-- Header with Toggle --}}
                            <div class="flex justify-between items-center mb-4">
                                <h3
                                    class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                    Item List
                                </h3>
                                <button type="button" wire:click="$toggle('showTable')"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $showTable ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-600 dark:text-gray-300 dark:hover:bg-neutral-500' }}">
                                    @if (!$showTable)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                        <span>Show</span>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                        <span>Hide</span>
                                    @endif
                                </button>
                            </div>

                            {{-- Table Section --}}
                            @if ($showTable)
                                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-neutral-600">
                                    <x-forms.prItems-table :form="$form" model="form.items" :page="$page"
                                        :per-page="$perPage" :viewOnly="true" />
                                </div>

                                {{-- Pagination Controls --}}
                                @php
                                    $totalItems = count($form['items'] ?? []);
                                    $currentPage = $page ?? 1;
                                    $itemsPerPage = $perPage ?? 10;
                                    $totalPages = ceil($totalItems / $itemsPerPage);
                                @endphp

                                @if ($totalPages > 1)
                                    <div
                                        class="mt-4 flex items-center justify-between flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-neutral-600">
                                        {{-- Items per page --}}
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600 dark:text-gray-400">Show:</label>
                                            <select wire:model.live="perPage"
                                                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                                <option value="{{ $totalItems }}">All</option>
                                            </select>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">items per page</span>
                                        </div>

                                        {{-- Page info --}}
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Showing {{ $totalItems > 0 ? ($currentPage - 1) * $itemsPerPage + 1 : 0 }}
                                            to
                                            {{ min($currentPage * $itemsPerPage, $totalItems) }} of
                                            {{ $totalItems }} items
                                        </div>

                                        {{-- Pagination buttons --}}
                                        <div class="flex items-center gap-2">
                                            {{-- Previous Button --}}
                                            <button type="button"
                                                wire:click="$set('page', {{ max(1, $currentPage - 1) }})"
                                                @if ($currentPage <= 1) disabled @endif
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                                {{ $currentPage <= 1
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                                    : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                                Previous
                                            </button>

                                            {{-- Page Numbers --}}
                                            <div class="flex items-center gap-1">
                                                @php
                                                    $startPage = max(1, $currentPage - 2);
                                                    $endPage = min($totalPages, $currentPage + 2);
                                                @endphp

                                                @if ($startPage > 1)
                                                    <button type="button" wire:click="$set('page', 1)"
                                                        class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPage == 1
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                        1
                                                    </button>
                                                    @if ($startPage > 2)
                                                        <span class="text-gray-500 px-1">...</span>
                                                    @endif
                                                @endif

                                                @for ($i = $startPage; $i <= $endPage; $i++)
                                                    <button type="button"
                                                        wire:click="$set('page', {{ $i }})"
                                                        class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPage == $i
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                        {{ $i }}
                                                    </button>
                                                @endfor

                                                @if ($endPage < $totalPages)
                                                    @if ($endPage < $totalPages - 1)
                                                        <span class="text-gray-500 px-1">...</span>
                                                    @endif
                                                    <button type="button"
                                                        wire:click="$set('page', {{ $totalPages }})"
                                                        class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPage == $totalPages
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                        {{ $totalPages }}
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Next Button --}}
                                            <button type="button"
                                                wire:click="$set('page', {{ min($totalPages, $currentPage + 1) }})"
                                                @if ($currentPage >= $totalPages) disabled @endif
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                                {{ $currentPage >= $totalPages
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                                    : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                                Next
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

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
                            <x-forms.input id="category_type" label="Category Type" model="form.category_type"
                                :form="$form" :required="false" :viewOnly="true" :colspan="1" />
                            <x-forms.input id="rbac_sbac" label="RBAC / SBAC" model="form.rbac_sbac"
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
                            <x-forms.input id="category_venue" label="Category / Venue" model="form.category_venue"
                                :form="$form" :required="false" :viewOnly="true" colspan="col-span-4" />
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
                <div class="space-y-4">
                    @if ($form['procurement_type'] === 'perLot')
                        {{-- PER LOT DISPLAY with History Toggle --}}
                        @php
                            $filteredItems = collect($form['mop_items'] ?? [])
                                ->filter(function ($item) {
                                    return $item['uid'] !== 'MOP-1-1';
                                })
                                ->sortBy('mode_order')
                                ->values()
                                ->reverse()
                                ->values();

                            $hasAnyHistory = $filteredItems->count() > 1;
                        @endphp

                        @if ($filteredItems->isNotEmpty())
                            @php
                                $currentMode = $filteredItems->first();
                                $historyModes = $filteredItems->slice(1);
                            @endphp

                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                {{-- Header with Mode and Order --}}
                                <div
                                    class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-sm">
                                                {{ $currentMode['mode_order'] }}
                                            </span>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $currentMode['mode_of_procurement_name'] }}
                                            </h3>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if ($currentMode['bidding_result'])
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentMode['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ $currentMode['bidding_result'] }}
                                            </span>
                                        @endif

                                        {{-- Toggle Button for History --}}
                                        @if ($hasAnyHistory)
                                            <button type="button" wire:click="toggleMopSection('lot_history')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-600 transition-colors"
                                                title="{{ $mopToggles['lot_history'] ?? false ? 'Hide History' : 'Show History' }}">
                                                @if ($mopToggles['lot_history'] ?? false)
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-emerald-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-emerald-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Current Mode Data --}}
                                @php
                                    $modeId = $currentMode['mode_of_procurement_id'];
                                    $hasBiddingData =
                                        !in_array($modeId, [
                                            1,
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
                                        ]) &&
                                        ($currentMode['bidding_number'] ||
                                            $currentMode['ib_number'] ||
                                            $currentMode['pre_proc_conference'] ||
                                            $currentMode['ads_post_ib'] ||
                                            $currentMode['pre_bid_conf'] ||
                                            $currentMode['eligibility_check'] ||
                                            $currentMode['sub_open_bids'] ||
                                            $currentMode['bidding_date']);

                                    $hasSvpData =
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
                                        ]) &&
                                        ($currentMode['rfq_no'] ||
                                            $currentMode['canvass_date'] ||
                                            $currentMode['date_returned_of_canvass'] ||
                                            $currentMode['abstract_of_canvass_date'] ||
                                            $currentMode['resolution_number']);
                                @endphp

                                {{-- Mode Information --}}
                                @if ($hasBiddingData)
                                    <div class="mb-6">
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Mode Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            @if ($currentMode['bidding_number'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bidding #
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['bidding_number'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['ib_number'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">IB No.</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['ib_number'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['pre_proc_conference'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pre-Proc
                                                        Conference</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['pre_proc_conference'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['ads_post_ib'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ads/Post
                                                        IB</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['ads_post_ib'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['pre_bid_conf'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pre-Bid
                                                        Conference</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['pre_bid_conf'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['eligibility_check'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                        Eligibility Check</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['eligibility_check'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['sub_open_bids'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sub/Open
                                                        of Bids</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['sub_open_bids'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['bidding_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bidding
                                                        Date</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['bidding_date'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($hasSvpData)
                                    <div>
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                            Mode Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            @if ($currentMode['rfq_no'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">RFQ No.
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['rfq_no'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['canvass_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Canvass
                                                        Date</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['canvass_date'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['date_returned_of_canvass'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Returned
                                                        of Canvass</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['date_returned_of_canvass'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['abstract_of_canvass_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Abstract
                                                        of Canvass</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['abstract_of_canvass_date'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['resolution_number'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Resolution
                                                        Number</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['resolution_number'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- History Section --}}
                                @if ($hasAnyHistory && ($mopToggles['lot_history'] ?? false))
                                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-600">
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Mode History
                                        </h4>

                                        <div class="space-y-4">
                                            @foreach ($historyModes as $historyItem)
                                                @php
                                                    $historyModeId = $historyItem['mode_of_procurement_id'];

                                                    $hasHistoryBidding =
                                                        !in_array($historyModeId, [
                                                            1,
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
                                                        ]) &&
                                                        ($historyItem['bidding_number'] ||
                                                            $historyItem['ib_number'] ||
                                                            $historyItem['pre_proc_conference'] ||
                                                            $historyItem['ads_post_ib'] ||
                                                            $historyItem['pre_bid_conf'] ||
                                                            $historyItem['eligibility_check'] ||
                                                            $historyItem['sub_open_bids'] ||
                                                            $historyItem['bidding_date']);

                                                    $hasHistorySvp =
                                                        in_array($historyModeId, [
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
                                                        ]) &&
                                                        ($historyItem['rfq_no'] ||
                                                            $historyItem['canvass_date'] ||
                                                            $historyItem['date_returned_of_canvass'] ||
                                                            $historyItem['abstract_of_canvass_date'] ||
                                                            $historyItem['resolution_number']);
                                                @endphp

                                                <div
                                                    class="bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-4 border border-gray-200 dark:border-neutral-700">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex items-center gap-2">
                                                            <span
                                                                class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-gray-400 font-semibold text-xs">
                                                                {{ $historyItem['mode_order'] }}
                                                            </span>
                                                            <span
                                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                {{ $historyItem['mode_of_procurement_name'] }}
                                                            </span>
                                                        </div>
                                                        @if ($historyItem['bidding_result'])
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $historyItem['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                                {{ $historyItem['bidding_result'] }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if ($hasHistoryBidding)
                                                        <div class="mb-3">
                                                            <p
                                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                Mode Information</p>
                                                            <div
                                                                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                @if ($historyItem['bidding_number'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Bidding #</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['bidding_number'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['ib_number'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            IB No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['ib_number'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['pre_proc_conference'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['pre_proc_conference'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['ads_post_ib'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Ads/Post IB</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['ads_post_ib'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['pre_bid_conf'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Bid Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['pre_bid_conf'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['eligibility_check'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Eligibility</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['eligibility_check'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['sub_open_bids'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Sub/Open Bids</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['sub_open_bids'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['bidding_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Bidding Date</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['bidding_date'] }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($hasHistorySvp)
                                                        <div>
                                                            <p
                                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                Mode Information</p>
                                                            <div
                                                                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                @if ($historyItem['rfq_no'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            RFQ No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['rfq_no'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['canvass_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Canvass Date</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['canvass_date'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['date_returned_of_canvass'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Returned</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['date_returned_of_canvass'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['abstract_of_canvass_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Abstract</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['abstract_of_canvass_date'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['resolution_number'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Resolution</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['resolution_number'] }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No mode of procurement
                                        data available</p>
                                </div>
                            </div>
                        @endif
                    @elseif ($form['procurement_type'] === 'perItem')
                        {{-- PER ITEM DISPLAY WITH SEARCH AND PAGINATION --}}
                        @php
                            // Group items by prItemID to show current mode + history
                            $groupedItems = collect($form['items'] ?? [])->groupBy('prItemID');

                            // Apply search filter
                            if (!empty($mopSearchTerm)) {
                                $groupedItems = $groupedItems->filter(function ($itemGroup) {
                                    $firstItem = $itemGroup->first();
                                    return stripos($firstItem['description'] ?? '', $this->mopSearchTerm) !== false;
                                });
                            }

                            $totalItems = $groupedItems->count();

                            // Pagination settings
                            $currentPage = $page ?? 1;
                            $itemsPerPage = $perPage ?? 10;
                            $totalPages = ceil($totalItems / $itemsPerPage);

                            // Paginate grouped items
                            $paginatedGroups = $groupedItems->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage);
                        @endphp

                        @if ($totalItems > 0)
                            {{-- Search and Pagination Controls (One Row) --}}
                            <div
                                class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    {{-- Left: Search --}}
                                    <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                                        <div class="relative flex-1 max-w-md">
                                            <input type="text" wire:model.live.debounce.300ms="mopSearchTerm"
                                                placeholder="Search by item name..."
                                                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        @if (!empty($mopSearchTerm))
                                            <button wire:click="$set('mopSearchTerm', '')"
                                                class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                                Clear
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Center: Item Count --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Showing
                                            {{ $totalItems > 0 ? ($currentPage - 1) * $itemsPerPage + 1 : 0 }} to
                                            {{ min($currentPage * $itemsPerPage, $totalItems) }} of
                                            {{ $totalItems }} items
                                        </span>
                                    </div>

                                    {{-- Right: Items Per Page --}}
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Items per page:</label>
                                        <select wire:model.live="perPage"
                                            class="px-3 py-1 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="{{ $totalItems }}">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Item Cards --}}
                            @foreach ($paginatedGroups as $prItemID => $itemGroup)
                                @php
                                    // Get the most recent item (first in the group, assuming sorted by mode_order desc)
                                    $currentItem = $itemGroup->first();
                                    $hasHistory = $itemGroup->count() > 1;
                                    $isExpanded = $mopToggles[$prItemID] ?? false;
                                @endphp

                                <div
                                    class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    {{-- Item Header --}}
                                    <div
                                        class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span
                                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-sm">
                                                    {{ $currentItem['item_no'] }}
                                                </span>
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $currentItem['description'] }}
                                                </h3>
                                            </div>
                                            <div
                                                class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                @php
                                                    $mode = $modeOfProcurements->firstWhere(
                                                        'id',
                                                        $currentItem['mode_of_procurement_id'],
                                                    );
                                                @endphp
                                                <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                                    {{ $mode?->modeofprocurements ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if ($currentItem['bidding_result'])
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentItem['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $currentItem['bidding_result'] }}
                                                </span>
                                            @endif

                                            @if ($hasHistory)
                                                <button type="button"
                                                    wire:click="toggleMopSection('{{ $prItemID }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-600 transition-colors"
                                                    title="{{ $isExpanded ? 'Hide History' : 'Show History' }}">
                                                    @if ($isExpanded)
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-emerald-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-emerald-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Current Mode Data --}}
                                    @php
                                        $modeId = $currentItem['mode_of_procurement_id'];
                                        $hasBiddingData =
                                            !in_array($modeId, [
                                                1,
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
                                            ]) &&
                                            ($currentItem['bidding_number'] ||
                                                $currentItem['ib_number'] ||
                                                $currentItem['pre_proc_conference'] ||
                                                $currentItem['ads_post_ib'] ||
                                                $currentItem['pre_bid_conf'] ||
                                                $currentItem['eligibility_check'] ||
                                                $currentItem['sub_open_bids'] ||
                                                $currentItem['bidding_date']);

                                        $hasSvpData =
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
                                            ]) &&
                                            ($currentItem['rfq_no'] ||
                                                $currentItem['canvass_date'] ||
                                                $currentItem['date_returned_of_canvass'] ||
                                                $currentItem['abstract_of_canvass_date'] ||
                                                $currentItem['resolution_number']);
                                    @endphp

                                    {{-- Mode Information --}}
                                    @if ($hasBiddingData)
                                        <div class="mb-6">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Mode Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                                @if ($currentItem['bidding_number'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Bidding #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['bidding_number'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['ib_number'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">IB No.
                                                        </p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['ib_number'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['pre_proc_conference'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Pre-Proc Conference</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['pre_proc_conference'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['ads_post_ib'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Ads/Post IB</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['ads_post_ib'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['pre_bid_conf'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Pre-Bid Conference</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['pre_bid_conf'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['eligibility_check'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Eligibility Check</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['eligibility_check'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['sub_open_bids'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Sub/Open of Bids</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['sub_open_bids'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['bidding_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Bidding Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['bidding_date'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if ($hasSvpData)
                                        <div>
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                                Mode Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                                @if ($currentItem['rfq_no'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">RFQ
                                                            No.</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['rfq_no'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['canvass_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Canvass Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['canvass_date'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['date_returned_of_canvass'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Returned of Canvass</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['date_returned_of_canvass'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['abstract_of_canvass_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Abstract of Canvass</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['abstract_of_canvass_date'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['resolution_number'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Resolution Number</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['resolution_number'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- History Section --}}
                                    @if ($hasHistory && $isExpanded)
                                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-600">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Mode History
                                            </h4>

                                            <div class="space-y-4">
                                                @foreach ($itemGroup->skip(1) as $historyItem)
                                                    @php
                                                        $historyModeId = $historyItem['mode_of_procurement_id'];
                                                        $historyMode = $modeOfProcurements->firstWhere(
                                                            'id',
                                                            $historyModeId,
                                                        );

                                                        $hasHistoryBidding =
                                                            !in_array($historyModeId, [
                                                                1,
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
                                                            ]) &&
                                                            ($historyItem['bidding_number'] ||
                                                                $historyItem['ib_number'] ||
                                                                $historyItem['pre_proc_conference'] ||
                                                                $historyItem['ads_post_ib'] ||
                                                                $historyItem['pre_bid_conf'] ||
                                                                $historyItem['eligibility_check'] ||
                                                                $historyItem['sub_open_bids'] ||
                                                                $historyItem['bidding_date']);

                                                        $hasHistorySvp =
                                                            in_array($historyModeId, [
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
                                                            ]) &&
                                                            ($historyItem['rfq_no'] ||
                                                                $historyItem['canvass_date'] ||
                                                                $historyItem['date_returned_of_canvass'] ||
                                                                $historyItem['abstract_of_canvass_date'] ||
                                                                $historyItem['resolution_number']);
                                                    @endphp

                                                    <div
                                                        class="bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-4 border border-gray-200 dark:border-neutral-700">
                                                        <div class="flex items-center justify-between mb-3">
                                                            <span
                                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                {{ $historyMode?->modeofprocurements ?? 'N/A' }}
                                                            </span>
                                                            @if ($historyItem['bidding_result'])
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $historyItem['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                                    {{ $historyItem['bidding_result'] }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if ($hasHistoryBidding)
                                                            <div class="mb-3">
                                                                <p
                                                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                    Mode Information</p>
                                                                <div
                                                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                    @if ($historyItem['bidding_number'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Bidding #</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['bidding_number'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['ib_number'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                IB No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['ib_number'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['pre_proc_conference'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['pre_proc_conference'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['ads_post_ib'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Ads/Post IB</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['ads_post_ib'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['pre_bid_conf'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Bid Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['pre_bid_conf'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['eligibility_check'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Eligibility</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['eligibility_check'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['sub_open_bids'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Sub/Open Bids</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['sub_open_bids'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['bidding_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Bidding Date</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['bidding_date'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($hasHistorySvp)
                                                            <div>
                                                                <p
                                                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                    Mode Information</p>
                                                                <div
                                                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                    @if ($historyItem['rfq_no'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                RFQ No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['rfq_no'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['canvass_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Canvass Date</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['canvass_date'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['date_returned_of_canvass'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Returned</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['date_returned_of_canvass'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['abstract_of_canvass_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Abstract</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['abstract_of_canvass_date'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['resolution_number'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Resolution</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['resolution_number'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Pagination Controls --}}
                            @if ($totalPages > 1)
                                <div
                                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        {{-- Previous Button --}}
                                        <button type="button"
                                            wire:click="$set('page', {{ max(1, $currentPage - 1) }})"
                                            @if ($currentPage <= 1) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                        {{ $currentPage <= 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                            : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Previous
                                        </button>

                                        {{-- Page Numbers --}}
                                        <div class="flex items-center gap-2">
                                            @php
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($totalPages, $currentPage + 2);
                                            @endphp

                                            @if ($startPage > 1)
                                                <button type="button" wire:click="$set('page', 1)"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                {{ $currentPage == 1
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    1
                                                </button>
                                                @if ($startPage > 2)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                            @endif

                                            @for ($i = $startPage; $i <= $endPage; $i++)
                                                <button type="button"
                                                    wire:click="$set('page', {{ $i }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                {{ $currentPage == $i
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor

                                            @if ($endPage < $totalPages)
                                                @if ($endPage < $totalPages - 1)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                                <button type="button"
                                                    wire:click="$set('page', {{ $totalPages }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                {{ $currentPage == $totalPages
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $totalPages }}
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Next Button --}}
                                        <button type="button"
                                            wire:click="$set('page', {{ min($totalPages, $currentPage + 1) }})"
                                            @if ($currentPage >= $totalPages) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                        {{ $currentPage >= $totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                            : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            Next
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @elseif (!empty($mopSearchTerm))
                            {{-- No Results Found --}}
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No items found matching
                                        "{{ $mopSearchTerm }}"</p>
                                    <button wire:click="$set('mopSearchTerm', '')"
                                        class="mt-4 px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        Clear Search
                                    </button>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No mode of procurement
                                        data available</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            @if ($activeTab == 3)
                {{-- Post Procurement Tab --}}
                <div class="space-y-4">
                    @if ($form['procurement_type'] === 'perLot')
                        {{-- PER LOT POST PROCUREMENT --}}
                        @if ($this->hasPostData)
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                {{-- Header --}}
                                <div
                                    class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-emerald-700 dark:text-emerald-300" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            Post-Procurement Details</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Award and contract
                                            information</p>
                                    </div>
                                </div>

                                {{-- Award Information --}}
                                <div class="mb-6">
                                    <h4
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Award Information
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                        @if ($resolutionNumber)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Resolution #
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $resolutionNumber }}</p>
                                            </div>
                                        @endif
                                        @if ($bidEvaluationDate)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bid Evaluation
                                                    Date</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $bidEvaluationDate }}</p>
                                            </div>
                                        @endif
                                        @if ($postQualDate)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Post Qual Date
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $postQualDate }}</p>
                                            </div>
                                        @endif
                                        @if ($recommendingForAward)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Recommending
                                                    for Award</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $recommendingForAward }}</p>
                                            </div>
                                        @endif
                                        @if ($noticeOfAwardNumber)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notice of
                                                    Award #</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $noticeOfAwardNumber }}</p>
                                            </div>
                                        @endif
                                        @if ($noticeOfAward)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notice of
                                                    Award</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $noticeOfAward }}</p>
                                            </div>
                                        @endif
                                        @if ($awardedAmount)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Awarded Amount
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    ₱{{ number_format($awardedAmount, 2) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- PhilGEPS Information --}}
                                @if ($philgepsReferenceNo || $awardNoticeNumber || $dateOfPostingOfAwardOnPhilGEPS)
                                    <div class="mb-6">
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                            PhilGEPS Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @if ($philgepsReferenceNo)
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PhilGEPS
                                                        Reference #</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $philgepsReferenceNo }}</p>
                                                </div>
                                            @endif
                                            @if ($awardNoticeNumber)
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Award
                                                        Notice #</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $awardNoticeNumber }}</p>
                                                </div>
                                            @endif
                                            @if ($dateOfPostingOfAwardOnPhilGEPS)
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Posting
                                                        Date on PhilGEPS</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $dateOfPostingOfAwardOnPhilGEPS }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Supplier Information --}}
                                @if ($supplier_id)
                                    <div>
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            Supplier
                                        </h4>
                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4">
                                            @php
                                                $supplier = $suppliers->firstWhere('id', $supplier_id);
                                            @endphp
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $supplier?->name ?? 'Unknown Supplier' }}
                                                </p>
                                                @if ($supplier)
                                                    <button wire:click="viewSupplierDetails({{ $supplier->id }})"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 transition-colors"
                                                        title="View Contact Details">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No post-procurement data
                                        available</p>
                                </div>
                            </div>
                        @endif
                    @elseif ($form['procurement_type'] === 'perItem')
                        {{-- PER ITEM POST PROCUREMENT WITH SEARCH AND PAGINATION --}}
                        @php
                            // Get items with post-procurement data
                            $itemsWithPostData = collect($form['items'] ?? [])
                                ->filter(function ($item) {
                                    $prItemID = $item['prItemID'] ?? null;
                                    return $prItemID &&
                                        isset($this->postItems[$prItemID]) &&
                                        !empty(array_filter($this->postItems[$prItemID] ?? []));
                                })
                                ->groupBy('prItemID')
                                ->map(fn($group) => $group->first());

                            // Apply search filter
                            if (!empty($postSearchTerm)) {
                                $itemsWithPostData = $itemsWithPostData->filter(function ($item) {
                                    return stripos($item['description'] ?? '', $this->postSearchTerm) !== false;
                                });
                            }

                            $totalPostItems = $itemsWithPostData->count();

                            // Pagination settings
                            $currentPostPage = $postPage ?? 1;
                            $itemsPerPostPage = $postPerPage ?? 10;
                            $totalPostPages = ceil($totalPostItems / $itemsPerPostPage);

                            // Paginate items
                            $paginatedPostItems = $itemsWithPostData->slice(
                                ($currentPostPage - 1) * $itemsPerPostPage,
                                $itemsPerPostPage,
                            );
                        @endphp

                        @if ($totalPostItems > 0)
                            {{-- Search and Pagination Controls (One Row) --}}
                            <div
                                class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    {{-- Left: Search --}}
                                    <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                                        <div class="relative flex-1 max-w-md">
                                            <input type="text" wire:model.live.debounce.300ms="postSearchTerm"
                                                placeholder="Search by item name..."
                                                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        @if (!empty($postSearchTerm))
                                            <button wire:click="$set('postSearchTerm', '')"
                                                class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                                Clear
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Center: Item Count --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Showing
                                            {{ $totalPostItems > 0 ? ($currentPostPage - 1) * $itemsPerPostPage + 1 : 0 }}
                                            to
                                            {{ min($currentPostPage * $itemsPerPostPage, $totalPostItems) }} of
                                            {{ $totalPostItems }} items
                                        </span>
                                    </div>

                                    {{-- Right: Items Per Page --}}
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Items per page:</label>
                                        <select wire:model.live="postPerPage"
                                            class="px-3 py-1 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="{{ $totalPostItems }}">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Items with Post Data --}}
                            @foreach ($paginatedPostItems as $prItemID => $item)
                                @php
                                    $postData = $this->postItems[$prItemID] ?? [];
                                    $mode = $modeOfProcurements->firstWhere('id', $item['mode_of_procurement_id']);
                                @endphp

                                <div
                                    class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    {{-- Item Header --}}
                                    <div
                                        class="flex items-start justify-between mb-2 pb-2 border-b border-gray-200 dark:border-neutral-600">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-sm">
                                                    {{ $item['item_no'] }}
                                                </span>
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $item['description'] }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Award Information --}}
                                    @if (
                                        !empty(array_filter([
                                                $postData['resolutionNumber'] ?? null,
                                                $postData['bidEvaluationDate'] ?? null,
                                                $postData['postQualDate'] ?? null,
                                                $postData['recommendingForAward'] ?? null,
                                                $postData['noticeOfAward'] ?? null,
                                                $postData['awardedAmount'] ?? null,
                                            ])
                                        ))
                                        <div class="mb-6">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Award Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @if (!empty($postData['resolutionNumber']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Resolution #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['resolutionNumber'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['bidEvaluationDate']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bid
                                                            Evaluation Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['bidEvaluationDate'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['postQualDate']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Post
                                                            Qual Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['postQualDate'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['recommendingForAward']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Recommending for Award</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['recommendingForAward'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['noticeOfAward']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notice
                                                            of Award</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['noticeOfAward'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['awardedAmount']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Awarded Amount</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            ₱{{ number_format($postData['awardedAmount'], 2) }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- PhilGEPS Information --}}
                                    @if (
                                        !empty(array_filter([
                                                $postData['philgepsReferenceNo'] ?? null,
                                                $postData['awardNoticeNumber'] ?? null,
                                                $postData['dateOfPostingOfAwardOnPhilGEPS'] ?? null,
                                            ])
                                        ))
                                        <div class="mb-6">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                </svg>
                                                PhilGEPS Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                @if (!empty($postData['philgepsReferenceNo']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            PhilGEPS Reference #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['philgepsReferenceNo'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['awardNoticeNumber']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Award
                                                            Notice #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['awardNoticeNumber'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['dateOfPostingOfAwardOnPhilGEPS']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Posting Date on PhilGEPS</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['dateOfPostingOfAwardOnPhilGEPS'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Supplier Information --}}
                                    @if (!empty($postData['supplier_id']))
                                        <div>
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Supplier
                                            </h4>
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4">
                                                @php
                                                    $supplier = $suppliers->firstWhere('id', $postData['supplier_id']);
                                                @endphp
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $supplier?->name ?? 'Unknown Supplier' }}
                                                    </p>
                                                    @if ($supplier)
                                                        <button wire:click="viewSupplierDetails({{ $supplier->id }})"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 transition-colors"
                                                            title="View Contact Details">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Pagination Controls --}}
                            @if ($totalPostPages > 1)
                                <div
                                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        {{-- Previous Button --}}
                                        <button type="button"
                                            wire:click="$set('postPage', {{ max(1, $currentPostPage - 1) }})"
                                            @if ($currentPostPage <= 1) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                {{ $currentPostPage <= 1
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                                    : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Previous
                                        </button>

                                        {{-- Page Numbers --}}
                                        <div class="flex items-center gap-2">
                                            @php
                                                $startPostPage = max(1, $currentPostPage - 2);
                                                $endPostPage = min($totalPostPages, $currentPostPage + 2);
                                            @endphp

                                            @if ($startPostPage > 1)
                                                <button type="button" wire:click="$set('postPage', 1)"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPostPage == 1
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    1
                                                </button>
                                                @if ($startPostPage > 2)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                            @endif

                                            @for ($i = $startPostPage; $i <= $endPostPage; $i++)
                                                <button type="button"
                                                    wire:click="$set('postPage', {{ $i }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPostPage == $i
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor

                                            @if ($endPostPage < $totalPostPages)
                                                @if ($endPostPage < $totalPostPages - 1)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                                <button type="button"
                                                    wire:click="$set('postPage', {{ $totalPostPages }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPostPage == $totalPostPages
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $totalPostPages }}
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Next Button --}}
                                        <button type="button"
                                            wire:click="$set('postPage', {{ min($totalPostPages, $currentPostPage + 1) }})"
                                            @if ($currentPostPage >= $totalPostPages) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                {{ $currentPostPage >= $totalPostPages
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                                    : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            Next
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @elseif (!empty($postSearchTerm))
                            {{-- No Results Found --}}
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No items found matching
                                        "{{ $postSearchTerm }}"</p>
                                    <button wire:click="$set('postSearchTerm', '')"
                                        class="mt-4 px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        Clear Search
                                    </button>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No post-procurement data
                                        available</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Supplier Details Modal - Using your forms modal component --}}
    <x-forms.modal title="Supplier Contact Details" size="max-w-lg">
        @if ($selectedSupplier)
            <div class="px-6 py-4 space-y-4">
                {{-- Company Name --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Company Name
                    </label>
                    <p class="text-sm font-semibold text-black dark:text-white">
                        {{ $selectedSupplier['name'] ?? 'N/A' }}
                    </p>
                </div>


                {{-- Mobile --}}
                @if (!empty(trim($selectedSupplier['mobile'] ?? '')))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Mobile
                        </label>
                        <p class="text-sm text-black dark:text-white">
                            {{ $selectedSupplier['mobile'] }}
                        </p>
                    </div>
                @endif

                {{-- Telephone --}}
                @if (!empty(trim($selectedSupplier['telephone'] ?? '')))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Telephone
                        </label>
                        <p class="text-sm text-black dark:text-white">
                            {{ $selectedSupplier['telephone'] }}
                        </p>
                    </div>
                @endif

                {{-- Email --}}
                @if (!empty(trim($selectedSupplier['email'] ?? '')))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email
                        </label>
                        <p class="text-sm text-black dark:text-white">
                            {{ $selectedSupplier['email'] }}
                        </p>
                    </div>
                @endif

                {{-- Contact Person --}}
                @if (!empty(trim($selectedSupplier['contact_person'] ?? '')))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Contact Person
                        </label>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $selectedSupplier['contact_person'] }}
                        </p>
                    </div>
                @endif

                {{-- No Additional Data Message --}}
                @if (empty(trim($selectedSupplier['tin'] ?? '')) &&
                        empty(trim($selectedSupplier['address'] ?? '')) &&
                        empty(trim($selectedSupplier['mobile'] ?? '')) &&
                        empty(trim($selectedSupplier['telephone'] ?? '')) &&
                        empty(trim($selectedSupplier['email'] ?? '')) &&
                        empty(trim($selectedSupplier['contact_person'] ?? '')))
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                            No additional contact information available for this supplier.
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </x-forms.modal>
</div>
