                                            <table
                                                class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 rounded-lg overflow-hidden">
                                                <thead
                                                    class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-neutral-900 dark:to-neutral-800 sticky top-0 z-10">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            PR Number</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Title / Description</th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            ABC / Amount</th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            Awarded Amount</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Supplier</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            Date Receipt of Supplier (NOA)</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            PO Date Deadline</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            PO Date</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            PO / Contract No.</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            PO Status</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            PO Issuance</th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            Contract Amount</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            Contract Signing Date</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            NTP Date</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">
                                                            NTP Link</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                                                    @forelse ($expandedPaginator as $procRow)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm font-medium text-emerald-700 dark:text-emerald-300">
                                                                @can('view_procurement')
                                                                    <a href="{{ route('procurements.view', ['procurement' => $procRow->procID]) }}"
                                                                        target="_blank"
                                                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:border-emerald-400 transition-colors">
                                                                        {{ $procRow->pr_number }}
                                                                    </a>
                                                                @else
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-md font-mono text-xs">
                                                                        {{ $procRow->pr_number }}
                                                                    </span>
                                                                @endcan
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white max-w-[14rem]">
                                                                <div class="font-medium truncate"
                                                                    title="{{ $procRow->description }}">
                                                                    {{ $procRow->description }}</div>
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white font-medium">
                                                                ₱ {{ number_format($procRow->abc, 2) }}
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $procRow->awarded_amount ? '₱ ' . number_format($procRow->awarded_amount, 2) : '—' }}
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 min-w-[16rem]">
                                                                <div class="break-words whitespace-normal">
                                                                    {{ $procRow->supplier_name ?? '—' }}</div>
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $procRow->date_receipt_of_supplier_noa ? \Carbon\Carbon::parse($procRow->date_receipt_of_supplier_noa)->format('M d, Y') : '—' }}
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                                @php
                                                                    $deadlineWarning = null;
                                                                    if ($procRow->po_date_deadline) {
                                                                        $deadline = \Carbon\Carbon::parse(
                                                                            $procRow->po_date_deadline,
                                                                        );
                                                                        $today = \Carbon\Carbon::today();
                                                                        $daysUntil = $today->diffInDays(
                                                                            $deadline,
                                                                            false,
                                                                        );
                                                                        if (
                                                                            $procRow->po_date &&
                                                                            \Carbon\Carbon::parse(
                                                                                $procRow->po_date,
                                                                            )->gt($deadline)
                                                                        ) {
                                                                            $deadlineWarning = 'exceeded';
                                                                        } elseif (
                                                                            $daysUntil < 0 &&
                                                                            !$procRow->po_date
                                                                        ) {
                                                                            $deadlineWarning = 'overdue';
                                                                        } elseif (
                                                                            $daysUntil >= 0 &&
                                                                            $daysUntil <= 3 &&
                                                                            !$procRow->po_date
                                                                        ) {
                                                                            $deadlineWarning = 'soon';
                                                                        }
                                                                    }
                                                                @endphp
                                                                <div class="flex flex-col gap-1">
                                                                    <span>{{ $procRow->po_date_deadline ? \Carbon\Carbon::parse($procRow->po_date_deadline)->format('M d, Y') : '—' }}</span>
                                                                    @if ($deadlineWarning === 'exceeded')
                                                                        <span
                                                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                                                            <svg class="w-3 h-3" fill="currentColor"
                                                                                viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                            Exceeded
                                                                        </span>
                                                                    @elseif ($deadlineWarning === 'overdue')
                                                                        <span
                                                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                                                            <svg class="w-3 h-3" fill="currentColor"
                                                                                viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                            Overdue
                                                                        </span>
                                                                    @elseif ($deadlineWarning === 'soon')
                                                                        <span
                                                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                                                            <svg class="w-3 h-3" fill="currentColor"
                                                                                viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                            Due Soon
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $procRow->po_date ? \Carbon\Carbon::parse($procRow->po_date)->format('M d, Y') : '—' }}
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                @if ($procRow->po_contract_number)
                                                                    @if ($procRow->po_contract_number_link)
                                                                        <a href="{{ $procRow->po_contract_number_link }}"
                                                                            target="_blank" rel="noopener noreferrer"
                                                                            class="inline-flex items-center gap-1 font-medium text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 underline underline-offset-2 transition-colors">
                                                                            {{ $procRow->po_contract_number }}
                                                                        </a>
                                                                    @else
                                                                        <span
                                                                            class="font-medium text-gray-900 dark:text-white">{{ $procRow->po_contract_number }}</span>
                                                                    @endif
                                                                @else
                                                                    <span
                                                                        class="text-gray-400 dark:text-gray-500">—</span>
                                                                @endif
                                                            </td>
                                                            {{-- PO Status Column (moved here) --}}
                                                            <td class="px-4 py-3 min-w-[13rem]">
                                                                @php
                                                                    $manualStatus = $procRow->pmu_manual_status ?? null;
                                                                    $manualStatusLabel = match ($manualStatus) {
                                                                        'return_to_bac' => 'Return to BAC',
                                                                        'for_end_user_compliance'
                                                                            => 'For End-User Compliance',
                                                                        default => null,
                                                                    };
                                                                    $hasPoDate = !empty($procRow->po_date);
                                                                    $hasPoNumber = !empty($procRow->po_contract_number);
                                                                    $hasContractAmount =
                                                                        !empty($procRow->pmu_contract_amount) &&
                                                                        (float) $procRow->pmu_contract_amount > 0;
                                                                    if (
                                                                        $hasPoDate &&
                                                                        $hasPoNumber &&
                                                                        $hasContractAmount
                                                                    ) {
                                                                        $autoStatusLabel = 'For Approval of USEC';
                                                                        $autoStatusClass =
                                                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300';
                                                                    } elseif ($hasPoDate && $hasPoNumber) {
                                                                        $autoStatusLabel = 'PO Preparation';
                                                                        $autoStatusClass =
                                                                            'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
                                                                    } else {
                                                                        $autoStatusLabel = null;
                                                                        $autoStatusClass = '';
                                                                    }
                                                                @endphp
                                                                @if ($manualStatusLabel)
                                                                    <span
                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                                        {{ $manualStatus === 'return_to_bac' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300' }}">
                                                                        {{ $manualStatusLabel }}
                                                                    </span>
                                                                @elseif ($autoStatusLabel)
                                                                    <span
                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $autoStatusClass }}">
                                                                        {{ $autoStatusLabel }}
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                                                                @endif
                                                            </td>
                                                            {{-- PO Issuance Column --}}
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                @if ($deadlineWarning === 'exceeded')
                                                                    <span
                                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                                                        <svg class="w-3 h-3" fill="currentColor"
                                                                            viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd"
                                                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                        Exceeded
                                                                    </span>
                                                                @elseif ($deadlineWarning === 'overdue')
                                                                    <span
                                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                                                        <svg class="w-3 h-3" fill="currentColor"
                                                                            viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd"
                                                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                        Overdue
                                                                    </span>
                                                                @elseif ($deadlineWarning === 'soon')
                                                                    <span
                                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                                                        <svg class="w-3 h-3" fill="currentColor"
                                                                            viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd"
                                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                        Due Soon
                                                                    </span>
                                                                @elseif ($procRow->po_date_deadline)
                                                                    <span
                                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400">
                                                                        <svg class="w-3 h-3" fill="currentColor"
                                                                            viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd"
                                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                        On Track
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="text-gray-400 dark:text-gray-500">—</span>
                                                                @endif
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $procRow->pmu_contract_amount ? '₱ ' . number_format($procRow->pmu_contract_amount, 2) : '—' }}
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $procRow->pmu_contract_signing_date ? \Carbon\Carbon::parse($procRow->pmu_contract_signing_date)->format('M d, Y') : '—' }}
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                                {{ $procRow->pmu_notice_to_proceed_date ? \Carbon\Carbon::parse($procRow->pmu_notice_to_proceed_date)->format('M d, Y') : '—' }}
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                @if ($procRow->ntp_link)
                                                                    <a href="{{ $procRow->ntp_link }}" target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        class="inline-flex items-center gap-1 font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 underline underline-offset-2 transition-colors">
                                                                        View NTP
                                                                    </a>
                                                                @else
                                                                    <span
                                                                        class="text-gray-400 dark:text-gray-500">—</span>
                                                                @endif
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 min-w-[16rem] w-60">
                                                                @if ($procRow->pmu_remarks)
                                                                    <span title="{{ $procRow->pmu_remarks }}"
                                                                        class="cursor-help break-words whitespace-normal">{{ $procRow->pmu_remarks }}</span>
                                                                @else
                                                                    <span
                                                                        class="text-gray-400 dark:text-gray-500">—</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="16"
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
