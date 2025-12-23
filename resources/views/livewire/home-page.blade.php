<div class="space-y-6">
    <!-- Hero Summary Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Total Procurements Card -->
        <div
            class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold opacity-90">Total Procurements</h3>
                </div>

                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-6xl font-bold mb-2">{{ $summaryStats['total'] }}</p>
                        <p class="text-sm opacity-80">Total Procurements</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-white/20">
                    <p class="text-sm opacity-80 mb-2">Total ABC Value</p>
                    <p class="text-3xl font-bold">₱{{ $summaryStats['totalAbc'] }}</p>
                </div>
            </div>
        </div>

        <!-- BAC Categories Card -->
        <div
            class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
            <div class="flex items-center gap-3 mb-5">
                <div class="bg-blue-500/10 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">BAC Categories</h3>
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                @foreach ($summaryStats['bacCategories'] as $bac)
                    <div
                        class="group bg-gradient-to-r from-gray-50 to-white dark:from-neutral-700 dark:to-neutral-700 rounded-xl p-4 hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-neutral-600">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex-1 pr-3">
                                {{ $bac['fullName'] }}
                            </span>
                            <span
                                class="bg-blue-500 text-white text-sm font-bold px-3 py-1 rounded-lg min-w-[3rem] text-center">
                                {{ $bac['count'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">ABC:</span>
                            <span
                                class="text-lg font-bold text-blue-600 dark:text-blue-400">₱{{ $bac['totalAbc'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Division ABC Breakdown Card -->
        <div
            class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
            <div class="flex items-center gap-3 mb-5">
                <div class="bg-purple-500/10 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Division Breakdown</h3>
            </div>

            <div class="space-y-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                @foreach ($summaryStats['divisionAbc'] as $div)
                    <div
                        class="flex items-center justify-between bg-gradient-to-r from-purple-50 to-white dark:from-neutral-700 dark:to-neutral-700 rounded-lg px-4 py-3 hover:shadow-sm transition-all border border-purple-100 dark:border-neutral-600">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate max-w-[60%]">
                            {{ $div['abbreviation'] }}
                        </span>
                        <span class="text-base font-bold text-purple-600 dark:text-purple-400 whitespace-nowrap">
                            ₱{{ $div['totalAbc'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Division Cards Section -->
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-emerald-500/10 p-3 rounded-xl">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Divisions Overview</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach ($divisionCounts as $div)
                <div wire:click="selectDivision({{ $div['id'] }})"
                    class="group relative bg-gradient-to-br from-gray-50 to-white dark:from-neutral-700 dark:to-neutral-800 rounded-xl p-5 border-2 border-gray-200 dark:border-neutral-600 hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-lg transition-all duration-300 cursor-pointer">

                    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <span class="text-2xl font-bold text-white">{{ $div['count'] }}</span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0 mt-1">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100 leading-tight line-clamp-3">
                                {{ $div['name'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cluster Committee Breakdown -->
    @if ($selectedDivisionId)
        <div
            class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700 animate-fade-in">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-500/10 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                            {{ $selectedDivisionName }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Cluster/Committee Breakdown</p>
                    </div>
                </div>
                <button wire:click="selectDivision({{ $selectedDivisionId }})"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($clusterCommitteeCounts as $cluster)
                    <div
                        class="bg-gradient-to-br from-indigo-50 to-white dark:from-neutral-700 dark:to-neutral-800 rounded-xl p-5 border-2 border-indigo-100 dark:border-neutral-600 hover:border-indigo-500 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100 flex-1 pr-3 line-clamp-2">
                                {{ $cluster['name'] }}
                            </p>
                            <span
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg text-white font-bold text-lg flex items-center justify-center shadow-md">
                                {{ $cluster['count'] }}
                            </span>
                        </div>
                        <div class="pt-3 border-t border-indigo-200 dark:border-neutral-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400">ABC:</span>
                                <span class="text-sm font-bold text-purple-600 dark:text-purple-400 truncate">
                                    ₱{{ $cluster['totalAbc'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6" wire:ignore>
        <!-- Category Chart -->
        <div
            class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-orange-500/10 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Procurement by Categories</h3>
            </div>
            @if ($categoryCounts->isNotEmpty())
                <div class="h-80">
                    <canvas id="categoryChart"></canvas>
                </div>
            @else
                <div class="h-80 flex items-center justify-center">
                    <p class="text-gray-400 dark:text-gray-500">No category data available</p>
                </div>
            @endif
        </div>

        <!-- Stage & Remarks Combined -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2 gap-6">
            <!-- Procurement Stage Chart -->
            <div
                class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-blue-500/10 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">By Stage</h3>
                </div>
                @if ($procurementStagePerLotCounts->isNotEmpty() || $procurementStagePerItemCounts->isNotEmpty())
                    <div class="h-80">
                        <canvas id="procurementStageChart"></canvas>
                    </div>
                @else
                    <div class="h-80 flex items-center justify-center">
                        <p class="text-gray-400 dark:text-gray-500">No stage data available</p>
                    </div>
                @endif
            </div>

            <!-- Remarks Chart -->
            <div
                class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-pink-500/10 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">By Remarks</h3>
                </div>
                @if ($remarksPerLotCounts->isNotEmpty() || $remarksPerItemCounts->isNotEmpty())
                    <div class="h-80">
                        <canvas id="remarksChart"></canvas>
                    </div>
                @else
                    <div class="h-80 flex items-center justify-center">
                        <p class="text-gray-400 dark:text-gray-500">No remarks data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Venue Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6" wire:ignore>
        <!-- Venue Specific Chart -->
        <div
            class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-cyan-500/10 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Procurement by Venue Specific</h3>
            </div>
            @if ($venueSpecificCounts->isNotEmpty())
                <div class="h-80">
                    <canvas id="venueSpecificChart"></canvas>
                </div>
            @else
                <div class="h-80 flex items-center justify-center">
                    <p class="text-gray-400 dark:text-gray-500">No venue specific data available</p>
                </div>
            @endif
        </div>

        <!-- Venue Province/HUC Chart -->
        <div
            class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-neutral-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-teal-500/10 p-3 rounded-xl">
                    <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Procurement by Province/HUC</h3>
            </div>
            @if ($venueProvinceHucCounts->isNotEmpty())
                <div class="h-80">
                    <canvas id="venueProvinceHucChart"></canvas>
                </div>
            @else
                <div class="h-80 flex items-center justify-center">
                    <p class="text-gray-400 dark:text-gray-500">No province/HUC data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Responsive breakpoints */
        @media (min-width: 475px) {
            .xs\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 639px) {
            .responsive-text {
                font-size: 0.875rem;
            }
        }
    </style>

    <script>
        let categoryChart = null;
        let procurementStageChart = null;
        let remarksChart = null;
        let venueSpecificChart = null;
        let venueProvinceHucChart = null;

        function initializeCharts() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded!');
                return;
            }

            const isDarkMode = document.documentElement.classList.contains('dark');
            const isMobile = window.innerWidth < 640;

            // Chart.js defaults - Updated for better dark mode support
            Chart.defaults.color = isDarkMode ? '#E5E7EB' : '#6B7280';
            Chart.defaults.plugins.legend.labels.color = isDarkMode ? '#F3F4F6' : '#374151';
            Chart.defaults.plugins.legend.labels.font = {
                size: isMobile ? 10 : 11,
                weight: '600'
            };
            Chart.defaults.plugins.legend.labels.padding = isMobile ? 12 : 15;
            Chart.defaults.plugins.tooltip.backgroundColor = isDarkMode ? 'rgba(31, 41, 55, 0.95)' :
                'rgba(255, 255, 255, 0.95)';
            Chart.defaults.plugins.tooltip.titleColor = isDarkMode ? '#F9FAFB' : '#111827';
            Chart.defaults.plugins.tooltip.bodyColor = isDarkMode ? '#F3F4F6' : '#374151';
            Chart.defaults.plugins.tooltip.borderColor = isDarkMode ? '#6B7280' : '#E5E7EB';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;

            // Destroy existing charts
            [categoryChart, procurementStageChart, remarksChart, venueSpecificChart, venueProvinceHucChart].forEach(
                chart => {
                    if (chart) chart.destroy();
                });

            // Modern color palettes
            const vibrantColors = [
                '#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EC4899',
                '#06B6D4', '#84CC16', '#F97316', '#EF4444', '#14B8A6',
                '#F43F5E', '#6366F1', '#A855F7', '#22D3EE', '#FACC15'
            ];

            const blueShades = ['#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE', '#2563EB', '#1D4ED8'];
            const greenShades = ['#10B981', '#34D399', '#6EE7B7', '#A7F3D0', '#059669', '#047857'];
            const purpleShades = ['#8B5CF6', '#A78BFA', '#C4B5FD', '#DDD6FE', '#7C3AED', '#6D28D9'];
            const pinkShades = ['#EC4899', '#F472B6', '#F9A8D4', '#FBCFE8', '#DB2777', '#BE185D'];

            // Helper function for doughnut charts
            function createDoughnutChart(canvasId, perLotData, perItemData, lotColors, itemColors) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return null;

                const labels = [];
                const data = [];
                const colors = [];

                if (perLotData && perLotData.length > 0) {
                    perLotData.forEach((item, idx) => {
                        labels.push(`${item.name} (Lot)`);
                        data.push(item.count);
                        colors.push(lotColors[idx % lotColors.length]);
                    });
                }

                if (perItemData && perItemData.length > 0) {
                    perItemData.forEach((item, idx) => {
                        labels.push(`${item.name} (Item)`);
                        data.push(item.count);
                        colors.push(itemColors[idx % itemColors.length]);
                    });
                }

                if (data.length === 0) return null;

                return new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderColor: isDarkMode ? '#1F2937' : '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 15,
                            hoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: isMobile ? 'bottom' : 'right',
                                labels: {
                                    color: isDarkMode ? '#F3F4F6' : '#374151',
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: isMobile ? 12 : 15,
                                    font: {
                                        size: isMobile ? 10 : 11,
                                        weight: '600'
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        return data.labels.map((label, i) => ({
                                            text: `${label}: ${data.datasets[0].data[i]}`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            fontColor: isDarkMode ? '#F3F4F6' : '#374151',
                                            hidden: false,
                                            index: i
                                        }));
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            // Category Chart
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
                                backgroundColor: vibrantColors,
                                borderColor: isDarkMode ? '#1F2937' : '#ffffff',
                                borderWidth: 3,
                                hoverOffset: 15,
                                hoverBorderWidth: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: isMobile ? 'bottom' : 'right',
                                    labels: {
                                        color: isDarkMode ? '#F3F4F6' : '#374151',
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        padding: isMobile ? 12 : 15,
                                        font: {
                                            size: isMobile ? 10 : 11,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
            }

            // Procurement Stage Chart
            procurementStageChart = createDoughnutChart(
                'procurementStageChart',
                @json($procurementStagePerLotCounts ?? []),
                @json($procurementStagePerItemCounts ?? []),
                blueShades,
                greenShades
            );

            // Remarks Chart
            remarksChart = createDoughnutChart(
                'remarksChart',
                @json($remarksPerLotCounts ?? []),
                @json($remarksPerItemCounts ?? []),
                purpleShades,
                pinkShades
            );

            // Venue Specific Chart
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
                                backgroundColor: '#06B6D4',
                                borderRadius: 8,
                                borderSkipped: false,
                                hoverBackgroundColor: '#0891B2'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Count: ${context.parsed.y}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: isDarkMode ? '#9CA3AF' : '#6B7280',
                                        font: {
                                            size: isMobile ? 9 : 10
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: isDarkMode ? '#9CA3AF' : '#6B7280',
                                        font: {
                                            size: isMobile ? 9 : 10
                                        }
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 0.8)'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Venue Province/HUC Chart
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
                                backgroundColor: '#14B8A6',
                                borderRadius: 8,
                                borderSkipped: false,
                                hoverBackgroundColor: '#0D9488'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Count: ${context.parsed.y}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: isDarkMode ? '#9CA3AF' : '#6B7280',
                                        font: {
                                            size: isMobile ? 9 : 10
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: isDarkMode ? '#9CA3AF' : '#6B7280',
                                        font: {
                                            size: isMobile ? 9 : 10
                                        }
                                    },
                                    grid: {
                                        color: isDarkMode ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 0.8)'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            console.log('Charts initialized successfully');
        }

        // Debounce helper
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Initialize charts
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => setTimeout(initializeCharts, 100));
        } else {
            setTimeout(initializeCharts, 100);
        }

        // Handle resize
        window.addEventListener('resize', debounce(initializeCharts, 300));

        // Livewire integration
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', () => setTimeout(initializeCharts, 100));
        });

        // Dark mode observer
        const themeObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    setTimeout(initializeCharts, 100);
                }
            });
        });

        themeObserver.observe(document.documentElement, {
            attributes: true
        });

        // System theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                setTimeout(initializeCharts, 100);
            });
        }

        // Cleanup
        document.addEventListener('livewire:shutdown', () => {
            themeObserver.disconnect();
            [categoryChart, procurementStageChart, remarksChart, venueSpecificChart, venueProvinceHucChart].forEach(
                chart => {
                    if (chart) chart.destroy();
                });
        });
    </script>

</div>
