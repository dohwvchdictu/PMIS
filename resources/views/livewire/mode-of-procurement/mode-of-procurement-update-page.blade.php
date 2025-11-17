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
            <div class="flex flex-col gap-6 pt-2">
                <div
                    class="bg-white p-4 rounded-xl shadow border border-gray-200
                dark:bg-neutral-700 dark:border-neutral-700 ">
                    <!-- Grid for PR No. + Program/Project -->
                    <div class="grid grid-cols-2 md:grid-cols-10 gap-4">
                        <!-- PR Number -->
                        <div class="col-span-1">
                            <x-forms.input id="pr_number" label="PR No." model="form.pr_number" :form="$form"
                                :required="true" textAlign="right" :readonly="false" :disabled="false" />
                        </div>

                        <!-- Procurement Program / Project -->
                        <div class="col-span-9">
                            <x-forms.textarea id="procurement_program_project" label="Procurement Program / Project"
                                model="form.procurement_program_project" :required="true" :rows="$textareaRows"
                                :readonly="true" />
                        </div>
                    </div>

                    <!-- Per Lot / Per Item Toggle + Table -->

                </div>
                <div
                    class="bg-white p-4 rounded-xl shadow border border-gray-200
                dark:bg-neutral-700 dark:border-neutral-700 ">

                    <table class="w-full text-xs">
                        <thead class="sticky bg-gray-200 dark:bg-neutral-800">
                            <tr>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-16">
                                    Item No.
                                </th>

                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Item Description
                                </th>

                                <th
                                    class="px-2 py-1 text-center font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600 w-32">
                                    Amount
                                </th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Bidding #</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    IB No.</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Pre-Proc Conference</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Ads/Post IB</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Pre-Bid Conference</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Eligibility Check</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Sub/Open of Bids</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Bidding Date</th>
                                <th
                                    class="px-2 py-1 text-left font-semibold text-black dark:text-white border-b border-gray-300 dark:border-neutral-600">
                                    Bidding Result</th>
                                <th
                                    class="px-2 py-1 text-center font-semibold text-black dark:text-white w-12 border-b border-gray-300 dark:border-neutral-600">
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-800">
                            @foreach ($form['items'] as $item)
                                <tr wire:key="item-{{ $item['prItemID'] }}">

                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ $item['item_no'] }}
                                    </td>

                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100">
                                        {{ $item['description'] }}
                                    </td>

                                    <td class="px-2 py-1 text-right text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        <span class="text-gray-500">₱</span>
                                        <span>{{ number_format($item['amount'], 2) }}</span>
                                    </td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="text"
                                            value="2" readonly class="text-right" maxlength="2">
                                    </td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="text"
                                            placeholder="IB-2025-002" class="text-right"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="date"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="date"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="date"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="date"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="date"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100"><input type="date"></td>
                                    <td class="px-2 py-1 text-gray-900 dark:text-gray-100">
                                        <select>
                                            <option value="">Select...</option>
                                            <option value="SUCCESSFUL">SUCCESSFUL</option>
                                            <option value="UNSUCCESSFUL">UNSUCCESSFUL</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-1 text-center">
                                        <button wire:click.prevent="removeItem('{{ $item['prItemID'] }}')"
                                            class="font-medium text-red-500 hover:text-red-700 text-base">×</button>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>



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
