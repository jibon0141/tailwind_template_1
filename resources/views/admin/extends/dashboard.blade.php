@extends('admin.master')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="page-title">Dashboard</h1>
    <p class="text-sm text-slate-500 mt-1">Welcome back, {{ Auth::user()->name ?? 'Admin' }}. Here's what's happening today.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-6 lg:mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-blue-100 text-blue-600"><i class="fa-solid fa-dollar-sign"></i></div>
            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-arrow-up mr-0.5"></i>+12.5%</span>
        </div>
        <div class="stat-value">$48,295</div>
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-amber-100 text-amber-600"><i class="fa-solid fa-cart-shopping"></i></div>
            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-arrow-up mr-0.5"></i>+8.2%</span>
        </div>
        <div class="stat-value">1,842</div>
        <div class="stat-label">Total Orders</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-green-100 text-green-600"><i class="fa-solid fa-users"></i></div>
            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-arrow-up mr-0.5"></i>+5.7%</span>
        </div>
        <div class="stat-value">6,431</div>
        <div class="stat-label">Total Customers</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-purple-100 text-purple-600"><i class="fa-solid fa-chart-line"></i></div>
            <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-arrow-down mr-0.5"></i>-2.1%</span>
        </div>
        <div class="stat-value">92.5%</div>
        <div class="stat-label">Growth Rate</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6 lg:mb-8">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Revenue Overview</h3>
            <select class="text-xs border border-slate-200 rounded-lg px-2 py-1 text-slate-500 bg-white outline-none cursor-pointer">
                <option>Last 7 days</option>
                <option selected>This Month</option>
                <option>This Year</option>
            </select>
        </div>
        <div style="position:relative;width:100%;height:280px">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Orders Overview</h3>
            <select class="text-xs border border-slate-200 rounded-lg px-2 py-1 text-slate-500 bg-white outline-none cursor-pointer">
                <option>Last 7 days</option>
                <option selected>This Month</option>
                <option>This Year</option>
            </select>
        </div>
        <div style="position:relative;width:100%;height:280px">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <div class="lg:col-span-2 card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Recent Orders</h3>
            <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">View All &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="px-2 py-3">Order</th>
                        <th class="px-2 py-3">Customer</th>
                        <th class="px-2 py-3">Product</th>
                        <th class="px-2 py-3">Amount</th>
                        <th class="px-2 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-3 font-medium text-slate-700">#INV-2024-0842</td>
                        <td class="px-2 py-3">Sarah Johnson</td>
                        <td class="px-2 py-3">Amoxicillin 500mg</td>
                        <td class="px-2 py-3">$245.00</td>
                        <td class="px-2 py-3"><span class="text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">Completed</span></td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3 font-medium text-slate-700">#INV-2024-0841</td>
                        <td class="px-2 py-3">Michael Chen</td>
                        <td class="px-2 py-3">Omeprazole 20mg</td>
                        <td class="px-2 py-3">$128.50</td>
                        <td class="px-2 py-3"><span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">Processing</span></td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3 font-medium text-slate-700">#INV-2024-0840</td>
                        <td class="px-2 py-3">Emily Davis</td>
                        <td class="px-2 py-3">Atorvastatin 10mg</td>
                        <td class="px-2 py-3">$89.99</td>
                        <td class="px-2 py-3"><span class="text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">Completed</span></td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3 font-medium text-slate-700">#INV-2024-0839</td>
                        <td class="px-2 py-3">James Wilson</td>
                        <td class="px-2 py-3">Metformin 850mg</td>
                        <td class="px-2 py-3">$56.00</td>
                        <td class="px-2 py-3"><span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">Pending</span></td>
                    </tr>
                    <tr>
                        <td class="px-2 py-3 font-medium text-slate-700">#INV-2024-0838</td>
                        <td class="px-2 py-3">Lisa Anderson</td>
                        <td class="px-2 py-3">Lisinopril 5mg</td>
                        <td class="px-2 py-3">$192.75</td>
                        <td class="px-2 py-3"><span class="text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">Cancelled</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Top Products</h3>
        </div>
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold flex-shrink-0">AM</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">Amoxicillin 500mg</p>
                    <p class="text-xs text-slate-400">842 orders</p>
                </div>
                <span class="text-sm font-semibold text-slate-800">$12,580</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold flex-shrink-0">OM</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">Omeprazole 20mg</p>
                    <p class="text-xs text-slate-400">654 orders</p>
                </div>
                <span class="text-sm font-semibold text-slate-800">$8,240</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold flex-shrink-0">AT</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">Atorvastatin 10mg</p>
                    <p class="text-xs text-slate-400">521 orders</p>
                </div>
                <span class="text-sm font-semibold text-slate-800">$6,720</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold flex-shrink-0">MF</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 truncate">Metformin 850mg</p>
                    <p class="text-xs text-slate-400">398 orders</p>
                </div>
                <span class="text-sm font-semibold text-slate-800">$4,150</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    var primary = '#3b82f6';
    var gridColor = 'rgba(0,0,0,0.04)';
    var textColor = '#94a3b8';
    var days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

    function hexToRgb(h) {
        var r = parseInt(h.slice(1,3),16), g = parseInt(h.slice(3,5),16), b = parseInt(h.slice(5,7),16);
        return r+','+g+','+b;
    }

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                data: [12400, 18900, 15200, 22100, 19800, 25600, 23400],
                borderColor: primary,
                backgroundColor: 'rgba('+hexToRgb(primary)+',0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 1.5,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#e2e8f0', cornerRadius: 8, padding: 10 } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, font: { size: 11 }, callback: function(v) { return '$'+(v/1000).toFixed(0)+'k'; } },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor, font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    new Chart(document.getElementById('ordersChart'), {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                data: [42, 78, 55, 91, 67, 103, 88],
                backgroundColor: 'rgba('+hexToRgb(primary)+',0.75)',
                borderColor: primary,
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#e2e8f0', cornerRadius: 8, padding: 10 } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, font: { size: 11 } },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor, font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
})();
</script>
@endsection
