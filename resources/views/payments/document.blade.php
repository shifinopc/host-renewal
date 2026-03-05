@php
    $kind = $type ?? 'invoice'; // 'invoice' | 'proforma' | 'receipt'
    $isInvoice = $kind === 'invoice';
    $isProforma = $kind === 'proforma';
    $isReceipt = $kind === 'receipt';

    if ($isReceipt) {
        $pageTitle = 'Receipt';
        $pageSubtitle = 'Payment receipt';
    } else {
        $pageTitle = 'Proforma invoice';
        $pageSubtitle = $isInvoice ? 'Proforma invoice for payment' : 'Proforma invoice';
    }
@endphp

@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-end mb-4">
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 print:hidden"
            >
                <span class="material-symbols-rounded text-[18px]">print</span>
                Print
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 print:shadow-none print:border print:rounded-none">
            @if(!empty($settings['invoice_header']))
                <div class="mb-6">
                    <img src="{{ asset('storage/' . $settings['invoice_header']) }}" alt="Header" class="max-w-full w-full h-auto object-contain">
                </div>
            @endif

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
                @if(empty($settings['invoice_header']))
                    <div class="flex items-start gap-4">
                        @if(!empty($settings['company_logo']))
                            <div class="shrink-0">
                                <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo" class="h-10 w-auto object-contain">
                            </div>
                        @endif
                        <h1 class="text-xl font-semibold text-slate-900">
                            {{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}
                        </h1>
                    </div>
                @endif
                <div class="text-xs text-slate-500 text-right space-y-0.5">
                    @if(!empty($settings['company_address']))
                        <p class="whitespace-pre-line">
                            {{ $settings['company_address'] }}
                        </p>
                    @endif
                    <div class="space-y-0.5">
                        @if(!empty($settings['company_email']))
                            <p>Email: {{ $settings['company_email'] }}</p>
                        @endif
                        @if(!empty($settings['company_phone']))
                            <p>Phone: {{ $settings['company_phone'] }}</p>
                        @endif
                        @if(!empty($settings['company_website']))
                            <p>Website: {{ $settings['company_website'] }}</p>
                        @endif
                        @if(!empty($settings['company_tax_id']))
                            <p>Tax/VAT ID: {{ $settings['company_tax_id'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="text-right mt-4 md:mt-0">
                    <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">
                        @if($isReceipt)
                            Receipt
                        @else
                            Proforma invoice
                        @endif
                    </p>
                    <p class="text-xl font-semibold text-slate-900 mt-1">
                        {{ $payment->invoice_number }}
                    </p>
                    <dl class="mt-3 space-y-1 text-xs text-slate-600">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Date</dt>
                            <dd>{{ optional($payment->payment_date)->format('d-m-Y') }}</dd>
                        </div>
                        @if($isInvoice)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Status</dt>
                                <dd class="text-emerald-600 font-semibold">Paid</dd>
                            </div>
                        @elseif($isProforma)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Status</dt>
                                <dd class="text-amber-600 font-semibold">{{ ucfirst($payment->status ?? 'draft') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 text-xs">
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">
                        Billed to
                    </p>
                    <p class="text-sm font-semibold text-slate-900">
                        {{ $payment->domain?->customer?->name ?? 'Customer' }}
                    </p>
                    @if($payment->domain?->customer?->company)
                        <p class="text-xs text-slate-600">
                            {{ $payment->domain->customer->company }}
                        </p>
                    @endif
                    @if($payment->domain?->customer?->address)
                        <p class="text-xs text-slate-500 whitespace-pre-line mt-1">
                            {{ $payment->domain->customer->address }}
                        </p>
                    @endif
                    @if($payment->domain?->customer?->email || $payment->domain?->customer?->phone)
                        <div class="mt-1 space-y-0.5 text-[11px] text-slate-500">
                            @if($payment->domain?->customer?->email)
                                <p>Email: {{ $payment->domain->customer->email }}</p>
                            @endif
                            @if($payment->domain?->customer?->phone)
                                <p>Phone: {{ $payment->domain->customer->phone }}</p>
                            @endif
                        </div>
                    @endif
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">
                        Service
                    </p>
                    <p class="text-sm font-semibold text-slate-900">
                        {{ $payment->domain?->domain_name ?? 'Domain/Hosting service' }}
                    </p>
                    @if($payment->domain?->server)
                        <p class="text-xs text-slate-600">
                            Server: {{ $payment->domain->server->name }}
                        </p>
                    @endif
                    @if($payment->domainRenewal)
                        <p class="text-xs text-slate-500 mt-1">
                            Period:
                            {{ $payment->domainRenewal->start_date->format('d-m-Y') }}
                            –
                            {{ $payment->domainRenewal->end_date->format('d-m-Y') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="mb-8">
                <table class="w-full text-xs border border-slate-100 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">Description</th>
                            <th class="px-4 py-2 text-right font-semibold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-3 text-slate-800">
                                @if($isInvoice)
                                    Domain/hosting renewal
                                @else
                                    Payment received
                                @endif
                                @if($payment->domain?->domain_name)
                                    — {{ $payment->domain->domain_name }}
                                @endif
                                @if($payment->domainRenewal)
                                    ({{ $payment->domainRenewal->start_date->format('d M Y') }}
                                    – {{ $payment->domainRenewal->end_date->format('d M Y') }})
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-slate-800 font-semibold">
                                {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 text-xs text-slate-700">
                        <tr>
                            <td class="px-4 py-2 text-right font-semibold">
                                Total
                            </td>
                            <td class="px-4 py-2 text-right font-semibold">
                                {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 text-[11px] text-slate-500">
                <div>
                    @if(!empty($settings['invoice_footer']))
                        <img src="{{ asset('storage/' . $settings['invoice_footer']) }}" alt="Footer" class="max-w-full w-full max-h-40 h-auto object-contain">
                    @endif
                </div>
                <div class="text-right">
                    @if($isReceipt)
                        <p class="font-semibold text-emerald-600">
                            Paid in full
                        </p>
                    @endif
                    <p class="mt-1">
                        {{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

