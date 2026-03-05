<div class="bg-white p-6 print:p-0">
    @if(!empty($settings['invoice_header']))
        <div class="mb-4">
            <img src="{{ asset('storage/' . $settings['invoice_header']) }}" alt="Header" class="header-img w-full h-auto object-contain rounded-lg">
        </div>
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">Proforma invoice</p>
                <p class="text-lg font-bold text-slate-900">{{ $payment->invoice_number }}</p>
                @if($payment->payment_date)
                    <p class="text-[11px] text-slate-500 mt-0.5">Date: {{ $payment->payment_date->format('d-m-Y') }}</p>
                @endif
            </div>
        </div>
    @else
        <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                @if(!empty($settings['company_logo']))
                    <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo" class="logo h-8 w-auto object-contain">
                @endif
                <div>
                    <p class="font-semibold text-slate-900 text-sm">{{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}</p>
                    @if(!empty($settings['company_address']))
                        <p class="text-[11px] text-slate-500 whitespace-pre-line">{{ $settings['company_address'] }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">Proforma invoice</p>
                <p class="text-lg font-bold text-slate-900">{{ $payment->invoice_number }}</p>
            </div>
        </div>
    @endif

    @php
        $customer = $payment->domain?->customer;
        $currency = 'INR';
        $taxRatePercent = 18; // default GST rate
        $companyHasTax = !empty($settings['company_tax_id'] ?? null) || !empty($settings['company_gstin'] ?? null);
        $customerIsTaxable = isset($customer) && ($customer->tax_preference ?? 'taxable') === 'taxable';

        $invoiceTaxableFlag = $payment->is_taxable;
        $isTaxableInvoice = $invoiceTaxableFlag !== null ? (bool) $invoiceTaxableFlag : $customerIsTaxable;
        $applyGst = $companyHasTax && $isTaxableInvoice;

        $taxableAmount = (float) $payment->amount;
        $gstAmount = $applyGst ? round($taxableAmount * ($taxRatePercent / 100), 2) : 0.0;
        $totalAmount = $taxableAmount + $gstAmount;
    @endphp

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-slate-50 rounded-xl p-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Billed to</p>
            <p class="text-sm font-semibold text-slate-900">{{ $payment->domain?->customer?->name ?? 'Customer' }}</p>
            @if($payment->domain?->customer?->company)
                <p class="text-xs text-slate-600">{{ $payment->domain->customer->company }}</p>
            @endif
            @if($payment->domain?->customer?->address)
                <p class="text-[11px] text-slate-500 mt-1 whitespace-pre-line">{{ $payment->domain->customer->address }}</p>
            @endif
            @if($payment->domain?->customer?->email)
                <p class="text-[11px] text-slate-500 mt-1">{{ $payment->domain->customer->email }}</p>
            @endif
            @if($payment->domain?->customer?->phone)
                <p class="text-[11px] text-slate-500">{{ $payment->domain->customer->phone }}</p>
            @endif
            @if($payment->domain?->customer?->gstin)
                <p class="text-[11px] text-slate-500 mt-0.5">GSTIN: {{ $payment->domain->customer->gstin }}</p>
            @endif
        </div>
        @php
            $isProformaRecord = ($payment->type ?? 'invoice') === 'proforma';
        @endphp
        <div class="bg-slate-50 rounded-xl p-3">
            <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Details</p>
            <dl class="space-y-1 text-xs">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Date</dt>
                    <dd class="text-slate-800 font-medium">{{ optional($payment->payment_date)->format('d-m-Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Status</dt>
                    <dd @class([
                        'font-semibold',
                        'text-emerald-600' => ! $isProformaRecord,
                        'text-amber-600' => $isProformaRecord,
                    ])>
                        {{ $isProformaRecord ? ucfirst($payment->status ?? 'draft') : 'Paid' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bg-slate-50 rounded-xl p-3 mb-4">
        <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Service</p>
        <p class="text-sm font-semibold text-slate-900">{{ $payment->domain?->domain_name ?? 'Domain/Hosting service' }}</p>
        @if($payment->domain?->server)
            <p class="text-xs text-slate-600">Server: {{ $payment->domain->server->name }}</p>
        @endif
        @if($payment->domainRenewal)
            <p class="text-xs text-slate-500 mt-1">
                Period: {{ $payment->domainRenewal->start_date->format('d M Y') }} – {{ $payment->domainRenewal->end_date->format('d M Y') }}
            </p>
        @endif
    </div>

    <table class="w-full text-xs border border-slate-200 rounded-xl overflow-hidden mb-4">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-3 py-2 text-left font-semibold">Description</th>
                <th class="px-3 py-2 text-right font-semibold">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-t border-slate-200">
                <td class="px-3 py-2 text-slate-700">
                    Domain/hosting renewal
                    @if($payment->domain?->domain_name)
                        — {{ $payment->domain->domain_name }}
                    @endif
                    @if($payment->domainRenewal)
                        <span class="text-slate-500">({{ $payment->domainRenewal->start_date->format('d M Y') }} – {{ $payment->domainRenewal->end_date->format('d M Y') }})</span>
                    @endif
                </td>
                <td class="px-3 py-2 text-right font-semibold text-slate-800">{{ number_format($taxableAmount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot class="bg-slate-100">
            <tr>
                <td class="px-3 py-2 text-right font-semibold text-slate-700">Subtotal</td>
                <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format($taxableAmount, 2) }}</td>
            </tr>
            @if($applyGst)
                <tr>
                    <td class="px-3 py-2 text-right font-semibold text-slate-700">
                        GST @ {{ $taxRatePercent }}%
                    </td>
                    <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format($gstAmount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="px-3 py-2 text-right font-bold text-slate-700">Total</td>
                <td class="px-3 py-2 text-right font-bold text-slate-900">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if(!empty($settings['invoice_footer']))
        <div class="mb-3">
            <img src="{{ asset('storage/' . $settings['invoice_footer']) }}" alt="Footer" class="footer-img w-full h-auto object-contain rounded-lg">
        </div>
    @endif

    <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
        <p>{{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}</p>
        <div>
            @if(!empty($settings['company_email']))
                <span>{{ $settings['company_email'] }}</span>
            @endif
            @if(!empty($settings['company_phone']))
                <span> • {{ $settings['company_phone'] }}</span>
            @endif
        </div>
    </div>
</div>
