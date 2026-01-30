<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6">
        <!-- Page header -->
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-2xl font-semibold leading-6 text-gray-900">Dashboard</h3>
            <p class="mt-2 max-w-4xl text-sm text-gray-500">
                Welcome back, {{ $user->name }}! Here's an overview of your inventory.
            </p>
        </div>

        <!-- Period Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
            <nav class="flex space-x-1" aria-label="Tabs">
                <a href="{{ route('dashboard', ['period' => 'today']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('period') === 'today' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Today
                </a>
                <a href="{{ route('dashboard', ['period' => 'week']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('period') === 'week' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    This Week
                </a>
                <a href="{{ route('dashboard', ['period' => 'month']) || !request('period') }} ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    This Month
                </a>
                <a href="{{ route('dashboard', ['period' => 'year']) }}" 
                   class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-center transition-all {{ request('period') === 'year' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    This Year
                </a>
            </nav>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Products</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($summary['total_products']) }}</dd>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Categories</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($summary['total_categories']) }}</dd>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Orders</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ number_format($summary['total_orders']) }}</dd>
            </div>

            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Inventory Value</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">${{ number_format($summary['total_value'], 2) }}</dd>
            </div>
        </div>

        <!-- Alerts -->
        @if($summary['low_stock_alerts'] > 0 || $summary['out_of_stock_count'] > 0)
        <div class="rounded-md bg-yellow-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Stock Alerts</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @if($summary['low_stock_alerts'] > 0)
                                <li>{{ $summary['low_stock_alerts'] }} product(s) running low on stock</li>
                            @endif
                            @if($summary['out_of_stock_count'] > 0)
                                <li>{{ $summary['out_of_stock_count'] }} product(s) out of stock</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Charts -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Sales Chart -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold leading-6 text-gray-900">Revenue Trends</h3>
                        <span class="text-xs text-gray-500">Monthly Overview</span>
                    </div>
                    <div class="mt-6">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Category Distribution Chart -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold leading-6 text-gray-900">Category Distribution</h3>
                        <span class="text-xs text-gray-500">By Product Count</span>
                    </div>
                    <div class="mt-6 flex justify-center">
                        <canvas id="categoryChart" width="250" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Revenue ($)',
                    data: {!! json_encode($chartData['revenue']) !!},
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(79, 70, 229)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
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
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryNames = {!! json_encode(array_column($summary['top_categories'], 'name')) !!};
        const categoryPercentages = {!! json_encode(array_column($summary['top_categories'], 'percentage')) !!};
        
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryNames,
                datasets: [{
                    data: categoryPercentages,
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(129, 140, 248, 0.8)',
                        'rgba(165, 180, 252, 0.8)',
                        'rgba(199, 210, 254, 0.8)'
                    ],
                    borderColor: [
                        'rgb(79, 70, 229)',
                        'rgb(99, 102, 241)',
                        'rgb(129, 140, 248)',
                        'rgb(165, 180, 252)',
                        'rgb(199, 210, 254)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
