@php($pageTitle = 'Customers')
@php($pageSubtitle = 'Manage customer accounts and companies')

@extends('layouts.app')

@section('content')
    <div x-data="{ openCreate: false, editId: null }">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs text-slate-400">Total customers</p>
            <p class="text-xl font-semibold text-slate-900">{{ $customers->total() }}</p>
        </div>
        <button type="button" @click="openCreate = true" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
            + Add Customer
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Name</th>
                    <th class="px-4 py-3 text-left font-semibold">Company</th>
                    <th class="px-4 py-3 text-left font-semibold">Email</th>
                    <th class="px-4 py-3 text-left font-semibold">Phone</th>
                    <th class="px-4 py-3 text-right font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($customers as $customer)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">
                            <a href="{{ route('customers.show', $customer) }}" class="underline decoration-indigo-100 hover:decoration-indigo-400">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $customer->company ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $customer->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" @click="editId = {{ $customer->id }}" class="text-[11px] text-indigo-500 font-semibold mr-2">Edit</button>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Delete this customer?');"
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
                            No customers added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Create Customer Modal -->
    <div
        x-show="openCreate"
        x-cloak
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40"
    >
        <div @click.away="openCreate = false" class="w-full max-w-xl bg-white rounded-3xl shadow-xl border border-slate-100 p-6" x-data="{ taxPreference: 'taxable' }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-900">Add Customer</h2>
                <button type="button" @click="openCreate = false" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
            </div>
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                    <input name="name" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Company</label>
                        <input name="company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
                        <input name="phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input name="email" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Address</label>
                    <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="block text-xs font-semibold text-slate-700 mb-1">Tax preference</p>
                        <div class="flex items-center gap-4 text-xs">
                            <label class="inline-flex items-center gap-1">
                                <input type="radio" name="tax_preference" value="taxable" x-model="taxPreference" checked>
                                <span>Taxable</span>
                            </label>
                            <label class="inline-flex items-center gap-1">
                                <input type="radio" name="tax_preference" value="exempt" x-model="taxPreference">
                                <span>Tax exempt</span>
                            </label>
                        </div>
                    </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="taxPreference === 'taxable'">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Business type</label>
                        <select name="business_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select business type</option>
                            @foreach($businessTypes as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">GSTIN / Unit (optional)</label>
                        <input name="gstin" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <label class="block text-xs font-semibold text-slate-700 mb-1 mt-3">Place of supply</label>
                        <select name="place_of_supply" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select state</option>
                            @foreach($indianStates as $code => $label)
                                <option value="{{ $code }}">[{{ $code }}] - {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                    <div x-show="taxPreference === 'exempt'">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tax exemption description</label>
                        <textarea name="tax_exempt_reason" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openCreate = false" class="text-xs text-slate-500">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Customer Modals -->
    @foreach ($customers as $customer)
        <div
            x-show="editId === {{ $customer->id }}"
            x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40"
        >
            <div @click.away="editId = null" class="w-full max-w-xl bg-white rounded-3xl shadow-xl border border-slate-100 p-6" x-data="{ taxPreference: '{{ $customer->tax_preference ?? 'taxable' }}' }">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-900">Edit Customer</h2>
                    <button type="button" @click="editId = null" class="h-7 w-7 rounded-full border border-slate-200 text-xs text-slate-400">✕</button>
                </div>
                <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                        <input name="name" value="{{ $customer->name }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Company</label>
                            <input name="company" value="{{ $customer->company }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
                            <input name="phone" value="{{ $customer->phone }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input name="email" type="email" value="{{ $customer->email }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Address</label>
                        <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $customer->address }}</textarea>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="block text-xs font-semibold text-slate-700 mb-1">Tax preference</p>
                            <div class="flex items-center gap-4 text-xs">
                                <label class="inline-flex items-center gap-1">
                                    <input type="radio" name="tax_preference" value="taxable" x-model="taxPreference">
                                    <span>Taxable</span>
                                </label>
                                <label class="inline-flex items-center gap-1">
                                    <input type="radio" name="tax_preference" value="exempt" x-model="taxPreference">
                                    <span>Tax exempt</span>
                                </label>
                            </div>
                        </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="taxPreference === 'taxable'">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Business type</label>
                            <select name="business_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select business type</option>
                                @foreach($businessTypes as $code => $label)
                                    <option value="{{ $code }}" @selected($customer->business_type === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">GSTIN / Unit (optional)</label>
                            <input name="gstin" value="{{ $customer->gstin }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <label class="block text-xs font-semibold text-slate-700 mb-1 mt-3">Place of supply</label>
                            <select name="place_of_supply" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select state</option>
                                @foreach($indianStates as $code => $label)
                                    <option value="{{ $code }}" @selected($customer->place_of_supply === $code)>[{{ $code }}] - {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div x-show="taxPreference === 'exempt'">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tax exemption description</label>
                            <textarea name="tax_exempt_reason" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $customer->tax_exempt_reason }}</textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="editId = null" class="text-xs text-slate-500">Cancel</button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                            Update Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    </div>
@endsection

