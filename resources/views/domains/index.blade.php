@php($pageTitle = 'Domains')
@php($pageSubtitle = 'Track hosting, expiry, and status')

@extends('layouts.app')

@section('content')
    <div x-data="{
        openCreate: false,
        editId: null,
        viewModalOpen: false,
        viewModalContent: '',
        viewModalDetailsUrl: '',
        actionModalOpen: false,
        actionModalTitle: '',
        actionModalContent: '',
        actionModalUrl: '',
        async openStacked(linkEl) {
            if (!linkEl) return;

            const action = linkEl.dataset.action || '';
            const domainId = parseInt(linkEl.dataset.domainId || '0', 10);

            if (action === 'edit') {
                this.editId = domainId;
                return;
            }

            const url = linkEl.dataset.url;
            if (!url) return;

            const titles = { payments: 'Payments', addPayment: 'Add Payment', renew: 'Renew domain' };
            this.actionModalTitle = titles[action] || action;
            this.actionModalUrl = url;
            this.actionModalOpen = true;
            this.actionModalContent = '<p class=\'text-slate-400 text-sm\'>Loading…</p>';

            try {
                const r = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                });
                const html = await r.text();
                const status = (r && r.status) || 0;

                if (!r.ok) {
                    this.actionModalContent = '<p class=\'text-rose-500 text-sm\'>Failed to load (HTTP ' + status + ').</p>';
                } else if (html.trim().startsWith('<!') || html.includes('<!DOCTYPE')) {
                    this.actionModalContent = '<p class=\'text-rose-500 text-sm\'>Unexpected full page response. Check the URL.</p>';
                } else {
                    this.actionModalContent = html;
                }
            } catch (err) {
                this.actionModalContent = '<p class=\'text-rose-500 text-sm\'>Failed to load (network error).</p>';
            }
        },
        closeStacked() {
            this.actionModalOpen = false;
        },
        async submitStackedForm(e) {
            e.preventDefault();
            const form = e.target;
            const fd = new FormData(form);
            const r = await fetch(form.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await r.json().catch(() => ({}));
            if (data.details_url) {
                const hr = await fetch(data.details_url);
                viewModalContent = await hr.text();
                actionModalOpen = false;
            }
        },
        async submitStackedDelete(e) {
            e.preventDefault();
            if (!confirm('Delete this payment?')) return;
            const form = e.target.closest('form');
            const fd = new FormData(form);
            fd.set('_method', 'DELETE');
            const r = await fetch(form.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await r.json().catch(() => ({}));
            if (data.list_url) {
                const hr = await fetch(data.list_url);
                actionModalContent = await hr.text();
            }
        }
    }">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs text-slate-400">Total domains</p>
            <p class="text-xl font-semibold text-slate-900">{{ $domains->total() }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('domains.index') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search domain or customer..."
                    class="rounded-full border border-slate-200 px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                <select name="status" class="rounded-full border border-slate-200 px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="expiring" @selected(($filters['status'] ?? '') === 'expiring')>Expiring</option>
                    <option value="expired" @selected(($filters['status'] ?? '') === 'expired')>Expired</option>
                </select>
                <select name="server_id" class="rounded-full border border-slate-200 px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All servers</option>
                    @foreach ($servers as $server)
                        <option value="{{ $server->id }}" @selected(($filters['server_id'] ?? null) == $server->id)>{{ $server->name }}</option>
                    @endforeach
                </select>
                <select name="customer_id" class="rounded-full border border-slate-200 px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(($filters['customer_id'] ?? null) == $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="text-xs px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200">
                    Filter
                </button>
            </form>
            <button type="button" @@click="openCreate = true" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                + Add Domain
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Domain</th>
                    <th class="px-4 py-3 text-left font-semibold">Customer</th>
                    <th class="px-4 py-3 text-left font-semibold">Server</th>
                    <th class="px-4 py-3 text-left font-semibold">Price</th>
                    <th class="px-4 py-3 text-left font-semibold">Expiry</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Options</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($domains as $domain)
                    @php($status = $domain->expiry_status)
                    <tr class="hover:bg-slate-50 {{ $status === 'Expiring' ? 'bg-amber-50/60' : '' }}">
                        <td class="px-4 py-3 text-slate-800">
                            <div class="flex items-center gap-2">
                                @if($domain->favicon_url)
                                    <img
                                        src="{{ $domain->favicon_url }}"
                                        alt=""
                                        class="h-4 w-4 rounded shrink-0"
                                        loading="lazy"
                                        onerror="this.style.display='none';"
                                    >
                                @endif
                                <span class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('domains.show', $domain) }}"
                                       data-details-url="{{ route('domains.details', $domain) }}"
                                       @@click.prevent="fetch($event.currentTarget.dataset.detailsUrl).then(r => r.text()).then(html => { viewModalContent = html; viewModalOpen = true; })"
                                       class="underline decoration-indigo-100 hover:decoration-indigo-400 cursor-pointer">
                                        {{ $domain->domain_name }}
                                    </a>
                                    @if($domain->site_url)
                                        <a href="{{ $domain->site_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-slate-400 hover:text-indigo-500" title="Open site">
                                            <span class="material-symbols-rounded text-[14px] leading-none align-middle">open_in_new</span>
                                        </a>
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $domain->customer?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $domain->server?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ $domain->price ? number_format($domain->price, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ optional($domain->expiry_date)->format('d-m-Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold',
                                'bg-emerald-50 text-emerald-600' => $status === 'Active',
                                'bg-amber-50 text-amber-600' => $status === 'Expiring',
                                'bg-rose-50 text-rose-600' => $status === 'Expired',
                            ])>
                                {{ $status }}
                                @php($days = $domain->days_until_expiry)
                                @if($days !== null)
                                    <span class="ml-1 opacity-90">
                                        @if($days > 0)
                                            ({{ $days }} {{ $days === 1 ? 'day' : 'days' }} left)
                                        @elseif($days < 0)
                                            ({{ abs($days) }} {{ abs($days) === 1 ? 'day' : 'days' }} ago)
                                        @endif
                                    </span>
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('payments.index', $domain) }}" class="text-[11px] text-sky-500 font-semibold mr-2">
                                Payments
                            </a>
                            @if($domain->expiry_date && $domain->expiry_date->lte(now()->addDays(60)))
                                <a href="{{ route('domains.show', $domain) }}?renew=1" class="text-[11px] text-amber-600 font-semibold mr-2">
                                    Renew
                                </a>
                            @endif
                            <button type="button" @@click="editId = {{ $domain->id }}" class="text-[11px] text-indigo-500 font-semibold mr-2">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('domains.destroy', $domain) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Delete this domain?');"
                                    class="text-[11px] text-rose-500 font-semibold"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                            No domains added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $domains->links() }}
        </div>
    </div>

    <!-- View Domain Details Modal -->
    <div x-show="viewModalOpen" x-cloak @@click.self="viewModalOpen = false" class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-100 max-h-[90vh] flex flex-col" @@click.stop>
            <div class="flex items-center justify-between p-4 border-b border-slate-100 shrink-0">
                <h2 class="text-sm font-semibold text-slate-900">Domain details</h2>
                <button type="button" @@click="viewModalOpen = false" class="h-7 w-7 rounded-full border border-slate-200 text-slate-400 hover:bg-slate-50">✕</button>
            </div>
            <div class="p-4 overflow-y-auto flex-1" x-html="viewModalContent" @@click.capture="const a = $event.target.closest('.js-open-stacked'); if (a) { $event.preventDefault(); $event.stopPropagation(); openStacked(a); }"></div>
        </div>
    </div>

    <!-- Stacked action modal (Edit / Payments / Add Payment / Renew) -->
    <div x-show="actionModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4" @@click.self="closeStacked()" @@click="if ($event.target.closest('.js-close-stacked-modal')) closeStacked()" @@submit="if ($event.target.closest('.js-stacked-modal-form')) { $event.preventDefault(); submitStackedForm($event); } else if ($event.target.closest('.js-stacked-delete-form')) { $event.preventDefault(); submitStackedDelete($event); }">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-100 max-h-[90vh] flex flex-col" @@click.stop>
            <div class="flex items-center justify-between p-4 border-b border-slate-100 shrink-0">
                <h2 class="text-sm font-semibold text-slate-900" x-text="actionModalTitle"></h2>
                <button type="button" @@click="closeStacked()" class="h-7 w-7 rounded-full border border-slate-200 text-slate-400 hover:bg-slate-50">✕</button>
            </div>
            <div class="p-4 overflow-y-auto flex-1" x-html="actionModalContent"></div>
        </div>
    </div>

    <!-- Create Domain Modal -->
    <div
        x-show="openCreate"
        x-cloak
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40"
    >
        <div @@click.away="openCreate = false" class="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-900">Add Domain</h2>
                <button type="button" @@click="openCreate = false" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
            </div>
            <form method="POST" action="{{ route('domains.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Customer</label>
                        <select name="customer_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach (\App\Models\Customer::orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} @if($customer->company) — {{ $customer->company }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Server</label>
                        <select name="server_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Not assigned —</option>
                            @foreach (\App\Models\Server::orderBy('name')->get() as $server)
                                <option value="{{ $server->id }}">
                                    {{ $server->name }} @if($server->provider) — {{ $server->provider }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2" x-data="{ domainName: '', faviconLoaded: false }" x-effect="domainName; faviconLoaded = false">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Domain Name</label>
                        <div class="flex items-center gap-2">
                            <input name="domain_name" x-model="domainName" required placeholder="example.com" pattern="([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}" title="Enter a valid domain (e.g. example.com)" class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div class="shrink-0 h-9 w-9 rounded-lg border border-slate-200 flex items-center justify-center overflow-hidden bg-slate-50" title="Favicon">
                                <img x-show="domainName && domainName.trim().includes('.') && faviconLoaded"
                                     :src="domainName && domainName.trim().includes('.') ? 'https://www.google.com/s2/favicons?domain=' + encodeURIComponent((domainName.replace(/^https?:\/\//,'').split('/')[0] || '').trim()) + '&sz=32' : ''"
                                     class="h-5 w-5 rounded"
                                     @@load="faviconLoaded = true"
                                     @@error="faviconLoaded = false"
                                     alt="">
                                <span x-show="!domainName || !domainName.trim().includes('.') || !faviconLoaded"
                                      class="material-symbols-rounded text-[18px] text-slate-400">image</span>
                            </div>
                        </div>
                        <p x-show="domainName && domainName.trim().length > 3 && !/^([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/.test(domainName.replace(/^https?:\/\//,'').split('/')[0] || '')" x-cloak class="text-[10px] text-amber-600 mt-0.5">Enter a valid domain (e.g. example.com)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Plan Name</label>
                        <input name="plan_name" placeholder="Reseller Basic" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Price</label>
                        <input name="price" type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Start Date</label>
                        <input name="start_date" type="date" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                        <input name="expiry_date" type="date" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @@click="openCreate = false" class="text-xs text-slate-500">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Save Domain
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Domain Modals -->
    @foreach ($domains as $domain)
        <div
            x-show="editId === {{ $domain->id }}"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40"
        >
            <div @@click.away="editId = null" class="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-900">Edit Domain</h2>
                    <button type="button" @@click="editId = null" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
                </div>
                <form method="POST" action="{{ route('domains.update', $domain) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Customer</label>
                            <select name="customer_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach (\App\Models\Customer::orderBy('name')->get() as $customer)
                                    <option value="{{ $customer->id }}" @selected($domain->customer_id === $customer->id)>
                                        {{ $customer->name }} @if($customer->company) — {{ $customer->company }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Server</label>
                            <select name="server_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Not assigned —</option>
                                @foreach (\App\Models\Server::orderBy('name')->get() as $server)
                                    <option value="{{ $server->id }}" @selected($domain->server_id === $server->id)>
                                        {{ $server->name }} @if($server->provider) — {{ $server->provider }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2" x-data="{ domainName: @js($domain->domain_name), faviconLoaded: false }" x-effect="domainName; faviconLoaded = false">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Domain Name</label>
                            <div class="flex items-center gap-2">
                                <input name="domain_name" x-model="domainName" value="{{ $domain->domain_name }}" required pattern="([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}" title="Enter a valid domain (e.g. example.com)" class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <div class="shrink-0 h-9 w-9 rounded-lg border border-slate-200 flex items-center justify-center overflow-hidden bg-slate-50" title="Favicon">
                                    <img x-show="domainName && domainName.trim().includes('.') && faviconLoaded"
                                         :src="domainName && domainName.trim().includes('.') ? 'https://www.google.com/s2/favicons?domain=' + encodeURIComponent((domainName.replace(/^https?:\/\//,'').split('/')[0] || '').trim()) + '&sz=32' : ''"
                                         class="h-5 w-5 rounded"
                                         @@load="faviconLoaded = true"
                                         @@error="faviconLoaded = false"
                                         alt="">
                                    <span x-show="!domainName || !domainName.trim().includes('.') || !faviconLoaded"
                                          class="material-symbols-rounded text-[18px] text-slate-400">image</span>
                                </div>
                            </div>
                            <p x-show="domainName && domainName.trim().length > 3 && !/^([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/.test(domainName.replace(/^https?:\/\//,'').split('/')[0] || '')" x-cloak class="text-[10px] text-amber-600 mt-0.5">Enter a valid domain (e.g. example.com)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Plan Name</label>
                            <input name="plan_name" value="{{ $domain->plan_name }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Price</label>
                            <input name="price" type="number" step="0.01" value="{{ $domain->price }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Start Date</label>
                            <input name="start_date" type="date" value="{{ optional($domain->start_date)->format('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                            <input name="expiry_date" type="date" value="{{ optional($domain->expiry_date)->format('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $domain->notes }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @@click="editId = null" class="text-xs text-slate-500">Cancel</button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                            Update Domain
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    </div>
@endsection

