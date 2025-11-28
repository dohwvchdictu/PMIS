<div
    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col">

    <div
        class="sticky top-0 z-20 bg-white px-6 py-4 border-b border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 w-full">

        <div class="flex items-center gap-x-2 flex-wrap">
            <!-- Search Input -->
            <div class="relative">
                <input type="text" wire:model.live="search" placeholder="Search Procurements..."
                    class="px-4 py-2 border border-gray-300 rounded-lg w-80 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-700" />
                <svg class="absolute right-3 top-2.5 text-gray-500 dark:text-white" width="20" height="20"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M21 21l-4.35-4.35" />
                    <circle cx="10" cy="10" r="7" />
                </svg>
            </div>

            @can('create_procurement')
                <a href="{{ route('procurements.create') }}"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-hidden focus:bg-emerald-700">
                    <svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Procurement
                </a>
            @endcan

            <!-- Division Filter -->
            <div class="relative group">
                <select wire:model.live="divisionFilter"
                    class="pl-3 pr-12 py-2 text-sm font-medium border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 appearance-none cursor-pointer hover:border-emerald-400 hover:shadow-sm dark:hover:border-emerald-500 transition-all duration-200 {{ $divisionFilter ? 'bg-blue-50 border-emerald-400 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-300' : 'bg-white dark:bg-neutral-800' }}">
                    <option value="">Division</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->abbreviation }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-0.5 pointer-events-none">
                    @if ($divisionFilter)
                        <button wire:click="$set('divisionFilter', '')" type="button"
                            class="pointer-events-auto p-0.5 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors"
                            onclick="event.stopPropagation();">
                            <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                    <svg class="w-4 h-4 {{ $divisionFilter ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Cluster/Committee Filter -->
            <div class="relative group">
                <select wire:model.live="clusterCommitteeFilter"
                    class="pl-3 pr-12 py-2 text-sm font-medium border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 appearance-none cursor-pointer hover:border-emerald-400 hover:shadow-sm dark:hover:border-emerald-500 transition-all duration-200 {{ $clusterCommitteeFilter ? 'bg-blue-50 border-emerald-400 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-300' : 'bg-white dark:bg-neutral-800' }}">
                    <option value="">Cluster</option>
                    @foreach ($clusterCommittees as $cluster)
                        <option value="{{ $cluster->id }}">{{ $cluster->clustercommittee }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-0.5 pointer-events-none">
                    @if ($clusterCommitteeFilter)
                        <button wire:click="$set('clusterCommitteeFilter', '')" type="button"
                            class="pointer-events-auto p-0.5 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors"
                            onclick="event.stopPropagation();">
                            <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                    <svg class="w-4 h-4 {{ $clusterCommitteeFilter ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- End User Filter -->
            <div class="relative group">
                <select wire:model.live="endUserFilter"
                    class="pl-3 pr-12 py-2 text-sm font-medium border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 appearance-none cursor-pointer hover:border-emerald-400 hover:shadow-sm dark:hover:border-emerald-500 transition-all duration-200 {{ $endUserFilter ? 'bg-emerald-50 border-emerald-400 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-300' : 'bg-white dark:bg-neutral-800' }}">
                    <option value="">End User</option>
                    @foreach ($endUsers as $endUser)
                        <option value="{{ $endUser->id }}">{{ $endUser->endusers }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-0.5 pointer-events-none">
                    @if ($endUserFilter)
                        <button wire:click="$set('endUserFilter', '')" type="button"
                            class="pointer-events-auto p-0.5 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors"
                            onclick="event.stopPropagation();">
                            <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                    <svg class="w-4 h-4 {{ $endUserFilter ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Source of Fund Filter -->
            <div class="relative group">
                <select wire:model.live="fundSourceFilter"
                    class="pl-3 pr-12 py-2 text-sm font-medium border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 appearance-none cursor-pointer hover:border-emerald-400 hover:shadow-sm dark:hover:border-emerald-500 transition-all duration-200 {{ $fundSourceFilter ? 'bg-emerald-50 border-emerald-400 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-300' : 'bg-white dark:bg-neutral-800' }}">
                    <option value="">Fund</option>
                    @foreach ($fundSources as $fundSource)
                        <option value="{{ $fundSource->id }}">{{ $fundSource->fundsources }}</option>
                    @endforeach
                </select>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-0.5 pointer-events-none">
                    @if ($fundSourceFilter)
                        <button wire:click="$set('fundSourceFilter', '')" type="button"
                            class="pointer-events-auto p-0.5 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors"
                            onclick="event.stopPropagation();">
                            <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                    <svg class="w-4 h-4 {{ $fundSourceFilter ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

        </div>
    </div>

    <div class="overflow-auto flex-1">
        <table class="table-fixed w-full min-w-[1200px] divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-200 dark:bg-neutral-900 sticky top-0 z-10">
                <tr>
                    <th class="px-2 py-2 sticky left-0 z-30 bg-gray-200 dark:bg-neutral-900 w-12"></th>

                    <th
                        class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white uppercase sticky left-[32px] z-20 bg-gray-200 dark:bg-neutral-900 w-24">
                        PR Number
                    </th>

                    <th
                        class="px-1 py-1 text-left text-xs font-medium text-black dark:text-white uppercase sticky left-[128px] z-10 bg-gray-200 dark:bg-neutral-900 w-md">
                        Procurement Program / Project
                    </th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-40">
                        Stage
                    </th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-20">
                        Remarks
                    </th>
                    <th class="px-1 py-1 text-center text-xs  font-medium text-black dark:text-white w-28">
                        Date Receipt</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-24">
                        BAC Category</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-20">
                        Division</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-32">
                        Cluster / Committee</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-36">
                        Category</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-28">
                        Early Procurement</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-36">
                        Source of Funds</th>
                    <th class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-32">
                        ABC Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                @foreach ($procurements as $procurement)

                    <!-- Main Row -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                        <td
                            class="px-1 py-1 text-center sticky left-0 bg-white text-black dark:text-white dark:bg-neutral-800">
                            <div class="flex items-center justify-center">
                                <!-- Expand/Collapse Button (only for perItem) -->
                                @if ($procurement->procurement_type === 'perItem')
                                    <button type="button"
                                        wire:click.stop="toggle('expandedProcurementId', '{{ $procurement->procID }}')"
                                        class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-neutral-600">
                                        @if ($expandedProcurementId === $procurement->procID)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600"
                                                fill="none" viewBox="0 0 22 22" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600"
                                                fill="none" viewBox="0 0 22 22" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                    </button>
                                @endif

                                <!-- Alpine action dropdown -->
                                <div x-data="{ open: false }" class="relative inline-block" x-ref="menuWrapper">
                                    <button @click="open = !open" @click.away="open = false"
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full hover:bg-gray-200 dark:hover:bg-neutral-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                        </svg>
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="open" x-transition @click.away="open = false"
                                            class="absolute z-[9999] bg-white border border-gray-200 rounded shadow-lg dark:bg-neutral-800 dark:border-neutral-700"
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
                                                            class="w-full flex items-center gap-1 text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-neutral-700 text-blue-500">
                                                            <x-heroicon-o-eye class="w-4 h-4 text-blue-500" />
                                                            View
                                                        </button>
                                                    </li>
                                                @endcan
                                                @can('view_procurement')
                                                    @if (!empty($procurement->bacApprovedPr?->filepath))
                                                        <li>
                                                            <a href="{{ $procurement->bacApprovedPr->filepath }}"
                                                                target="_blank" rel="noopener noreferrer"
                                                                class="w-full flex items-center gap-1 text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-neutral-700 text-green-600">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                                </svg>
                                                                View PR
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endcan
                                                @can('edit_procurement')
                                                    <li>
                                                        <a href="{{ route('procurements.edit', $procurement->procID) }}"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-1 text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-neutral-700 text-amber-600">
                                                            <x-heroicon-o-pencil class="w-4 h-4 text-amber-600" /> Edit
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('update_procurement')
                                                    <li>
                                                        <a href="{{ route('procurements.update_status', $procurement->procID) }}"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-1 text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-neutral-700 text-amber-400">
                                                            <x-heroicon-o-pencil class="w-4 h-4 text-amber-400" /> Update
                                                            Status
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
                            class="px-1 py-1 text-center text-sm sticky left-[32px] z-20 bg-white dark:bg-neutral-800 text-black dark:text-white">
                            {{ $procurement->pr_number }}
                        </td>

                        <td
                            class="px-1 py-1 text-left text-sm sticky left-[128px] z-10 bg-white dark:bg-neutral-800 text-black dark:text-white">
                            {{ $procurement->procurement_program_project }}
                        </td>

                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            @if ($procurement->procurement_type === 'perLot')
                                {{-- Show stage for perLot --}}
                                @if ($procurement->currentPrStage && $procurement->currentPrStage->procurementStage)
                                    <span>
                                        {{ $procurement->currentPrStage->procurementStage->procurementstage }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 text-xs">No Stage</span>
                                @endif
                            @else
                                {{-- For perItem, show in expanded rows --}}
                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">See items</span>
                            @endif
                        </td>
                        @php
                            $remarks = $procurement->currentLotRemark->remark->remarks ?? '';

                            $remarksColor = match (true) {
                                str_contains($remarks, 'Ongoing') => 'bg-yellow-400 text-black',
                                str_contains($remarks, 'Awarded') => 'bg-green-600 text-white',
                                str_contains($remarks, 'Cancelled') => 'bg-black text-white',
                                default => 'bg-gray-200 text-gray-800 dark:bg-neutral-700 dark:text-white',
                            };
                        @endphp
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            @if ($procurement->procurement_type === 'perLot')
                                {{-- Show remark for perLot --}}
                                @if ($procurement->currentLotRemark && $procurement->currentLotRemark->remark)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold {{ $remarksColor }}">
                                        {{ $remarks }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 text-xs">No Remark</span>
                                @endif
                            @else
                                {{-- For perItem, show in expanded rows --}}
                                <span class="text-gray-400 dark:text-gray-500 text-xs italic">See items</span>
                            @endif

                        </td>

                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            {{ $procurement->date_receipt }}
                        </td>
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            {{ $procurement->category?->bacType?->abbreviation ?? '' }}
                        </td>
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            {{ $procurement->division->abbreviation }}
                        </td>
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            {{ $procurement->clusterCommittee->clustercommittee }}
                        </td>
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            {{ $procurement->category->category }}
                        </td>
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-neutral-200">
                            @if ($procurement->early_procurement)
                                <x-heroicon-s-check-circle title="Yes" class="h-5 w-5 text-emerald-600 mx-auto" />
                            @else
                                <x-heroicon-s-x-circle title="No" class="h-5 w-5 text-red-600 mx-auto" />
                            @endif
                        </td>
                        <td class="px-1 py-1 text-center text-sm text-black dark:text-white">
                            {{ $procurement->fundSource ? $procurement->fundSource->fundsources : '' }}
                        </td>
                        <td class="px-1 py-1 pr-4 text-right text-sm text-black dark:text-white relative">
                            <span class="text-black dark:text-white">₱</span>
                            <span>{{ number_format($procurement->abc ?? 0, 2) }}</span>
                        </td>
                    </tr>

                    <!-- Expanded Per Item Rows -->
                    @if ($procurement->procurement_type === 'perItem' && $expandedProcurementId == $procurement->procID)
                        <tr>
                            <td colspan="13" class="bg-gray-50 dark:bg-neutral-900">
                                <div>
                                    <table class="w-full text-sm border border-gray-300 dark:border-neutral-700">
                                        <thead class="bg-gray-200 dark:bg-neutral-900">
                                            <tr>
                                                <th
                                                    class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-2">
                                                    Item #
                                                </th>
                                                <th
                                                    class="px-1 py-1 text-left text-xs font-medium text-black dark:text-white w-md">
                                                    Item Description
                                                </th>
                                                <th
                                                    class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-7">
                                                    PR Stage
                                                </th>
                                                <th
                                                    class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-48">
                                                    Remarks
                                                </th>
                                                <th
                                                    class="px-1 py-1 text-center text-xs font-medium text-black dark:text-white w-10">
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
                                                <tr>
                                                    <td
                                                        class="px-1 py-1 text-center text-sm text-black dark:text-white">
                                                        {{ $item->item_no }}
                                                    </td>
                                                    <td class="px-1 py-1 text-left text-sm text-black dark:text-white">
                                                        {{ $item->description }}
                                                    </td>
                                                    <td
                                                        class="px-1 py-1 text-center text-sm text-black dark:text-white">
                                                        @if ($item->prstage && $item->prstage->stage)
                                                            <span>
                                                                {{ $item->prstage->stage->procurementstage }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400 dark:text-gray-500 text-xs">No
                                                                Stage</span>
                                                        @endif
                                                    </td>
                                                    @php
                                                        $remarks = $item->currentItemRemark->remark->remarks ?? '';

                                                        $remarksColor = match (true) {
                                                            str_contains($remarks, 'Ongoing')
                                                                => 'bg-yellow-400 text-black',
                                                            str_contains($remarks, 'Awarded')
                                                                => 'bg-green-600 text-white',
                                                            str_contains($remarks, 'Cancelled')
                                                                => 'bg-black text-white',
                                                            default
                                                                => 'bg-gray-200 text-gray-800 dark:bg-neutral-700 dark:text-white',
                                                        };
                                                    @endphp
                                                    <td
                                                        class="px-1 py-1 text-center text-sm text-black dark:text-white">
                                                        @if ($item->currentItemRemark && $item->currentItemRemark->remark)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold {{ $remarksColor }}">
                                                                {{ $remarks }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400 dark:text-gray-500 text-xs">No
                                                                Remark</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="px-1 py-1 text-center text-sm text-black dark:text-white">
                                                        ₱{{ number_format($item->amount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5"
                                                        class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        No items found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    {{-- Items Pagination --}}
                                    @if ($totalItems > $itemsPerPage)
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-2 border-t border-gray-200 dark:border-neutral-700 gap-2 sm:gap-4 bg-white dark:bg-neutral-800">

                                            {{-- Left: Per-page selector --}}
                                            <div class="flex items-center gap-x-2 sm:justify-start w-full sm:w-auto">
                                                <label for="itemsPerPage_{{ $procurement->procID }}"
                                                    class="text-xs text-gray-600 dark:text-gray-300">Show</label>
                                                <select id="itemsPerPage_{{ $procurement->procID }}"
                                                    wire:model.live="itemsPerPage"
                                                    class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-700">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </div>

                                            {{-- Center: Summary + Pagination --}}
                                            <div class="flex flex-col items-center justify-center w-full">
                                                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                                    Showing {{ $offset + 1 }} to
                                                    {{ min($offset + $itemsPerPage, $totalItems) }} of
                                                    {{ $totalItems }} items
                                                </div>

                                                {{-- Pagination Links --}}
                                                @if ($totalPages > 1)
                                                    <div class="flex justify-center gap-1 mt-2">
                                                        {{-- Previous Button --}}
                                                        @if ($currentPage > 1)
                                                            <a href="?itemsPage_{{ $procurement->procID }}={{ $currentPage - 1 }}"
                                                                class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100 dark:border-neutral-700 dark:hover:bg-neutral-700 dark:text-white">
                                                                Previous
                                                            </a>
                                                        @endif

                                                        {{-- Page Numbers --}}
                                                        @for ($i = 1; $i <= $totalPages; $i++)
                                                            <a href="?itemsPage_{{ $procurement->procID }}={{ $i }}"
                                                                class="px-2 py-1 text-xs border rounded {{ $i == $currentPage ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-700 dark:hover:bg-neutral-700 dark:text-white' }}">
                                                                {{ $i }}
                                                            </a>
                                                        @endfor

                                                        {{-- Next Button --}}
                                                        @if ($currentPage < $totalPages)
                                                            <a href="?itemsPage_{{ $procurement->procID }}={{ $currentPage + 1 }}"
                                                                class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100 dark:border-neutral-700 dark:hover:bg-neutral-700 dark:text-white">
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

    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-2 border-t border-gray-200 dark:border-neutral-700 gap-2 sm:gap-4">

        {{-- Left: Per-page selector --}}
        <div class="flex items-center gap-x-2 sm:justify-start w-full sm:w-auto">
            <label for="perPage" class="text-xs text-gray-600 dark:text-gray-300">Show</label>
            <select id="perPage" wire:model.live="perPage"
                class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-700">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        {{-- Center: Summary + Pagination --}}
        <div class="flex flex-col items-center justify-center w-full">
            <div class="text-xs text-gray-500 text-center">
                Showing {{ $procurements->firstItem() }} to {{ $procurements->lastItem() }} of
                {{ $procurements->total() }} items
            </div>
            <div class="flex justify-center">
                {{ $procurements->links('vendor.pagination.tailwind') }}
            </div>
        </div>

    </div>
    {{-- @if ($showEarlyPrompt)
        <div
            class="fixed inset-0 flex items-center justify-center bg-emerald-600/20  z-50 backdrop-blur-sm dark:bg-neutral-700/20">
            <div
                class="bg-white rounded-xl shadow-lg p-6 w-96 text-center dark:bg-neutral-700 dark:border-neutral-700">
                <h2 class="text-lg font-bold mb-4 text-black dark:text-white">Is this an Early
                    Procurement?
                </h2>

                <div class="flex justify-center gap-4">
                    <button wire:click="confirmEarly(false)" class="px-4 py-2 bg-red-500 text-white rounded-lg">
                        No
                    </button>
                    <button wire:click="confirmEarly(true)" class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                        Yes
                    </button>
                </div>
            </div>
        </div>
    @endif --}}

    {{-- ViewModal --}}

    <livewire:procurements.procurement-view-page />
    <x-forms.pdf-viewer />
    <!-- End Card -->
</div>
