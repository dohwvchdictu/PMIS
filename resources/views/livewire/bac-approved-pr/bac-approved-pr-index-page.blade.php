<div
    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col">

    <!-- Enhanced Header with Expandable Filters -->
    <div class="sticky top-0 z-40 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 w-full"
        x-data="{ showFilters: false }">
        <!-- Single Row: Search and Add Button -->
        <div class="px-6 py-4 flex items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by PR No. or Project..."
                    class="w-full px-4 py-2.5 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Add Button -->
            <div class="flex items-center gap-2">
                @can('create_b::a::c::approved::p::r')
                    <a href="{{ route('bac-approved-pr.create') }}"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add
                    </a>
                @endcan
            </div>
        </div>
    </div>
    <!-- Enhanced Table Section -->
    <div class="overflow-auto flex-1">
        <table class="table-fixed w-full min-w-[1100px] divide-y divide-gray-200 dark:divide-neutral-700">
            <thead
                class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-10">
                <tr>
                    <th class="px-2 py-1 sticky left-0 z-40 bg-gray-100 dark:bg-neutral-900 w-12"></th>

                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[48px] z-30 bg-gray-100 dark:bg-neutral-900 w-24">
                        PR Number
                    </th>

                    <th
                        class="px-2 py-1 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[104px] z-20 bg-gray-100 dark:bg-neutral-900 w-96">
                        Procurement Program / Project
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                        Cluster / Committee
                    </th>
                    <th
                        class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                        ABC Amount
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800">
                @forelse ($approvedPrs as $pr)
                    <!-- Enhanced Main Row with Alternating Colors -->
                    <tr
                        class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-gray-50/50 dark:bg-neutral-900/50' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20 transition-all duration-200 group">
                        <td
                            class="px-1 py-4 text-center sticky left-0 z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-black dark:text-white">
                            <div class="flex items-center justify-center">
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
                                                @can('view_b::a::c::approved::p::r')
                                                    <li>
                                                        <a href="{{ $pr->filepath }}" target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="w-full flex items-center gap-1 text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 text-green-600">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                class="size-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                            </svg>
                                                            View PR
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('edit_b::a::c::approved::p::r')
                                                    <li>
                                                        <a href="{{ route('bac-approved-pr.edit', [
                                                            'bacapprovedpr' => $pr->procID,
                                                            'search' => $search,
                                                            'perPage' => $perPage,
                                                            'page' => $approvedPrs->currentPage(),
                                                        ]) }}"
                                                            @click="open = false"
                                                            class="w-full flex items-center gap-1 text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 text-amber-600">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 9.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                                                            </svg>
                                                            Edit
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
                            class="px-2 py-4 text-center text-sm font-bold sticky left-[48px] z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-emerald-700 dark:text-emerald-300 w-24">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-s">
                                {{ $pr->procurement->pr_number ?? 'N/A' }}
                            </span>
                        </td>
                        <td
                            class="px-3 py-4 text-left text-xs sticky left-[104px] z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-900 dark:text-gray-100 w-96">
                            <div class="font-medium break-words whitespace-normal"
                                title="{{ $pr->procurement->procurement_program_project ?? 'N/A' }}">
                                {{ $pr->procurement->procurement_program_project ?? 'N/A' }}
                            </div>
                        </td>
                        <td
                            class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                            {{ $pr->procurement->clusterCommittee->clustercommittee ?? 'N/A' }}
                        </td>
                        <td
                            class="px-3 py-4 pr-4 text-right text-sm font-bold {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-900 dark:text-white">
                            <div class="inline-flex items-baseline gap-0.5">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                <span
                                    class="text-emerald-700 dark:text-emerald-400">{{ number_format($pr->procurement->abc ?? 0, 2) }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- This will be shown if '$approvedPrs' is empty --}}
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500 dark:text-neutral-400">
                            No records found.
                        </td>
                    </tr>
                @endforelse
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
                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $approvedPrs->firstItem() }}</span>
                to
                <span
                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $approvedPrs->lastItem() }}</span>
                of
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $approvedPrs->total() }}</span>
                items
            </div>
            <div class="flex justify-center">
                {{ $approvedPrs->links('vendor.pagination.tailwind') }}
            </div>
        </div>

    </div>


</div>
