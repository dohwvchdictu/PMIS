<div>
    <div class="space-y-6">
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden relative">
            <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-500"></div>

            <!-- PR Number, Stage and Remarks -->
            <div class="absolute top-1.5 left-0 z-10 flex items-center">
                <!-- PR Number Badge -->
                <span
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-sm font-semibold bg-emerald-600 text-white shadow-sm {{ $form['procurement_type'] === 'perItem' ? 'rounded-br-lg' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>PR #{{ $form['pr_number'] ?? 'N/A' }}</span>
                </span>

                @if ($form['procurement_type'] === 'perLot')
                    <!-- Stage Badge -->
                    @if ($procurement->currentPrStage?->procurementStage)
                        <button type="button" wire:click="viewStageHistory" title="View stage history"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-sm font-semibold bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-colors cursor-pointer {{ !$procurement->currentLotRemark?->remark ? 'rounded-br-lg' : '' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span
                                class="truncate max-w-xs">{{ $procurement->currentPrStage->procurementStage->procurementstage ?? 'N/A' }}</span>
                        </button>
                    @endif

                    <!-- Remark Badge -->
                    @if ($procurement->currentLotRemark?->remark)
                        @php
                            $remarks = $procurement->currentLotRemark->remark->remarks ?? '';

                            $remarksColor = match (true) {
                                str_contains($remarks, 'Ongoing') => 'bg-yellow-600 text-white',
                                str_contains($remarks, 'Awarded') => 'bg-green-600 text-white',
                                str_contains($remarks, 'Cancelled') => 'bg-red-600 text-white',
                                default => 'bg-gray-600 text-white',
                            };

                            $remarkIcon = match (true) {
                                str_contains($remarks, 'Ongoing')
                                    => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>',
                                str_contains($remarks, 'Awarded')
                                    => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                                str_contains($remarks, 'Cancelled')
                                    => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                                default => '',
                            };
                        @endphp
                        <span
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-br-lg text-sm font-semibold {{ $remarksColor }} shadow-sm">
                            {!! $remarkIcon !!}
                            <span class="truncate max-w-xs">{{ $remarks }}</span>
                        </span>
                    @endif
                @endif
            </div>

            <div class="p-6 pt-8">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Procurement Program / Project</p>
                        <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                            {{ $form['procurement_program_project'] ?? 'No project description available' }}
                        </h1>
                    </div>
                    @can('view_procurement')
                        @if (!empty($procurement->bacApprovedPr?->filepath))
                            <a href="{{ $procurement->bacApprovedPr->filepath }}" target="_blank" rel="noopener noreferrer"
                                title="View PR"
                                class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 group-hover:scale-110 transition-transform">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <div
            class="bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 overflow-hidden">
            <ul class="flex items-center w-full px-4 py-3">
                {{-- Step 1: Basic Details --}}
                <li class="flex items-center flex-1">
                    <button type="button" wire:click="setStep(1)"
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 hover:scale-105 shadow-md {{ $activeTab == 1 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400' : ($this->hasMopData ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-emerald-600 text-white') }}">
                        1
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500' }}">
                        Details
                    </span>
                    <div
                        class="h-px flex-1 mx-3 transition-all duration-300 {{ $activeTab > 1 || $this->hasMopData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-600' }}">
                    </div>
                </li>

                {{-- Step 2: Mode of Procurement --}}
                <li class="flex items-center flex-1">
                    <button type="button" wire:click="setStep(2)" @if (!$this->hasMopData) disabled @endif
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 shadow-md {{ $activeTab == 2 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400 hover:scale-105' : ($this->hasMopData ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-105' : 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-neutral-600') }}">
                        2
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 2 || $this->hasMopData ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                        Mode of Procurement
                    </span>
                    <div
                        class="h-px flex-1 mx-3 transition-all duration-300 {{ $activeTab > 2 || $this->hasPostData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-600' }}">
                    </div>
                </li>

                {{-- Step 3: Post Procurement --}}
                <li class="flex items-center flex-1">
                    <button type="button" wire:click="setStep(3)" @if (!$this->hasPostData) disabled @endif
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 shadow-md {{ $activeTab == 3 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400 hover:scale-105' : ($this->hasPostData ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-105' : 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-neutral-600') }}">
                        3
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 3 || $this->hasPostData ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                        Post Procurement
                    </span>
                    <div
                        class="h-px flex-1 mx-3 transition-all duration-300 {{ $activeTab > 3 || $this->hasPmuData ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-600' }}">
                    </div>
                </li>

                {{-- Step 4: PMU --}}
                <li class="flex items-center">
                    <button type="button" wire:click="setStep(4)" @if (!$this->hasPmuData) disabled @endif
                        class="size-10 flex justify-center items-center rounded-full font-semibold text-sm transition-all duration-200 shadow-md {{ $activeTab == 4 ? 'bg-emerald-600 text-white ring-3 ring-emerald-400 dark:ring-emerald-400 hover:scale-105' : ($this->hasPmuData ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-105' : 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-neutral-600') }}">
                        4
                    </button>
                    <span
                        class="ml-2 text-sm font-semibold whitespace-nowrap {{ $activeTab >= 4 || $this->hasPmuData ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                        PMU
                    </span>
                </li>
            </ul>
        </div>

        {{-- Tab Content --}}
        <div>
            @if ($activeTab == 1)
                {{-- Basic Details Tab --}}
                <div class="space-y-6 mb-6">

                    @if ($form['procurement_type'] === 'perItem')
                        <div
                            class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                            {{-- Header with Search and Toggle --}}
                            <div
                                class="flex items-center justify-between gap-4 mb-4 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                <div class="flex-1">
                                    <h3
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        Item List
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Procurement items and details
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="text" wire:model.live.debounce.300ms="itemSearchTerm"
                                        placeholder="Search items..."
                                        class="w-full max-w-xs px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                    <button type="button" wire:click="$toggle('showTable')"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $showTable ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-600 dark:text-gray-300 dark:hover:bg-neutral-500' }}">
                                        @if (!$showTable)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <span>Show</span>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                            <span>Hide</span>
                                        @endif
                                    </button>
                                </div>
                            </div>

                            {{-- Table Section --}}
                            @if ($showTable)
                                @php
                                    // Apply search filter
                                    $allItems = $form['items'] ?? [];
                                    $searchLower = strtolower($itemSearchTerm ?? '');
                                    $filteredItems = empty($itemSearchTerm)
                                        ? $allItems
                                        : array_filter($allItems, function ($item) use ($searchLower) {
                                            return str_contains(strtolower($item['description'] ?? ''), $searchLower) ||
                                                str_contains(strtolower($item['item_no'] ?? ''), $searchLower);
                                        });

                                    $totalItems = count($filteredItems);
                                    $currentPage = $page ?? 1;
                                    $itemsPerPage = $perPage ?? 10;
                                    $totalPages = max(1, ceil($totalItems / $itemsPerPage));
                                @endphp

                                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-neutral-600">
                                    <x-forms.prItems-table :form="$form" model="form.items" :page="$page"
                                        :per-page="$perPage" :viewOnly="true" :filteredItems="$filteredItems" />
                                </div>

                                {{-- Pagination Controls --}}
                                @php
                                @endphp

                                @if ($totalItems > 0)
                                    <div
                                        class="mt-4 flex items-center justify-between flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-neutral-600">
                                        {{-- Items per page --}}
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600 dark:text-gray-400">Show:</label>
                                            <select wire:model.live="perPage"
                                                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">per page</span>
                                        </div>

                                        {{-- Center: Pagination --}}
                                        <div class="flex flex-col items-center gap-2 flex-1">
                                            {{-- Page info --}}
                                            <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                Showing <span
                                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalItems > 0 ? ($currentPage - 1) * $itemsPerPage + 1 : 0 }}</span>
                                                to
                                                <span
                                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ min($currentPage * $itemsPerPage, $totalItems) }}</span>
                                                of
                                                <span
                                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalItems }}</span>
                                                items
                                            </div>

                                            @if ($totalPages > 1)
                                                {{-- Pagination buttons --}}
                                                <div class="flex items-center gap-1">
                                                    {{-- Previous Button --}}
                                                    <button type="button"
                                                        wire:click="$set('page', {{ max(1, $currentPage - 1) }})"
                                                        @if ($currentPage <= 1) disabled @endif
                                                        class="p-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 19l-7-7 7-7" />
                                                        </svg>
                                                    </button>

                                                    {{-- Page Numbers --}}
                                                    @for ($i = 1; $i <= $totalPages; $i++)
                                                        @if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2)
                                                            <button type="button"
                                                                wire:click="$set('page', {{ $i }})"
                                                                class="px-3 py-1.5 text-xs font-medium border rounded-lg transition-colors duration-150 {{ $currentPage == $i ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'border-gray-300 hover:bg-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white' }}">
                                                                {{ $i }}
                                                            </button>
                                                        @elseif (abs($i - $currentPage) == 3)
                                                            <span class="px-2 text-xs text-gray-500">...</span>
                                                        @endif
                                                    @endfor

                                                    {{-- Next Button --}}
                                                    <button type="button"
                                                        wire:click="$set('page', {{ min($totalPages, $currentPage + 1) }})"
                                                        @if ($currentPage >= $totalPages) disabled @endif
                                                        class="p-1.5 text-xs font-medium border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-neutral-600 dark:hover:bg-neutral-700 dark:text-white transition-colors duration-150">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            <span class="font-medium">No items found</span>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    {{-- Category and Division Details --}}
                    <div
                        class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="mb-4 pb-3 border-b border-gray-200 dark:border-neutral-600">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Category and Division Information
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if ($form['date_receipt'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Date Receipt</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['date_receipt'] }}</p>
                                </div>
                            @endif
                            @if ($form['category_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Category</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $categories->firstWhere('id', $form['category_id'])?->category ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                            @if ($form['category_type'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Category Type</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['category_type'] }}</p>
                                </div>
                            @endif
                            @if ($form['rbac_sbac'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">RBAC / SBAC</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['rbac_sbac'] }}</p>
                                </div>
                            @endif
                            @if ($form['dtrack_no'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">DTRACK #</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['dtrack_no'] }}</p>
                                </div>
                            @endif
                            @if ($form['unicode'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">UniCode</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['unicode'] }}</p>
                                </div>
                            @endif
                            @if ($form['divisions_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Division</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $divisions->firstWhere('id', $form['divisions_id'])?->divisions ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                            @if ($form['cluster_committees_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Cluster / Committee</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $clusterCommittees->firstWhere('id', $form['cluster_committees_id'])?->clustercommittee ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Venue Details --}}
                    <div
                        class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="mb-4 pb-3 border-b border-gray-200 dark:border-neutral-600">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Venue Information
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if ($form['venue_specific_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Venue | Specific</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $venueSpecifics->firstWhere('id', $form['venue_specific_id'])?->name ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                            @if ($form['venue_province_huc_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Venue | Province/HUC</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $venueProvinces->firstWhere('id', $form['venue_province_huc_id'])?->province_huc ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                            @if ($form['category_venue'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Category / Venue</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['category_venue'] }}</p>
                                </div>
                            @endif
                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Approved PPMP</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $form['approved_ppmp'] ? 'Yes' : 'No' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">APP Updated</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $form['app_updated'] ? 'Yes' : 'No' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Date Needed and End User --}}
                    <div
                        class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="mb-4 pb-3 border-b border-gray-200 dark:border-neutral-600">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Timeline and End User
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if ($form['immediate_date_needed'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Immediate Date Needed</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['immediate_date_needed'] }}</p>
                                </div>
                            @endif
                            @if ($form['date_needed'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Date Needed</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['date_needed'] }}</p>
                                </div>
                            @endif
                            @if ($form['end_users_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PMO/End-User</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $endUsers->firstWhere('id', $form['end_users_id'])?->endusers ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Early Procurement</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $form['early_procurement'] ? 'Yes' : 'No' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Fund Source and ABC --}}
                    <div
                        class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                        <div class="mb-4 pb-3 border-b border-gray-200 dark:border-neutral-600">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Budget Information
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if ($form['fund_source_id'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Source of Funds</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $fundSources->firstWhere('id', $form['fund_source_id'])?->fundsources ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif
                            @if ($form['expense_class'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Expense Class</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $form['expense_class'] }}</p>
                                </div>
                            @endif
                            @if ($form['abc'])
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ABC Amount</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        ₱{{ number_format($form['abc'], 2) }}
                                    </p>
                                </div>
                            @endif
                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ABC ⇔ 50k</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $form['abc_50k'] ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            @if ($activeTab == 2)
                {{-- Mode of Procurement Tab --}}
                <div class="space-y-6 mb-6">
                    @if ($form['procurement_type'] === 'perLot')
                        {{-- PER LOT DISPLAY with History Toggle --}}
                        @php
                            $filteredItems = collect($form['mop_items'] ?? [])
                                ->filter(function ($item) {
                                    return $item['uid'] !== 'MOP-1-1';
                                })
                                ->sortBy('mode_order')
                                ->values()
                                ->reverse()
                                ->values();

                            $hasAnyHistory = $filteredItems->count() > 1;
                        @endphp

                        @if ($filteredItems->isNotEmpty())
                            @php
                                $currentMode = $filteredItems->first();
                                $historyModes = $filteredItems->slice(1);
                            @endphp

                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                {{-- Header with Mode and Order --}}
                                <div
                                    class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-sm">
                                                {{ $currentMode['mode_order'] }}
                                            </span>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $currentMode['mode_of_procurement_name'] }}
                                            </h3>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if ($currentMode['bidding_result'])
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentMode['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ $currentMode['bidding_result'] }}
                                            </span>
                                        @endif

                                        {{-- Toggle Button for History --}}
                                        @if ($hasAnyHistory)
                                            <button type="button" wire:click="toggleMopSection('lot_history')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-600 transition-colors"
                                                title="{{ $mopToggles['lot_history'] ?? false ? 'Hide History' : 'Show History' }}">
                                                @if ($mopToggles['lot_history'] ?? false)
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-emerald-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-emerald-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Current Mode Data --}}
                                @php
                                    $modeId = $currentMode['mode_of_procurement_id'];
                                    $hasBiddingData =
                                        !in_array($modeId, [
                                            1,
                                            7,
                                            8,
                                            9,
                                            10,
                                            11,
                                            12,
                                            13,
                                            14,
                                            15,
                                            16,
                                            17,
                                            18,
                                            19,
                                            20,
                                            21,
                                            22,
                                            23,
                                            24,
                                        ]) &&
                                        ($currentMode['bidding_number'] ||
                                            $currentMode['ib_number'] ||
                                            $currentMode['philgeps_posting_ref_no'] ||
                                            $currentMode['ads_post_ib'] ||
                                            $currentMode['pre_proc_conference'] ||
                                            $currentMode['list_invited_observerspre_bid_conf'] ||
                                            $currentMode['obsrvr_prebid_conf'] ||
                                            $currentMode['obsrvr_eligibility'] ||
                                            $currentMode['obsrvr_sub_open_of_bid'] ||
                                            $currentMode['obsrvr_bid'] ||
                                            $currentMode['obsrvr_post_qual'] ||
                                            $currentMode['obsrvr_post_qual'] ||
                                            $currentMode['pre_bid_conf'] ||
                                            $currentMode['eligibility_check'] ||
                                            $currentMode['sub_open_bids'] ||
                                            $currentMode['bid_evaluation_date'] ||
                                            $currentMode['post_qualification_date'] ||
                                            $currentMode['bidding_result'] ||
                                            $currentMode['resolution_number_mop']);

                                    $hasSvpData =
                                        in_array($modeId, [
                                            7,
                                            8,
                                            9,
                                            10,
                                            11,
                                            12,
                                            13,
                                            14,
                                            15,
                                            16,
                                            17,
                                            18,
                                            19,
                                            20,
                                            21,
                                            22,
                                            23,
                                            24,
                                        ]) &&
                                        ($currentMode['philgeps_posting_ref_no'] ||
                                            $currentMode['ads_post_ib'] ||
                                            $currentMode['resolution_number_mop'] ||
                                            $currentMode['rfq_no'] ||
                                            $currentMode['canvass_date'] ||
                                            $currentMode['date_returned_of_canvass'] ||
                                            $currentMode['abstract_of_canvass_date']);
                                @endphp

                                {{-- Mode Information --}}
                                @if ($hasBiddingData)
                                    <div class="mb-6">
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Mode Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            @if ($currentMode['bidding_number'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bidding #
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['bidding_number'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['ib_number'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">IB No.</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['ib_number'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['philgeps_posting_ref_no'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PhilGEPS
                                                        Posting Ref #</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['philgeps_posting_ref_no'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['ads_post_ib'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ads/Post
                                                        IB</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['ads_post_ib'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['pre_proc_conference'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pre-Proc
                                                        Conference</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['pre_proc_conference'] }}</p>
                                                </div>
                                            @endif
                                            {{-- Observer fields only for competitive bidding modes 2-6 --}}
                                            @if (in_array($modeId, [2, 3, 4, 5, 6]))
                                                @if ($currentMode['list_invited_observers'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">List
                                                            of Invited Observers</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentMode['list_invited_observers'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentMode['obsrvr_prebid_conf'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Observer Pre-Bid Conf</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentMode['obsrvr_prebid_conf'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentMode['obsrvr_eligibility'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Observer Eligibility</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentMode['obsrvr_eligibility'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentMode['obsrvr_sub_open_of_bid'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Observer Sub/Open of Bid</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentMode['obsrvr_sub_open_of_bid'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentMode['obsrvr_bid'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Observer Bid</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentMode['obsrvr_bid'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentMode['obsrvr_post_qual'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Observer Post Qual</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentMode['obsrvr_post_qual'] }}</p>
                                                    </div>
                                                @endif
                                            @endif
                                            @if ($currentMode['pre_bid_conf'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pre-Bid
                                                        Conference</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['pre_bid_conf'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['eligibility_check'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                        Eligibility Check</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['eligibility_check'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['sub_open_bids'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sub/Open
                                                        of Bids</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['sub_open_bids'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['bid_evaluation_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bid
                                                        Evaluation Date</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['bid_evaluation_date'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['post_qualification_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Post
                                                        Qualification Date</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['post_qualification_date'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['resolution_number_mop'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Resolution
                                                        #</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['resolution_number_mop'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($hasSvpData)
                                    <div>
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                            Mode Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            @if ($currentMode['philgeps_posting_ref_no'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PhilGEPS
                                                        Posting Ref #</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['philgeps_posting_ref_no'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['ads_post_ib'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ads/Post
                                                        IB</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['ads_post_ib'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['resolution_number_mop'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Resolution
                                                        #</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['resolution_number_mop'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['rfq_no'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">RFQ No.
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['rfq_no'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['canvass_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Canvass
                                                        Date</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['canvass_date'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['date_returned_of_canvass'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Returned
                                                        of Canvass</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['date_returned_of_canvass'] }}</p>
                                                </div>
                                            @endif
                                            @if ($currentMode['abstract_of_canvass_date'])
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Abstract
                                                        of Canvass</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $currentMode['abstract_of_canvass_date'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- History Section --}}
                                @if ($hasAnyHistory && ($mopToggles['lot_history'] ?? false))
                                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-600">
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Mode History
                                        </h4>

                                        <div class="space-y-4">
                                            @foreach ($historyModes as $historyItem)
                                                @php
                                                    $historyModeId = $historyItem['mode_of_procurement_id'];

                                                    $hasHistoryBidding =
                                                        !in_array($historyModeId, [
                                                            1,
                                                            7,
                                                            8,
                                                            9,
                                                            10,
                                                            11,
                                                            12,
                                                            13,
                                                            14,
                                                            15,
                                                            16,
                                                            17,
                                                            18,
                                                            19,
                                                            20,
                                                            21,
                                                            22,
                                                            23,
                                                            24,
                                                        ]) &&
                                                        ($currentMode['bidding_number'] ||
                                                            $currentMode['ib_number'] ||
                                                            $currentMode['philgeps_posting_ref_no'] ||
                                                            $currentMode['ads_post_ib'] ||
                                                            $currentMode['pre_proc_conference'] ||
                                                            $currentMode['list_invited_observerspre_bid_conf'] ||
                                                            $currentMode['obsrvr_prebid_conf'] ||
                                                            $currentMode['obsrvr_eligibility'] ||
                                                            $currentMode['obsrvr_sub_open_of_bid'] ||
                                                            $currentMode['obsrvr_bid'] ||
                                                            $currentMode['obsrvr_post_qual'] ||
                                                            $currentMode['obsrvr_post_qual'] ||
                                                            $currentMode['pre_bid_conf'] ||
                                                            $currentMode['eligibility_check'] ||
                                                            $currentMode['sub_open_bids'] ||
                                                            $currentMode['bid_evaluation_date'] ||
                                                            $currentMode['post_qualification_date'] ||
                                                            $currentMode['bidding_result'] ||
                                                            $currentMode['resolution_number_mop']);

                                                    $hasHistorySvp =
                                                        in_array($historyModeId, [
                                                            7,
                                                            8,
                                                            9,
                                                            10,
                                                            11,
                                                            12,
                                                            13,
                                                            14,
                                                            15,
                                                            16,
                                                            17,
                                                            18,
                                                            19,
                                                            20,
                                                            21,
                                                            22,
                                                            23,
                                                            24,
                                                        ]) &&
                                                        ($currentMode['philgeps_posting_ref_no'] ||
                                                            $currentMode['ads_post_ib'] ||
                                                            $currentMode['resolution_number_mop'] ||
                                                            $currentMode['rfq_no'] ||
                                                            $currentMode['canvass_date'] ||
                                                            $currentMode['date_returned_of_canvass'] ||
                                                            $currentMode['abstract_of_canvass_date']);
                                                @endphp

                                                <div
                                                    class="bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-4 border border-gray-200 dark:border-neutral-700">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex items-center gap-2">
                                                            <span
                                                                class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-gray-400 font-semibold text-xs">
                                                                {{ $historyItem['mode_order'] }}
                                                            </span>
                                                            <span
                                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                {{ $historyItem['mode_of_procurement_name'] }}
                                                            </span>
                                                        </div>
                                                        @if ($historyItem['bidding_result'])
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $historyItem['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                                {{ $historyItem['bidding_result'] }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if ($hasHistoryBidding)
                                                        <div class="mb-3">
                                                            <p
                                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                Mode Information</p>
                                                            <div
                                                                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                @if ($historyItem['bidding_number'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Bidding #</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['bidding_number'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['ib_number'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            IB No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['ib_number'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['philgeps_posting_ref_no'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            IB No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['philgeps_posting_ref_no'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['ads_post_ib'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            IB No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['ads_post_ib'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['pre_proc_conference'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['pre_proc_conference'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['list_invited_observers'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['list_invited_observers'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['obsrvr_prebid_conf'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['obsrvr_prebid_conf'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['obsrvr_eligibility'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['obsrvr_eligibility'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['obsrvr_sub_open_of_bid'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['obsrvr_sub_open_of_bid'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['obsrvr_bid'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['obsrvr_bid'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['obsrvr_post_qual'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['obsrvr_post_qual'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['obsrvr_post_qual'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Proc Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['obsrvr_post_qual'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['pre_bid_conf'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Pre-Bid Conf</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['pre_bid_conf'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['eligibility_check'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Eligibility</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['eligibility_check'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['sub_open_bids'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Sub/Open Bids</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['sub_open_bids'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['bid_evaluation_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Sub/Open Bids</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['bid_evaluation_date'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['post_qualification_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Sub/Open Bids</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['post_qualification_date'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['bidding_result'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Sub/Open Bids</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['bidding_result'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['resolution_number_mop'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Sub/Open Bids</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['resolution_number_mop'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($hasHistorySvp)
                                                        <div>
                                                            <p
                                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                Mode Information</p>
                                                            <div
                                                                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                @if ($historyItem['philgeps_posting_ref_no'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            RFQ No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['philgeps_posting_ref_no'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['ads_post_ib'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            RFQ No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['ads_post_ib'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['resolution_number_mop'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            RFQ No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['resolution_number_mop'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['rfq_no'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            RFQ No.</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['rfq_no'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['canvass_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Canvass Date</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['canvass_date'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['date_returned_of_canvass'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Returned</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['date_returned_of_canvass'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                                @if ($historyItem['abstract_of_canvass_date'])
                                                                    <div>
                                                                        <p
                                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                                            Abstract</p>
                                                                        <p
                                                                            class="text-xs font-medium text-gray-900 dark:text-white">
                                                                            {{ $historyItem['abstract_of_canvass_date'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No mode of procurement
                                        data available</p>
                                </div>
                            </div>
                        @endif
                    @elseif ($form['procurement_type'] === 'perItem')
                        {{-- PER ITEM DISPLAY WITH SEARCH AND PAGINATION --}}
                        @php
                            // Group items by prItemID to show current mode + history
                            $groupedItems = collect($form['items'] ?? [])->groupBy('prItemID');

                            // Apply search filter
                            if (!empty($mopSearchTerm)) {
                                $groupedItems = $groupedItems->filter(function ($itemGroup) {
                                    $firstItem = $itemGroup->first();
                                    return stripos($firstItem['description'] ?? '', $this->mopSearchTerm) !== false;
                                });
                            }

                            $totalItems = $groupedItems->count();

                            // Pagination settings
                            $currentPage = $page ?? 1;
                            $itemsPerPage = $perPage ?? 10;
                            $totalPages = ceil($totalItems / $itemsPerPage);

                            // Paginate grouped items
                            $paginatedGroups = $groupedItems->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage);
                        @endphp

                        @if ($totalItems > 0)
                            {{-- Search and Pagination Controls (One Row) --}}
                            <div
                                class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    {{-- Left: Search --}}
                                    <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                                        <div class="relative flex-1 max-w-md">
                                            <input type="text" wire:model.live.debounce.300ms="mopSearchTerm"
                                                placeholder="Search by item name..."
                                                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        @if (!empty($mopSearchTerm))
                                            <button wire:click="$set('mopSearchTerm', '')"
                                                class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                                Clear
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Center: Item Count --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Showing
                                            {{ $totalItems > 0 ? ($currentPage - 1) * $itemsPerPage + 1 : 0 }} to
                                            {{ min($currentPage * $itemsPerPage, $totalItems) }} of
                                            {{ $totalItems }} items
                                        </span>
                                    </div>

                                    {{-- Right: Items Per Page --}}
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Items per page:</label>
                                        <select wire:model.live="perPage"
                                            class="px-3 py-1 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="{{ $totalItems }}">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Item Cards --}}
                            @foreach ($paginatedGroups as $prItemID => $itemGroup)
                                @php
                                    // Get the most recent item (first in the group, assuming sorted by mode_order desc)
                                    $currentItem = $itemGroup->first();
                                    $hasHistory = $itemGroup->count() > 1;
                                    $isExpanded = $mopToggles[$prItemID] ?? false;
                                @endphp

                                <div
                                    class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    {{-- Item Header --}}
                                    <div
                                        class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span
                                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-sm">
                                                    {{ $currentItem['item_no'] }}
                                                </span>
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $currentItem['description'] }}
                                                </h3>
                                            </div>
                                            <div
                                                class="flex items-center flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                @php
                                                    $mode = $modeOfProcurements->firstWhere(
                                                        'id',
                                                        $currentItem['mode_of_procurement_id'],
                                                    );
                                                @endphp
                                                <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                                    {{ $mode?->modeofprocurements ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if ($currentItem['bidding_result'])
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentItem['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $currentItem['bidding_result'] }}
                                                </span>
                                            @endif

                                            @if ($hasHistory)
                                                <button type="button"
                                                    wire:click="toggleMopSection('{{ $prItemID }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-600 transition-colors"
                                                    title="{{ $isExpanded ? 'Hide History' : 'Show History' }}">
                                                    @if ($isExpanded)
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-emerald-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-emerald-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Current Mode Data --}}
                                    @php
                                        $modeId = $currentItem['mode_of_procurement_id'];
                                        $hasBiddingData =
                                            !in_array($modeId, [
                                                1,
                                                7,
                                                8,
                                                9,
                                                10,
                                                11,
                                                12,
                                                13,
                                                14,
                                                15,
                                                16,
                                                17,
                                                18,
                                                19,
                                                20,
                                                21,
                                                22,
                                                23,
                                                24,
                                            ]) &&
                                            ($currentItem['bidding_number'] ||
                                                $currentItem['ib_number'] ||
                                                $currentItem['philgeps_posting_ref_no'] ||
                                                $currentItem['ads_post_ib'] ||
                                                $currentItem['pre_proc_conference'] ||
                                                $currentItem['list_invited_observerspre_bid_conf'] ||
                                                $currentItem['obsrvr_prebid_conf'] ||
                                                $currentItem['obsrvr_eligibility'] ||
                                                $currentItem['obsrvr_sub_open_of_bid'] ||
                                                $currentItem['obsrvr_bid'] ||
                                                $currentItem['obsrvr_post_qual'] ||
                                                $currentItem['obsrvr_post_qual'] ||
                                                $currentItem['pre_bid_conf'] ||
                                                $currentItem['eligibility_check'] ||
                                                $currentItem['sub_open_bids'] ||
                                                $currentItem['bid_evaluation_date'] ||
                                                $currentItem['post_qualification_date'] ||
                                                $currentItem['bidding_result'] ||
                                                $currentItem['resolution_number_mop']);

                                        $hasSvpData =
                                            in_array($modeId, [
                                                7,
                                                8,
                                                9,
                                                10,
                                                11,
                                                12,
                                                13,
                                                14,
                                                15,
                                                16,
                                                17,
                                                18,
                                                19,
                                                20,
                                                21,
                                                22,
                                                23,
                                                24,
                                            ]) &&
                                            ($currentItem['philgeps_posting_ref_no'] ||
                                                $currentItem['ads_post_ib'] ||
                                                $currentItem['resolution_number_mop'] ||
                                                $currentItem['rfq_no'] ||
                                                $currentItem['canvass_date'] ||
                                                $currentItem['date_returned_of_canvass'] ||
                                                $currentItem['abstract_of_canvass_date']);
                                    @endphp

                                    {{-- Mode Information --}}
                                    @if ($hasBiddingData)
                                        <div class="mb-6">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Mode Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                                @if ($currentItem['bidding_number'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Bidding #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['bidding_number'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['ib_number'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">IB No.
                                                        </p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['ib_number'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['philgeps_posting_ref_no'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            PhilGEPS Posting Ref #
                                                        </p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['philgeps_posting_ref_no'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['ads_post_ib'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Ads/Post IB</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['ads_post_ib'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['pre_proc_conference'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Pre-Proc Conference</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['pre_proc_conference'] }}</p>
                                                    </div>
                                                @endif
                                                {{-- Observer fields only for competitive bidding modes 2-6 --}}
                                                @if (in_array($modeId, [2, 3, 4, 5, 6]))
                                                    @if ($currentItem['list_invited_observers'])
                                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                List of Invited Observers</p>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $currentItem['list_invited_observers'] }}</p>
                                                        </div>
                                                    @endif
                                                    @if ($currentItem['obsrvr_prebid_conf'])
                                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                Observer Pre-Bid Conf</p>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $currentItem['obsrvr_prebid_conf'] }}</p>
                                                        </div>
                                                    @endif
                                                    @if ($currentItem['obsrvr_eligibility'])
                                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                Observer Eligibility</p>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $currentItem['obsrvr_eligibility'] }}</p>
                                                        </div>
                                                    @endif
                                                    @if ($currentItem['obsrvr_sub_open_of_bid'])
                                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                Observer Sub/Open of Bid</p>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $currentItem['obsrvr_sub_open_of_bid'] }}</p>
                                                        </div>
                                                    @endif
                                                    @if ($currentItem['obsrvr_bid'])
                                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                Observer Bid</p>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $currentItem['obsrvr_bid'] }}</p>
                                                        </div>
                                                    @endif
                                                    @if ($currentItem['obsrvr_post_qual'])
                                                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                Observer Post Qual</p>
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $currentItem['obsrvr_post_qual'] }}</p>
                                                        </div>
                                                    @endif
                                                @endif
                                                @if ($currentItem['pre_bid_conf'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Pre-Bid Conference</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['pre_bid_conf'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['eligibility_check'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Eligibility Check</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['eligibility_check'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['sub_open_bids'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Sub/Open of Bids</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['sub_open_bids'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['bid_evaluation_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bid
                                                            Evaluation Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['bid_evaluation_date'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['post_qualification_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Post
                                                            Qualification Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['post_qualification_date'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['resolution_number_mop'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Resolution #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['resolution_number_mop'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if ($hasSvpData)
                                        <div>
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                                Mode Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                                @if ($currentItem['philgeps_posting_ref_no'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            PhilGEPS Posting Ref #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['philgeps_posting_ref_no'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['ads_post_ib'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Ads/Post IB</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['ads_post_ib'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['resolution_number_mop'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Resolution #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['resolution_number_mop'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['rfq_no'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">RFQ
                                                            No.</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['rfq_no'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['canvass_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Canvass Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['canvass_date'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['date_returned_of_canvass'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Returned of Canvass</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['date_returned_of_canvass'] }}</p>
                                                    </div>
                                                @endif
                                                @if ($currentItem['abstract_of_canvass_date'])
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Abstract of Canvass</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $currentItem['abstract_of_canvass_date'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- History Section --}}
                                    @if ($hasHistory && $isExpanded)
                                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-600">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Mode History
                                            </h4>

                                            <div class="space-y-4">
                                                @foreach ($itemGroup->skip(1) as $historyItem)
                                                    @php
                                                        $historyModeId = $historyItem['mode_of_procurement_id'];
                                                        $historyMode = $modeOfProcurements->firstWhere(
                                                            'id',
                                                            $historyModeId,
                                                        );

                                                        $hasHistoryBidding =
                                                            !in_array($historyModeId, [
                                                                1,
                                                                7,
                                                                8,
                                                                9,
                                                                10,
                                                                11,
                                                                12,
                                                                13,
                                                                14,
                                                                15,
                                                                16,
                                                                17,
                                                                18,
                                                                19,
                                                                20,
                                                                21,
                                                                22,
                                                                23,
                                                                24,
                                                            ]) &&
                                                            ($currentMode['bidding_number'] ||
                                                                $currentMode['ib_number'] ||
                                                                $currentMode['philgeps_posting_ref_no'] ||
                                                                $currentMode['ads_post_ib'] ||
                                                                $currentMode['pre_proc_conference'] ||
                                                                $currentMode['list_invited_observerspre_bid_conf'] ||
                                                                $currentMode['obsrvr_prebid_conf'] ||
                                                                $currentMode['obsrvr_eligibility'] ||
                                                                $currentMode['obsrvr_sub_open_of_bid'] ||
                                                                $currentMode['obsrvr_bid'] ||
                                                                $currentMode['obsrvr_post_qual'] ||
                                                                $currentMode['obsrvr_post_qual'] ||
                                                                $currentMode['pre_bid_conf'] ||
                                                                $currentMode['eligibility_check'] ||
                                                                $currentMode['sub_open_bids'] ||
                                                                $currentMode['bid_evaluation_date'] ||
                                                                $currentMode['post_qualification_date'] ||
                                                                $currentMode['bidding_result'] ||
                                                                $currentMode['resolution_number_mop']);

                                                        $hasHistorySvp =
                                                            in_array($historyModeId, [
                                                                7,
                                                                8,
                                                                9,
                                                                10,
                                                                11,
                                                                12,
                                                                13,
                                                                14,
                                                                15,
                                                                16,
                                                                17,
                                                                18,
                                                                19,
                                                                20,
                                                                21,
                                                                22,
                                                                23,
                                                                24,
                                                            ]) &&
                                                            ($currentMode['philgeps_posting_ref_no'] ||
                                                                $currentMode['ads_post_ib'] ||
                                                                $currentMode['resolution_number_mop'] ||
                                                                $currentMode['rfq_no'] ||
                                                                $currentMode['canvass_date'] ||
                                                                $currentMode['date_returned_of_canvass'] ||
                                                                $currentMode['abstract_of_canvass_date']);
                                                    @endphp

                                                    <div
                                                        class="bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-4 border border-gray-200 dark:border-neutral-700">
                                                        <div class="flex items-center justify-between mb-3">
                                                            <span
                                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                {{ $historyMode?->modeofprocurements ?? 'N/A' }}
                                                            </span>
                                                            @if ($historyItem['bidding_result'])
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $historyItem['bidding_result'] === 'SUCCESSFUL' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                                    {{ $historyItem['bidding_result'] }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if ($hasHistoryBidding)
                                                            <div class="mb-3">
                                                                <p
                                                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                    Mode Information</p>
                                                                <div
                                                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                    @if ($historyItem['bidding_number'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Bidding #</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['bidding_number'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['ib_number'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                IB No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['ib_number'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['philgeps_posting_ref_no'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                IB No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['philgeps_posting_ref_no'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['ads_post_ib'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Ads/Post IB</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['ads_post_ib'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['pre_proc_conference'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['pre_proc_conference'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['list_invited_observers'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['list_invited_observers'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['obsrvr_prebid_conf'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['obsrvr_prebid_conf'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['obsrvr_eligibility'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['obsrvr_eligibility'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['obsrvr_sub_open_of_bid'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['obsrvr_sub_open_of_bid'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['obsrvr_bid'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['obsrvr_bid'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['obsrvr_post_qual'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Proc Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['obsrvr_post_qual'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['pre_bid_conf'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Pre-Bid Conf</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['pre_bid_conf'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['eligibility_check'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Eligibility</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['eligibility_check'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['sub_open_bids'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Sub/Open Bids</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['sub_open_bids'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['bid_evaluation_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Sub/Open Bids</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['bid_evaluation_date'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['post_qualification_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Sub/Open Bids</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['post_qualification_date'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['bidding_result'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Sub/Open Bids</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['bidding_result'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['resolution_number_mop'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Sub/Open Bids</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['resolution_number_mop'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($hasHistorySvp)
                                                            <div>
                                                                <p
                                                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">
                                                                    Mode Information</p>
                                                                <div
                                                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                                    @if ($historyItem['philgeps_posting_ref_no'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                RFQ No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['philgeps_posting_ref_no'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['ads_post_ib'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                RFQ No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['ads_post_ib'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['resolution_number_mop'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                RFQ No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['resolution_number_mop'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['rfq_no'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                RFQ No.</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['rfq_no'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['canvass_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Canvass Date</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['canvass_date'] }}</p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['date_returned_of_canvass'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Returned</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['date_returned_of_canvass'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                    @if ($historyItem['abstract_of_canvass_date'])
                                                                        <div>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                Abstract</p>
                                                                            <p
                                                                                class="text-xs font-medium text-gray-900 dark:text-white">
                                                                                {{ $historyItem['abstract_of_canvass_date'] }}
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Pagination Controls --}}
                            @if ($totalPages > 1)
                                <div
                                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        {{-- Previous Button --}}
                                        <button type="button"
                                            wire:click="$set('page', {{ max(1, $currentPage - 1) }})"
                                            @if ($currentPage <= 1) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                        {{ $currentPage <= 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                            : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Previous
                                        </button>

                                        {{-- Page Numbers --}}
                                        <div class="flex items-center gap-2">
                                            @php
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($totalPages, $currentPage + 2);
                                            @endphp

                                            @if ($startPage > 1)
                                                <button type="button" wire:click="$set('page', 1)"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                {{ $currentPage == 1
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    1
                                                </button>
                                                @if ($startPage > 2)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                            @endif

                                            @for ($i = $startPage; $i <= $endPage; $i++)
                                                <button type="button"
                                                    wire:click="$set('page', {{ $i }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                {{ $currentPage == $i
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor

                                            @if ($endPage < $totalPages)
                                                @if ($endPage < $totalPages - 1)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                                <button type="button"
                                                    wire:click="$set('page', {{ $totalPages }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                {{ $currentPage == $totalPages
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $totalPages }}
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Next Button --}}
                                        <button type="button"
                                            wire:click="$set('page', {{ min($totalPages, $currentPage + 1) }})"
                                            @if ($currentPage >= $totalPages) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                        {{ $currentPage >= $totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                            : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            Next
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @elseif (!empty($mopSearchTerm))
                            {{-- No Results Found --}}
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No items found matching
                                        "{{ $mopSearchTerm }}"</p>
                                    <button wire:click="$set('mopSearchTerm', '')"
                                        class="mt-4 px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        Clear Search
                                    </button>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No mode of procurement
                                        data available</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            @if ($activeTab == 3)
                {{-- Post Procurement Tab --}}
                <div class="space-y-6 mb-6">
                    @if ($form['procurement_type'] === 'perLot')
                        {{-- PER LOT POST PROCUREMENT --}}
                        @if ($this->hasPostData)
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                {{-- Header --}}
                                <div
                                    class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-emerald-700 dark:text-emerald-300" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            Post-Procurement Details</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Award and contract
                                            information</p>
                                    </div>
                                </div>

                                {{-- Award Information --}}
                                <div class="mb-6">
                                    <h4
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Award Information
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                        @if ($resolutionAwardNumber)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Resolution
                                                    Award #
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $resolutionAwardNumber }}</p>
                                            </div>
                                        @endif
                                        @if ($resolutionAwardDate)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Resolution
                                                    Award Date
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $resolutionAwardDate }}</p>
                                            </div>
                                        @endif
                                        @if ($noticeOfAwardNumber)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notice of
                                                    Award Number
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $noticeOfAwardNumber }}</p>
                                            </div>
                                        @endif
                                        @if ($noticeOfAward)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notice of
                                                    Award Date
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $noticeOfAward }}</p>
                                            </div>
                                        @endif
                                        @if ($awardedAmount)
                                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Awarded Amount
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    ₱{{ number_format($awardedAmount, 2) }}</p>
                                            </div>
                                        @endif

                                    </div>
                                </div>

                                {{-- PhilGEPS Information --}}
                                @if ($philgepsNoticeOfAwardNo || $philgepsPostingOfAward)
                                    <div class="mb-6">
                                        <h4
                                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                            PhilGEPS Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @if ($philgepsNoticeOfAwardNo)
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PhilGEPS|
                                                        Notice of Award No.
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $philgepsNoticeOfAwardNo }}</p>
                                                </div>
                                            @endif
                                            @if ($philgepsPostingOfAward)
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PhilGEPS|
                                                        Posting of Award
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $philgepsPostingOfAward }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif



                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No post-procurement data
                                        available</p>
                                </div>
                            </div>
                        @endif
                    @elseif ($form['procurement_type'] === 'perItem')
                        {{-- PER ITEM POST PROCUREMENT WITH SEARCH AND PAGINATION --}}
                        @php
                            // Get items with post-procurement data
                            $itemsWithPostData = collect($form['items'] ?? [])
                                ->filter(function ($item) {
                                    $prItemID = $item['prItemID'] ?? null;
                                    return $prItemID &&
                                        isset($this->postItems[$prItemID]) &&
                                        !empty(array_filter($this->postItems[$prItemID] ?? []));
                                })
                                ->groupBy('prItemID')
                                ->map(fn($group) => $group->first());

                            // Apply search filter
                            if (!empty($postSearchTerm)) {
                                $itemsWithPostData = $itemsWithPostData->filter(function ($item) {
                                    return stripos($item['description'] ?? '', $this->postSearchTerm) !== false;
                                });
                            }

                            $totalPostItems = $itemsWithPostData->count();

                            // Pagination settings
                            $currentPostPage = $postPage ?? 1;
                            $itemsPerPostPage = $postPerPage ?? 10;
                            $totalPostPages = ceil($totalPostItems / $itemsPerPostPage);

                            // Paginate items
                            $paginatedPostItems = $itemsWithPostData->slice(
                                ($currentPostPage - 1) * $itemsPerPostPage,
                                $itemsPerPostPage,
                            );
                        @endphp

                        @if ($totalPostItems > 0)
                            {{-- Search and Pagination Controls (One Row) --}}
                            <div
                                class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    {{-- Left: Search --}}
                                    <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                                        <div class="relative flex-1 max-w-md">
                                            <input type="text" wire:model.live.debounce.300ms="postSearchTerm"
                                                placeholder="Search by item name..."
                                                class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        @if (!empty($postSearchTerm))
                                            <button wire:click="$set('postSearchTerm', '')"
                                                class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                                Clear
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Center: Item Count --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Showing
                                            {{ $totalPostItems > 0 ? ($currentPostPage - 1) * $itemsPerPostPage + 1 : 0 }}
                                            to
                                            {{ min($currentPostPage * $itemsPerPostPage, $totalPostItems) }} of
                                            {{ $totalPostItems }} items
                                        </span>
                                    </div>

                                    {{-- Right: Items Per Page --}}
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Items per page:</label>
                                        <select wire:model.live="postPerPage"
                                            class="px-3 py-1 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="{{ $totalPostItems }}">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Items with Post Data --}}
                            @foreach ($paginatedPostItems as $prItemID => $item)
                                @php
                                    $postData = $this->postItems[$prItemID] ?? [];
                                    $mode = $modeOfProcurements->firstWhere('id', $item['mode_of_procurement_id']);
                                @endphp

                                <div
                                    class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    {{-- Item Header --}}
                                    <div
                                        class="flex items-start justify-between mb-2 pb-2 border-b border-gray-200 dark:border-neutral-600">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-sm">
                                                    {{ $item['item_no'] }}
                                                </span>
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $item['description'] }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Award Information --}}
                                    @if (
                                        !empty(array_filter([
                                                $postData['resolutionAwardNumber'] ?? null,
                                                $postData['resolutionAwardDate'] ?? null,
                                                $postData['noticeOfAwardNumber'] ?? null,
                                                $postData['noticeOfAward'] ?? null,
                                                $postData['awardedAmount'] ?? null,
                                                $postData['philgepsNoticeOfAwardNo'] ?? null,
                                                $postData['philgepsPostingOfAward'] ?? null,
                                            ])
                                        ))
                                        <div class="mb-6">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Award Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @if (!empty($postData['resolutionAwardNumber']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Resolution Award #</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['resolutionAwardNumber'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['resolutionAwardDate']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Resolution
                                                            Award Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['resolutionAwardDate'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['noticeOfAwardNumber']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notice
                                                            of Award Number</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['noticeOfAwardNumber'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['noticeOfAward']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Notice of Award Date</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['noticeOfAward'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['awardedAmount']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            Awarded Amount</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            ₱{{ number_format($postData['awardedAmount'], 2) }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- PhilGEPS Information --}}
                                    @if (!empty(array_filter([$postData['philgepsNoticeOfAwardNo'] ?? null, $postData['philgepsPostingOfAward'] ?? null])))
                                        <div class="mb-6">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                </svg>
                                                PhilGEPS Information
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                @if (!empty($postData['philgepsNoticeOfAwardNo']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            PhilGEPS| Notice of Award No.</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['philgepsNoticeOfAwardNo'] }}</p>
                                                    </div>
                                                @endif
                                                @if (!empty($postData['philgepsPostingOfAward']))
                                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            PhilGEPS| Posting of Award</p>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $postData['philgepsPostingOfAward'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Pagination Controls --}}
                            @if ($totalPostPages > 1)
                                <div
                                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        {{-- Previous Button --}}
                                        <button type="button"
                                            wire:click="$set('postPage', {{ max(1, $currentPostPage - 1) }})"
                                            @if ($currentPostPage <= 1) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                {{ $currentPostPage <= 1
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                                    : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Previous
                                        </button>

                                        {{-- Page Numbers --}}
                                        <div class="flex items-center gap-2">
                                            @php
                                                $startPostPage = max(1, $currentPostPage - 2);
                                                $endPostPage = min($totalPostPages, $currentPostPage + 2);
                                            @endphp

                                            @if ($startPostPage > 1)
                                                <button type="button" wire:click="$set('postPage', 1)"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPostPage == 1
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    1
                                                </button>
                                                @if ($startPostPage > 2)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                            @endif

                                            @for ($i = $startPostPage; $i <= $endPostPage; $i++)
                                                <button type="button"
                                                    wire:click="$set('postPage', {{ $i }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPostPage == $i
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor

                                            @if ($endPostPage < $totalPostPages)
                                                @if ($endPostPage < $totalPostPages - 1)
                                                    <span class="text-gray-500">...</span>
                                                @endif
                                                <button type="button"
                                                    wire:click="$set('postPage', {{ $totalPostPages }})"
                                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                        {{ $currentPostPage == $totalPostPages
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700' }}">
                                                    {{ $totalPostPages }}
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Next Button --}}
                                        <button type="button"
                                            wire:click="$set('postPage', {{ min($totalPostPages, $currentPostPage + 1) }})"
                                            @if ($currentPostPage >= $totalPostPages) disabled @endif
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                {{ $currentPostPage >= $totalPostPages
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800'
                                    : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                            Next
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @elseif (!empty($postSearchTerm))
                            {{-- No Results Found --}}
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No items found matching
                                        "{{ $postSearchTerm }}"</p>
                                    <button wire:click="$set('postSearchTerm', '')"
                                        class="mt-4 px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        Clear Search
                                    </button>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No post-procurement data
                                        available</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            @if ($activeTab == 4)
                {{-- PMU Tab --}}
                <div class="space-y-6 mb-6">
                    @if ($form['procurement_type'] === 'perLot')
                        {{-- PER LOT PMU --}}
                        @if ($supplier_id)
                            @php
                                $supplier = $suppliers->firstWhere('id', $supplier_id);
                            @endphp

                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="mb-4 pb-3 border-b border-gray-200 dark:border-neutral-600">
                                    <h4
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Supplier Information
                                    </h4>
                                </div>

                                @if ($supplier)
                                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Supplier Name</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $supplier->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Supplier not found</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No supplier information
                                        available</p>
                                </div>
                            </div>
                        @endif
                    @elseif ($form['procurement_type'] === 'perItem')
                        {{-- PER ITEM PMU WITH SEARCH AND PAGINATION --}}
                        @php
                            // Filter items that have supplier_id in postItems
                            $itemsWithSuppliers = collect($postItems)
                                ->filter(fn($item) => !empty($item['supplier_id']))
                                ->values();

                            // Apply search filter
                            if (!empty($postSearchTerm)) {
                                $itemsWithSuppliers = $itemsWithSuppliers->filter(function ($postItem) use (
                                    $postSearchTerm,
                                ) {
                                    $searchLower = strtolower($postSearchTerm);
                                    $item = collect($form['items'] ?? [])->firstWhere('prItemID', $postItem['ref_id']);

                                    if (!$item) {
                                        return false;
                                    }

                                    return str_contains(strtolower($item['description'] ?? ''), $searchLower) ||
                                        str_contains(strtolower($item['item_no'] ?? ''), $searchLower);
                                });
                            }

                            $totalPostItems = $itemsWithSuppliers->count();

                            // Pagination
                            $currentPostPage = $postPage ?? 1;
                            $itemsPerPostPage = $postPerPage ?? 10;
                            $totalPostPages = max(1, ceil($totalPostItems / $itemsPerPostPage));

                            // Paginate
                            $paginatedPostItems = $itemsWithSuppliers->slice(
                                ($currentPostPage - 1) * $itemsPerPostPage,
                                $itemsPerPostPage,
                            );
                        @endphp

                        @if ($totalPostItems > 0)
                            {{-- Search and Pagination Controls --}}
                            <div
                                class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 max-w-md">
                                        <input type="text" wire:model.live.debounce.300ms="postSearchTerm"
                                            placeholder="Search items..."
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Show:</label>
                                        <select wire:model.live="postPerPage"
                                            class="px-3 py-1.5 text-sm border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Item Cards with Supplier Info --}}
                            @foreach ($paginatedPostItems as $postItem)
                                @php
                                    $item = collect($form['items'] ?? [])->firstWhere('prItemID', $postItem['ref_id']);
                                    $supplier = $suppliers->firstWhere('id', $postItem['supplier_id']);
                                @endphp

                                @if ($item)
                                    <div
                                        class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                        {{-- Item Header --}}
                                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-neutral-600">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                            Item #{{ $item['item_no'] ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $item['description'] ?? 'No description' }}
                                                    </h3>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        Amount: ₱{{ number_format($item['amount'] ?? 0, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Supplier Information --}}
                                        <div class="mb-4">
                                            <h4
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Supplier Information
                                            </h4>

                                            @if ($supplier)
                                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Supplier
                                                        Name</p>
                                                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                        {{ $supplier->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            @else
                                                <div
                                                    class="text-center py-6 bg-gray-50 dark:bg-neutral-800 rounded-lg">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Supplier
                                                        information not available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Pagination Controls --}}
                            @if ($totalPostPages > 1)
                                <div
                                    class="bg-white rounded-xl p-4 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        {{-- Page info --}}
                                        <div class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                            Showing <span
                                                class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalPostItems > 0 ? ($currentPostPage - 1) * $itemsPerPostPage + 1 : 0 }}</span>
                                            to
                                            <span
                                                class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ min($currentPostPage * $itemsPerPostPage, $totalPostItems) }}</span>
                                            of
                                            <span
                                                class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $totalPostItems }}</span>
                                            items
                                        </div>

                                        {{-- Pagination buttons --}}
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                wire:click="$set('postPage', {{ max(1, $currentPostPage - 1) }})"
                                                @if ($currentPostPage <= 1) disabled @endif
                                                class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $currentPostPage <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800' : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                                Previous
                                            </button>

                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Page {{ $currentPostPage }} of {{ $totalPostPages }}
                                            </span>

                                            <button type="button"
                                                wire:click="$set('postPage', {{ min($totalPostPages, $currentPostPage + 1) }})"
                                                @if ($currentPostPage >= $totalPostPages) disabled @endif
                                                class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors {{ $currentPostPage >= $totalPostPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-neutral-800' : 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600' }}">
                                                Next
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif (!empty($postSearchTerm))
                            {{-- No Results Found --}}
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No items found matching
                                        "{{ $postSearchTerm }}"</p>
                                    <button wire:click="$set('postSearchTerm', '')"
                                        class="mt-4 px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                        Clear Search
                                    </button>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-white rounded-xl p-6 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No supplier information
                                        available</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Supplier Details Modal --}}
    @if ($modalType === 'supplier')
        <x-forms.modal title="Supplier Contact Details" size="max-w-lg">
            @if ($selectedSupplier)
                <div class="px-6 py-4 space-y-4">
                    {{-- Company Name --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Company Name
                        </label>
                        <p class="text-sm font-semibold text-black dark:text-white">
                            {{ $selectedSupplier['name'] ?? 'N/A' }}
                        </p>
                    </div>


                    {{-- Mobile --}}
                    @if (!empty(trim($selectedSupplier['mobile'] ?? '')))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Mobile
                            </label>
                            <p class="text-sm text-black dark:text-white">
                                {{ $selectedSupplier['mobile'] }}
                            </p>
                        </div>
                    @endif

                    {{-- Telephone --}}
                    @if (!empty(trim($selectedSupplier['telephone'] ?? '')))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Telephone
                            </label>
                            <p class="text-sm text-black dark:text-white">
                                {{ $selectedSupplier['telephone'] }}
                            </p>
                        </div>
                    @endif

                    {{-- Email --}}
                    @if (!empty(trim($selectedSupplier['email'] ?? '')))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email
                            </label>
                            <p class="text-sm text-black dark:text-white">
                                {{ $selectedSupplier['email'] }}
                            </p>
                        </div>
                    @endif

                    {{-- Contact Person --}}
                    @if (!empty(trim($selectedSupplier['contact_person'] ?? '')))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Contact Person
                            </label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $selectedSupplier['contact_person'] }}
                            </p>
                        </div>
                    @endif

                    {{-- No Additional Data Message --}}
                    @if (empty(trim($selectedSupplier['tin'] ?? '')) &&
                            empty(trim($selectedSupplier['address'] ?? '')) &&
                            empty(trim($selectedSupplier['mobile'] ?? '')) &&
                            empty(trim($selectedSupplier['telephone'] ?? '')) &&
                            empty(trim($selectedSupplier['email'] ?? '')) &&
                            empty(trim($selectedSupplier['contact_person'] ?? '')))
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                No additional contact information available for this supplier.
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </x-forms.modal>
    @endif

    {{-- Stage History Modal --}}
    @if ($modalType === 'stageHistory')
        @php
            $modalTitle = 'Stage History';
            if ($selectedPrItemID && $form['procurement_type'] === 'perItem') {
                $selectedItem = collect($form['items'] ?? [])->firstWhere('prItemID', $selectedPrItemID);
                if ($selectedItem) {
                    $modalTitle = 'Stage History - Item #' . ($selectedItem['item_no'] ?? 'N/A');
                }
            }
        @endphp
        <x-forms.modal :title="$modalTitle" size="max-w-md" closeMethod="closeStageHistoryModal">
            @if ($modalType === 'stageHistory')
                <div class="px-6 py-4">
                    @if (count($stageHistory) > 0)
                        {{-- Scrollable Timeline Container --}}
                        <div class="relative max-h-[32rem] overflow-y-auto pr-2 pl-4">
                            {{-- Vertical Line (Centered) --}}
                            <div
                                class="absolute left-[32px] top-4 bottom-4 w-px bg-gradient-to-b from-emerald-500 via-emerald-400 to-gray-300 dark:from-emerald-600 dark:via-emerald-500 dark:to-gray-600">
                            </div>

                            <div class="space-y-4 pt-2">
                                @foreach ($stageHistory as $index => $history)
                                    <div class="relative flex items-start gap-4 group">
                                        {{-- Timeline Node --}}
                                        <div class="flex-shrink-0 relative z-10">
                                            @if ($index === 0)
                                                {{-- Current Stage - Green --}}
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center shadow-md ring-2 ring-emerald-200 dark:ring-emerald-800">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @else
                                                {{-- Past Stages - Grey --}}
                                                <div
                                                    class="w-8 h-8 bg-gray-400 dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm ring-2 ring-white dark:ring-neutral-800">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Content Card --}}
                                        <div class="flex-1 pb-4">
                                            <div
                                                class="bg-white dark:bg-neutral-700 rounded-lg shadow-sm border {{ $index === 0 ? 'border-emerald-300 dark:border-emerald-700' : 'border-gray-200 dark:border-neutral-600' }} p-3 transition-all hover:shadow-md">
                                                {{-- Current Badge (Above Stage) --}}
                                                @if ($index === 0)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-600 text-white mb-2">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Current
                                                    </span>
                                                @endif

                                                {{-- Stage Name --}}
                                                <h4
                                                    class="text-sm font-semibold {{ $index === 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-300' }} tracking-tight break-words">
                                                    {{ $history['stage'] }}
                                                </h4>

                                                {{-- User Info --}}
                                                <div
                                                    class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="tracking-tight">{{ $history['user'] }}</span>
                                                </div>

                                                {{-- Date/Time --}}
                                                {{-- <div
                                                    class="flex items-center gap-1.5 text-xs {{ $index === 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400' }} mt-2">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span
                                                        class="font-medium tracking-tight">{{ $history['date'] }}</span>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-neutral-700 mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">No Stage History
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                No stage updates recorded yet.
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </x-forms.modal>
    @endif
</div>
