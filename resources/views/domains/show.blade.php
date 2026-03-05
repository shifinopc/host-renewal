@php($pageTitle = $domain->domain_name)
@php($pageSubtitle = 'Domain details, hosting, and payments')

@extends('layouts.app')

@section('content')
    <div x-data="{ renewModalOpen: {{ ($errors->any() && old('_renew')) || request()->has('renew') ? 'true' : 'false' }} }" x-init="if (new URLSearchParams(window.location.search).get('renew')) renewModalOpen = true">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400">Domain</p>
                    <div class="flex items-center gap-2">
                        @if($domain->favicon_url)
                            <img
                                src="{{ $domain->favicon_url }}"
                                alt=""
                                class="h-5 w-5 rounded shrink-0"
                                loading="lazy"
                                onerror="this.style.display='none';"
                            >
                        @endif
                        <span class="inline-flex items-center gap-1.5">
                            <p class="text-lg font-semibold text-slate-900">{{ $domain->domain_name }}</p>
                            @if($domain->site_url)
                                <a href="{{ $domain->site_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-slate-400 hover:text-indigo-500" title="Open site">
                                    <span class="material-symbols-rounded text-[18px] leading-none align-middle">open_in_new</span>
                                </a>
                            @endif
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        {{ $domain->customer?->name }} @if($domain->customer?->company) — {{ $domain->customer->company }} @endif
                    </p>
                </div>
                @php($status = $domain->expiry_status)
                <span @class([
                    'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold',
                    'bg-emerald-50 text-emerald-600' => $status === 'Active',
                    'bg-amber-50 text-amber-600' => $status === 'Expiring',
                    'bg-rose-50 text-rose-600' => $status === 'Expired',
                ])>
                    {{ $status }}
                    @if($domain->days_until_expiry !== null)
                        @if($domain->days_until_expiry > 0)
                            ({{ $domain->days_until_expiry }} days left)
                        @elseif($domain->days_until_expiry < 0)
                            ({{ abs($domain->days_until_expiry) }} days ago)
                        @endif
                    @endif
                </span>
            </div>

            <dl class="space-y-2 text-xs text-slate-600">
                <div class="flex justify-between">
                    <dt class="text-slate-400">Server</dt>
                    <dd class="text-slate-800">{{ $domain->server?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Plan</dt>
                    <dd class="text-slate-800">{{ $domain->plan_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Price</dt>
                    <dd class="text-slate-800">
                        {{ $domain->price ? number_format($domain->price, 2) : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Current period</dt>
                    <dd class="text-slate-800">
                        {{ optional($domain->start_date)->format('d-m-Y') ?? '—' }} → {{ optional($domain->expiry_date)->format('d-m-Y') ?? '—' }}
                    </dd>
                </div>
            </dl>

            <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                <p class="text-slate-400 mb-1">Notes</p>
                <p>{{ $domain->notes ?? 'No notes added yet.' }}</p>
            </div>

            <div class="flex items-center justify-end gap-3 flex-wrap">
                <a href="{{ route('domains.edit', $domain) }}" class="text-xs text-indigo-500 font-semibold">Edit</a>
                <a href="{{ route('payments.create', $domain) }}" class="text-xs text-emerald-600 font-semibold">Add Payment</a>
                @if($domain->expiry_date && $domain->expiry_date->lte(now()->addDays(60)))
                    <button type="button" @@click="renewModalOpen = true" class="text-xs text-amber-600 font-semibold">
                        Renew Domain
                    </button>
                @endif
            </div>
        </div>

        <div class="xl:col-span-2 space-y-4">
            <!-- Renewal history -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Renewal history</h2>
                <div class="space-y-2">
                    @forelse ($domain->renewals as $renewal)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2 text-xs">
                            <div>
                                <p class="font-semibold text-slate-800">
                                    {{ $renewal->start_date->format('d M Y') }} → {{ $renewal->end_date->format('d M Y') }}
                                </p>
                                @if($renewal->payment)
                                    <p class="text-slate-500 mt-0.5">
                                        Payment: {{ number_format($renewal->payment->amount, 2) }} ({{ $renewal->payment->payment_date->format('d-m-Y') }})
                                    </p>
                                @endif
                            </div>
                            @if($renewal->payment)
                                <span class="text-emerald-600 font-semibold">+ {{ number_format($renewal->payment->amount, 2) }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No renewals recorded yet. Current period: {{ optional($domain->start_date)->format('d-m-Y') ?? '—' }} to {{ optional($domain->expiry_date)->format('d-m-Y') ?? '—' }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Payment history -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-900">Payment history</h2>
                    <a href="{{ route('payments.create', $domain) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-500 text-white text-[11px] font-semibold hover:bg-emerald-600">
                        <span class="material-symbols-rounded text-[14px]">add</span>
                        Add Payment
                    </a>
                </div>

                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Date</th>
                            <th class="px-4 py-3 text-left font-semibold">Amount</th>
                            <th class="px-4 py-3 text-left font-semibold">Method</th>
                            <th class="px-4 py-3 text-left font-semibold">Reference</th>
                            <th class="px-4 py-3 text-left font-semibold">Period</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($domain->payments()->orderByDesc('payment_date')->get() as $payment)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-800">
                                    {{ optional($payment->payment_date)->format('d-m-Y') }}
                                </td>
                                <td class="px-4 py-3 text-slate-800 font-semibold">
                                    {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $payment->method ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $payment->reference_no ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-500">
                                    @if($payment->domainRenewal)
                                        {{ $payment->domainRenewal->start_date->format('d M Y') }} – {{ $payment->domainRenewal->end_date->format('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Renew Domain Modal -->
    <div x-show="renewModalOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40">
        <div @@click.away="renewModalOpen = false" class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-900">Renew domain</h2>
                <button type="button" @@click="renewModalOpen = false" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
            </div>
            <p class="text-xs text-slate-500 mb-4">Set the new start and end dates for the renewal period. Optionally add the payment received.</p>
            <form method="POST" action="{{ route('domains.renew', $domain) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_renew" value="1">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">New start date</label>
                        <input name="start_date" type="date" value="{{ old('start_date', $domain->expiry_date ? $domain->expiry_date->copy()->addDay()->format('Y-m-d') : now()->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">New end date</label>
                        <input name="end_date" type="date" value="{{ old('end_date') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[11px] text-slate-500 mb-2">Optional: record payment for this renewal</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
                            <input name="amount" type="number" step="0.01" value="{{ old('amount', $domain->price) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Payment date</label>
                            <input name="payment_date" type="date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Method</label>
                            <input name="method" value="{{ old('method', 'Bank Transfer') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Reference</label>
                            <input name="reference_no" value="{{ old('reference_no') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('notes') }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @@click="renewModalOpen = false" class="text-xs text-slate-500">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-amber-500 text-white text-xs font-semibold hover:bg-amber-600">
                        Renew domain
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
