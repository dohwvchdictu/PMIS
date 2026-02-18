<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">PMU - Forwarded Procurements</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Procurements forwarded to PMU grouped by Notice
                    of Award Number</p>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div
        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">

        <!-- Search and Filters -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex items-center gap-4">
                <!-- Search Bar -->
                <div class="relative flex-1 max-w-md">
                    <input type="text" wire:model.live="search"
                        placeholder="Search by PR Number, NOA Number, Division..."
                        class="w-full px-4 py-2.5 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                    <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Division Filter -->
                <select wire:model.live="divisionFilter"
                    class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600">
                    <option value="">All Divisions</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->divisions }}</option>
                    @endforeach
                </select>

                <!-- Reset Button -->
                <button wire:click="resetFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all dark:bg-neutral-700 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Notice of Award Number
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            PR Count
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Total ABC
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Date Range
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                    @forelse ($groupedItems as $group)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $group->notice_of_award_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    {{ $group->procurement_count }} PR(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                                ₱ {{ number_format($group->total_abc, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if ($group->earliest_date == $group->latest_date)
                                    {{ \Carbon\Carbon::parse($group->earliest_date)->format('M d, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($group->earliest_date)->format('M d, Y') }} -
                                    {{ \Carbon\Carbon::parse($group->latest_date)->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('view_pmu')
                                        <a href="{{ route('pmu.view', $group->notice_of_award_number) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No procurements forwarded to PMU
                                    </p>
                                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Try adjusting your search
                                        or
                                        filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($groupedItems->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $groupedItems->links() }}
            </div>
        @endif
    </div>
</div>
