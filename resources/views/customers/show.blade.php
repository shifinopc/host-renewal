@php($pageTitle = $customer->name)
@php($pageSubtitle = 'Customer overview and domains')

@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Customer Details</h2>
            <dl class="space-y-2 text-xs text-slate-600">
                <div class="flex justify-between">
                    <dt class="text-slate-400">Name</dt>
                    <dd class="text-slate-800">{{ $customer->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Company</dt>
                    <dd class="text-slate-800">{{ $customer->company ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Email</dt>
                    <dd class="text-slate-800">{{ $customer->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Phone</dt>
                    <dd class="text-slate-800">{{ $customer->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 mb-1">Address</dt>
                    <dd class="text-slate-800">{{ $customer->address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-900">Domains</h2>
                <a href="{{ route('domains.create', ['customer_id' => $customer->id]) }}" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                    + Add Domain
                </a>
            </div>

            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Domain</th>
                        <th class="px-4 py-3 text-left font-semibold">Server</th>
                        <th class="px-4 py-3 text-left font-semibold">Expiry</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customer->domains as $domain)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-800">
                                <a href="{{ route('domains.show', $domain) }}" class="underline decoration-indigo-100 hover:decoration-indigo-400">
                                    {{ $domain->domain_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $domain->server?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ optional($domain->expiry_date)->format('d-m-Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php($status = $domain->expiry_status)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold
                                    @if($status === 'Active') bg-emerald-50 text-emerald-600
                                    @elseif($status === 'Expiring') bg-amber-50 text-amber-600
                                    @else bg-rose-50 text-rose-600
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                No domains for this customer yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

