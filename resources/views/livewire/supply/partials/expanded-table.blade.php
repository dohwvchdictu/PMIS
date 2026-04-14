<table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 rounded-lg overflow-hidden">
    <thead
        class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-10">
        <tr>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                PR Number</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                Title / Description</th>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                Supplier</th>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                Description / Item(s)</th>
            <th
                class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                Contract Amount</th>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                End User</th>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                PO Date Received by Supplier</th>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                Date of Acceptance</th>
            <th
                class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                Date to COA</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
        @forelse ($expandedPaginator as $row)
            @php $spo = ($supplyPoByRefId ?? collect())->get($row->rowKey ?? null); @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                {{-- PR Number --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-emerald-700 dark:text-emerald-300">
                    @can('view_procurement')
                        <a href="{{ route('procurements.view', ['procurement' => $row->procID]) }}" target="_blank"
                            class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-400 transition-colors">
                            {{ $row->pr_number ?? '—' }}
                        </a>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs">
                            {{ $row->pr_number ?? '—' }}
                        </span>
                    @endcan
                </td>

                {{-- Title / Description --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white max-w-[14rem]">
                    <div class="font-medium truncate" title="{{ $row->description }}">
                        {{ $row->description ?? '—' }}
                    </div>
                </td>

                {{-- Supplier --}}
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 min-w-[14rem]">
                    <div class="break-words whitespace-normal">
                        {{ $row->supplier_name ?? '—' }}
                    </div>
                </td>

                {{-- Description / Item(s) --}}
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-[200px]">
                    <div class="whitespace-nowrap overflow-hidden text-ellipsis" title="{{ $spo?->description ?? '' }}">
                        {{ $spo?->description ?? '—' }}
                    </div>
                </td>

                {{-- Contract Amount --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">
                    {{ $row->contract_amount ? '₱ ' . number_format((float) $row->contract_amount, 2) : '—' }}
                </td>

                {{-- End User --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ $row->end_user_name ?? '—' }}
                </td>

                {{-- PO Date Received by Supplier --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ $row->date_po_receipt_by_supplier ? \Carbon\Carbon::parse($row->date_po_receipt_by_supplier)->format('M d, Y') : '—' }}
                </td>

                {{-- Date of Acceptance --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ $spo?->date_of_acceptance ? \Carbon\Carbon::parse($spo->date_of_acceptance)->format('M d, Y') : '—' }}
                </td>

                {{-- Date to COA --}}
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    {{ $row->date_coa_stamped_received ? \Carbon\Carbon::parse($row->date_coa_stamped_received)->format('M d, Y') : '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
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

        {{-- Left: Per-page selector --}}
        <div class="flex items-center gap-x-2">
            <label for="expandedPerPage" class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
            <select id="expandedPerPage" wire:model.live="expandedPerPage"
                class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
        </div>

        {{-- Center: Summary + Pagination --}}
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
