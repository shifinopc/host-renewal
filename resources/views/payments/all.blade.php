@php($pageTitle = 'Payments')
@php($pageSubtitle = 'All payment records')

@extends('layouts.app')

@section('content')
    <div x-data="{ addModalOpen: {{ (request()->has('add') || $errors->any()) ? 'true' : 'false' }} }" x-init="if (new URLSearchParams(window.location.search).get('add')) addModalOpen = true">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs text-slate-400">Total payments</p>
            <p class="text-xl font-semibold text-slate-900">{{ $payments->total() }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" @@click="addModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600">
                <span class="material-symbols-rounded text-[18px]">add</span>
                Add Payment
            </button>
            <a href="{{ route('payments.export') }}?{{ http_build_query(array_filter($filters)) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                <span class="material-symbols-rounded text-[18px]">download</span>
                Export CSV
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('payments.all') }}" class="mb-4 p-4 bg-white rounded-2xl border border-slate-100">
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[180px]">
                <label class="block text-[10px] text-slate-500 mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Domain, customer, reference..."
                    class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
            </div>
            <div>
                <label class="block text-[10px] text-slate-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
            </div>
            <div>
                <label class="block text-[10px] text-slate-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
            </div>
            <div class="min-w-[140px]">
                <label class="block text-[10px] text-slate-500 mb-1">Customer</label>
                <select name="customer_id" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
                    <option value="">All customers</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" @selected(($filters['customer_id'] ?? null) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[180px]">
                <label class="block text-[10px] text-slate-500 mb-1">Domain</label>
                <select name="domain_id" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
                    <option value="">All domains</option>
                    @foreach ($domains as $d)
                        <option value="{{ $d->id }}" @selected(($filters['domain_id'] ?? null) == $d->id)>
                            {{ $d->domain_name }} @if($d->customer) ({{ $d->customer->name }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">Filter</button>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Date</th>
                    <th class="px-4 py-3 text-left font-semibold">Domain</th>
                    <th class="px-4 py-3 text-left font-semibold">Customer</th>
                    <th class="px-4 py-3 text-left font-semibold">Amount</th>
                    <th class="px-4 py-3 text-left font-semibold">Method</th>
                    <th class="px-4 py-3 text-left font-semibold">Reference</th>
                    <th class="px-4 py-3 text-right font-semibold">Options</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">
                            {{ optional($payment->payment_date)->format('d-m-Y') }}
                        </td>
                        <td class="px-4 py-3 text-slate-800">
                            @if($payment->domain)
                                <a href="{{ route('domains.show', $payment->domain) }}" class="underline decoration-indigo-100 hover:decoration-indigo-400">
                                    {{ $payment->domain->domain_name }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->domain?->customer?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-800 font-semibold">
                            {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->method ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->reference_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($payment->domain)
                                <a href="{{ route('payments.index', $payment->domain) }}" class="text-[11px] text-sky-500 font-semibold mr-2">
                                    View
                                </a>
                                <a href="{{ route('payments.invoice', $payment) }}" class="text-[11px] text-sky-500 font-semibold mr-2">
                                    Proforma
                                </a>
                                <a href="{{ route('payments.receipt', $payment) }}" class="text-[11px] text-emerald-500 font-semibold mr-2">
                                    Receipt
                                </a>
                                <form method="POST" action="{{ route('payments.destroy', [$payment->domain, $payment]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        onclick="return confirm('Delete this payment?');"
                                        class="text-[11px] text-rose-500 font-semibold"
                                    >
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                            No payments recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40">
        <div @@click.away="addModalOpen = false" class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-900">Add Payment</h2>
                <button type="button" @@click="addModalOpen = false" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
            </div>
            <form method="POST" action="{{ route('payments.quickStore') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Domain</label>
                    <select name="domain_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">— Select domain —</option>
                        @foreach ($domains as $domain)
                            <option value="{{ $domain->id }}" @selected(old('domain_id') == $domain->id)>
                                {{ $domain->domain_name }} @if($domain->customer) — {{ $domain->customer->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
                        <input name="amount" type="number" step="0.01" value="{{ old('amount') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Date</label>
                        <input name="payment_date" type="date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Method</label>
                        <input name="method" value="{{ old('method', 'Bank Transfer') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Reference No.</label>
                        <input name="reference_no" value="{{ old('reference_no') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @@click="addModalOpen = false" class="text-xs text-slate-500">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600">
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
