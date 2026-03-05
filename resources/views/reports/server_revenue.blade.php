@php($pageTitle = 'Server-wise Revenue')
@php($pageSubtitle = 'Revenue by server for the current year')

@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Server</th>
                    <th class="px-4 py-3 text-left font-semibold">Provider</th>
                    <th class="px-4 py-3 text-left font-semibold">Type</th>
                    <th class="px-4 py-3 text-left font-semibold">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $server)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">{{ $server->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $server->provider ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold
                                @if($server->type === 'Shared') bg-sky-50 text-sky-600
                                @elseif($server->type === 'VPS') bg-indigo-50 text-indigo-600
                                @else bg-emerald-50 text-emerald-600
                                @endif">
                                {{ $server->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-800">
                            {{ number_format($server->revenue ?? 0, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                            No revenue data available yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

