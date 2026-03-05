@php($pageTitle = 'Add Domain')
@php($pageSubtitle = 'Create a new domain under a customer and server')

@extends('layouts.app')

@section('content')
    <div class="max-w-3xl" x-data="{ domainName: @js(old('domain_name', '')), faviconLoaded: false }" x-effect="domainName; faviconLoaded = false">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
            <form method="POST" action="{{ route('domains.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Customer</label>
                        <select name="customer_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id', request('customer_id')) == $customer->id)>
                                    {{ $customer->name }} @if($customer->company) — {{ $customer->company }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Server</label>
                        <select name="server_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Not assigned —</option>
                            @foreach ($servers as $server)
                                <option value="{{ $server->id }}" @selected(old('server_id') == $server->id)>
                                    {{ $server->name }} @if($server->provider) — {{ $server->provider }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Domain Name</label>
                        <div class="flex items-center gap-2">
                            <input name="domain_name" x-model="domainName" value="{{ old('domain_name') }}" required placeholder="example.com" pattern="([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}" title="Enter a valid domain (e.g. example.com)" class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                        <input name="plan_name" value="{{ old('plan_name') }}" placeholder="Reseller Basic" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Price</label>
                        <input name="price" type="number" step="0.01" value="{{ old('price') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Start Date</label>
                        <input name="start_date" type="date" value="{{ old('start_date') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                        <input name="expiry_date" type="date" value="{{ old('expiry_date') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('domains.index') }}" class="text-xs text-slate-500">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Save Domain
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

