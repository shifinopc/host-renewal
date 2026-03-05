@php($title = 'Dashboard - Host Renewal')
@php($pageTitle = 'Dashboard')
@php($pageSubtitle = 'Overview of domains, renewals, and revenue')

@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input
                    type="text"
                    placeholder="Search domains, customers, servers..."
                    class="w-full rounded-full border border-slate-200 pl-10 pr-4 py-2 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                <span class="material-symbols-rounded text-slate-400 text-[18px] absolute left-3 top-1/2 -translate-y-1/2">
                    search
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button class="h-8 w-8 rounded-full border border-slate-200 flex items-center justify-center text-xs text-slate-500">
                <span class="material-symbols-rounded text-[18px]">notifications</span>
            </button>
            <a href="{{ route('domains.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                <span class="material-symbols-rounded text-[18px]">add</span>
                <span>Create Domain</span>
            </a>
        </div>
    </div>

    <!-- Top stat cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                <span class="material-symbols-rounded text-[18px]">public</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">Active Domains</p>
                <p class="text-lg font-semibold text-slate-900">{{ $totalActiveDomains }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                <span class="material-symbols-rounded text-[18px]">schedule</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">Expiring (7 days)</p>
                <p class="text-lg font-semibold text-amber-500">{{ $expiringIn7Days }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-rose-50 flex items-center justify-center text-rose-500">
                <span class="material-symbols-rounded text-[18px]">report</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">Expired Domains</p>
                <p class="text-lg font-semibold text-rose-500">{{ $expiredCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                <span class="material-symbols-rounded text-[18px]">payments</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">Revenue (year)</p>
                <p class="text-lg font-semibold text-slate-900">{{ number_format($revenueThisYear, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- High-level summary full-width -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Summary</h2>
        <div class="grid grid-cols-2 gap-3 text-[11px] text-slate-500">
            <div class="rounded-xl border border-slate-100 px-3 py-2">
                <p class="text-[10px] text-slate-400 mb-0.5">Servers</p>
                <p class="text-base font-semibold text-slate-900">{{ $totalServers }}</p>
            </div>
            <div class="rounded-xl border border-slate-100 px-3 py-2">
                <p class="text-[10px] text-slate-400 mb-0.5">Customers</p>
                <p class="text-base font-semibold text-slate-900">{{ $totalCustomers }}</p>
            </div>
            <div class="rounded-xl border border-slate-100 px-3 py-2">
                <p class="text-[10px] text-slate-400 mb-0.5">Domains</p>
                <p class="text-base font-semibold text-slate-900">{{ $totalDomains }}</p>
            </div>
            <div class="rounded-xl border border-slate-100 px-3 py-2">
                <p class="text-[10px] text-slate-400 mb-0.5">Revenue (year)</p>
                <p class="text-base font-semibold text-slate-900">{{ number_format($revenueThisYear, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Domains overview full-width section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Domains Overview</h2>
            <span class="text-[11px] text-slate-400">Monthly (count of domains)</span>
        </div>
        <div class="h-56">
            <canvas id="dashboardRevenueChart" data-values='@json($monthlyData)'></canvas>
        </div>
    </div>

    <!-- Upcoming renewals + recent payments 50/50 -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <!-- Upcoming renewals -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Upcoming Renewals</h2>
                <span class="text-[11px] text-slate-400">Next</span>
            </div>
            <div class="space-y-3">
                @forelse ($upcomingExpiries as $domain)
                    <div class="rounded-xl border border-slate-100 px-3 py-2 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-900">{{ $domain->domain_name }}</p>
                            <p class="text-[11px] text-slate-400">
                                {{ $domain->customer?->name ?? '—' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] text-slate-500">
                                {{ optional($domain->expiry_date)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No upcoming renewals.</p>
                @endforelse
            </div>
            <div class="mt-3">
                {{ $upcomingExpiries->onEachSide(0)->links() }}
            </div>
        </div>

        <!-- Recent payments -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Recent Payments</h2>
                <span class="text-[11px] text-slate-400">Latest</span>
            </div>
            <div class="space-y-3">
                @forelse ($recentPayments as $payment)
                    <div class="border border-slate-100 rounded-xl px-3 py-2">
                        <p class="text-xs font-semibold text-slate-900">
                            {{ $payment->domain?->domain_name ?? 'Unknown domain' }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            {{ $payment->domain?->customer?->name ?? '—' }}
                            • {{ optional($payment->payment_date)->format('d M Y') }}
                        </p>
                        <p class="text-xs font-semibold text-emerald-600 mt-1">
                            + {{ number_format($payment->amount, 2) }}
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No recent payments.</p>
                @endforelse
            </div>
            <div class="mt-3">
                {{ $recentPayments->onEachSide(0)->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('turbo:load', initDashboardCharts);
        document.addEventListener('DOMContentLoaded', initDashboardCharts);

        function initDashboardCharts() {
            const barEl = document.getElementById('dashboardRevenueChart');
            if (barEl && !barEl.dataset.initialized) {
                barEl.dataset.initialized = 'true';
                const values = JSON.parse(barEl.dataset.values || '[]');

                const ctx = barEl.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, barEl.height);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
                gradient.addColorStop(1, 'rgba(129, 140, 248, 0.02)');

                new Chart(barEl, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            data: values,
                            tension: 0.35,
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#4f46e5',
                            pointBorderWidth: 0,
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#0f172a',
                                titleColor: '#e5e7eb',
                                bodyColor: '#e5e7eb',
                                padding: 10,
                                borderColor: '#1f2937',
                                borderWidth: 1,
                            },
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: '#f3f4f6',
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#9ca3af',
                                },
                            },
                            y: {
                                grid: {
                                    color: '#e5e7eb',
                                    drawBorder: false,
                                },
                                beginAtZero: true,
                                ticks: {
                                    color: '#9ca3af',
                                    precision: 0,
                                },
                                title: {
                                    display: true,
                                    text: 'Domains',
                                    color: '#6b7280',
                                    font: { size: 11 },
                                },
                            },
                        },
                    },
                });
            }
        }
    </script>
@endsection
