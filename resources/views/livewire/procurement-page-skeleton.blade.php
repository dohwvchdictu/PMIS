<div class="space-y-4 animate-pulse">
    <!-- Header / Search Bar Skeleton -->
    <div class="bg-white dark:bg-neutral-700 rounded-2xl p-4 flex items-center gap-4 skeleton">
        <div class="h-9 w-64 bg-gray-200 dark:bg-neutral-600 rounded-lg"></div>
        <div class="h-9 w-24 bg-gray-200 dark:bg-neutral-600 rounded-lg ml-auto"></div>
        <div class="h-9 w-20 bg-gray-200 dark:bg-neutral-600 rounded-lg"></div>
    </div>

    <!-- Table Skeleton -->
    <div class="bg-white dark:bg-neutral-700 rounded-2xl overflow-hidden skeleton">
        <!-- Table Header -->
        <div class="flex gap-4 p-4 border-b border-gray-100 dark:border-neutral-600">
            <div class="h-4 w-20 bg-gray-300 dark:bg-neutral-500 rounded"></div>
            <div class="h-4 flex-1 bg-gray-300 dark:bg-neutral-500 rounded"></div>
            <div class="h-4 w-28 bg-gray-300 dark:bg-neutral-500 rounded"></div>
            <div class="h-4 w-24 bg-gray-300 dark:bg-neutral-500 rounded"></div>
            <div class="h-4 w-24 bg-gray-300 dark:bg-neutral-500 rounded"></div>
            <div class="h-4 w-20 bg-gray-300 dark:bg-neutral-500 rounded"></div>
        </div>
        <!-- Table Rows -->
        @foreach (range(1, 10) as $row)
            <div class="flex gap-4 p-4 border-b border-gray-50 dark:border-neutral-700/50">
                <div class="h-4 w-20 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 flex-1 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 w-28 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-600 rounded"></div>
                <div class="h-4 w-20 bg-gray-200 dark:bg-neutral-600 rounded"></div>
            </div>
        @endforeach
    </div>

    <!-- Pagination Skeleton -->
    <div class="flex justify-between items-center px-2">
        <div class="h-4 w-40 bg-gray-200 dark:bg-neutral-600 rounded skeleton"></div>
        <div class="flex gap-2">
            @foreach (range(1, 5) as $page)
                <div class="h-8 w-8 bg-gray-200 dark:bg-neutral-600 rounded skeleton"></div>
            @endforeach
        </div>
    </div>
</div>
