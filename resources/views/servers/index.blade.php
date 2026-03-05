@php($pageTitle = 'Servers')
@php($pageSubtitle = 'Manage hosting servers and providers')

@extends('layouts.app')

@section('content')
    <div x-data="{ openCreate: false, editId: null }">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs text-slate-400">Total servers</p>
            <p class="text-xl font-semibold text-slate-900">{{ $servers->total() }}</p>
        </div>
        <button type="button" @click="openCreate = true" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
            + Add Server
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Name</th>
                    <th class="px-4 py-3 text-left font-semibold">Provider</th>
                    <th class="px-4 py-3 text-left font-semibold">IP Address</th>
                    <th class="px-4 py-3 text-left font-semibold">Type</th>
                    <th class="px-4 py-3 text-right font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($servers as $server)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">{{ $server->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $server->provider ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $server->ip_address ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold
                                @if($server->type === 'Shared') bg-sky-50 text-sky-600
                                @elseif($server->type === 'VPS') bg-indigo-50 text-indigo-600
                                @else bg-emerald-50 text-emerald-600
                                @endif">
                                {{ $server->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" @click="editId = {{ $server->id }}" class="text-[11px] text-indigo-500 font-semibold mr-2">Edit</button>
                            <form method="POST" action="{{ route('servers.destroy', $server) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Delete this server?');"
                                    class="text-[11px] text-rose-500 font-semibold"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                            No servers added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $servers->links() }}
        </div>
    </div>

    <!-- Create Server Modal -->
    <div
        x-show="openCreate"
        x-cloak
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40"
    >
        <div @click.away="openCreate = false" class="w-full max-w-xl bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-900">Add Server</h2>
                <button type="button" @click="openCreate = false" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
            </div>
            <form method="POST" action="{{ route('servers.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                    <input name="name" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Provider</label>
                        <input name="provider" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">IP Address</label>
                        <input name="ip_address" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                    <select name="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Shared">Shared</option>
                        <option value="VPS">VPS</option>
                        <option value="Cloud">Cloud</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openCreate = false" class="text-xs text-slate-500">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Save Server
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Server Modals -->
    @foreach ($servers as $server)
        <div
            x-show="editId === {{ $server->id }}"
            x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40"
        >
            <div @click.away="editId = null" class="w-full max-w-xl bg-white rounded-3xl shadow-xl border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-900">Edit Server</h2>
                    <button type="button" @click="editId = null" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
                </div>
                <form method="POST" action="{{ route('servers.update', $server) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                        <input name="name" value="{{ $server->name }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Provider</label>
                            <input name="provider" value="{{ $server->provider }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">IP Address</label>
                            <input name="ip_address" value="{{ $server->ip_address }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                        <select name="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Shared" @selected($server->type === 'Shared')>Shared</option>
                            <option value="VPS" @selected($server->type === 'VPS')>VPS</option>
                            <option value="Cloud" @selected($server->type === 'Cloud')>Cloud</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $server->notes }}</textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="editId = null" class="text-xs text-slate-500">Cancel</button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                            Update Server
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    </div>
@endsection

