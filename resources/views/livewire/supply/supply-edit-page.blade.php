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
                            Description / Item(s)</th>
                        <th
                            class="px-3 py-3 text-right font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            Contract Amount</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            End User</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            PO Date Received by Supplier</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            Date of Acceptance</th>
                        <th
                            class="px-3 py-3 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 whitespace-nowrap">
                            Date to COA</th>
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
                            @php $spo = $supplyPoByRefId->get($row->rowKey); @endphp
                            <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300 max-w-[200px]">
                                <div class="whitespace-nowrap overflow-hidden text-ellipsis"
                                    title="{{ $spo?->description ?? '' }}">{{ $spo?->description ?? '—' }}</div>
                            </td>
                            <td
                                class="px-3 py-2 whitespace-nowrap text-right text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->contract_amount ? '₱ ' . number_format($row->contract_amount, 2) : '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->end_user_name ?? '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->date_po_receipt_by_supplier ? \Carbon\Carbon::parse($row->date_po_receipt_by_supplier)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                {{ $spo?->date_of_acceptance ? \Carbon\Carbon::parse($spo->date_of_acceptance)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700 dark:text-gray-300">
                                {{ $row->date_coa_stamped_received ? \Carbon\Carbon::parse($row->date_coa_stamped_received)->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
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
    <x-forms.modal :model="'showBulkEditModal'" :closeMethod="'closeBulkEditModal'" title="Edit — Supply Details" size="max-w-4xl">

        <div class="px-4 py-4" x-data="{
            category: '',
            subCategory: '',
            resetSub() { this.subCategory = ''; },
            get subOptions() {
                if (this.category === 'Pharma') return [
                    { value: 'Sub 1', label: 'Sub 1 — Prescription / Rx Drugs' },
                    { value: 'Sub 2', label: 'Sub 2 — OTC / Herbal / Vitamins' }
                ];
                if (this.category === 'Non-Pharma') return [
                    { value: 'Sub 1', label: 'Sub 1 — Equipment / Devices' },
                    { value: 'Sub 2', label: 'Sub 2 — Office Supplies / Consumables' }
                ];
                return [
                    { value: 'Sub 1', label: 'Sub 1' },
                    { value: 'Sub 2', label: 'Sub 2' }
                ];
            }
        }">

            <div class="grid grid-cols-3 sm:grid-cols-5 gap-4">

                {{-- Description / Item(s) --}}
                <div class="col-span-2 sm:col-span-5">
                    <label for="bulk_description"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Description / Item(s)
                    </label>
                    <textarea id="bulk_description" wire:model="bulk_description" rows="2"
                        placeholder="Enter description or items..."
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600 resize-none
                        @error('bulk_description') border-red-500 @else border-gray-300 @enderror"></textarea>
                    @error('bulk_description')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deadline --}}
                <div>
                    <label for="bulk_deadline"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Deadline
                    </label>
                    <input type="date" id="bulk_deadline" wire:model="bulk_deadline"
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                        @error('bulk_deadline') border-red-500 @else border-gray-300 @enderror">
                    @error('bulk_deadline')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date of Delivery --}}
                <div>
                    <label for="bulk_date_of_delivery"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Date of Delivery
                    </label>
                    <input type="date" id="bulk_date_of_delivery" wire:model="bulk_date_of_delivery"
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                        @error('bulk_date_of_delivery') border-red-500 @else border-gray-300 @enderror">
                    @error('bulk_date_of_delivery')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date of Acceptance --}}
                <div>
                    <label for="bulk_date_of_acceptance"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Date of Acceptance
                    </label>
                    <input type="date" id="bulk_date_of_acceptance" wire:model="bulk_date_of_acceptance"
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                        @error('bulk_date_of_acceptance') border-red-500 @else border-gray-300 @enderror">
                    @error('bulk_date_of_acceptance')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Delivery Completion --}}
                <div>
                    <label for="bulk_delivery_completion"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Delivery Completion
                    </label>
                    <input type="date" id="bulk_delivery_completion" wire:model="bulk_delivery_completion"
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                        @error('bulk_delivery_completion') border-red-500 @else border-gray-300 @enderror">
                    @error('bulk_delivery_completion')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date Received from End User --}}
                <div>
                    <label for="bulk_date_received_from_end_user"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Date Received from End User
                    </label>
                    <input type="datetime-local" id="bulk_date_received_from_end_user"
                        wire:model="bulk_date_received_from_end_user"
                        class="w-full px-3 py-2 text-xs border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600
                        @error('bulk_date_received_from_end_user') border-red-500 @else border-gray-300 @enderror">
                    @error('bulk_date_received_from_end_user')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Supply Category --}}
                <div>
                    <label for="bulk_supply_category"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Supply Category
                    </label>
                    <select id="bulk_supply_category" x-model="category" @change="resetSub()"
                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                        <option value="">— Select Category —</option>
                        <option value="Pharma">Pharma</option>
                        <option value="Non-Pharma">Non-Pharma</option>
                    </select>
                </div>

                {{-- Sub Category (both Pharma & Non-Pharma) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Sub Category
                    </label>
                    <select x-model="subCategory" :disabled="category === ''"
                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                        <option value="">— Select Sub Category —</option>
                        <template x-for="opt in subOptions" :key="opt.value">
                            <option :value="opt.value" x-text="opt.label"></option>
                        </template>
                    </select>
                </div>

            </div>

            {{-- ─── Pharma · Sub 1: Prescription / Rx Drugs ────────────────────────── --}}
            <div x-show="category === 'Pharma' && subCategory === 'Sub 1'" x-transition.duration.200ms
                class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <h4 class="text-xs font-semibold text-blue-700 dark:text-blue-300">Pharma — Sub 1: Prescription /
                        Rx Drugs</h4>
                    <span
                        class="px-1.5 py-0.5 text-[10px] font-medium text-blue-600 bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 rounded">Sample
                        UI Only</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Generic
                            Name</label>
                        <input type="text" placeholder="e.g. Amoxicillin"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dosage
                            Form</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>Tablet</option>
                            <option>Capsule</option>
                            <option>Syrup</option>
                            <option>Injectable</option>
                            <option>Ointment</option>
                            <option>Suppository</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Strength /
                            Concentration</label>
                        <input type="text" placeholder="e.g. 500mg"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Lot
                            Number</label>
                        <input type="text" placeholder="e.g. LOT-2025-001"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Expiry
                            Date</label>
                        <input type="date"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Batch
                            Number</label>
                        <input type="text" placeholder="e.g. BATCH-001"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                </div>
            </div>

            {{-- ─── Pharma · Sub 2: OTC / Herbal / Vitamins ─────────────────────────── --}}
            <div x-show="category === 'Pharma' && subCategory === 'Sub 2'" x-transition.duration.200ms
                class="mt-4 p-4 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <h4 class="text-xs font-semibold text-sky-700 dark:text-sky-300">Pharma — Sub 2: OTC / Herbal /
                        Vitamins</h4>
                    <span
                        class="px-1.5 py-0.5 text-[10px] font-medium text-sky-600 bg-sky-100 dark:bg-sky-900/40 dark:text-sky-300 rounded">Sample
                        UI Only</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Product
                            Name</label>
                        <input type="text" placeholder="e.g. Vitamin C 500mg"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Drug
                            Classification</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>OTC (Over-the-Counter)</option>
                            <option>Herbal / Traditional</option>
                            <option>Dietary Supplement</option>
                            <option>Vitamin / Mineral</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dosage
                            Form</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>Tablet</option>
                            <option>Capsule</option>
                            <option>Syrup</option>
                            <option>Drops</option>
                            <option>Powder</option>
                            <option>Lozenges</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Lot
                            Number</label>
                        <input type="text" placeholder="e.g. LOT-2025-002"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Expiry
                            Date</label>
                        <input type="date"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Registration
                            No. (FDA)</label>
                        <input type="text" placeholder="e.g. FR-12345"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                </div>
            </div>

            {{-- ─── Non-Pharma · Sub 1: Equipment / Devices ──────────────────────── --}}
            <div x-show="category === 'Non-Pharma' && subCategory === 'Sub 1'" x-transition.duration.200ms
                class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <h4 class="text-xs font-semibold text-orange-700 dark:text-orange-300">Non-Pharma — Sub 1:
                        Equipment / Devices</h4>
                    <span
                        class="px-1.5 py-0.5 text-[10px] font-medium text-orange-600 bg-orange-100 dark:bg-orange-900/40 dark:text-orange-300 rounded">Sample
                        UI Only</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Brand</label>
                        <input type="text" placeholder="e.g. Samsung"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Model</label>
                        <input type="text" placeholder="e.g. Galaxy S24"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Serial
                            Number</label>
                        <input type="text" placeholder="e.g. SN-2025-0001"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit of
                            Measure</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>PC</option>
                            <option>SET</option>
                            <option>UNIT</option>
                            <option>PAIR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Warranty
                            Period</label>
                        <input type="text" placeholder="e.g. 1 Year"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Condition</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>Brand New</option>
                            <option>Refurbished</option>
                            <option>Reconditioned</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ─── Non-Pharma · Sub 2: Office Supplies / Consumables ───────────────── --}}
            <div x-show="category === 'Non-Pharma' && subCategory === 'Sub 2'" x-transition.duration.200ms
                class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <h4 class="text-xs font-semibold text-amber-700 dark:text-amber-300">Non-Pharma — Sub 2: Office
                        Supplies / Consumables</h4>
                    <span
                        class="px-1.5 py-0.5 text-[10px] font-medium text-amber-600 bg-amber-100 dark:bg-amber-900/40 dark:text-amber-300 rounded">Sample
                        UI Only</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Item
                            Code</label>
                        <input type="text" placeholder="e.g. OFF-001"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit of
                            Measure</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>REAM</option>
                            <option>BOX</option>
                            <option>ROLL</option>
                            <option>BOTTLE</option>
                            <option>PACK</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Color /
                            Specification</label>
                        <input type="text" placeholder="e.g. White, A4, 80gsm"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantity per
                            Pack</label>
                        <input type="number" min="1" placeholder="e.g. 500"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Shelf
                            Life</label>
                        <input type="text" placeholder="e.g. 2 Years"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Storage
                            Requirement</label>
                        <select
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-neutral-700 dark:text-white dark:border-neutral-600">
                            <option value="">Select...</option>
                            <option>Room Temperature</option>
                            <option>Cool &amp; Dry Place</option>
                            <option>Away from Sunlight</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-5 pt-4 border-t border-gray-200 dark:border-neutral-600 flex justify-end gap-3">
                <button type="button" wire:click="closeBulkEditModal"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </button>
                <button type="button" wire:click="saveBulkEdit" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-60">
                    <svg wire:loading wire:target="saveBulkEdit" class="w-4 h-4 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    <svg wire:loading.remove wire:target="saveBulkEdit" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Save
                </button>
            </div>

        </div>

    </x-forms.modal>

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
</div>
