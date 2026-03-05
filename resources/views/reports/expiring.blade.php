@php($pageTitle = 'Expiring Domains')
@php($pageSubtitle = 'Domains expiring within the next 30 days')

@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Domain</th>
                    <th class="px-4 py-3 text-left font-semibold">Customer</th>
                    <th class="px-4 py-3 text-left font-semibold">Server</th>
                    <th class="px-4 py-3 text-left font-semibold">Expiry</th>
                    <th class="px-4 py-3 text-left font-semibold">Days Left</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($domains as $domain)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">
                            <a href="{{ route('domains.show', $domain) }}" class="underline decoration-indigo-100 hover:decoration-indigo-400">
                                {{ $domain->domain_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $domain->customer?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $domain->server?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ optional($domain->expiry_date)->format('d-m-Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            @if($domain->expiry_date)
                                {{ now()->diffInDays($domain->expiry_date, false) }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                            No domains expiring in the next 30 days.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-top border-slate-100">
            {{ $domains->links() }}
        </div>
    </div>
@endsection

