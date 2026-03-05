@php($pageTitle = 'Add Server')
@php($pageSubtitle = 'Create a new hosting server')

@extends('layouts.app')

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('servers.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                    <input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Provider</label>
                        <input name="provider" value="{{ old('provider') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">IP Address</label>
                        <input name="ip_address" value="{{ old('ip_address') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                    <select name="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Shared" @selected(old('type') === 'Shared')>Shared</option>
                        <option value="VPS" @selected(old('type') === 'VPS')>VPS</option>
                        <option value="Cloud" @selected(old('type') === 'Cloud')>Cloud</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('servers.index') }}" class="text-xs text-slate-500">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Save Server
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

