<div>
    <div wire:poll.15s>

        {{-- ═══ Pending Receipt ════════════════════════════════════════════════════ --}}
        @if ($pendingItems->total() > 0)
            <div x-data="{ open: false }"
                class="bg-white border border-orange-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-orange-800/50 flex flex-col mb-6">

                {{-- Card Header --}}
                <div @click="open = !open" role="button"
                    class="flex items-center justify-between px-6 py-3 border-b border-orange-200 dark:border-orange-800/50 bg-orange-50 dark:bg-orange-950/20 cursor-pointer select-none">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/40">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
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
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        <span
                                            class="font-semibold text-emerald-600 dark:text-emerald-400">{{ count($selectedSupplyIds) }}</span>
                                        selected
                                    </span>
                                    <button wire:click="clearSelection"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 transition-colors">
                                        Clear
                                    </button>
                                    <button wire:click="openBulkReceiveModal"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Receive
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
                                <th class="px-3 py-2 bg-gray-100 dark:bg-neutral-900 w-10">
                                    <input type="checkbox"
                                        @if ($pendingItems->total() > 0) x-data x-on:click="
                                            let boxes = document.querySelectorAll('[data-supply-check]');
                                            let anyUnchecked = Array.from(boxes).some(b => !b.checked);
                                            boxes.forEach(b => {
                                                b.checked = anyUnchecked;
                                                b.dispatchEvent(new Event('change'));
                                            });
                                        " @endif
                                        class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-500 cursor-pointer"
                                        title="Select / deselect all visible" />
                                </th>
                                <th class="px-2 py-1 bg-gray-100 dark:bg-neutral-900 w-12"></th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    PO / Contract No.</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Date Forwarded from PMU</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                            @forelse ($pendingItems as $supply)
                                <tr
                                    class="transition-colors border-l-4 border-l-orange-400 {{ in_array($supply->id, $selectedSupplyIds) ? 'bg-emerald-50 dark:bg-emerald-950/20 ring-1 ring-inset ring-emerald-300 dark:ring-emerald-700' : 'bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/20 dark:hover:bg-orange-950/30' }}">

                                    @can('update_supply')
                                        <td class="px-3 py-4 whitespace-nowrap" @click.stop>
                                            <input type="checkbox" data-supply-check wire:model.live="selectedSupplyIds"
                                                value="{{ $supply->id }}"
                                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-500 cursor-pointer" />
                                        </td>
                                    @endcan

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <button wire:click="toggleExpand('{{ $supply->po_contract_number }}')"
                                            class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                            <svg class="w-5 h-5 transition-transform {{ $expandedPoNumber === $supply->po_contract_number ? 'rotate-90' : '' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </td>

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
                                </tr>

                                {{-- Expanded Procurement Details --}}
                                @if ($expandedPoNumber === $supply->po_contract_number)
                                    <tr class="bg-gray-50 dark:bg-neutral-900">
                                        <td colspan="99" class="px-4 py-3">
                                            <div
                                                class="overflow-x-auto overflow-y-auto max-h-[55vh] rounded-lg border border-gray-200 dark:border-neutral-700">
                                                @include('livewire.supply.partials.expanded-table', [
                                                    'expandedPaginator' => $expandedPaginator,
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">All records
                                                have been received</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">No pending items to
                                                process</p>
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
                class="px-4 py-2.5 border-b border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 flex justify-start">
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
            </div>

            {{-- Received Table --}}
            <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead
                        class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th class="px-2 py-1 bg-gray-100 dark:bg-neutral-900 w-12"></th>
                            <th class="px-2 py-1 bg-gray-100 dark:bg-neutral-900 w-12"></th>
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
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                        @forelse ($receivedItems as $supply)
                            <tr
                                class="transition-colors bg-white hover:bg-gray-50 dark:bg-neutral-800 dark:hover:bg-neutral-700">

                                {{-- Toggle --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <button wire:click="toggleExpand('{{ $supply->po_contract_number }}')"
                                        class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                        <svg class="w-5 h-5 transition-transform {{ $expandedPoNumber === $supply->po_contract_number ? 'rotate-90' : '' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </td>
                                {{-- Actions Dropdown --}}
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div x-data="{ open: false }" class="relative inline-block" x-ref="menuWrapper">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:border-emerald-300 dark:hover:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
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
                                                class="absolute z-[9999] bg-white border border-gray-200 rounded-xl shadow-2xl dark:bg-neutral-800 dark:border-neutral-700 min-w-[160px] overflow-hidden"
                                                x-ref="dropdown" x-init="$watch('open', value => {
                                                    if (value) {
                                                        let rect = $refs.menuWrapper.getBoundingClientRect();
                                                        $refs.dropdown.style.top = (rect.top + window.scrollY) + 'px';
                                                        $refs.dropdown.style.left = (rect.right + 10 + window.scrollX) + 'px';
                                                    }
                                                })">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                    @can('update_supply')
                                                        <li>
                                                            <button wire:click="openReceiveModal({{ $supply->id }})"
                                                                @click="open = false"
                                                                class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-emerald-100 dark:hover:from-emerald-900/30 dark:hover:to-emerald-800/30 text-emerald-600 dark:text-emerald-400 transition-all duration-150 group/item">
                                                                <x-heroicon-o-pencil-square
                                                                    class="w-4 h-4 group-hover/item:scale-110 transition-transform" />
                                                                <span class="font-medium">Edit Received</span>
                                                            </button>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </template>
                                    </div>
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $supply->po_contract_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    @if ($supply->date_forwarded)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($supply->date_forwarded)->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <td
                                    class="px-6 py-3 whitespace-nowrap text-sm font-medium text-emerald-700 dark:text-emerald-300">
                                    <span class="inline-flex items-center gap-1.5">
                                        {{ \Carbon\Carbon::parse($supply->date_received)->format('M d, Y') }}
                                    </span>
                                </td>

                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs">
                                    @if ($supply->remarks)
                                        <span class="block truncate" title="{{ $supply->remarks }}">
                                            {{ $supply->remarks }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Expanded Procurement Details --}}
                            @if ($expandedPoNumber === $supply->po_contract_number)
                                <tr class="bg-gray-50 dark:bg-neutral-900">
                                    <td colspan="99" class="px-4 py-3">
                                        <div
                                            class="overflow-x-auto overflow-y-auto max-h-[55vh] rounded-lg border border-gray-200 dark:border-neutral-700">
                                            @include('livewire.supply.partials.expanded-table', [
                                                'expandedPaginator' => $expandedPaginator,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                                            @if (!empty($search))
                                                No records match your search.
                                            @else
                                                No received records yet
                                            @endif
                                        </p>
                                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Mark pending items as
                                            received to see them here</p>
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
