<div>
    <div
        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col mb-6">

        <!-- Search Bar -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="relative max-w-md">
                <input type="text" wire:model.live="search" placeholder="Search by PR Number, NOA Number, Division..."
                    class="w-full px-4 py-2.5 pl-10 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:placeholder-gray-400" />
                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Enhanced Table Section -->
        <div class="overflow-auto flex-1">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
                    <tr>
                        <th class="px-2 py-1 bg-gray-100 dark:bg-neutral-900 w-16"></th>
                        <th class="px-2 py-1 bg-gray-100 dark:bg-neutral-900 w-16"></th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            Notice of Award Number
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            Date Forwarded
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            Contract Amount
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            PO / Contract Number
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            Contract Signing Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            Notice to Proceed Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                            Remarks
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                    @forelse ($groupedItems as $group)
                        <!-- Main Row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                            <!-- Toggle Button with PR Count Badge -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button wire:click="toggle('expandedNoaNumber', '{{ $group->notice_of_award_number }}')"
                                    class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                    <svg class="w-5 h-5 transition-transform {{ $expandedNoaNumber === $group->notice_of_award_number ? 'rotate-90' : '' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span
                                        class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 min-w-[20px]">
                                        {{ $group->procurement_count }}
                                    </span>
                                </button>
                            </td>
                            <!-- Actions -->
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
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
                                                @can('view_pmu')
                                                    <li>
                                                        <button
                                                            wire:click="openViewModal('{{ $group->notice_of_award_number }}')"
                                                            class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 dark:hover:from-blue-900/30 dark:hover:to-blue-800/30 text-blue-600 dark:text-blue-400 transition-all duration-150 group/item">
                                                            <x-heroicon-o-eye
                                                                class="w-4 h-4 group-hover/item:scale-110 transition-transform" />
                                                            <span class="font-medium">View Details</span>
                                                        </button>
                                                    </li>
                                                @endcan
                                                @can('update_pmu')
                                                    <li>
                                                        <a href="{{ route('pmu.edit', $group->notice_of_award_number) }}"
                                                            class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-amber-50 hover:to-amber-100 dark:hover:from-amber-900/30 dark:hover:to-amber-800/30 text-amber-600 dark:text-amber-400 transition-all duration-150 group/item">
                                                            <x-heroicon-o-pencil-square
                                                                class="w-4 h-4 group-hover/item:scale-110 transition-transform" />
                                                            <span class="font-medium">Update</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <!-- NOA Number -->
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $group->notice_of_award_number }}
                            </td>
                            <!-- Date Forwarded -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $group->date_forwarded ? \Carbon\Carbon::parse($group->date_forwarded)->format('M d, Y') : '—' }}
                            </td>
                            <!-- Contract Amount -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                                {{ $group->contract_amount ? '₱ ' . number_format($group->contract_amount, 2) : '—' }}
                            </td>
                            <!-- PO / Contract Number -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $group->po_contract_number ?? '—' }}
                            </td>
                            <!-- Contract Signing Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $group->contract_signing_date ? \Carbon\Carbon::parse($group->contract_signing_date)->format('M d, Y') : '—' }}
                            </td>
                            <!-- Notice to Proceed Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $group->notice_to_proceed_date ? \Carbon\Carbon::parse($group->notice_to_proceed_date)->format('M d, Y') : '—' }}
                            </td>
                            <!-- Remarks -->
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                <div class="truncate" title="{{ $group->remarks }}">
                                    {{ $group->remarks ?? '—' }}
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded Row with Procurements -->
                        @if ($expandedNoaNumber === $group->notice_of_award_number && $expandedPaginator)
                            <tr class="bg-gray-50 dark:bg-neutral-900">
                                <td colspan="9" class="px-6 py-4">
                                    <div class="space-y-4">

                                        <div class="overflow-x-auto">
                                            <table
                                                class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 rounded-lg overflow-hidden">
                                                <thead
                                                    class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            PR Number
                                                        </th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Title/Description
                                                        </th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            ABC Amount/Amount
                                                        </th>
                                                        <th class="px-2 py-1 w-16"></th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                                                    @forelse ($expandedPaginator as $procRow)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm font-medium text-emerald-700 dark:text-emerald-300">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs">
                                                                    {{ $procRow->pr_number }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                                <div class="font-medium break-words whitespace-normal">
                                                                    {{ $procRow->description }}
                                                                </div>
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white font-medium">
                                                                ₱ {{ number_format($procRow->abc, 2) }}
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                                                @can('view_procurement')
                                                                    <a href="{{ route('procurements.view', ['procurement' => $procRow->procID]) }}"
                                                                        target="_blank"
                                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                                        <svg class="w-5 h-5 inline" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4"
                                                                class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                                No items found.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                            {{-- Inner pagination --}}
                                            @if ($expandedPaginator->hasPages() || $expandedPaginator->total() > 0)
                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

                                                    <!-- Left: Per-page selector -->
                                                    <div class="flex items-center gap-x-2">
                                                        <label for="expandedPerPage"
                                                            class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
                                                        <select id="expandedPerPage" wire:model.live="expandedPerPage"
                                                            class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                        </select>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">per
                                                            page</span>
                                                    </div>

                                                    <!-- Center: Summary + Pagination -->
                                                    <div
                                                        class="flex flex-col items-center justify-center gap-3 flex-1">
                                                        <div
                                                            class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                            Showing
                                                            <span
                                                                class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $expandedPaginator->firstItem() ?? 0 }}</span>
                                                            to
                                                            <span
                                                                class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $expandedPaginator->lastItem() ?? 0 }}</span>
                                                            of
                                                            <span
                                                                class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $expandedPaginator->total() }}</span>
                                                            items
                                                        </div>

                                                        @if ($expandedPaginator->hasPages())
                                                            <div class="flex items-center gap-1">
                                                                <button
                                                                    wire:click="setExpandedPage({{ $expandedPaginator->currentPage() - 1 }})"
                                                                    @disabled($expandedPaginator->onFirstPage())
                                                                    class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                                                                    Previous
                                                                </button>
                                                                @for ($p = 1; $p <= $expandedPaginator->lastPage(); $p++)
                                                                    <button
                                                                        wire:click="setExpandedPage({{ $p }})"
                                                                        class="px-3 py-1.5 text-xs font-medium border rounded-lg transition-colors duration-150 {{ $p === $expandedPaginator->currentPage() ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white' }}">
                                                                        {{ $p }}
                                                                    </button>
                                                                @endfor
                                                                <button
                                                                    wire:click="setExpandedPage({{ $expandedPaginator->currentPage() + 1 }})"
                                                                    @disabled(!$expandedPaginator->hasMorePages())
                                                                    class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                                                                    Next
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No procurements forwarded to
                                        PMU
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

    {{-- View Details Modal --}}
    @if ($showViewModal)
        <x-forms.modal title="PMU Details — {{ $viewNoaNumber }}" size="max-w-4xl" closeMethod="closeViewModal"
            model="showViewModal">
            <div class="space-y-4 p-4" x-data="{ showLinkedPrs: false }">

                {{-- NOA Header Box --}}
                <div
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl shadow-sm overflow-hidden">
                    <div class="h-1 bg-emerald-600"></div>
                    <div class="flex flex-wrap items-stretch">

                        {{-- NOA Number — emerald panel --}}
                        <div class="bg-emerald-600 dark:bg-emerald-700 px-5 py-4 flex flex-col justify-center">
                            <p class="text-xs font-medium text-emerald-100 mb-0.5">Notice of Award No.</p>
                            <p class="text-lg font-bold text-white">{{ $viewNoaNumber ?? '—' }}</p>
                        </div>

                        {{-- Divider --}}
                        <div class="w-px bg-gray-200 dark:bg-neutral-600 hidden sm:block"></div>

                        {{-- Date Forwarded --}}
                        <div class="px-5 py-4 flex flex-col justify-center">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Date Forwarded</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $viewPmuRecord?->date_forwarded ? \Carbon\Carbon::parse($viewPmuRecord->date_forwarded)->format('M d, Y') : '—' }}
                            </p>
                        </div>

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- View PRs Toggle --}}
                        <div class="flex items-center px-4 py-4">
                            <button type="button" x-on:click="showLinkedPrs = !showLinkedPrs"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                View
                            </button>
                        </div>

                    </div>
                </div>

                {{-- Linked PRs / Items (collapsible) --}}
                <div x-show="showLinkedPrs" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

                    {{-- Section header --}}
                    <div
                        class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-neutral-600 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-800 dark:to-neutral-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Linked PRs /
                                Items</span>
                            @php $totalLinked = $modalPaginator?->total() ?? 0; @endphp
                            <span
                                class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                {{ $totalLinked }}
                            </span>
                        </div>
                        <button type="button" x-on:click="showLinkedPrs = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-600">
                            <thead
                                class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        PR Number</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Title / Description</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        ABC / Amount</th>
                                    <th class="px-4 py-3 w-12"></th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                                @forelse ($modalPaginator ?? [] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs text-emerald-700 dark:text-emerald-300">
                                                {{ $row->pr_number }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                            <div class="break-words whitespace-normal">{{ $row->description }}</div>
                                        </td>
                                        <td
                                            class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white">
                                            ₱ {{ number_format($row->abc, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            @can('view_procurement')
                                                <a href="{{ route('procurements.view', ['procurement' => $row->procID]) }}"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                                    target="_blank">
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No linked PRs or items found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Modal pagination --}}
                        @if ($modalPaginator && ($modalPaginator->hasPages() || $modalPaginator->total() > 0))
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

                                <!-- Left: Per-page selector -->
                                <div class="flex items-center gap-x-2">
                                    <label for="modalPerPage"
                                        class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
                                    <select id="modalPerPage" wire:model.live="modalPerPage"
                                        class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
                                </div>

                                <!-- Center: Summary + Pagination -->
                                <div class="flex flex-col items-center justify-center gap-3 flex-1">
                                    <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Showing
                                        <span
                                            class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $modalPaginator->firstItem() ?? 0 }}</span>
                                        to
                                        <span
                                            class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $modalPaginator->lastItem() ?? 0 }}</span>
                                        of
                                        <span
                                            class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $modalPaginator->total() }}</span>
                                        items
                                    </div>

                                    @if ($modalPaginator->hasPages())
                                        <div class="flex items-center gap-1">
                                            <button
                                                wire:click="setModalPage({{ $modalPaginator->currentPage() - 1 }})"
                                                @disabled($modalPaginator->onFirstPage())
                                                class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                                                Previous
                                            </button>
                                            @for ($p = 1; $p <= $modalPaginator->lastPage(); $p++)
                                                <button wire:click="setModalPage({{ $p }})"
                                                    class="px-3 py-1.5 text-xs font-medium border rounded-lg transition-colors duration-150 {{ $p === $modalPaginator->currentPage() ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white' }}">
                                                    {{ $p }}
                                                </button>
                                            @endfor
                                            <button
                                                wire:click="setModalPage({{ $modalPaginator->currentPage() + 1 }})"
                                                @disabled(!$modalPaginator->hasMorePages())
                                                class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                                                Next
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PO / Contract Details (read-only) --}}
                <div
                    class="bg-white rounded-xl border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

                    {{-- Section header --}}
                    <div
                        class="flex items-center justify-between px-4 py-2.5 border-b border-gray-200 dark:border-neutral-600 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-800 dark:to-neutral-700">
                        <span
                            class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">PO
                            / Contract Details</span>
                        @if ($viewPmuRecord?->po_contract_number_link)
                            <a href="{{ $viewPmuRecord->po_contract_number_link }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 rounded-lg transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Show PO
                            </a>
                        @endif
                    </div>

                    {{-- Fields --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 dark:bg-neutral-600">

                        {{-- PO / Contract Number --}}
                        <div class="bg-white dark:bg-neutral-700 px-4 py-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">PO / Contract Number</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $viewPmuRecord?->po_contract_number ?? '—' }}
                            </p>
                        </div>

                        {{-- Contract Amount --}}
                        <div class="bg-white dark:bg-neutral-700 px-4 py-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Contract Amount</p>
                            <p class="text-sm font-semibold text-black dark:text-white">
                                {{ $viewPmuRecord?->contract_amount ? '₱ ' . number_format($viewPmuRecord->contract_amount, 2) : '—' }}
                            </p>
                        </div>

                        {{-- Contract Signing Date --}}
                        <div class="bg-white dark:bg-neutral-700 px-4 py-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Contract Signing Date</p>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewPmuRecord?->contract_signing_date ? \Carbon\Carbon::parse($viewPmuRecord->contract_signing_date)->format('M d, Y') : '—' }}
                            </p>
                        </div>

                        {{-- Notice to Proceed Date --}}
                        <div class="bg-white dark:bg-neutral-700 px-4 py-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Notice to Proceed Date</p>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewPmuRecord?->notice_to_proceed_date ? \Carbon\Carbon::parse($viewPmuRecord->notice_to_proceed_date)->format('M d, Y') : '—' }}
                            </p>
                        </div>

                    </div>

                    {{-- Remarks --}}
                    @if ($viewPmuRecord?->remarks)
                        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-600">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Remarks</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $viewPmuRecord->remarks }}</p>
                        </div>
                    @endif

                </div>

            </div>
        </x-forms.modal>
    @endif

</div>
