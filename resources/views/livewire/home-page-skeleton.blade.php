<div class="space-y-6 animate-pulse">
    <!-- Hero Summary Skeleton -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-emerald-200 dark:bg-emerald-900/40 rounded-2xl h-48 skeleton"></div>
        <div class="bg-gray-200 dark:bg-neutral-700 rounded-2xl h-48 skeleton"></div>
        <div class="bg-gray-200 dark:bg-neutral-700 rounded-2xl h-48 skeleton"></div>
    </div>

    <!-- Filter Bar Skeleton -->
    <div class="bg-white dark:bg-neutral-700 rounded-2xl p-4 flex gap-4 skeleton">
        <div class="h-9 w-40 bg-gray-200 dark:bg-neutral-600 rounded-lg"></div>
        <div class="h-9 w-40 bg-gray-200 dark:bg-neutral-600 rounded-lg"></div>
        <div class="h-9 w-24 bg-gray-200 dark:bg-neutral-600 rounded-lg ml-auto"></div>
    </div>

    <!-- Charts Skeleton -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-neutral-700 rounded-2xl h-64 skeleton"></div>
        <div class="bg-white dark:bg-neutral-700 rounded-2xl h-64 skeleton"></div>
        <div class="bg-white dark:bg-neutral-700 rounded-2xl h-64 skeleton"></div>
    </div>

    <!-- Table Skeleton -->
    <div class="bg-white dark:bg-neutral-700 rounded-2xl p-6 space-y-3 skeleton">
        <div class="h-5 w-48 bg-gray-200 dark:bg-neutral-600 rounded"></div>
        @foreach (range(1, 8) as $row)
            <div class="flex gap-4">
                <div class="h-4 w-16 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 flex-1 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 w-20 bg-gray-200 dark:bg-neutral-600 rounded"></div>
            </div>
        @endforeach
    </div>
</div>
