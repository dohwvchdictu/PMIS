@props([
    'title' => null,
    'size' => 'max-w-6xl',
])

<div x-data="{ show: @entangle('showModal') }" @keydown.escape.window="show = false" x-cloak>

    {{-- ✅ CRITICAL FIX: Teleport to body --}}
    <template x-teleport="body">
        <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-emerald-700/50 p-4"
            style="display: none;">

            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" @click.stop
                class="bg-white shadow-xl w-full {{ $size }} rounded-2xl overflow-hidden dark:bg-neutral-800 my-8 flex flex-col max-h-[85vh]">

                {{-- Header --}}
                <div
                    class="flex justify-between items-center px-4 py-2 bg-emerald-600 text-white font-semibold dark:bg-emerald-600 dark:text-neutral-200">
                    <h2 class="text-lg font-semibold">{{ $title ?? 'Modal' }}</h2>
                    <button @click="$wire.closeModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-red-500 hover:bg-gray-100 dark:bg-neutral-700 dark:text-red-500 dark:hover:bg-neutral-600 transition">
                        ✕
                    </button>
                </div>

                {{-- Content --}}
                <div class="overflow-y-auto flex-1">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Ensure x-cloak works --}}
@once
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
@endonce
