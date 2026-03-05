@php($pageTitle = 'Create proforma invoice')
@php($pageSubtitle = 'Generate a proforma invoice for any domain')

@extends('layouts.app')

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('proforma-invoices.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Domain</label>
                    <select
                        name="domain_id"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Select domain</option>
                        @foreach($domains as $domain)
                            <option value="{{ $domain->id }}" @selected(old('domain_id') == $domain->id)">
                                {{ $domain->domain_name }} — {{ $domain->customer?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
                        <input
                            name="amount"
                            type="number"
                            step="0.01"
                            value="{{ old('amount') }}"
                            required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice date</label>
                        <input
                            name="payment_date"
                            type="date"
                            value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                            required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Method</label>
                        <input
                            name="method"
                            value="{{ old('method', 'Bank Transfer') }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Reference No.</label>
                        <input
                            name="reference_no"
                            value="{{ old('reference_no') }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('proforma-invoices.index') }}" class="text-xs text-slate-500">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Save proforma
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

