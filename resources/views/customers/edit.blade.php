@php($pageTitle = 'Edit Customer')
@php($pageSubtitle = 'Update customer profile')

@extends('layouts.app')

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4" x-data="{ taxPreference: '{{ old('tax_preference', $customer->tax_preference ?? 'taxable') }}' }">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                    <input name="name" value="{{ old('name', $customer->name) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Company</label>
                        <input name="company" value="{{ old('company', $customer->company) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
                        <input name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email', $customer->email) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Address</label>
                    <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="block text-xs font-semibold text-slate-700 mb-1">Tax preference</p>
                        <div class="flex items-center gap-4 text-xs">
                            <label class="inline-flex items-center gap-1">
                                <input type="radio" name="tax_preference" value="taxable"
                                       x-model="taxPreference">
                                <span>Taxable</span>
                            </label>
                            <label class="inline-flex items-center gap-1">
                                <input type="radio" name="tax_preference" value="exempt"
                                       x-model="taxPreference">
                                <span>Tax exempt</span>
                            </label>
                        </div>
                    </div>

                    @php
                        $businessTypes = [
                            'business' => 'Registered Business - Regular | Registered under GST',
                            'composition' => 'Registered Business - Composition | Under Composition Scheme',
                            'non_business' => 'Unregistered Business | Not registered under GST',
                            'consumer' => 'Consumer | Regular consumer',
                            'overseas' => 'Overseas | Import/export supplies outside India',
                            'business_sez' => 'SEZ Business | Unit in SEZ / SEZ Developer',
                            'deemed_export' => 'Deemed Export | Supplies to EOUs / similar',
                            'tax_deductor' => 'Tax Deductor | Govt departments / local authorities',
                            'sez_developer' => 'SEZ Developer | Owns >=26% equity in SEZ unit',
                        ];
                        $indianStates = [
                            'AN' => 'Andaman and Nicobar Islands',
                            'AP' => 'Andhra Pradesh',
                            'AR' => 'Arunachal Pradesh',
                            'AS' => 'Assam',
                            'BR' => 'Bihar',
                            'CH' => 'Chandigarh',
                            'CT' => 'Chhattisgarh',
                            'DL' => 'Delhi',
                            'GA' => 'Goa',
                            'GJ' => 'Gujarat',
                            'HR' => 'Haryana',
                            'HP' => 'Himachal Pradesh',
                            'JK' => 'Jammu and Kashmir',
                            'JH' => 'Jharkhand',
                            'KA' => 'Karnataka',
                            'KL' => 'Kerala',
                            'LA' => 'Ladakh',
                            'LD' => 'Lakshadweep',
                            'MP' => 'Madhya Pradesh',
                            'MH' => 'Maharashtra',
                            'MN' => 'Manipur',
                            'ML' => 'Meghalaya',
                            'MZ' => 'Mizoram',
                            'NL' => 'Nagaland',
                            'OR' => 'Odisha',
                            'PY' => 'Puducherry',
                            'PB' => 'Punjab',
                            'RJ' => 'Rajasthan',
                            'SK' => 'Sikkim',
                            'TN' => 'Tamil Nadu',
                            'TG' => 'Telangana',
                            'TR' => 'Tripura',
                            'UP' => 'Uttar Pradesh',
                            'UK' => 'Uttarakhand',
                            'WB' => 'West Bengal',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="taxPreference === 'taxable'">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Business type</label>
                            <select name="business_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select business type</option>
                                @foreach($businessTypes as $code => $label)
                                    <option value="{{ $code }}" @selected(old('business_type', $customer->business_type) === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">GSTIN / Unit (optional)</label>
                            <input name="gstin" value="{{ old('gstin', $customer->gstin) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <label class="block text-xs font-semibold text-slate-700 mb-1 mt-3">Place of supply</label>
                            <select name="place_of_supply" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select state</option>
                                @foreach($indianStates as $code => $label)
                                    <option value="{{ $code }}" @selected(old('place_of_supply', $customer->place_of_supply) === $code)>{{ '[' . $code . '] - ' . $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div x-show="taxPreference === 'exempt'">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tax exemption description</label>
                        <textarea name="tax_exempt_reason" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('tax_exempt_reason', $customer->tax_exempt_reason) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('customers.index') }}" class="text-xs text-slate-500">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                        Update Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

