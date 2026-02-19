<div class="space-y-6 p-2 pb-[5rem]">

    {{-- NOA Header Box --}}
    <div
        class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl shadow-sm overflow-hidden">

        {{-- Emerald top line --}}
        <div class="h-1 bg-emerald-600"></div>

        <div class="flex flex-wrap items-stretch">

            {{-- NOA Number — emerald panel --}}
            <div class="bg-emerald-600 dark:bg-emerald-700 px-5 py-4 flex flex-col justify-center">
                <p class="text-xs font-medium text-emerald-100 mb-0.5">Notice of Award No.</p>
                <p class="text-lg font-bold text-white">{{ $noticeOfAwardNumber ?? '—' }}</p>
            </div>

            {{-- Divider --}}
            <div class="w-px bg-gray-200 dark:bg-neutral-600 hidden sm:block"></div>

            {{-- Date Forwarded --}}
            <div class="px-5 py-4 flex flex-col justify-center">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Date Forwarded</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ $date_forwarded ? \Carbon\Carbon::parse($date_forwarded)->format('M d, Y') : '—' }}
                </p>
            </div>

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- Button --}}
            <div class="flex items-center px-4 py-4">
                <button type="button" x-data x-on:click="$dispatch('toggle-linked-prs')"
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
    <div x-data="{ open: false }" x-on:toggle-linked-prs.window="open = !open">
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">

            {{-- Section header --}}
            <div
                class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-neutral-600 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-800 dark:to-neutral-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Linked PRs / Items</span>
                    @php $totalLinked = $editPaginator->total(); @endphp
                    <span
                        class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        {{ $totalLinked }}
                    </span>
                </div>
                <button type="button" x-on:click="open = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-600">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800">
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
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                        @forelse ($editPaginator as $row)
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

                {{-- Linked PRs pagination --}}
                @if ($editPaginator->hasPages() || $editPaginator->total() > 0)
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full p-4 border-t border-gray-200 dark:border-neutral-700 gap-3 bg-gradient-to-r from-gray-50 to-white dark:from-neutral-900 dark:to-neutral-800">

                        <!-- Left: Per-page selector -->
                        <div class="flex items-center gap-x-2">
                            <label for="editPerPage"
                                class="text-xs font-medium text-gray-600 dark:text-gray-300">Show</label>
                            <select id="editPerPage" wire:model.live="editPerPage"
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
                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $editPaginator->firstItem() ?? 0 }}</span>
                                to
                                <span
                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $editPaginator->lastItem() ?? 0 }}</span>
                                of
                                <span
                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $editPaginator->total() }}</span>
                                items
                            </div>

                            @if ($editPaginator->hasPages())
                                <div class="flex items-center gap-1">
                                    <button wire:click="setEditPage({{ $editPaginator->currentPage() - 1 }})"
                                        @disabled($editPaginator->onFirstPage())
                                        class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed">
                                        Previous
                                    </button>
                                    @for ($p = 1; $p <= $editPaginator->lastPage(); $p++)
                                        <button wire:click="setEditPage({{ $p }})"
                                            class="px-3 py-1.5 text-xs font-medium border rounded-lg transition-colors duration-150 {{ $p === $editPaginator->currentPage() ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white' }}">
                                            {{ $p }}
                                        </button>
                                    @endfor
                                    <button wire:click="setEditPage({{ $editPaginator->currentPage() + 1 }})"
                                        @disabled(!$editPaginator->hasMorePages())
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
    </div>

    {{-- PO / Contract Details + Dates + Remarks --}}
    <div class="bg-white p-4 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
        <div class="grid grid-cols-6 md:grid-cols-9 gap-4">

            {{-- PO / Contract Number --}}
            <div class="col-span-1">
                <label for="po_contract_number"
                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    PO / Contract Number <span class="text-red-500">*</span>
                </label>
                <input type="text" id="po_contract_number" wire:model="po_contract_number"
                    placeholder="e.g. 2026-01-0001" required
                    class="w-full px-3 py-2 text-sm border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600
                    @error('po_contract_number') border-red-500 @else border-gray-300 @enderror">
                @error('po_contract_number')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contract Amount --}}
            <div class="col-span-1" x-data="{
                display: {{ $contract_amount && is_numeric($contract_amount) ? "'" . number_format((float) $contract_amount, 2) . "'" : "''" }},
                format(val) {
                    let n = parseFloat(String(val).replace(/,/g, ''));
                    return isNaN(n) ? '' : n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                handleInput(e) {
                    let raw = e.target.value.replace(/[^0-9.]/g, '');
                    let parts = raw.split('.');
                    if (parts.length > 2) raw = parts[0] + '.' + parts.slice(1).join('');
                    this.display = raw;
                    $wire.set('contract_amount', raw === '' ? '' : raw);
                },
                handleBlur() {
                    let raw = String(this.display).replace(/,/g, '');
                    let n = parseFloat(raw);
                    if (!isNaN(n)) {
                        this.display = this.format(n);
                        $wire.set('contract_amount', n);
                    } else {
                        this.display = '';
                        $wire.set('contract_amount', '');
                    }
                }
            }">
                <label for="contract_amount"
                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Contract Amount <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500 dark:text-gray-400 pointer-events-none">
                        ₱
                    </span>
                    <input type="text" id="contract_amount" x-model="display" x-on:input="handleInput($event)"
                        x-on:blur="handleBlur()" placeholder="0.00"
                        class="w-full pl-6 pr-3 py-2 text-sm text-right border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600
                        @error('contract_amount') border-red-500 @else border-gray-300 @enderror">
                </div>
                @error('contract_amount')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contract Signing Date --}}
            <div class="col-span-1">
                <label for="contract_signing_date"
                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Contract Signing Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="contract_signing_date" wire:model="contract_signing_date" required
                    class="w-full px-3 py-2 text-sm border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600
                    @error('contract_signing_date') border-red-500 @else border-gray-300 @enderror">
                @error('contract_signing_date')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notice to Proceed Date --}}
            <div class="col-span-1">
                <label for="notice_to_proceed_date"
                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Notice to Proceed Date
                </label>
                <input type="date" id="notice_to_proceed_date" wire:model="notice_to_proceed_date"
                    class="w-full px-3 py-2 text-sm border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600
                    @error('notice_to_proceed_date') border-red-500 @else border-gray-300 @enderror">
                @error('notice_to_proceed_date')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- PO / Contract Number Link --}}
            <div class="col-span-5">
                <label for="po_contract_number_link"
                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    PO / Contract Number Link
                    @if ($pmuRecord?->po_contract_number_link)
                        <a href="{{ $pmuRecord->po_contract_number_link }}" target="_blank"
                            rel="noopener noreferrer" title="Open saved link"
                            class="inline-flex items-center gap-1 ml-1.5 px-1.5 py-0.5 text-xs font-medium text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </span>
                    <input type="url" id="po_contract_number_link" wire:model="po_contract_number_link"
                        placeholder="https://..."
                        class="w-full pl-8 pr-3 py-2 text-sm border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600
                        @error('po_contract_number_link') border-red-500 @else border-gray-300 @enderror">
                </div>
                @error('po_contract_number_link')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remarks --}}
            <div class="col-span-9">
                <label for="remarks" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Remarks
                </label>
                <textarea id="remarks" wire:model="remarks" rows="2" placeholder="Enter any remarks..."
                    class="w-full px-3 py-2 text-sm border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600
                    @error('remarks') border-red-500 @else border-gray-300 @enderror"></textarea>
                @error('remarks')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Fixed Bottom Footer --}}
    <div
        class="fixed bottom-4 right-0 left-0 lg:left-48 flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-700 z-30">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-3">
            <button wire:click="cancel"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600">
                Cancel
            </button>
            <button wire:click="update"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save
            </button>
        </div>
    </div>

    <!-- Bottom Spacer -->
    <div class="h-16"></div>

</div>
