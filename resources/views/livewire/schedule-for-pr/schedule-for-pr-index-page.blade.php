<div x-data="{ showTypeModal: false }">
    <div
        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col">

        <!-- Enhanced Header -->
        <div
            class="sticky top-0 z-40 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 w-full">
            <!-- Single Row: Search and Add Button -->
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <!-- Search Bar -->
                <div class="relative flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by IB No. or Project Name..."
                        class="w-full px-4 py-2.5 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                    <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Schedule Button -->
                <div class="flex items-center gap-2">
                    @can('create_schedule::for::procurement')
                        <button type="button" @click="showTypeModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all duration-200 shadow-sm hover:shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Schedule
                        </button>
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
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[48px] z-30 bg-gray-100 dark:bg-neutral-900 w-44">
                            IB Number
                        </th>
                        <th
                            class="px-1 py-1 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[224px] z-20 bg-gray-100 dark:bg-neutral-900 w-32">
                            Opening of Bids
                        </th>
                        <th
                            class="px-2 py-1 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider sticky left-[352px] z-20 bg-gray-100 dark:bg-neutral-900 w-sm">
                            Name of Project
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                            Framework
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-34">
                            Bidding Status
                        </th>

                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                            ABC Amount
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                            2%
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                            5%
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                            30%
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-28">
                            Action Taken
                        </th>
                        <th
                            class="px-1 py-1 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-32">
                            Next Bidding
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-neutral-800">
                    @forelse ($schedules as $schedule)
                        <tr wire:key="schedule-{{ $schedule->id }}"
                            class="border-b border-gray-100 dark:border-neutral-700 {{ $loop->even ? 'bg-gray-50/50 dark:bg-neutral-900/50' : 'bg-white dark:bg-neutral-800' }} hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20 transition-all duration-200 group">
                            {{-- Col 1: Actions --}}
                            <td
                                class="px-1 py-4 text-center sticky left-0 z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-black dark:text-white">
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
                                                @can('view_schedule::for::procurement')
                                                    <li>
                                                        <a href="{{ $schedule->google_drive_link }}" target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="w-full flex items-center gap-1 text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 text-green-600">

                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                class="size-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                            </svg>
                                                            View File
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('edit_schedule::for::procurement')
                                                    <li>
                                                        <a href="{{ route('schedule-for-procurement.edit', [
                                                            'id' => $schedule->id,
                                                            'search' => $search,
                                                            'perPage' => $perPage,
                                                            'page' => $schedules->currentPage(),
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
                            </td>
                            <td
                                class="px-2 py-4 text-center text-sm font-bold sticky left-[48px] z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-emerald-700 dark:text-emerald-300">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-s">
                                    {{ $schedule->ib_number }}
                                </span>
                            </td>
                            <td
                                class="px-3 py-4 text-center text-sm sticky left-[224px] z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                {{ optional($schedule->opening_of_bids)->format('M d, Y') }}
                            </td>
                            <td
                                class="px-3 py-4 text-left text-sm sticky left-[352px] z-20 {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-900 dark:text-gray-100">
                                <div class="font-medium break-words whitespace-normal"
                                    title="{{ $schedule->project_name }}">
                                    {{ $schedule->project_name }}
                                </div>
                            </td>
                            <td
                                class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-neutral-200">
                                @if ($schedule->is_framework)
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
                                        <span class="text-xs font-medium text-red-700 dark:text-red-300">No</span>
                                    </div>
                                @endif
                            </td>
                            @php
                                $status = $schedule->biddingStatus?->name ?? '';

                                $statusColor = match (true) {
                                    $status === 'Awarded'
                                        => 'from-lime-100 to-lime-200 text-lime-800 border-lime-300 dark:from-lime-900/40 dark:to-lime-800/40 dark:text-lime-200 dark:border-lime-700',
                                    str_contains($status, 'Failed')
                                        => 'from-red-100 to-red-200 text-red-800 border-red-300 dark:from-red-900/40 dark:to-red-800/40 dark:text-red-200 dark:border-red-700',
                                    str_contains($status, 'For Checking')
                                        => 'from-rose-100 to-rose-200 text-rose-800 border-rose-300 dark:from-rose-900/40 dark:to-rose-800/40 dark:text-rose-200 dark:border-rose-700',
                                    str_contains($status, 'On Hold') || str_contains($status, 'On-Hold')
                                        => 'from-slate-100 to-slate-200 text-slate-800 border-slate-300 dark:from-slate-900/40 dark:to-slate-800/40 dark:text-slate-200 dark:border-slate-700',
                                    $status === 'For Posting'
                                        => 'from-blue-100 to-blue-200 text-blue-800 border-blue-300 dark:from-blue-900/40 dark:to-blue-800/40 dark:text-blue-200 dark:border-blue-700',
                                    $status === 'Posted'
                                        => 'from-sky-100 to-sky-200 text-sky-800 border-sky-300 dark:from-sky-900/40 dark:to-sky-800/40 dark:text-sky-200 dark:border-sky-700',
                                    str_contains($status, 'Evaluation')
                                        => 'from-orange-100 to-orange-200 text-orange-800 border-orange-300 dark:from-orange-900/40 dark:to-orange-800/40 dark:text-orange-200 dark:border-orange-700',
                                    $status === 'For Bid Docs' || $status === 'For Biddocs'
                                        => 'from-amber-100 to-amber-200 text-amber-800 border-amber-300 dark:from-amber-900/40 dark:to-amber-800/40 dark:text-amber-200 dark:border-amber-700',
                                    str_contains($status, 'Post-Qualified')
                                        => 'from-emerald-100 to-emerald-200 text-emerald-800 border-emerald-300 dark:from-emerald-900/40 dark:to-emerald-800/40 dark:text-emerald-200 dark:border-emerald-700',
                                    $status === 'Partially Awarded'
                                        => 'from-green-100 to-green-200 text-green-800 border-green-300 dark:from-green-900/40 dark:to-green-800/40 dark:text-green-200 dark:border-green-700',
                                    default
                                        => 'from-gray-100 to-gray-200 text-gray-800 border-gray-300 dark:from-neutral-700 dark:to-neutral-600 dark:text-gray-200 dark:border-neutral-600',
                                };
                            @endphp

                            <td
                                class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r {{ $statusColor }} border shadow-sm">
                                    {{ $status }}
                                </span>
                            </td>


                            <td
                                class="px-3 py-4 text-right text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                <div class="inline-flex items-baseline gap-0.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                    <span
                                        class="text-emerald-700 dark:text-emerald-400">{{ number_format($schedule->ABC, 2) }}</span>
                                </div>
                            </td>
                            <td
                                class="px-3 py-4 text-right text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                <div class="inline-flex items-baseline gap-0.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                    <span
                                        class="text-emerald-700 dark:text-emerald-400">{{ number_format($schedule->two_percent, 2) }}</span>
                                </div>
                            </td>
                            <td
                                class="px-3 py-4 text-right text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                <div class="inline-flex items-baseline gap-0.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                    <span
                                        class="text-emerald-700 dark:text-emerald-400">{{ number_format($schedule->five_percent, 2) }}</span>
                                </div>
                            </td>
                            <td
                                class="px-3 py-4 text-right text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                <div class="inline-flex items-baseline gap-0.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">₱</span>
                                    <span
                                        class="text-emerald-700 dark:text-emerald-400">{{ number_format($schedule->thirty_percent, 2) }}</span>
                                </div>
                            </td>
                            <td
                                class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                {{ $schedule->action_taken }}
                            </td>
                            <td
                                class="px-3 py-4 text-center text-sm {{ $loop->even ? 'bg-gray-50 dark:bg-neutral-900' : 'bg-white dark:bg-neutral-800' }} group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 text-gray-700 dark:text-gray-200">
                                {{ optional($schedule->next_bidding_schedule)->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-gray-500 dark:text-neutral-400">
                                No bidding schedules found.
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
                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $schedules->firstItem() }}</span>
                    to
                    <span
                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $schedules->lastItem() }}</span>
                    of
                    <span
                        class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $schedules->total() }}</span>
                    items
                </div>
                <div class="flex justify-center">
                    {{ $schedules->links('vendor.pagination.tailwind') }}
                </div>
            </div>

        </div>

        <div @keydown.escape.window="showTypeModal = false" x-show="showTypeModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.outside="showTypeModal = false"
                class="bg-white shadow-xl w-full max-w-md rounded-2xl dark:bg-neutral-800">
                <div
                    class="flex justify-between items-center px-4 py-2 bg-emerald-600 text-white font-semibold dark:bg-neutral-900 rounded-t-2xl">
                    <h2 class="text-lg font-semibold">Select Procurement Type</h2>
                    <button @click="showTypeModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-white/70 hover:text-white transition">✕</button>
                </div>
                <div class="p-6 text-center">
                    <p class="text-gray-700 dark:text-neutral-300">Please choose the type of procurement you are
                        creating schedule for.</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 rounded-b-2xl flex justify-center gap-x-4">
                    <a href="{{ route('schedule-for-procurement.create', ['type' => 'perLot']) }}"
                        class="py-2 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-emerald-600 text-white hover:bg-emerald-700">Per
                        Lot</a>
                    <a href="{{ route('schedule-for-procurement.create', ['type' => 'perItem']) }}"
                        class="py-2 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-sky-600 text-white hover:bg-sky-700">Per
                        Item</a>
                </div>
            </div>
        </div>
    </div>
</div>
