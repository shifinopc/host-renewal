@php($pageTitle = 'Payment Reports')
@php($pageSubtitle = 'Revenue summary and analytics')

@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                <span class="material-symbols-rounded text-[18px]">payments</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">Total Revenue</p>
                <p class="text-lg font-semibold text-slate-900">{{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                <span class="material-symbols-rounded text-[18px]">calendar_month</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">This Month</p>
                <p class="text-lg font-semibold text-slate-900">{{ number_format($thisMonth, 2) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                <span class="material-symbols-rounded text-[18px]">trending_up</span>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 mb-0.5">This Year</p>
                <p class="text-lg font-semibold text-slate-900">{{ number_format($thisYear, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Monthly Revenue</h2>
            <span class="text-[11px] text-slate-400">{{ now()->year }}</span>
        </div>
        <div class="h-56">
            <canvas id="paymentsReportChart" data-values='@json($monthlyData)'></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Recent Payments</h2>
            <a href="{{ route('payments.all') }}" class="text-[11px] text-indigo-500 font-semibold">View all</a>
        </div>
        <div class="space-y-2">
            @forelse ($recentPayments as $payment)
                <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-900">{{ $payment->domain?->domain_name ?? '—' }}</p>
                        <p class="text-[11px] text-slate-400">{{ $payment->domain?->customer?->name ?? '—' }} · {{ optional($payment->payment_date)->format('d M Y') }}</p>
                    </div>
                    <p class="text-xs font-semibold text-emerald-600">+ {{ number_format($payment->amount, 2) }}</p>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4">No recent payments.</p>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('turbo:load', initPaymentsReportChart);
        document.addEventListener('DOMContentLoaded', initPaymentsReportChart);

        function initPaymentsReportChart() {
            const el = document.getElementById('paymentsReportChart');
            if (!el || el.dataset.initialized) return;
            el.dataset.initialized = 'true';

            const values = JSON.parse(el.dataset.values || '[]');

            new Chart(el, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        data: values,
                        backgroundColor: ['#10b981', '#34d399', '#6ee7b7', '#10b981', '#34d399', '#6ee7b7', '#10b981', '#34d399', '#6ee7b7', '#10b981', '#34d399', '#6ee7b7'],
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: '#e5e7eb' },
                            beginAtZero: true,
                            ticks: { callback: v => v.toLocaleString() },
                        },
                    },
                },
            });
        }
    </script>
@endsection
