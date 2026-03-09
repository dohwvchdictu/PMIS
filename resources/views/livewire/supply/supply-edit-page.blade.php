<div class="space-y-6 p-2 pb-[5rem]">

    {{-- ─── Supply Header Box ─────────────────────────────────────────────────── --}}
    <div
        class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl shadow-sm overflow-hidden">
        <div class="h-1 bg-emerald-600"></div>

        <div class="flex flex-wrap items-stretch">

            {{-- PO / Contract Number panel --}}
            <div class="bg-emerald-600 dark:bg-emerald-700 px-5 py-4 flex flex-col justify-center">
                <p class="text-xs font-medium text-emerald-100 mb-0.5">PO / Contract No.</p>
                <p class="text-lg font-bold text-white">{{ $supply->po_contract_number }}</p>
            </div>

            <div class="w-px bg-gray-200 dark:bg-neutral-600 hidden sm:block"></div>

            {{-- Date Forwarded from PMU --}}
            <div class="px-5 py-4 flex flex-col justify-center">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Date Forwarded from PMU</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ $supply->date_forwarded ? \Carbon\Carbon::parse($supply->date_forwarded)->format('M d, Y') : '—' }}
                </p>
            </div>

            <div class="w-px bg-gray-200 dark:bg-neutral-600 hidden sm:block"></div>

            {{-- Date Received --}}
            <div class="px-5 py-4 flex flex-col justify-center">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Date Received</p>
                <p
                    class="text-sm font-medium {{ $supply->date_received ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $supply->date_received ? \Carbon\Carbon::parse($supply->date_received)->format('M d, Y') : 'Not yet received' }}
                </p>
            </div>

            <div class="w-px bg-gray-200 dark:bg-neutral-600 hidden sm:block"></div>

            {{-- Remarks with pencil button --}}
            <div class="flex-1 min-w-0 px-5 py-4 flex flex-col justify-center">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Remarks</p>
                <div class="flex items-center gap-1.5 min-w-0">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate min-w-0"
                        title="{{ $supply->remarks ?? '' }}">
                        {{ $supply->remarks ?: '—' }}
                    </p>
                    @can('update_supply')
                        <button type="button" wire:click="openEditRemarksModal"
                            class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 dark:hover:text-emerald-400 transition-colors"
                            title="Edit Remarks">
                            <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                        </button>
                    @endcan
                </div>
            </div>

        </div>

    </div>

    {{-- ─── Bulk Selection Banner ───────────────────────────────────────────────── --}}
    @if (count($selectedItems) > 0)
        <div
            class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 dark:border-emerald-600 rounded-lg shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                            {{ count($selectedItems) }} item(s) selected
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">Ready for bulk editing</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button wire:click="clearSelections" type="button"
                        class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                        Clear Selection
                    </button>
                    <button wire:click="openBulkEditModal" type="button"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>Edit
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Linked Procurements ────────────────────────────────────────────────── --}}
    <div
        class="bg-white rounded-xl shadow-md border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

        <div
            class="px-4 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-900/20 border-b-2 border-emerald-500 dark:border-emerald-600">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-emerald-600 dark:bg-emerald-700 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-100">Linked PRs / Items
                            <span
                                class="ml-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200">{{ $expandedPaginator->total() }}</span>
                        </h3>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">Select items to bulk-edit
                            supply details</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
            <table class="w-full text-xs min-w-max">
                <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-10">
                    <tr>
                        <th class="px-3 py-3 w-12 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            PR Number</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            Title / Description</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            Supplier</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            PO Date</th>
                        <th
                            class="px-3 py-3 text-right font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            Contract Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                    @forelse ($expandedPaginator as $row)
                        <tr
                            class="hover:bg-emerald-50 dark:hover:bg-neutral-800 transition-colors
                            {{ in_array($row->rowKey, $selectedItems) ? '!bg-emerald-50 dark:!bg-emerald-900/20' : 'bg-white dark:bg-neutral-700' }}">
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $row->rowKey }}"
                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @can('view_procurement')
                                    <a href="{{ route('procurements.view', ['procurement' => $row->procID]) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition-colors">
                                        {{ $row->pr_number }}
                                    </a>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 bg-emerald-50 border border-emerald-200 rounded-md font-mono text-xs text-emerald-700">
                                        {{ $row->pr_number }}
                                    </span>
                                @endcan
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-900 dark:text-white max-w-[250px]">
                                <div class="whitespace-nowrap overflow-hidden text-ellipsis"
                                    title="{{ $row->description }}">{{ $row->description }}</div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->supplier_name ?? '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->po_date ? \Carbon\Carbon::parse($row->po_date)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-right text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->contract_amount ? '₱ ' . number_format($row->contract_amount, 2) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                No linked procurement records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer — PMU style --}}
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
                    <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
                </div>

                <!-- Center: Summary + Pagination -->
                <div class="flex flex-col items-center justify-center gap-3 flex-1">
                    <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
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
                            <button wire:click="setExpandedPage({{ $expandedPaginator->currentPage() - 1 }})"
                                @disabled($expandedPaginator->onFirstPage())
                                class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                                Previous
                            </button>
                            @for ($p = 1; $p <= $expandedPaginator->lastPage(); $p++)
                                <button wire:click="setExpandedPage({{ $p }})"
                                    class="px-3 py-1.5 text-xs font-medium border rounded-lg transition-colors duration-150 {{ $p === $expandedPaginator->currentPage() ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white' }}">
                                    {{ $p }}
                                </button>
                            @endfor
                            <button wire:click="setExpandedPage({{ $expandedPaginator->currentPage() + 1 }})"
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


    {{-- Bottom spacer so footer doesn't overlap content --}}
    <div class="h-16"></div>

    {{-- ═══ MODALS ════════════════════════════════════════════════════════════════ --}}

    {{-- Bulk Edit Modal --}}
    @if ($showBulkEditModal)
        <div class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm"
            wire:click.self="closeBulkEditModal">
            <div
                class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl border border-gray-200 dark:border-neutral-700 w-full max-w-lg mx-4">

                {{-- Header --}}
                <div
                    class="flex items-center justify-between px-5 py-4 border-b border-emerald-200 dark:border-emerald-800/50 bg-emerald-50 dark:bg-emerald-950/20 rounded-t-xl">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Bulk Edit Supply
                                Details</h3>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">
                                Editing {{ count($selectedItems) }} selected item(s)</p>
                        </div>
                    </div>
                    <button wire:click="closeBulkEditModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-5 py-4 space-y-4">

                    {{-- Batch No --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Batch No. <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="text" wire:model="bulk_batch_no" placeholder="e.g. Batch 1"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white
                            @error('bulk_batch_no') border-red-400 dark:border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror" />
                        @error('bulk_batch_no')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Delivery Completion --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Delivery Completion <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="date" wire:model="bulk_delivery_completion"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white
                            @error('bulk_delivery_completion') border-red-400 dark:border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror" />
                        @error('bulk_delivery_completion')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date Received from End User --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date Received from End User <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="datetime-local" wire:model="bulk_date_received_from_end_user"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white
                            @error('bulk_date_received_from_end_user') border-red-400 dark:border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror" />
                        @error('bulk_date_received_from_end_user')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SOA Amount --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            SOA Amount <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="number" wire:model="bulk_soa_amount" step="0.01" min="0"
                            placeholder="0.00"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white
                            @error('bulk_soa_amount') border-red-400 dark:border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror" />
                        @error('bulk_soa_amount')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date Forwarded to Budget --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date Forwarded to Budget <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="datetime-local" wire:model="bulk_date_forwarded_to_budget"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white
                            @error('bulk_date_forwarded_to_budget') border-red-400 dark:border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror" />
                        @error('bulk_date_forwarded_to_budget')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Footer --}}
                <div
                    class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200 dark:border-neutral-700">
                    <button wire:click="closeBulkEditModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="saveBulkEdit" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 rounded-lg transition-colors">
                        <svg wire:loading wire:target="saveBulkEdit" class="w-4 h-4 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Remarks Modal --}}
    @if ($showEditRemarksModal)
        <div class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm"
            wire:click.self="closeEditRemarksModal">
            <div
                class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl border border-gray-200 dark:border-neutral-700 w-full max-w-md mx-4">

                {{-- Header --}}
                <div
                    class="flex items-center justify-between px-5 py-4 border-b border-emerald-200 dark:border-emerald-800/50 bg-emerald-50 dark:bg-emerald-950/20 rounded-t-xl">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                            <x-heroicon-o-pencil class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Edit Remarks</h3>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">
                                {{ $supply->po_contract_number }}</p>
                        </div>
                    </div>
                    <button wire:click="closeEditRemarksModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Remarks <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea wire:model="editRemarksValue" rows="4" placeholder="Enter remarks..."
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white resize-none
                        @error('editRemarksValue') border-red-400 dark:border-red-500 @else border-gray-300 dark:border-neutral-600 @enderror"></textarea>
                    @error('editRemarksValue')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Footer --}}
                <div
                    class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200 dark:border-neutral-700">
                    <button wire:click="closeEditRemarksModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="confirmEditRemarks" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 rounded-lg transition-colors">
                        <svg wire:loading wire:target="confirmEditRemarks" class="w-4 h-4 animate-spin"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Save Remarks
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ FIXED BOTTOM FOOTER ════════════════════════════════════════════════════ --}}
    <div
        class="fixed bottom-4 right-0 left-0 lg:left-48 flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-700 shadow-lg z-30">
        <div class="flex items-center gap-3 px-4">
            <button type="button" wire:click="cancel"
                class="px-5 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                Cancel
            </button>
            @can('update_supply')
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 rounded-lg transition-colors shadow">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
            @endcan
        </div>
    </div>

</div>
