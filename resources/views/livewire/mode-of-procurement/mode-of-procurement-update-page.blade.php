<div class="space-y-2">

    <div class="relative bg-white rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

        <ul class="flex items-center w-full max-w-7xl pt-2 p-2 bg-white dark:bg-neutral-700 dark:border-neutral-700 mx-auto"
            data-hs-stepper='{"isCompleted": true}'>

            <li class="flex items-center gap-x-2 flex-1 group"
                data-hs-stepper-nav-item='{"index": 1, "isCompleted": {{ $activeTab > 1 ? 'true' : 'false' }} }'>
                <button type="button" wire:click="setStep(1)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition
       hover:scale-105
       {{ $activeTab == 1
           ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
           : ($activeTab > 1
               ? 'bg-emerald-600 text-white hover:bg-emerald-700'
               : 'bg-gray-100 text-gray-800 hover:bg-gray-200') }}">
                    1
                </button>
                <span class="text-sm font-medium text-black dark:text-white whitespace-nowrap">
                    Details
                </span>
                <div
                    class="h-px grow transition-colors duration-300
            {{ $activeTab > 1 ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                </div>
            </li>

            <li class="flex items-center gap-x-2 flex-1 group"
                data-hs-stepper-nav-item='{"index": 2, "isCompleted": {{ $activeTab > 2 ? 'true' : 'false' }} }'>
                <button type="button" wire:click="setStep(2)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition
       hover:scale-105
       {{ $activeTab == 2
           ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
           : ($activeTab > 2
               ? 'bg-emerald-600 text-white hover:bg-emerald-700'
               : 'bg-gray-100 text-neutral-400 cursor-not-allowed') }}">
                    2
                </button>
                <span
                    class="text-sm font-medium whitespace-nowrap
            {{ $activeTab > 2 ? 'text-gray-800 dark:text-white' : 'text-neutral-400 dark:text-neutral-500' }}">
                    Mode of Procurement
                </span>

                <!-- changed: include || $mopGroupId so this line behaves like the one after tab 1 -->
                <div
                    class="h-px grow transition-colors duration-300
            {{ $activeTab > 2 ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-neutral-500' }}">
                </div>
            </li>

            <li class="flex items-center gap-x-2 group"
                data-hs-stepper-nav-item='{"index": 3, "isCompleted": {{ $activeTab > 3 ? 'true' : 'false' }} }'>
                <button type="button" wire:click="setStep(3)"
                    class="size-8 flex justify-center items-center rounded-full font-medium text-sm transition
       hover:scale-105
       {{ $activeTab == 3
           ? 'bg-green-500 text-white border-2 border-emerald-700 hover:bg-green-600'
           : ($activeTab > 3
               ? 'bg-emerald-600 text-white hover:bg-emerald-700'
               : 'bg-gray-100 text-neutral-400 cursor-not-allowed') }}">
                    3
                </button>
                <span
                    class="text-sm font-medium whitespace-nowrap
            {{ $activeTab > 3 ? 'text-gray-800 dark:text-white' : 'text-neutral-400 dark:text-neutral-500' }}">
                    Post
                </span>

                <!-- invisible spacer to keep spacing identical -->
                <div class="h-px grow invisible"></div>
            </li>
        </ul>

    </div>
    {{-- <hr class=" border-gray-200 dark:border-neutral-600"> --}}

    <div>
        @if ($activeTab == 1)
            <div class="flex flex-col gap-2 pt-2">
                <div
                    class="bg-white p-4 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">
                    <!-- Grid for PR No. + Program/Project -->
                    <div class="grid grid-cols-2 md:grid-cols-10 gap-4">
                        <!-- PR Number -->
                        <div class="col-span-1">
                            <x-forms.input id="pr_number" label="PR No." model="form.pr_number" :form="$form"
                                :required="true" textAlign="right" :readonly="true" />
                        </div>

                        <!-- Procurement Program / Project -->
                        <div class="col-span-9">
                            <x-forms.textarea id="procurement_program_project" label="Procurement Program / Project"
                                model="form.procurement_program_project" :required="true" :readonly="true"
                                :rows="$textareaRows" />
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl p-2 shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700 mt-4">

                    <div
                        class="flex justify-between items-center px-4 py-2 border-b border-gray-200 dark:border-neutral-600 mb-2">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            History of Modes
                        </h3>

                        <button type="button" wire:click="toggleHistory"
                            class="flex items-center gap-2 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors
            {{ $showHistory
                ? 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-neutral-600 dark:text-gray-300'
                : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' }}">
                            @if ($showHistory)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                    </path>
                                </svg>
                                Hide Previous
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                Show Previous ({{ count($form['items']) - 1 }})
                            @endif
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead class="sticky top-0 bg-gray-200 dark:bg-neutral-800 z-20">

                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                                {{-- Since you are using array_reverse, the NEWEST item is the FIRST iteration --}}
                                @forelse (array_reverse($form['items'] ?? [], true) as $itemIndex => $item)
                                    @php
                                        // ... existing setup code ...
                                        $modeId = $item['mode_of_procurement_id'] ?? null;
                                        $rowUid = $item['uid'] ?? 'temp_' . $itemIndex;
                                        $isSavedRecord =
                                            $form['procurement_type'] === 'perItem'
                                                ? isset($item['prItemID'])
                                                : isset($item['id']) && is_numeric($item['id']);
                                        $shouldDisable = $isSavedRecord && $modeId == 1;
                                        $showFields = $isSavedRecord;

                                        // NEW LOGIC:
                                        // $loop->first is the newest/top item (always visible).
                                        // Anything else is hidden unless $showHistory is true.
                                        $isVisible = $loop->first || $showHistory;
                                    @endphp

                                    {{-- Apply the hidden class conditionally --}}
                                    <tr wire:key="row-{{ $rowUid }}"
                                        class="{{ $isVisible ? '' : 'hidden' }} hover:bg-gray-50 dark:hover:bg-neutral-800">

                                        {{-- Action Column --}}
                                        <td class="px-2 py-2 text-center">
                                            {{-- Only show Add button on the topmost (active) row --}}
                                            @if ($loop->first)
                                                <button wire:click.prevent="addItem"
                                                    class="inline-flex items-center justify-center w-6 h-6 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded transition-colors"
                                                    title="Add New Row Above">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            @else
                                                {{-- Optional: Add a Locked icon for history items --}}
                                                <span class="text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Show Item columns only for perItem type --}}
                                        @if ($form['procurement_type'] === 'perItem')
                                            <td class="px-2 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                                {{ $item['item_no'] }}
                                            </td>
                                            <td class="px-2 py-2 text-gray-900 dark:text-gray-100">
                                                {{ $item['description'] }}
                                            </td>
                                        @endif

                                        {{-- Mode of Procurement --}}
                                        <td class="px-2 py-2">
                                            <select wire:key="select-mode-{{ $rowUid }}"
                                                wire:model.live="form.items.{{ $itemIndex }}.mode_of_procurement_id"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                                @disabled($shouldDisable)>

                                                <option value="">Select Mode...</option>
                                                @foreach ($modeOfProcurements ?? [] as $modeOption)
                                                    <option value="{{ $modeOption->id }}">
                                                        {{ $modeOption->modeofprocurements }}
                                                    </option>
                                                @endforeach>
                                            </select>
                                        </td>

                                        {{-- Bidding # --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [5, 1]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="bid-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_number"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    maxlength="2" value="1">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- IB No. through Sub/Open of Bids --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [5, 1]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="ib-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ib_number"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="IB-2025-002">
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-proc-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_proc_conference"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ads-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ads_post_ib"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="pre-bid-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.pre_bid_conf"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="elig-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.eligibility_check"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>

                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="sub-open-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.sub_open_bids"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- Bidding Date / NTF Bidding Date --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [4, 5, 1]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="bid-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                        @elseif ($showFields && $modeId == 4)
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ntf-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ntf_bidding_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- Bidding Result / NTF Bidding Result --}}
                                        @if ($showFields && $modeId && !in_array($modeId, [4, 5, 1]))
                                            <td class="px-2 py-2">
                                                <select wire:key="res-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.bidding_result"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                                    <option value="">Select...</option>
                                                    <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                    <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                                </select>
                                            </td>
                                        @elseif ($showFields && $modeId == 4)
                                            <td class="px-2 py-2">
                                                <select wire:key="ntf-res-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ntf_bidding_result"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                                    <option value="">Select...</option>
                                                    <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                    <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                                </select>
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- NTF No. --}}
                                        @if ($showFields && $modeId == 4)
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="ntf-no-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.ntf_no"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="NTF-2025-001">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- RFQ No. --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="rfq-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.rfq_no"
                                                    class="w-full px-2 py-1 text-xs text-right border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="RFQ-2025-001">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- Canvass Date --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="can-date-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.canvass_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- Returned of Canvass --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="ret-can-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.date_returned_of_canvass"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- Abstract of Canvass --}}
                                        @if ($showFields && in_array($modeId, [4, 5]))
                                            <td class="px-2 py-2">
                                                <input type="date" wire:key="abs-can-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.abstract_of_canvass_date"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                        {{-- Resolution Number --}}
                                        @if ($showFields && $modeId == 5)
                                            <td class="px-2 py-2">
                                                <input type="text" wire:key="res-num-{{ $rowUid }}"
                                                    wire:model.defer="form.items.{{ $itemIndex }}.resolution_number"
                                                    class="w-full px-2 py-1 text-xs text-center border border-gray-300 dark:border-neutral-600 rounded focus:ring-2 focus:ring-emerald-500 dark:bg-neutral-800 dark:text-white"
                                                    placeholder="RES-2025-001">
                                            </td>
                                        @else
                                            <td class="px-2 py-2 bg-gray-50 dark:bg-neutral-900"></td>
                                        @endif

                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="20"
                                            class="px-2 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No items available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        @endif

        @if ($activeTab == 2)
            {{-- Loop through Modes --}}
            <div class="flex flex-col items-center p-2">

                <div class="flex justify-center p-2">
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 inline-block dark:bg-neutral-700 dark:border-neutral-700">


                    </div>
                </div>


            </div>
        @endif

        @if ($activeTab == 3)
            <div class="flex flex-col items-center gap-6 p-6">

                {{-- Award Information Block --}}
                <div
                    class="w-full max-w-7xl bg-white p-6 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                </div>

                {{-- PhilGEPS & Supplier Information Block --}}
                <div
                    class="w-full max-w-7xl bg-white p-6 rounded-xl shadow border border-gray-200 dark:bg-neutral-700 dark:border-neutral-700">

                </div>


            </div>
        @endif
    </div>


    <div
        class="fixed bottom-5 right-0 left-0 lg:ml-[13.75rem] flex justify-end p-2 border-t border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 z-49">
        <div class="w-full max-w-[110rem] mx-auto sm:px-6 lg:px-8 flex justify-end gap-x-2">
            <a href="{{ route('mode-of-procurement.index') }}" wire:navigate
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300 dark:hover:bg-neutral-700">
                Cancel
            </a>
            <button wire:click="save" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50">
                <div wire:loading wire:target="save"
                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-white">
                </div>
                Save
            </button>
        </div>
    </div>
</div>
