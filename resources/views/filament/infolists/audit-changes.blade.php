@php
    $record = $getRecord();
    $oldValues = $record->old_values ?? [];
    $newValues = $record->new_values ?? [];
    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
@endphp

<div class="space-y-4">
    @if (empty($allKeys))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            No changes recorded for this audit entry.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Field
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Old Value
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            New Value
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($allKeys as $key)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ ucwords(str_replace('_', ' ', $key)) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if (isset($oldValues[$key]))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        {{ is_array($oldValues[$key]) ? json_encode($oldValues[$key]) : $oldValues[$key] }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if (isset($newValues[$key]))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ is_array($newValues[$key]) ? json_encode($newValues[$key]) : $newValues[$key] }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600 italic">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
