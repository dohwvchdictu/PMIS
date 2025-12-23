<div
    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col">

    <!-- Enhanced Header Section -->
    <div
        class="sticky top-0 z-20 bg-gradient-to-r from-white to-gray-50 dark:from-neutral-800 dark:to-neutral-900 px-6 py-4 border-b border-gray-200 dark:border-neutral-700 w-full">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filter Section -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Search Input with Enhanced Styling -->
                <div class="relative group">
                    <input type="text" wire:model.live="search" placeholder="Search Procurements..."
                        class="px-4 py-2.5 pl-10 border border-gray-300 rounded-lg w-80 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                    <svg class="absolute left-3 top-3 text-gray-400 dark:text-gray-500 group-focus-within:text-emerald-500 transition-colors"
                        width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 21l-4.35-4.35" />
                        <circle cx="10" cy="10" r="7" />
                    </svg>
                </div>

                <!-- Filter Dropdowns with Enhanced Styling -->
                <div class="relative group w-40">
                    <x-forms.searchable-select wire:model.live="divisionFilter" :options="$divisions"
                        labelKey="abbreviation" valueKey="id" placeholder="Division" />
                </div>

                <div class="relative group w-60">
                    <x-forms.searchable-select wire:model.live="clusterCommitteeFilter" :options="$clusterCommittees"
                        labelKey="clustercommittee" valueKey="id" placeholder="Cluster" />
                </div>

                <div class="relative group w-80">
                    <x-forms.searchable-select wire:model.live="endUserFilter" :options="$endUsers" labelKey="endusers"
                        valueKey="id" placeholder="End User" />
                </div>

                <div class="relative group w-48">
                    <x-forms.searchable-select wire:model.live="fundSourceFilter" :options="$fundSources"
                        labelKey="fundsources" valueKey="id" placeholder="Fund Source" />
                </div>

                <div class="relative group w-40">
                    <x-forms.searchable-select wire:model.live="remarkFilter" :options="$remarks" labelKey="remarks"
                        valueKey="id" placeholder="Remarks" />
                </div>
            </div>

            <!-- Action Button -->
            @can('create_procurement')
                <a href="{{ route('procurements.create') }}"
                    class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gradient-to-r from-emerald-600 to-emerald-700 text-white hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md dark:focus:ring-offset-neutral-800">
                    <svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    New Procurement
                </a>
            @endcan
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="overflow-auto flex-1">
        <table class="table-fixed w-full min-w-[1200px] divide-y divide-gray-200 dark:divide-neutral-700">
            <thead
                class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-20">
                <tr>
                    <th
                        class="px-2 py-3 sticky left-0 z-40 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 w-12">
                    </th>

                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[48px] z-30 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 w-40">
                        PR Number
                    </th>

                    <th
                        class="px-1 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[208px] z-20 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 w-80">
                        Procurement Program / Project
                    </th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">
                        Stage
                    </th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-40">
                        Remarks
                    </th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                        Date Receipt</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-24">
                        BAC Category</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-20">
                        Division</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                        Cluster / Committee</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">
                        Category</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                        Early Procurement</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-36">
                        Source of Funds</th>
                    <th
                        class="px-1 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                        ABC Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800">
                @foreach ($procurements as $procurement)

                    <!-- Enhanced Main Row with Alternating Colors -->
                    <tr
                        class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-gray-50/50 dark:bg-neutral-900/50' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20 transition-all duration-200 group">
                        <td
                            class="px-1 py-4 text-center sticky left-0 z-40 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-black dark:text-white">
                            <div class="flex items-center justify-center gap-1">
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
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
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
                                                @can('view_procurement')
                                                    <li>
                                                        <button
                                                            x-on:click="$dispatch('open-procurement-view', { procID: '{{ $procurement->procID }}' })"
                                                            type="button"
                                                            class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 dark:hover:from-blue-900/30 dark:hover:to-blue-800/30 text-blue-600 dark:text-blue-400 transition-all duration-150 group/item">
                                                            <x-heroicon-o-eye
                                                                class="w-4 h-4 group-hover/item:scale-110 transition-transform" />
                                                            <span class="font-medium">View Details</span>
                                                        </button>
                                                    </li>
                                                @endcan
                                                @can('view_procurement')
                                                    @if (!empty($procurement->bacApprovedPr?->filepath))
                                                        <li>
                                                            <a href="{{ $procurement->bacApprovedPr->filepath }}"
                                                                target="_blank" rel="noopener noreferrer"
                                                                class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 dark:hover:from-green-900/30 dark:hover:to-green-800/30 text-green-600 dark:text-green-400 transition-all duration-150 group/item">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor"
                                                                    class="size-4 group-hover/item:scale-110 transition-transform">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                                </svg>
                                                                <span class="font-medium">View PR File</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endcan
                                                @can('edit_procurement')
                                                    <li>
                                                        <a href="{{ route(
                                                            'procurements.edit',
                                                            array_merge(
                                                                ['procurement' => $procurement->procID],
                                                                [
                                                                    'page' => $procurements->currentPage(),
                                                                    'search' => $search,
                                                                    'divisionFilter' => $divisionFilter,
                                                                    'clusterCommitteeFilter' => $clusterCommitteeFilter,
                                                                    'endUserFilter' => $endUserFilter,
                                                                    'fundSourceFilter' => $fundSourceFilter,
                                                                ],
                                                            ),
                                                        ) }}"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-amber-50 hover:to-amber-100 dark:hover:from-amber-900/30 dark:hover:to-amber-800/30 text-amber-600 dark:text-amber-400 transition-all duration-150 group/item">
                                                            <x-heroicon-o-pencil
                                                                class="w-4 h-4 group-hover/item:scale-110 transition-transform" />
                                                            <span class="font-medium">Edit</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('update_procurement')
                                                    <li>
                                                        <a href="{{ route(
                                                            'procurements.update_status',
                                                            array_merge(
                                                                ['procurement' => $procurement->procID],
                                                                [
                                                                    'page' => $procurements->currentPage(),
                                                                    'search' => $search,
                                                                    'divisionFilter' => $divisionFilter,
                                                                    'clusterCommitteeFilter' => $clusterCommitteeFilter,
                                                                    'endUserFilter' => $endUserFilter,
                                                                    'fundSourceFilter' => $fundSourceFilter,
                                                                ],
                                                            ),
                                                        ) }}"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 dark:hover:from-purple-900/30 dark:hover:to-purple-800/30 text-purple-600 dark:text-purple-400 transition-all duration-150 group/item">
                                                            <svg class="w-4 h-4 group-hover/item:scale-110 transition-transform"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="font-medium">Update Status</span>
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
                            class="px-2 py-4 text-center text-sm font-bold sticky left-[48px] z-30 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-emerald-700 dark:text-emerald-300 w-40">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs">
                                {{ $procurement->pr_number }}
                            </span>
                        </td>

                        <td
                            class="px-3 py-4 text-left text-sm sticky left-[208px] z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-900 dark:text-gray-100 w-80">
                            <div class="font-medium break-words whitespace-normal"
                                title="{{ $procurement->procurement_program_project }}">
                                {{ $procurement->procurement_program_project }}
                            </div>
                        </td>

                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            @if ($procurement->procurement_type === 'perLot')
                                @if ($procurement->currentPrStage && $procurement->currentPrStage->procurementStage)
                                    {{ $procurement->currentPrStage->procurementStage->procurementstage }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic">No Stage</span>
                                @endif
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">See items ↓</span>
                            @endif
                        </td>
                        @php
                            $remarks = $procurement->currentLotRemark->remark->remarks ?? '';

                            $remarksColor = match (true) {
                                str_contains($remarks, 'Ongoing')
                                    => 'from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-300 dark:from-yellow-900/40 dark:to-yellow-800/40 dark:text-yellow-200 dark:border-yellow-700',
                                str_contains($remarks, 'Awarded')
                                    => 'from-green-100 to-green-200 text-green-800 border-green-300 dark:from-green-900/40 dark:to-green-800/40 dark:text-green-200 dark:border-green-700',
                                str_contains($remarks, 'Cancelled')
                                    => 'from-red-100 to-red-200 text-red-800 border-red-300 dark:from-red-900/40 dark:to-red-800/40 dark:text-red-200 dark:border-red-700',
                                default
                                    => 'from-gray-100 to-gray-200 text-gray-800 border-gray-300 dark:from-neutral-700 dark:to-neutral-600 dark:text-white dark:border-neutral-600',
                            };

                            $remarkIcon = match (true) {
                                str_contains($remarks, 'Ongoing')
                                    => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>',
                                str_contains($remarks, 'Awarded')
                                    => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                                str_contains($remarks, 'Cancelled')
                                    => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                                default => '',
                            };
                        @endphp
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200 w-40">
                            @if ($procurement->procurement_type === 'perLot')
                                @if ($procurement->currentLotRemark && $procurement->currentLotRemark->remark)
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r {{ $remarksColor }} border shadow-sm">
                                        {!! $remarkIcon !!}
                                        {{ $remarks }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 italic">No Remark</span>
                                @endif
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">See items ↓</span>
                            @endif
                        </td>

                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            <span class="font-medium">{{ $procurement->date_receipt }}</span>
                        </td>
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            {{ $procurement->category?->bacType?->abbreviation ?? 'N/A' }}
                        </td>
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            {{ $procurement->division->abbreviation }}
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
                            <td colspan="13"
                                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800 p-4">
                                <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-inner">
                                    <table
                                        class="w-full text-sm border border-gray-300 dark:border-neutral-700 rounded-lg overflow-hidden">
                                        <thead
                                            class="bg-gradient-to-r from-gray-200 to-gray-300 dark:from-neutral-900 dark:to-neutral-800">
                                            <tr>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-2">
                                                    Item #
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-md">
                                                    Item Description
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-7">
                                                    PR Stage
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-48">
                                                    Remarks
                                                </th>
                                                <th
                                                    class="px-2 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-10">
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
                                                        @if ($item->prstage && $item->prstage->stage)
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                {{ $item->prstage->stage->procurementstage }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="text-gray-400 dark:text-gray-500 text-xs italic">No
                                                                Stage</span>
                                                        @endif
                                                    </td>
                                                    @php
                                                        $remarks = $item->currentItemRemark->remark->remarks ?? '';

                                                        $remarksColor = match (true) {
                                                            str_contains($remarks, 'Ongoing')
                                                                => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-800',
                                                            str_contains($remarks, 'Awarded')
                                                                => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-800',
                                                            str_contains($remarks, 'Cancelled')
                                                                => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-800',
                                                            default
                                                                => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600',
                                                        };
                                                    @endphp
                                                    <td
                                                        class="px-2 py-3 text-center text-sm text-gray-700 dark:text-gray-200">
                                                        @if ($item->currentItemRemark && $item->currentItemRemark->remark)
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $remarksColor }}">
                                                                {{ $remarks }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="text-gray-400 dark:text-gray-500 text-xs italic">No
                                                                Remark</span>
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
                                                    <td colspan="5"
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
    <livewire:procurements.procurement-view-page />
    <x-forms.pdf-viewer />
</div>
