<div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-4">
        <!-- Total Procurements & ABC Value Combined -->
        <div
            class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-600 p-3 rounded-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-700 dark:text-gray-300 text-sm font-medium">
                        Total Procurements
                    </h3>
                </div>
                <p
                    class="text-5xl font-bold bg-emerald-600 dark:bg-emerald-600 text-white font-bold  px-4 py-1 rounded-lg">
                    {{ $summaryStats['total'] }}
                </p>
            </div>

            <div class="flex flex-col items-center justify-center mt-4">
                <span class="text-xs text-gray-500 dark:text-gray-400 mb-2">Total ABC</span>
                <p
                    class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-emerald-600 dark:text-emerald-400 break-all text-center">
                    ₱{{ $summaryStats['totalAbc'] }}
                </p>
            </div>

        </div>
        <!-- Division ABC Breakdown -->

        <div class="space-y-2 max-h-full overflow-y-auto">
            @foreach ($summaryStats['divisionAbc'] as $div)
                <div
                    class="flex items-center justify-between text-sm bg-gray-50 dark:bg-neutral-700 rounded-lg px-3 py-2">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $div['abbreviation'] }}</span>
                    <span
                        class="text-sm text-emerald-600 dark:text-emerald-600 font-bold">₱{{ $div['totalAbc'] }}</span>
                </div>
            @endforeach
        </div>

        <!-- BAC Categories with ABC Values -->


        <div class="flex-1 overflow-y-auto space-y-3">
            @foreach ($summaryStats['bacCategories'] as $bac)
                <div
                    class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-8 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-gray-900 dark:text-white font-semibold text-base">{{ $bac['fullName'] }}</span>

                        <div
                            class="bg-emerald-600 dark:bg-emerald-600 text-white font-bold text-xl px-4 py-1 rounded-lg">
                            {{ $bac['count'] }}
                        </div>
                        <p class="text-3xl text-center font-bold text-emerald-600 dark:text-emerald-400">
                            ₱{{ $bac['totalAbc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>


    </div>

    <!-- Per Division Count - 5 Column Layout -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach ($divisionCounts as $div)
            <div wire:click="selectDivision({{ $div['id'] }})"
                class="group relative overflow-hidden bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                <!-- Decorative background element -->
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-600/10 rounded-full -mr-10 -mt-10"></div>

                <div class="relative p-4 flex items-center gap-4">
                    <!-- Number Badge -->
                    <div class="flex-shrink-0">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-md flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                            <span class="text-2xl font-bold text-white">{{ $div['count'] }}</span>
                        </div>
                    </div>

                    <!-- Division Info -->
                    <div class="flex-1 min-w-0">
                        <p
                            class="
                                @if (strlen($div['name']) > 60) text-[11px]
                                @elseif(strlen($div['name']) > 40)
                                    text-xs
                                @else
                                    text-sm @endif
                                font-bold text-gray-800 dark:text-gray-200 line-clamp-2 leading-snug
                            ">
                            {{ $div['name'] }}
                        </p>
                    </div>

                    <!-- Arrow indicator -->
                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Cluster Committee Breakdown (shows when division is selected) -->
    @if ($selectedDivisionId)
        <div
            class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6 mt-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $selectedDivisionName }} - Cluster/Committee Breakdown
                </h3>
                <button wire:click="selectDivision({{ $selectedDivisionId }})"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($clusterCommitteeCounts as $cluster)
                    <div
                        class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4 border border-gray-200 dark:border-neutral-600">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $cluster['name'] }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 ml-3">
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 bg-emerald-600 rounded-lg text-white font-bold">
                                    {{ $cluster['count'] }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-gray-200 dark:border-neutral-600">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400">ABC:</span>
                                <span
                                    class="text-sm font-bold text-purple-600 dark:text-purple-400">₱{{ $cluster['totalAbc'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4" wire:ignore>
        <!-- Category Chart -->
        <div class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Procurements by Category</h3>
            @if ($categoryCounts->isNotEmpty())
                <div class="h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    No category data available
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" wire:ignore>
            <!-- Procurement Stage Chart -->
            <div
                class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Procurements by Stage</h3>
                @if ($procurementStageCounts->isNotEmpty())
                    <div class="h-64">
                        <canvas id="procurementStageChart"></canvas>
                    </div>
                @else
                    <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                        No procurement stage data available
                    </div>
                @endif
            </div>


            <!-- Remarks Chart -->
            <div
                class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Procurements by Remarks</h3>
                @if ($remarksCounts->isNotEmpty())
                    <div class="h-64">
                        <canvas id="remarksChart"></canvas>
                    </div>
                @else
                    <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                        No remarks data available
                    </div>
                @endif
            </div>

        </div>
        <!-- Venue Specific Chart -->
        <div class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Procurements by Venue Specific</h3>
            @if ($venueSpecificCounts->isNotEmpty())
                <div class="h-64">
                    <canvas id="venueSpecificChart"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    No venue specific data available
                </div>
            @endif
        </div>
        <!-- Venue Province/HUC Chart -->
        <div class="bg-white dark:bg-neutral-700 rounded-xl shadow border border-gray-200 dark:border-neutral-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Procurements by Province/HUC</h3>
            @if ($venueProvinceHucCounts->isNotEmpty())
                <div class="h-64">
                    <canvas id="venueProvinceHucChart"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    No province/HUC data available
                </div>
            @endif
        </div>
    </div>

    <script>
        let categoryChart = null;
        let procurementStageChart = null;
        let remarksChart = null;
        let venueSpecificChart = null;
        let venueProvinceHucChart = null;

        function initializeCharts() {
            console.log('Initializing charts...');

            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded!');
                return;
            }

            const isDarkMode = document.documentElement.classList.contains('dark');

            // Set global Chart.js color defaults
            Chart.defaults.color = isDarkMode ? '#ffffff' : '#6b7280';
            Chart.defaults.plugins.legend.labels.color = isDarkMode ? '#ffffff' : '#000000';
            Chart.defaults.plugins.tooltip.titleColor = isDarkMode ? '#ffffff' : '#000000';
            Chart.defaults.plugins.tooltip.bodyColor = isDarkMode ? '#ffffff' : '#000000';
            Chart.defaults.plugins.tooltip.backgroundColor = isDarkMode ? 'rgba(0, 0, 0, 0.8)' : 'rgba(255, 255, 255, 0.9)';
            Chart.defaults.plugins.tooltip.borderColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;

            // Destroy old charts
            if (categoryChart) categoryChart.destroy();
            if (procurementStageChart) procurementStageChart.destroy();
            if (remarksChart) remarksChart.destroy();
            if (venueSpecificChart) venueSpecificChart.destroy();
            if (venueProvinceHucChart) venueProvinceHucChart.destroy();

            // -------- Category Doughnut Chart ----------
            const categoryData = @json($categoryCounts);
            if (categoryData && categoryData.length > 0) {
                const categoryCtx = document.getElementById('categoryChart');
                if (categoryCtx) {
                    categoryChart = new Chart(categoryCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: categoryData.map(item => item.name),
                            datasets: [{
                                data: categoryData.map(item => item.count),
                                backgroundColor: [
                                    '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899',
                                    '#06b6d4', '#84cc16', '#f97316', '#ef4444', '#14b8a6',
                                    '#f43f5e', '#6366f1', '#a855f7', '#22d3ee', '#facc15',
                                    '#fb923c', '#4ade80', '#818cf8', '#c084fc', '#fbbf24'
                                ],
                                borderWidth: 2,
                                borderColor: isDarkMode ? '#3f3f46' : '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 10,
                                        font: {
                                            size: 10
                                        },
                                        boxWidth: 12,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // -------- Procurement Stage Doughnut Chart ----------
            const procurementStageData = @json($procurementStageCounts);
            if (procurementStageData && procurementStageData.length > 0) {
                const procurementStageCtx = document.getElementById('procurementStageChart');
                if (procurementStageCtx) {
                    procurementStageChart = new Chart(procurementStageCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: procurementStageData.map(item => item.name),
                            datasets: [{
                                data: procurementStageData.map(item => item.count),
                                backgroundColor: [
                                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                                    '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
                                    '#F97316', '#14B8A6', '#F43F5E', '#6366F1'
                                ],
                                borderWidth: 2,
                                borderColor: isDarkMode ? '#3f3f46' : '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 10,
                                        font: {
                                            size: 10
                                        },
                                        boxWidth: 12,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // -------- Remarks Bar Chart ----------
            const remarksData = @json($remarksCounts);
            if (remarksData && remarksData.length > 0) {
                const remarksCtx = document.getElementById('remarksChart');
                if (remarksCtx) {
                    remarksChart = new Chart(remarksCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: remarksData.map(item => item.name),
                            datasets: [{
                                label: 'Count',
                                data: remarksData.map(item => item.count),
                                backgroundColor: '#8B5CF6',
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(255,255,255,0.1)' : '#e5e7eb'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(255,255,255,0.1)' : '#e5e7eb'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // -------- Venue Specific Bar Chart ----------
            const venueSpecificData = @json($venueSpecificCounts);
            if (venueSpecificData && venueSpecificData.length > 0) {
                const venueSpecificCtx = document.getElementById('venueSpecificChart');
                if (venueSpecificCtx) {
                    venueSpecificChart = new Chart(venueSpecificCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: venueSpecificData.map(item => item.name),
                            datasets: [{
                                label: 'Count',
                                data: venueSpecificData.map(item => item.count),
                                backgroundColor: '#3b82f6',
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(255,255,255,0.1)' : '#e5e7eb'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(255,255,255,0.1)' : '#e5e7eb'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // -------- Venue Province/HUC Bar Chart ----------
            const venueProvinceHucData = @json($venueProvinceHucCounts);
            if (venueProvinceHucData && venueProvinceHucData.length > 0) {
                const venueProvinceHucCtx = document.getElementById('venueProvinceHucChart');
                if (venueProvinceHucCtx) {
                    venueProvinceHucChart = new Chart(venueProvinceHucCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: venueProvinceHucData.map(item => item.name),
                            datasets: [{
                                label: 'Count',
                                data: venueProvinceHucData.map(item => item.count),
                                backgroundColor: '#10b981',
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(255,255,255,0.1)' : '#e5e7eb'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: isDarkMode ? '#D1D5DB' : '#374151'
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(255,255,255,0.1)' : '#e5e7eb'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            console.log('Charts initialized');
        }

        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => setTimeout(initializeCharts, 100));
        } else {
            setTimeout(initializeCharts, 100);
        }

        // Reinitialize after Livewire updates
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', () => setTimeout(initializeCharts, 100));
        });

        // Listen for system color scheme changes
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleSystemThemeChange = () => {
                console.log('System color scheme changed.');
                initializeCharts();
            };

            if (darkModeQuery.addEventListener) {
                darkModeQuery.addEventListener('change', handleSystemThemeChange);
            } else if (darkModeQuery.addListener) {
                darkModeQuery.addListener(handleSystemThemeChange);
            }
        }

        // MutationObserver for manual toggles
        const themeObserver = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    console.log('HTML class attribute changed, re-initializing charts.');
                    initializeCharts();
                }
            }
        });

        themeObserver.observe(document.documentElement, {
            attributes: true
        });

        // Clean up observer and charts when Livewire component is destroyed
        document.addEventListener('livewire:shutdown', () => {
            themeObserver.disconnect();
            if (categoryChart) categoryChart.destroy();
            if (procurementStageChart) procurementStageChart.destroy();
            if (remarksChart) remarksChart.destroy();
            if (venueSpecificChart) venueSpecificChart.destroy();
            if (venueProvinceHucChart) venueProvinceHucChart.destroy();
        });
    </script>

</div>
