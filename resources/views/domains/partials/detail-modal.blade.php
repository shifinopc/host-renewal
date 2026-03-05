@php($status = $domain->expiry_status)
<div class="space-y-4 text-xs">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
            @if($domain->favicon_url)
                <img src="{{ $domain->favicon_url }}" alt="" class="h-6 w-6 rounded shrink-0" loading="lazy" onerror="this.style.display='none';">
            @endif
            <div class="min-w-0">
                <p class="font-semibold text-slate-900 truncate">{{ $domain->domain_name }}</p>
                <p class="text-slate-500">{{ $domain->customer?->name ?? '—' }}@if($domain->customer?->company) — {{ $domain->customer->company }}@endif</p>
            </div>
        </div>
        <span @class([
            'shrink-0 inline-flex px-2.5 py-1 rounded-full text-[10px] font-semibold',
            'bg-emerald-50 text-emerald-600' => $status === 'Active',
            'bg-amber-50 text-amber-600' => $status === 'Expiring',
            'bg-rose-50 text-rose-600' => $status === 'Expired',
        ])>
            {{ $status }}
            @if($domain->days_until_expiry !== null)
                @if($domain->days_until_expiry > 0)({{ $domain->days_until_expiry }} days left)
                @elseif($domain->days_until_expiry < 0)({{ abs($domain->days_until_expiry) }} days ago)
                @endif
            @endif
        </span>
    </div>

    <dl class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-slate-600">
        <div class="flex justify-between"><dt class="text-slate-400">Server</dt><dd>{{ $domain->server?->name ?? '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-400">Plan</dt><dd>{{ $domain->plan_name ?? '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-400">Price</dt><dd>{{ $domain->price ? number_format($domain->price, 2) : '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-400">Current period</dt><dd>{{ optional($domain->start_date)->format('d-m-Y') ?? '—' }} → {{ optional($domain->expiry_date)->format('d-m-Y') ?? '—' }}</dd></div>
    </dl>

    @if($domain->notes)
        <div class="pt-2 border-t border-slate-100">
            <p class="text-slate-400 mb-0.5">Notes</p>
            <p class="text-slate-600">{{ Str::limit($domain->notes, 120) }}</p>
        </div>
    @endif

    <!-- Renewal history -->
    <div>
        <h3 class="font-semibold text-slate-800 mb-2">Renewal history</h3>
        <div class="space-y-1.5 max-h-28 overflow-y-auto">
            @forelse ($domain->renewals as $renewal)
                <div class="flex justify-between items-center rounded-lg border border-slate-100 px-2.5 py-1.5">
                    <span class="text-slate-700">{{ $renewal->start_date->format('d M Y') }} → {{ $renewal->end_date->format('d M Y') }}</span>
                    @if($renewal->payment)
                        <span class="text-emerald-600 font-semibold">+ {{ number_format($renewal->payment->amount, 2) }}</span>
                    @endif
                </div>
            @empty
                <p class="text-slate-400 py-1">No renewals yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Payment history -->
    <div>
        <h3 class="font-semibold text-slate-800 mb-2">Recent payments</h3>
        <div class="space-y-1.5 max-h-32 overflow-y-auto">
            @forelse ($domain->payments()->orderByDesc('payment_date')->take(5)->get() as $payment)
                <div class="flex justify-between items-center rounded-lg border border-slate-100 px-2.5 py-1.5">
                    <span class="text-slate-700">{{ $payment->payment_date->format('d-m-Y') }} · {{ $payment->method ?? '—' }}</span>
                    <span class="font-semibold text-slate-800">{{ number_format($payment->amount, 2) }}</span>
                </div>
            @empty
                <p class="text-slate-400 py-1">No payments yet.</p>
            @endforelse
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-slate-100 js-detail-modal-actions">
        <a href="{{ route('domains.show', $domain) }}" class="text-indigo-600 font-semibold" data-turbo="false">View full page</a>
        <a href="#" data-action="payments" data-domain-id="{{ $domain->id }}" data-url="{{ route('payments.index.modal', $domain) }}" class="text-sky-600 font-semibold js-open-stacked">Payments</a>
        <a href="#" data-action="addPayment" data-domain-id="{{ $domain->id }}" data-url="{{ route('payments.create.modal', $domain) }}" class="text-emerald-600 font-semibold js-open-stacked">Add Payment</a>
        <a href="#" data-action="addProforma" data-domain-id="{{ $domain->id }}" data-url="{{ route('proforma.create.modal', $domain) }}" class="text-indigo-600 font-semibold js-open-stacked">New proforma</a>
        @if($domain->expiry_date && $domain->expiry_date->lte(now()->addDays(60)))
            <a href="#" data-action="renew" data-domain-id="{{ $domain->id }}" data-url="{{ route('domains.renew.modal', $domain) }}" class="text-amber-600 font-semibold js-open-stacked">Renew</a>
        @endif
        <a href="#" data-action="edit" data-domain-id="{{ $domain->id }}" class="text-slate-600 font-semibold js-open-stacked">Edit</a>
    </div>
</div>
