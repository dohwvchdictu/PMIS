@props([
    'form' => [],
    'model' => '',
    'showTable' => true,
    'page' => 1,
    'perPage' => 5,
    'viewOnly' => false,
    'filteredItems' => null,
])


@php
    $allItems = data_get($form, str_replace('form.', '', $model), []);

    // Use filtered items if provided (for search), otherwise use all items
    $itemsToDisplay = $filteredItems ?? $allItems;
    $totalItems = count($itemsToDisplay);
    $offset = ($page - 1) * $perPage;

    // Reverse and slice items while preserving original keys
    $items = collect($itemsToDisplay)->reverse()->slice($offset, $perPage);

    $totalPages = ceil($totalItems / $perPage);
@endphp

@if ($showTable || $viewOnly)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-600">
            <thead class="bg-gray-50 dark:bg-neutral-800">
                <tr>
                    <th style="min-width: 70px; width: 80px;"
                        class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Item #
                    </th>
                    <th style="min-width: 400px;"
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Description
                    </th>
                    <th style="min-width: 180px; width: 180px;"
                        class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Amount
                    </th>
                    @if ($viewOnly)
                        <th style="min-width: 180px;"
                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Stage
                        </th>
                        <th style="min-width: 200px;"
                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Remark
                        </th>
                    @endif
                    @unless ($viewOnly)
                        <th style="min-width: 60px;" class="px-4 py-3"></th>
                    @endunless
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-700 divide-y divide-gray-200 dark:divide-neutral-600">
                @foreach ($items as $rowIndex => $item)
                    <tr wire:key="item-{{ $item['uid'] ?? $item['prItemID'] }}"
                        class="hover:bg-gray-50 dark:hover:bg-neutral-600/50 transition-colors">

                        <td class="px-4 py-3">
                            @if ($viewOnly)
                                <div class="text-gray-900 dark:text-white text-center text-sm font-medium">
                                    {{ $item['item_no'] ?? '' }}</div>
                            @else
                                <input type="text"
                                    class="block w-full px-3 py-2 rounded-lg text-center text-sm border bg-gray-50 text-gray-700 border-gray-300 cursor-not-allowed dark:bg-neutral-800 dark:text-white dark:border-neutral-600"
                                    value="{{ $item['item_no'] ?? '' }}" disabled>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if ($viewOnly)
                                <div class="text-gray-900 dark:text-white text-sm max-w-md break-words">
                                    {{ $item['description'] ?? '' }}
                                </div>
                            @else
                                <textarea wire:model.defer="{{ $model }}.{{ $rowIndex }}.description"
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600 resize-none"
                                    placeholder="Enter item description" rows="1"></textarea>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div x-data="{
                                display: '{{ isset($item['amount']) && is_numeric($item['amount']) ? number_format($item['amount'], 2, '.', ',') : '0.00' }}',
                                formatNumber(num) { /* ... AlpineJS logic ... */ }
                            }" x-init="/* ... AlpineJS logic ... */">
                                @if ($viewOnly)
                                    <div class="text-right text-gray-900 dark:text-white text-sm font-medium">
                                        ₱
                                        {{ is_numeric($item['amount'] ?? null) ? number_format($item['amount'], 2, '.', ',') : '0.00' }}
                                    </div>
                                @else
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">₱</span>
                                        <input type="text"
                                            class="text-right border border-gray-300 rounded-lg pl-8 pr-3 py-2 w-full text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:text-white dark:border-neutral-600"
                                            x-model="display" @input="..."
                                            @blur="$wire.set('{{ $model }}.{{ $rowIndex }}.amount', display)"
                                            inputmode="decimal" placeholder="0.00" />
                                    </div>
                                @endif
                            </div>
                        </td>

                        @if ($viewOnly)
                            <td class="px-4 py-3">
                                @if (!empty($item['stage']))
                                    <div class="flex justify-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="truncate max-w-[150px]">{{ $item['stage'] }}</span>
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm">—</div>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if (!empty($item['remark']))
                                    @php
                                        $remarkText = $item['remark'];
                                        $remarkColor = match (true) {
                                            str_contains($remarkText, 'Ongoing')
                                                => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            str_contains($remarkText, 'Awarded')
                                                => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                            str_contains($remarkText, 'Cancelled')
                                                => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            default
                                                => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                        };
                                        $remarkIcon = match (true) {
                                            str_contains($remarkText, 'Ongoing')
                                                => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>',
                                            str_contains($remarkText, 'Awarded')
                                                => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                                            str_contains($remarkText, 'Cancelled')
                                                => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                                            default => '',
                                        };
                                    @endphp
                                    <div class="flex justify-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold {{ $remarkColor }}">
                                            {!! $remarkIcon !!}
                                            <span class="truncate max-w-[150px]">{{ $remarkText }}</span>
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm">—</div>
                                @endif
                            </td>
                        @endif

                        @unless ($viewOnly)
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300 transition-colors"
                                    wire:click="removeItem({{ $rowIndex }})" title="Remove item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        @endunless
                    </tr>
                @endforeach
                @if (count($items) === 0)
                    <tr>
                        <td colspan="{{ $viewOnly ? 5 : 4 }}" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">No items added yet
                                </p>
                                <p class="text-gray-400 dark:text-gray-500 text-xs">Click the "Add Item" button to get
                                    started</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endif
