<div
    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col">

    <!-- Enhanced Header with Expandable Filters -->
    <div class="sticky top-0 z-40 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 w-full"
        x-data="{ showFilters: false }">
        <!-- Single Row: Search, Filters, Add Button -->
        <div class="px-6 py-4 flex items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live="search" placeholder="Search procurements..."
                    class="w-full px-4 py-2.5 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Filter Toggle Button -->
            <div class="flex items-center gap-2">
                <!-- Bulk Edit Button -->
                @if (count($selectedItems) > 0)
                    <button wire:click="bulkEdit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border transition-all duration-200 bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Bulk Edit ({{ count($selectedItems) }})
                    </button>
                @endif

                <button @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border transition-all duration-200"
                    :class="showFilters ?
                        'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300' :
                        'bg-gray-100 dark:bg-neutral-700 border-gray-200 dark:border-neutral-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-neutral-600'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Expandable Filters Section -->
        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 max-h-96"
            x-transition:leave-end="opacity-0 max-h-0"
            class="relative z-40 overflow-visible border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900/50">
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <div class="relative z-50">
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-2">BAC
                            Category</label>
                        <x-forms.searchable-select wire:model.live="bacCategoryFilter" :options="$bacCategories" labelKey="name"
                            valueKey="id" placeholder="All" />
                    </div>

                    <!-- Category Filter -->
                    <div class="relative z-40">
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-2">
                            Category
                        </label>
                        <x-forms.searchable-select wire:model.live="categoryFilter" :options="$allCategories" labelKey="name"
                            valueKey="id" placeholder="All" />
                    </div>

                    <!-- IB Number Filter -->
                    <div class="relative z-30">
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-2">
                            IB Number
                        </label>
                        <x-forms.searchable-select wire:model.live="ibNumberFilter" :options="$ibNumbers" labelKey="name"
                            valueKey="id" placeholder="All" />
                    </div>

                    <!-- Current Mode Filter -->
                    <div class="relative z-20">
                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 block mb-2">
                            Current Mode
                        </label>
                        <x-forms.searchable-select wire:model.live="currentModeFilter" :options="$modes" labelKey="name"
                            valueKey="id" placeholder="All" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Items Section -->
    @if (count($selectedItems) > 0)
        <div class="mx-6 mt-4 mb-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg overflow-hidden"
            x-data="{ showSelected: true }">
            <div class="flex items-center justify-between px-4 py-3 cursor-pointer"
                @click="showSelected = !showSelected">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                        {{ count($selectedItems) }} PR(s) Selected
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button wire:click.stop="clearSelections"
                        class="text-xs font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                        Clear All
                    </button>
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 transition-transform"
                        :class="{ 'rotate-180': showSelected }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            <div x-show="showSelected" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0" class="border-t border-emerald-200 dark:border-emerald-700">
                <div class="overflow-auto max-h-96">
                    <table class="table-fixed w-full min-w-[1200px] divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead
                            class="bg-gradient-to-r from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40 sticky top-0 z-30">
                            <tr>
                                <th
                                    class="px-1 py-2 sticky left-0 z-50 bg-emerald-100 dark:bg-emerald-900/40 w-12 text-center align-middle">
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-200">✓</span>
                                </th>
                                <th class="px-2 py-1 sticky left-12 z-50 bg-emerald-100 dark:bg-emerald-900/40 w-16">
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider sticky left-28 z-40 bg-emerald-100 dark:bg-emerald-900/40 w-40">
                                    PR Number
                                </th>
                                <th
                                    class="px-2 py-1 text-left text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider sticky left-68 z-40 bg-emerald-100 dark:bg-emerald-900/40 w-80">
                                    Procurement Program / Project
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-28">
                                    BAC Category
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-36">
                                    IB Number
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-56">
                                    Current Mode
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-28">
                                    Status
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-40">
                                    Cluster / Committee
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-48">
                                    Category
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-40">
                                    Early Procurement
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-48">
                                    Source of Funds
                                </th>
                                <th
                                    class="px-1 py-1 text-center text-xs font-bold text-emerald-700 dark:text-emerald-200 uppercase tracking-wider w-32">
                                    ABC Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-neutral-800">
                            @php
                                $selectedProcurements = \App\Models\Procurement::whereIn('procID', $selectedItems)
                                    ->with([
                                        'currentPrStage.procurementStage',
                                        'mopLots.modeOfProcurement',
                                        'pr_items',
                                        'category.bacType',
                                        'clusterCommittee',
                                        'category',
                                        'fundSource',
                                    ])
                                    ->get();
                            @endphp
                            @foreach ($selectedProcurements as $procurement)
                                @php
                                    $modeStatus = $this->getCurrentModeAndStatus($procurement);
                                    $procurement->currentMode = $modeStatus['mode'];
                                    $procurement->currentStatus = $modeStatus['status'];
                                @endphp
                                <tr wire:key="selected-procurement-{{ $procurement->procID }}"
                                    class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-emerald-100 hover:to-teal-100 dark:hover:from-emerald-900/30 dark:hover:to-teal-900/30 transition-all duration-200 group">

                                    <td
                                        class="px-2 py-4 text-center sticky left-0 z-20 {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-black dark:text-white w-12 align-middle">
                                        <button
                                            wire:click.stop="$set('selectedItems', {{ json_encode(array_values(array_diff($selectedItems, [$procurement->procID]))) }})"
                                            class="p-1 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>

                                    <td
                                        class="py-4 pr-2 text-center sticky left-12 z-20 {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-black dark:text-white w-16">
                                    </td>

                                    <td
                                        class="px-2 py-4 text-center text-sm font-bold sticky left-28 z-20 {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-emerald-700 dark:text-emerald-300 w-40">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-s">
                                            {{ $procurement->pr_number }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-3 py-4 text-left text-xs sticky left-68 z-20 {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-900 dark:text-gray-100 w-80">
                                        <div class="font-medium truncate"
                                            title="{{ $procurement->procurement_program_project }}">
                                            {{ $procurement->procurement_program_project }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        <div class="truncate"
                                            title="{{ $procurement->category?->bacType?->abbreviation ?? 'N/A' }}">
                                            {{ $procurement->category?->bacType?->abbreviation ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        @php
                                            $latestMop = $procurement
                                                ->mopLots()
                                                ->orderBy('mode_order', 'desc')
                                                ->first();
                                            $ibNumber = null;
                                            if ($latestMop) {
                                                $bidSchedule = \App\Models\BidSchedule::where(
                                                    'mop_uid',
                                                    $latestMop->uid,
                                                )
                                                    ->where('ref_id', $procurement->procID)
                                                    ->first();
                                                $ibNumber = $bidSchedule?->ib_number;
                                            }
                                        @endphp
                                        @if ($ibNumber)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-md font-mono text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                                {{ $ibNumber }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 italic text-xs">N/A</span>
                                        @endif
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        @if ($procurement->currentMode)
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 dark:from-blue-900/40 dark:to-blue-800/40 dark:text-blue-200 dark:border-blue-700 shadow-sm">
                                                {{ $procurement->currentMode->modeofprocurements }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 italic text-xs">No
                                                Mode</span>
                                        @endif
                                    </td>

                                    @php
                                        $status = $procurement->currentStatus ?? '';
                                        $isSuccessful = in_array(strtoupper($status), ['SUCCESSFUL', 'COMPLETED']);

                                        $statusColor = $isSuccessful
                                            ? 'from-green-100 to-green-200 text-green-800 border-green-300 dark:from-green-900/40 dark:to-green-800/40 dark:text-green-200 dark:border-green-700'
                                            : 'from-red-100 to-red-200 text-red-800 border-red-300 dark:from-red-900/40 dark:to-red-800/40 dark:text-red-200 dark:border-red-700';

                                        $statusIcon = $isSuccessful
                                            ? '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                                            : '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                                    @endphp
                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        @if ($procurement->currentStatus)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r {{ $statusColor }} border shadow-sm">
                                                {!! $statusIcon !!}
                                                {{ $procurement->currentStatus }}
                                            </span>
                                        @else
                                            <span
                                                class="text-gray-500 dark:text-gray-400 italic text-xs">Pending</span>
                                        @endif
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        <div class="truncate"
                                            title="{{ $procurement->clusterCommittee->clustercommittee }}">
                                            {{ $procurement->clusterCommittee->clustercommittee }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        <div class="truncate" title="{{ $procurement->category->category }}">
                                            {{ $procurement->category->category }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-neutral-200">
                                        @if ($procurement->early_procurement)
                                            <div
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                                                <x-heroicon-s-check-circle title="Yes"
                                                    class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                                <span
                                                    class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Yes</span>
                                            </div>
                                        @else
                                            <div
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                                                <x-heroicon-s-x-circle title="No"
                                                    class="h-4 w-4 text-red-600 dark:text-red-400" />
                                                <span
                                                    class="text-xs font-medium text-red-700 dark:text-red-300">No</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td
                                        class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-700 dark:text-gray-200">
                                        <div class="truncate"
                                            title="{{ $procurement->fundSource ? $procurement->fundSource->fundsources : 'N/A' }}">
                                            <span
                                                class="text-xs font-medium">{{ $procurement->fundSource ? $procurement->fundSource->fundsources : 'N/A' }}</span>
                                        </div>
                                    </td>

                                    <td
                                        class="px-3 py-4 pr-4 text-right text-sm font-bold {{ $loop->even ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-100 group-hover:to-teal-100 dark:group-hover:from-emerald-900/30 dark:group-hover:to-teal-900/30 text-gray-900 dark:text-white relative">
                                        <div class="inline-flex items-baseline gap-0.5">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                            <span
                                                class="text-emerald-700 dark:text-emerald-400">{{ number_format($procurement->abc ?? 0, 2) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Table Section -->
    <div class="overflow-auto flex-1">
        <table class="table-fixed w-full min-w-[1200px] divide-y divide-gray-200 dark:divide-neutral-700">
            <thead
                class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-30">
                <tr>
                    <th
                        class="px-1 py-2 sticky left-0 z-50 bg-gray-100 dark:bg-neutral-900 w-12 text-center align-middle">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </th>

                    <th class="px-2 py-1 sticky left-12 z-50 bg-gray-100 dark:bg-neutral-900 w-16">
                    </th>

                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-28 z-40 bg-gray-100 dark:bg-neutral-900 w-40">
                        PR Number
                    </th>

                    <th
                        class="px-2 py-1 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-68 z-40 bg-gray-100 dark:bg-neutral-900 w-80">
                        Procurement Program / Project
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                        BAC Category
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">
                        IB Number
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">
                        Current Mode
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                        Status
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">
                        Cluster / Committee
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">
                        Category
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">
                        Early Procurement
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">
                        Source of Funds
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                        ABC Amount
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800">
                @foreach ($procurements as $procurement)
                    <!-- Enhanced Main Row with Alternating Colors -->
                    <tr wire:key="procurement-{{ $procurement->procID }}"
                        class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-gray-50/50 dark:bg-neutral-900/50' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20 transition-all duration-200 group">
                        <td
                            class="px-2 py-4 text-center sticky left-0 z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-black dark:text-white w-12 align-middle">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" wire:key="checkbox-{{ $procurement->procID }}"
                                    wire:model.live="selectedItems" value="{{ $procurement->procID }}"
                                    @if ($procurement->procurement_type === 'perItem') disabled @endif
                                    class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 {{ $procurement->procurement_type === 'perItem' ? 'opacity-50 cursor-not-allowed' : '' }}">
                            </div>
                        </td>

                        <td
                            class="py-4 pr-2 text-center sticky left-12 z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-black dark:text-white w-16">
                            <div class="flex items-center justify-end gap-1">
                                <!-- Enhanced Expand/Collapse Button -->
                                @if ($procurement->procurement_type === 'perItem')
                                    <button type="button"
                                        wire:click.stop="toggle('expandedProcurementId', '{{ $procurement->procID }}')"
                                        class="p-1.5 rounded-lg bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:border-emerald-300 dark:hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow">
                                        @if ($expandedProcurementId === $procurement->procID)
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                                viewBox="0 0 22 22" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                                viewBox="0 0 22 22" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                    </button>
                                @endif

                                <!-- Enhanced Action Dropdown -->
                                <div x-data="{ open: false }" class="relative inline-block" x-ref="menuWrapper">
                                    <button @click="open = !open" @click.away="open = false"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:border-emerald-300 dark:hover:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor"
                                            class="size-5 text-gray-600 dark:text-gray-300">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                        </svg>
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            @click.away="open = false"
                                            class="absolute z-[9999] bg-white border border-gray-200 rounded-xl shadow-2xl dark:bg-neutral-800 dark:border-neutral-700 min-w-[180px] overflow-hidden"
                                            x-ref="dropdown" x-init="$watch('open', value => {
                                                if (value) {
                                                    let rect = $refs.menuWrapper.getBoundingClientRect();
                                                    $refs.dropdown.style.top = (rect.top + window.scrollY) + 'px';
                                                    $refs.dropdown.style.left = (rect.right + 10 + window.scrollX) + 'px';
                                                }
                                            })">
                                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                @can('update_mode::of::procurement')
                                                    <li>
                                                        <a href="{{ route(
                                                            'mode-of-procurement.' . ($procurement->procurement_type === 'perItem' ? 'update-per-item' : 'update-per-lot'),
                                                            [
                                                                'procurement' => $procurement->procID,
                                                                'search' => $search,
                                                                'perPage' => $perPage,
                                                                'page' => $procurements->currentPage(),
                                                            ],
                                                        ) }}"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-amber-50 hover:to-amber-100 dark:hover:from-amber-900/30 dark:hover:to-amber-800/30 text-amber-600 dark:text-amber-400 transition-all duration-150 group/item">
                                                            <x-heroicon-o-pencil
                                                                class="w-4 h-4 group-hover/item:scale-110 transition-transform" />
                                                            <span class="font-medium">Update</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </td>

                        <td
                            class="px-2 py-4 text-center text-sm font-bold sticky left-28 z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-emerald-700 dark:text-emerald-300 w-40">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-s">
                                {{ $procurement->pr_number }}
                            </span>
                        </td>

                        <td
                            class="px-3 py-4 text-left text-xs sticky left-68 z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-900 dark:text-gray-100 w-80">
                            <div class="font-medium break-words whitespace-normal"
                                title="{{ $procurement->procurement_program_project }}">
                                {{ $procurement->procurement_program_project }}
                            </div>
                        </td>

                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            {{ $procurement->category?->bacType?->abbreviation ?? 'N/A' }}
                        </td>

                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            @if ($procurement->procurement_type === 'perLot')
                                @php
                                    $latestMop = $procurement->mopLots()->orderBy('mode_order', 'desc')->first();
                                    $ibNumber = null;
                                    if ($latestMop) {
                                        $bidSchedule = \App\Models\BidSchedule::where('mop_uid', $latestMop->uid)
                                            ->where('ref_id', $procurement->procID)
                                            ->first();
                                        $ibNumber = $bidSchedule?->ib_number;
                                    }
                                @endphp
                                @if ($ibNumber)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-md font-mono text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                        {{ $ibNumber }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic text-xs">N/A</span>
                                @endif
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic text-xs">See items ↓</span>
                            @endif
                        </td>

                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            @if ($procurement->procurement_type === 'perLot')
                                @if ($procurement->currentMode)
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 dark:from-blue-900/40 dark:to-blue-800/40 dark:text-blue-200 dark:border-blue-700 shadow-sm">
                                        {{ $procurement->currentMode->modeofprocurements }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic text-xs">No Mode</span>
                                @endif
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic text-xs">See items ↓</span>
                            @endif
                        </td>

                        @php
                            $status = $procurement->currentStatus ?? '';
                            $isSuccessful = in_array(strtoupper($status), ['SUCCESSFUL', 'COMPLETED']);

                            $statusColor = $isSuccessful
                                ? 'from-green-100 to-green-200 text-green-800 border-green-300 dark:from-green-900/40 dark:to-green-800/40 dark:text-green-200 dark:border-green-700'
                                : 'from-red-100 to-red-200 text-red-800 border-red-300 dark:from-red-900/40 dark:to-red-800/40 dark:text-red-200 dark:border-red-700';

                            $statusIcon = $isSuccessful
                                ? '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                                : '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                        @endphp
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            @if ($procurement->procurement_type === 'perLot')
                                @if ($procurement->currentStatus)
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r {{ $statusColor }} border shadow-sm">
                                        {!! $statusIcon !!}
                                        {{ $procurement->currentStatus }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic text-xs">Pending</span>
                                @endif
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic text-xs">See items ↓</span>
                            @endif
                        </td>

                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            {{ $procurement->clusterCommittee->clustercommittee }}
                        </td>
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            {{ $procurement->category->category }}
                        </td>
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-neutral-200">
                            @if ($procurement->early_procurement)
                                <div
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                                    <x-heroicon-s-check-circle title="Yes"
                                        class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Yes</span>
                                </div>
                            @else
                                <div
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                                    <x-heroicon-s-x-circle title="No"
                                        class="h-4 w-4 text-red-600 dark:text-red-400" />
                                    <span class="text-xs font-medium text-red-700 dark:text-red-300">No</span>
                                </div>
                            @endif
                        </td>
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            <span
                                class="text-xs font-medium">{{ $procurement->fundSource ? $procurement->fundSource->fundsources : 'N/A' }}</span>
                        </td>
                        <td
                            class="px-3 py-4 pr-4 text-right text-sm font-bold {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-900 dark:text-white relative">
                            <div class="inline-flex items-baseline gap-0.5">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                <span
                                    class="text-emerald-700 dark:text-emerald-400">{{ number_format($procurement->abc ?? 0, 2) }}</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Enhanced Expanded Per Item Rows -->
                    @if ($procurement->procurement_type === 'perItem' && $expandedProcurementId == $procurement->procID)
                        <tr>
                            <td colspan="12"
                                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800 p-4">
                                <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-inner">
                                    <table
                                        class="w-full text-sm border border-gray-300 dark:border-neutral-700 rounded-lg overflow-hidden">
                                        <thead
                                            class="bg-gradient-to-r from-gray-200 to-gray-300 dark:from-neutral-900 dark:to-neutral-800">
                                            <tr>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-16">
                                                    Item #
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-md">
                                                    Item Description
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                                                    IB Number
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">
                                                    Current Mode
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                                                    Status
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                                                    Amount
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                                            @php
                                                $items = $procurement->pr_items;
                                                $currentPage = request()->get('itemsPage_' . $procurement->procID, 1);
                                                $itemsPerPage = $itemsPerPage ?? 5;
                                                $offset = ($currentPage - 1) * $itemsPerPage;
                                                $paginatedItems = $items->slice($offset, $itemsPerPage);
                                                $totalItems = $items->count();
                                                $totalPages = ceil($totalItems / $itemsPerPage);
                                            @endphp

                                            @forelse($paginatedItems as $item)
                                                <tr
                                                    class="hover:bg-emerald-50 dark:hover:bg-neutral-700/50 transition-colors duration-150">
                                                    <td
                                                        class="px-2 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $item->item_no }}
                                                    </td>
                                                    <td
                                                        class="px-2 py-3 text-left text-sm text-gray-700 dark:text-gray-200">
                                                        {{ $item->description }}
                                                    </td>
                                                    <td
                                                        class="px-2 py-3 text-center text-sm text-gray-700 dark:text-gray-200">
                                                        @php
                                                            $latestMopItem = \App\Models\MopItem::where(
                                                                'prItemID',
                                                                $item->prItemID,
                                                            )
                                                                ->orderBy('mode_order', 'desc')
                                                                ->first();
                                                            $itemIbNumber = null;
                                                            if ($latestMopItem) {
                                                                $itemBidSchedule = \App\Models\BidSchedule::where(
                                                                    'mop_uid',
                                                                    $latestMopItem->uid,
                                                                )
                                                                    ->where('ref_id', $item->prItemID)
                                                                    ->first();
                                                                $itemIbNumber = $itemBidSchedule?->ib_number;
                                                            }
                                                        @endphp
                                                        @if ($itemIbNumber)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                                {{ $itemIbNumber }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="text-gray-400 dark:text-gray-500 text-xs italic">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="px-2 py-3 text-center text-sm text-gray-700 dark:text-gray-200">
                                                        @if ($item->currentMode)
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                {{ $item->currentMode->modeofprocurements }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="text-gray-400 dark:text-gray-500 text-xs italic">No
                                                                Mode</span>
                                                        @endif
                                                    </td>
                                                    @php
                                                        $itemStatus = $item->currentStatus ?? '';
                                                        $isItemSuccessful = in_array(strtoupper($itemStatus), [
                                                            'SUCCESSFUL',
                                                            'COMPLETED',
                                                        ]);

                                                        $itemStatusColor = $isItemSuccessful
                                                            ? 'from-green-100 to-green-200 text-green-800 border-green-300 dark:from-green-900/40 dark:to-green-800/40 dark:text-green-200 dark:border-green-700'
                                                            : 'from-red-100 to-red-200 text-red-800 border-red-300 dark:from-red-900/40 dark:to-red-800/40 dark:text-red-200 dark:border-red-700';

                                                        $itemStatusIcon = $isItemSuccessful
                                                            ? '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                                                            : '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                                                    @endphp
                                                    <td
                                                        class="px-2 py-3 text-center text-sm text-gray-700 dark:text-gray-200">
                                                        @if ($item->currentStatus)
                                                            <span
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r {{ $itemStatusColor }} border shadow-sm">
                                                                {!! $itemStatusIcon !!}
                                                                {{ $item->currentStatus }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="text-gray-500 dark:text-gray-400 italic text-xs">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="px-2 py-3 text-center text-sm font-semibold text-gray-900 dark:text-white">
                                                        <span
                                                            class="text-gray-500 dark:text-gray-400">₱</span>{{ number_format($item->amount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6"
                                                        class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        <div class="flex flex-col items-center gap-2">
                                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                                </path>
                                                            </svg>
                                                            <span class="font-medium">No items found</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <!-- Enhanced Items Pagination -->
                                    @if ($totalItems > $itemsPerPage)
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

                                            <!-- Left: Per-page selector -->
                                            <div class="flex items-center gap-x-2">
                                                <label for="itemsPerPage_{{ $procurement->procID }}"
                                                    class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
                                                <select id="itemsPerPage_{{ $procurement->procID }}"
                                                    wire:model.live="itemsPerPage"
                                                    class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                </select>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
                                            </div>

                                            <!-- Center: Summary + Pagination -->
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                    Showing <span
                                                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $offset + 1 }}</span>
                                                    to
                                                    <span
                                                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ min($offset + $itemsPerPage, $totalItems) }}</span>
                                                    of
                                                    <span
                                                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalItems }}</span>
                                                    items
                                                </div>

                                                <!-- Pagination Links -->
                                                @if ($totalPages > 1)
                                                    <div class="flex gap-1">
                                                        <!-- Previous Button -->
                                                        @if ($currentPage > 1)
                                                            <a href="?itemsPage_{{ $procurement->procID }}={{ $currentPage - 1 }}"
                                                                class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150">
                                                                Previous
                                                            </a>
                                                        @endif

                                                        <!-- Page Numbers -->
                                                        @for ($i = 1; $i <= $totalPages; $i++)
                                                            <a href="?itemsPage_{{ $procurement->procID }}={{ $i }}"
                                                                class="px-3 py-1.5 text-xs font-medium border rounded-lg transition-colors duration-150 {{ $i == $currentPage ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white' }}">
                                                                {{ $i }}
                                                            </a>
                                                        @endfor

                                                        <!-- Next Button -->
                                                        @if ($currentPage < $totalPages)
                                                            <a href="?itemsPage_{{ $procurement->procID }}={{ $currentPage + 1 }}"
                                                                class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150">
                                                                Next
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Enhanced Footer Pagination -->
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

        <!-- Left: Per-page selector -->
        <div class="flex items-center gap-x-2">
            <label for="perPage" class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
            <select id="perPage" wire:model.live="perPage"
                class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
        </div>

        <!-- Center: Summary + Pagination -->
        <div class="flex flex-col items-center justify-center gap-3 flex-1">
            <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                Showing <span
                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->firstItem() }}</span>
                to
                <span
                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->lastItem() }}</span>
                of
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $procurements->total() }}</span>
                items
            </div>
            <div class="flex justify-center">
                {{ $procurements->links('vendor.pagination.tailwind') }}
            </div>
        </div>

    </div>

    <!-- Modals -->
    <x-forms.pdf-viewer />
</div>
