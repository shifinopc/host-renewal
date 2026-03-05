@php($pageTitle = 'Revenue Report')
@php($pageSubtitle = 'Revenue per month for ' . $year)

@extends('layouts.app')

@section('content')
    @php
        $data = array_fill(1, 12, 0);
        foreach ($monthly as $row) {
            $data[$row->month] = (float) $row->total;
        }
    @endphp

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-slate-400">Year</p>
                <p class="text-lg font-semibold text-slate-900">{{ $year }}</p>
            </div>
        </div>

        <div class="h-72">
            <canvas id="revenueReportChart"
                data-values='@json(array_values($data))'>
            </canvas>
        </div>
    </div>

    <script>
        document.addEventListener('turbo:load', renderRevenueReport);
        document.addEventListener('DOMContentLoaded', renderRevenueReport);

        function renderRevenueReport() {
            const el = document.getElementById('revenueReportChart');
            if (!el || el.dataset.initialized) return;
            el.dataset.initialized = 'true';

            const values = JSON.parse(el.dataset.values || '[]');

            new Chart(el, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue',
                        data: values,
                        backgroundColor: 'rgba(79, 70, 229, 0.85)',
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: '#e5e7eb' },
                            ticks: {
                                callback(value) {
                                    return value.toLocaleString();
                                },
                            },
                        },
                    },
                },
            });
        }
    </script>
@endsection

