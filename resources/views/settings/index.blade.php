@php($pageTitle = 'Settings')
@php($pageSubtitle = 'Configure company profile and proforma invoice template')

@extends('layouts.app')

@section('content')
    <div class="max-w-6xl" x-data="{ tab: 'company' }">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="border-b border-slate-100 px-6 pt-4">
                <nav class="flex gap-4 text-xs font-semibold text-slate-500">
                    <button type="button"
                            @@click="tab = 'company'"
                            :class="tab === 'company' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-700'"
                            class="pb-3 border-b-2">
                        Company
                    </button>
                    <button type="button"
                            @@click="tab = 'invoice'"
                            :class="tab === 'invoice' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-700'"
                            class="pb-3 border-b-2">
                        Proforma invoice template
                    </button>
                    <button type="button"
                            @@click="tab = 'tax'"
                            :class="tab === 'tax' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-700'"
                            class="pb-3 border-b-2">
                        Tax settings
                    </button>
                </nav>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div x-show="tab === 'company'" x-cloak class="space-y-6">
                    <h2 class="text-sm font-semibold text-slate-900">Company profile</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Company name</label>
                            <input
                                type="text"
                                name="company_name"
                                value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Website</label>
                            <input
                                type="text"
                                name="company_website"
                                value="{{ old('company_website', $settings['company_website'] ?? '') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Address</label>
                        <textarea
                            name="company_address"
                            rows="2"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                            <input
                                type="email"
                                name="company_email"
                                value="{{ old('company_email', $settings['company_email'] ?? '') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
                            <input
                                type="text"
                                name="company_phone"
                                value="{{ old('company_phone', $settings['company_phone'] ?? '') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tax / VAT ID</label>
                            <input
                                type="text"
                                name="company_tax_id"
                                value="{{ old('company_tax_id', $settings['company_tax_id'] ?? '') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">GSTIN</label>
                            <input
                                type="text"
                                name="company_gstin"
                                value="{{ old('company_gstin', $settings['company_gstin'] ?? '') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Company logo</label>
                        <input
                            type="file"
                            name="company_logo"
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-indigo-600"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">PNG, JPG, GIF or WebP. Max 2MB.</p>
                        @if(!empty($settings['company_logo']))
                            <div class="mt-2 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Current logo" class="h-12 w-auto object-contain rounded border border-slate-200">
                                <button
                                    type="submit"
                                    name="remove_company_logo"
                                    value="1"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full border border-slate-200 text-[11px] font-semibold text-slate-500 hover:bg-slate-50"
                                >
                                    Remove
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div x-show="tab === 'tax'" x-cloak class="space-y-6">
                    <h2 class="text-sm font-semibold text-slate-900">Tax settings</h2>

                    @if(empty($settings['company_tax_id'] ?? null) && empty($settings['company_gstin'] ?? null))
                        <p class="text-xs text-slate-500">
                            Enter your Tax/VAT ID or GSTIN under the <strong>Company</strong> tab and save to see tax-related information here.
                        </p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-600 mb-1">Registration</p>
                                    <dl class="space-y-1 text-[11px] text-slate-700">
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-slate-500">Company</dt>
                                            <dd class="font-medium text-right">
                                                {{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}
                                            </dd>
                                        </div>
                                        @if(!empty($settings['company_tax_id'] ?? null))
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-slate-500">Tax / VAT ID</dt>
                                                <dd class="font-medium text-right">
                                                    {{ $settings['company_tax_id'] }}
                                                </dd>
                                            </div>
                                        @endif
                                        @if(!empty($settings['company_gstin'] ?? null))
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-slate-500">GSTIN</dt>
                                                <dd class="font-medium text-right">
                                                    {{ $settings['company_gstin'] }}
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-600 mb-1">Indian tax notes</p>
                                    <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-1">
                                        <li>Proforma invoices show a single total amount in INR.</li>
                                        <li>GSTIN and Tax/VAT ID are displayed on proforma PDFs when provided.</li>
                                        <li>Use GSTIN for Indian compliance; Tax/VAT ID is optional.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div x-show="tab === 'invoice'" x-cloak
                     x-data="{
                        headerPreview: null,
                        footerPreview: null,
                        hasStoredHeader: {{ !empty($settings['invoice_header']) ? 'true' : 'false' }},
                        invoicePrefix: '{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}',
                        invoiceLength: {{ (int) old('invoice_number_length', $settings['invoice_number_length'] ?? 6) }},
                        invoiceNext: {{ (int) old('invoice_next_number', $settings['invoice_next_number'] ?? 1) }},
                        padded() {
                            const len = Number(this.invoiceLength) || 6;
                            const num = String(this.invoiceNext || 1);
                            return num.padStart(len, '0');
                        },
                        onHeaderChange(e) {
                            const f = e.target.files[0];
                            if (f) {
                                this.headerPreview = URL.createObjectURL(f);
                            } else {
                                this.headerPreview = null;
                            }
                        },
                        onFooterChange(e) {
                            const f = e.target.files[0];
                            if (f) {
                                this.footerPreview = URL.createObjectURL(f);
                            } else {
                                this.footerPreview = null;
                            }
                        }
                     }"
                     x-init="$watch('tab', t => { if (t !== 'invoice') { headerPreview = null; footerPreview = null; } })"
                     class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h2 class="text-sm font-semibold text-slate-900">Proforma invoice template</h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice prefix</label>
                                    <input
                                        type="text"
                                        name="invoice_prefix"
                                        x-model="invoicePrefix"
                                        value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Number length</label>
                                    <input
                                        type="number"
                                        min="3"
                                        max="12"
                                        name="invoice_number_length"
                                        x-model.number="invoiceLength"
                                        value="{{ old('invoice_number_length', $settings['invoice_number_length'] ?? 6) }}"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Next number</label>
                                    <input
                                        type="number"
                                        min="1"
                                        name="invoice_next_number"
                                        x-model.number="invoiceNext"
                                        value="{{ old('invoice_next_number', $settings['invoice_next_number'] ?? 1) }}"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                    <p class="text-[11px] text-slate-400 mt-1">The next proforma invoice will use this number and then increment.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Proforma header image</label>
                                <input
                                    type="file"
                                    name="invoice_header"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    @@change="onHeaderChange($event)"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-indigo-600"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Image shown at the top of proforma invoices and receipts. PNG, JPG, GIF or WebP. Max 2MB.</p>
                                @if(!empty($settings['invoice_header']))
                                    <div class="mt-2 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $settings['invoice_header']) }}" alt="Current header" class="h-10 w-auto object-contain rounded border border-slate-200">
                                        <button
                                            type="submit"
                                            name="remove_invoice_header"
                                            value="1"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full border border-slate-200 text-[11px] font-semibold text-slate-500 hover:bg-slate-50"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Proforma footer image</label>
                                <input
                                    type="file"
                                    name="invoice_footer"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    @@change="onFooterChange($event)"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-indigo-600"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Image shown at the bottom of proforma invoices and receipts. PNG, JPG, GIF or WebP. Max 2MB.</p>
                                @if(!empty($settings['invoice_footer']))
                                    <div class="mt-2 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $settings['invoice_footer']) }}" alt="Current footer" class="h-10 w-auto object-contain rounded border border-slate-200">
                                        <button
                                            type="submit"
                                            name="remove_invoice_footer"
                                            value="1"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full border border-slate-200 text-[11px] font-semibold text-slate-500 hover:bg-slate-50"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="lg:sticky lg:top-6 self-start">
                            <p class="text-[11px] font-semibold text-slate-500 mb-2">Proforma invoice preview</p>
                            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 text-xs flex flex-col"
                                 style="aspect-ratio: 210 / 297;">
                                <div class="flex items-start justify-between gap-3 mb-4"
                                     x-show="!headerPreview && !hasStoredHeader">
                                    <div class="flex items-center gap-2">
                                        @if(!empty($settings['company_logo']))
                                            <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="" class="h-8 w-auto object-contain">
                                        @endif
                                        <span class="font-semibold text-slate-900">{{ $settings['company_name'] ?? config('app.name') }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Proforma invoice</p>
                                        <p class="font-semibold text-slate-800" x-text="invoicePrefix + padded()">INV-000001</p>
                                        <p class="text-slate-500 mt-0.5">Date: {{ now()->format('d-m-Y') }}</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <template x-if="headerPreview">
                                        <img :src="headerPreview" alt="Header" class="w-full max-h-32 object-contain rounded border border-slate-100 mb-2">
                                    </template>
                                    @if(!empty($settings['invoice_header']))
                                        <img x-show="!headerPreview" src="{{ asset('storage/' . $settings['invoice_header']) }}" alt="Header" class="w-full max-h-32 object-contain rounded border border-slate-100 mb-2">
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase mb-0.5">Billed to</p>
                                        <p class="font-medium text-slate-800">Sample Customer</p>
                                        <p class="text-slate-500">123 Street, City</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase mb-0.5">Service</p>
                                        <p class="font-medium text-slate-800">example.com</p>
                                        <p class="text-slate-500">Hosting renewal</p>
                                    </div>
                                </div>

                                <table class="w-full border border-slate-100 rounded-lg overflow-hidden mb-4">
                                    <thead class="bg-slate-50 text-slate-500">
                                        <tr>
                                            <th class="px-2 py-1.5 text-left font-semibold">Description</th>
                                            <th class="px-2 py-1.5 text-right font-semibold">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-t border-slate-100">
                                            <td class="px-2 py-1.5 text-slate-700">Domain renewal — example.com</td>
                                            <td class="px-2 py-1.5 text-right font-medium">99.00</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-slate-50">
                                        <tr>
                                            <td class="px-2 py-1.5 text-right font-semibold">Total</td>
                                            <td class="px-2 py-1.5 text-right font-semibold">99.00</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div class="mt-auto -mx-5 -mb-5 overflow-hidden rounded-b-2xl border-t border-slate-100">
                                    <template x-if="footerPreview">
                                        <img :src="footerPreview" alt="Footer" class="w-full max-h-40 object-contain">
                                    </template>
                                    @if(!empty($settings['invoice_footer']))
                                        <img x-show="!footerPreview" src="{{ asset('storage/' . $settings['invoice_footer']) }}" alt="Footer" class="w-full max-h-40 object-contain">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700"
                    >
                        Save settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

