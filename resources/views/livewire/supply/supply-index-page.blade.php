<div>
    <div wire:poll.15s>

        {{-- ═══ Page Header ════════════════════════════════════════════════════════ --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Supply Office</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Track PO/Contract document receipt</p>
            </div>
        </div>

        {{-- ═══ Pending Receipt ════════════════════════════════════════════════════ --}}
        @if ($pendingItems->total() > 0)
            <div x-data="{ open: true }"
                class="bg-white border border-orange-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-orange-800/50 flex flex-col mb-6">

                {{-- Card Header --}}
                <div @click="open = !open" role="button"
                    class="flex items-center justify-between px-6 py-3 border-b border-orange-200 dark:border-orange-800/50 bg-orange-50 dark:bg-orange-950/20 cursor-pointer select-none">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/40">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-orange-800 dark:text-orange-300">Pending Receipt</h3>
                        <span
                            class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-200 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300">
                            {{ $pendingItems->total() }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        @can('update_supply')
                            @if (count($selectedSupplyIds) > 0)
                                <div class="flex items-center gap-2" @click.stop>
                                    <span class="text-xs text-orange-700 dark:text-orange-300 font-medium">
                                        {{ count($selectedSupplyIds) }} selected
                                    </span>
                                    <button wire:click="openBulkReceiveModal"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Bulk Receive
                                    </button>
                                    <button wire:click="clearSelection"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                                        Clear
                                    </button>
                                </div>
                            @endif
                        @endcan

                        <svg class="w-4 h-4 text-orange-500 dark:text-orange-400 transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>

                {{-- Pending Table --}}
                <div x-show="open" x-transition class="overflow-auto flex-1">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead
                            class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
                            <tr>
                                @can('update_supply')
                                    <th class="px-3 py-2 bg-gray-100 dark:bg-neutral-900 w-10"></th>
                                @endcan
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    PO / Contract No.</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Date Forwarded from PMU</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Remarks</th>
                                @can('update_supply')
                                    <th
                                        class="px-6 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                            @forelse ($pendingItems as $supply)
                                <tr class="hover:bg-orange-50/50 dark:hover:bg-orange-950/10 transition-colors">

                                    @can('update_supply')
                                        <td class="px-3 py-3 text-center" @click.stop>
                                            <input type="checkbox" wire:click="toggleSupplySelection({{ $supply->id }})"
                                                {{ in_array($supply->id, $selectedSupplyIds) ? 'checked' : '' }}
                                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-700 cursor-pointer" />
                                        </td>
                                    @endcan

                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-900 dark:text-white">
                                            <svg class="w-3.5 h-3.5 text-orange-400 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $supply->po_contract_number }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        @if ($supply->date_forwarded)
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($supply->date_forwarded)->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                        {{ $supply->remarks ?? '—' }}
                                    </td>

                                    @can('update_supply')
                                        <td class="px-6 py-3 whitespace-nowrap text-right">
                                            <button wire:click="openReceiveModal({{ $supply->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Mark Received
                                            </button>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No pending records
                                                found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pending Pagination --}}
                <div x-show="open" x-transition>
                    @if ($pendingItems->hasPages())
                        <div class="px-6 py-4 border-t border-orange-200 dark:border-orange-800/50">
                            {{ $pendingItems->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ═══ Received Records ════════════════════════════════════════════════════ --}}
        <div
            class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700 flex flex-col mb-6">

            {{-- Search Bar --}}
            <div
                class="px-4 py-2.5 border-b border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 flex items-center justify-between gap-3">
                <div class="relative w-72">
                    <input type="text" wire:model.live="search"
                        placeholder="Search PO/contract number, remarks..."
                        class="w-full px-4 py-2 pl-9 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" />
                    <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Received Records</span>
                    <span
                        class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                        {{ $receivedItems->total() }}
                    </span>
                </div>
            </div>

            {{-- Received Table --}}
            <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead
                        class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                PO / Contract No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Date Forwarded from PMU</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Date Received</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Remarks</th>
                            @can('update_supply')
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                        @forelse ($receivedItems as $supply)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">

                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-900 dark:text-white">
                                        <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $supply->po_contract_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    @if ($supply->date_forwarded)
                                        {{ \Carbon\Carbon::parse($supply->date_forwarded)->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-sm text-emerald-700 dark:text-emerald-400 font-medium">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($supply->date_received)->format('M d, Y') }}
                                    </span>
                                </td>

                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ $supply->remarks ?? '—' }}
                                </td>

                                @can('update_supply')
                                    <td class="px-6 py-3 whitespace-nowrap text-right">
                                        <button wire:click="openReceiveModal({{ $supply->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @if (!empty($search))
                                                No records match your search.
                                            @else
                                                No received records yet.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Received Pagination --}}
            @if ($receivedItems->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                    {{ $receivedItems->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ═══ MODALS (Outside polling) ════════════════════════════════════════════ --}}

    {{-- Single Receive Modal --}}
    @if ($showReceiveModal)
        <div wire:ignore>
            <div class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm"
                wire:click.self="closeReceiveModal">
                <div
                    class="bg-white dark:bg-neutral-800 rounded-xl shadow-2xl border border-gray-200 dark:border-neutral-700 w-full max-w-md mx-4">

                    {{-- Header --}}
                    <div
                        class="flex items-center justify-between px-5 py-4 border-b border-emerald-200 dark:border-emerald-800/50 bg-emerald-50 dark:bg-emerald-950/20 rounded-t-xl">
                        <div class="flex items-center gap-2.5">
                            <div
                                class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Mark as Received
                                </h3>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400">Record document receipt date
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeReceiveModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-5 py-4 space-y-4">
                        {{-- Date Received --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Date Received <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="receiveDate"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                                @error('receiveDate') border-red-400 dark:border-red-500 @else border-gray-300 @enderror" />
                            @error('receiveDate')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remarks --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Remarks <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea wire:model="receiveRemarks" rows="3" placeholder="Add any notes about document receipt..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-neutral-700 dark:text-white resize-none"></textarea>
                            @error('receiveRemarks')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div
                        class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200 dark:border-neutral-700">
                        <button wire:click="closeReceiveModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="confirmReceive" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 rounded-lg transition-colors">
                            <svg wire:loading wire:target="confirmReceive" class="w-4 h-4 animate-spin"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <svg wire:loading.remove wire:target="confirmReceive" class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Confirm Received
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Bulk Receive Modal --}}
    @if ($showBulkReceiveModal)
        <div wire:ignore>
            <div class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm"
                wire:click.self="closeBulkReceiveModal">
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
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Bulk Mark as Received
                            </h3>
                        </div>
                        <button wire:click="closeBulkReceiveModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-5 py-4 space-y-4">

                        {{-- Summary --}}
                        <div
                            class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                            <p class="text-sm text-emerald-900 dark:text-emerald-100">
                                <span class="font-semibold">{{ count($selectedSupplyIds) }}</span> record(s) selected
                                for receipt.
                            </p>
                        </div>

                        {{-- Date Received --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Date Received <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="bulkReceiveDate"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                                @error('bulkReceiveDate') border-red-400 dark:border-red-500 @else border-gray-300 @enderror" />
                            @error('bulkReceiveDate')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">This date will be applied to all
                                selected records.</p>
                        </div>

                        {{-- Remarks --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Remarks <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea wire:model="bulkReceiveRemarks" rows="3" placeholder="Add notes about document receipt..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-neutral-700 dark:text-white resize-none"></textarea>
                            @error('bulkReceiveRemarks')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Footer --}}
                        <div
                            class="flex items-center justify-between gap-3 pt-2 border-t border-gray-200 dark:border-neutral-700">
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ count($selectedSupplyIds) }} record(s) will be updated
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="closeBulkReceiveModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                                    Cancel
                                </button>
                                <button type="button" wire:click="confirmBulkReceive" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 rounded-lg transition-colors">
                                    <svg wire:loading wire:target="confirmBulkReceive" class="w-4 h-4 animate-spin"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <svg wire:loading.remove wire:target="confirmBulkReceive" class="w-4 h-4"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Confirm Receive
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
