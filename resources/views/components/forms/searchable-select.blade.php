@props([
    'options' => [],
    'labelKey' => 'name',
    'valueKey' => 'id',
    'placeholder' => 'Select...',
    'emptyMessage' => 'No results found.',
])

<div x-data="{
    open: false,
    search: '',
    selected: @entangle($attributes->wire('model')),
    options: [], // Initialize empty, let x-effect fill it

    get filteredOptions() {
        if (this.search === '') {
            return this.options;
        }
        return this.options.filter(option => {
            return String(option['{{ $labelKey }}']).toLowerCase().includes(this.search.toLowerCase());
        });
    },

    get selectedLabel() {
        if (!this.selected) return '{{ $placeholder }}';
        const found = this.options.find(o => o['{{ $valueKey }}'] == this.selected);
        return found ? found['{{ $labelKey }}'] : '{{ $placeholder }}';
    },

    select(value) {
        if (this.selected === value) {
            this.selected = '';
        } else {
            this.selected = value;
        }
        this.open = false;
        this.search = '';
    },

    clear() {
        this.selected = '';
        this.search = '';
        this.open = false;
    }
}" {{-- This is the Magic Fix: x-effect watches for server-side updates --}} x-effect="options = {{ \Illuminate\Support\Js::from($options) }}"
    class="relative group w-full min-w-[150px]" @click.outside="open = false; search = ''">

    <button type="button" @click="open = !open"
        class="relative w-full text-left pl-3 pr-10 py-2 text-sm font-medium border-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200
        {{ $attributes->get('class') }}
        border-gray-300 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white
        hover:border-emerald-400 hover:shadow-sm dark:hover:border-emerald-500"
        :class="selected ?
            'bg-blue-50 border-emerald-400 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-300' :
            'bg-white dark:bg-neutral-800 text-gray-500 dark:text-gray-400'">
        <span class="block truncate" x-text="selectedLabel"></span>

        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                :class="selected ? 'text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </span>
    </button>

    <button type="button" x-show="selected" @click.stop="clear()"
        class="absolute right-8 top-1/2 -translate-y-1/2 p-0.5 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors z-10">
        <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col">

        <div class="p-2 sticky top-0 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
            <input type="text" x-model="search" placeholder="Search..." @keydown.enter.prevent
                class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-neutral-600 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-white dark:placeholder-gray-400" />
        </div>

        <ul class="overflow-y-auto flex-1 py-1">
            <template x-for="option in filteredOptions" :key="option['{{ $valueKey }}']">
                <li @click="select(option['{{ $valueKey }}'])"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-sm text-gray-900 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                    :class="{ 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-900 dark:text-emerald-200': selected ==
                            option['{{ $valueKey }}'] }">

                    <span x-text="option['{{ $labelKey }}']" class="block break-words whitespace-normal font-normal"></span>

                    <span x-show="selected == option['{{ $valueKey }}']"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </li>
            </template>

            <li x-show="filteredOptions.length === 0"
                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500 dark:text-gray-400 text-sm">
                {{ $emptyMessage }}
            </li>
        </ul>
    </div>
</div>
