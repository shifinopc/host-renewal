<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice {{ $payment->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000000;
            line-height: 1.4;
            margin: 0px 0px 0px 0px;
        }

        h1 {
            font-size: 18px;
            text-align: center;
            margin: 12px 0 16px 0;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            vertical-align: top;
            padding: 4px 8px;
            font-size: 11px;
        }

        .info-box {
            border: 1px solid #000000;
            border-radius: 6px;
            padding: 6px 8px;
            margin-bottom: 6px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 11px;
        }

        .items-table th {
            font-weight: bold;
            text-align: left;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .totals-row-label {
            text-align: right;
            font-weight: bold;
        }

        .totals-row-value {
            text-align: right;
            font-weight: bold;
        }

        .footer-fixed {
            position: fixed;
            left: 15px;
            right: 15px;
            bottom: 16px;
        }

        .footer-image img {
            width: 100%;
            height: auto;
            max-height: 200px;
        }

        .footer-meta {
            font-size: 10px;
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    @php
        $customer = $payment->domain?->customer;
        $domain = $payment->domain;
        $renewal = $payment->domainRenewal;
        $currency = 'INR';
        $amount = (float) $payment->amount;

        // GST calculation: depends on company registration + per-invoice choice.
        $taxRatePercent = 18; // default GST rate
        $companyHasTax = !empty($settings['company_tax_id'] ?? null) || !empty($settings['company_gstin'] ?? null);
        $customerIsTaxable = isset($customer) && ($customer->tax_preference ?? 'taxable') === 'taxable';

        $invoiceTaxableFlag = $payment->is_taxable;
        $isTaxableInvoice = $invoiceTaxableFlag !== null ? (bool) $invoiceTaxableFlag : $customerIsTaxable;
        $applyGst = $companyHasTax && $isTaxableInvoice;

        $taxableAmount = $amount;
        $gstAmount = $applyGst ? round($taxableAmount * ($taxRatePercent / 100), 2) : 0.0;
        $totalAmount = $taxableAmount + $gstAmount;

        $headerPath = !empty($settings['invoice_header'])
            ? public_path('storage/' . $settings['invoice_header'])
            : null;
        $footerPath = !empty($settings['invoice_footer'])
            ? public_path('storage/' . $settings['invoice_footer'])
            : null;
    @endphp

    @if($headerPath && file_exists($headerPath))
        <div style="margin-bottom: 12px;">
            <img src="{{ $headerPath }}" alt="Header" style="width: 100%; height: auto;">
        </div>
    @endif

    <h1>PROFORMA INVOICE</h1>

    <table class="info-table" style="margin-bottom: 12px;">
        <tr>
            <td style="width: 65%;">
                <div class="info-box">
                    <div class="section-title">Company</div>
                    <div>{{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}</div>
                    @if(!empty($settings['company_address']))
                        <div>{{ $settings['company_address'] }}</div>
                    @endif
                    @if(!empty($settings['company_email']))
                        <div>{{ $settings['company_email'] }}</div>
                    @endif
                    @if(!empty($settings['company_phone']))
                        <div>{{ $settings['company_phone'] }}</div>
                    @endif
                    @if($applyGst && !empty($settings['company_tax_id']))
                        <div>Tax/VAT: {{ $settings['company_tax_id'] }}</div>
                    @endif
                    @if($applyGst && !empty($settings['company_gstin']))
                        <div>GSTIN: {{ $settings['company_gstin'] }}</div>
                    @endif
                </div>

                <div class="info-box">
                    <div class="section-title">Billed to</div>
                    <div>{{ $customer->name ?? 'Customer' }}</div>
                    @if(!empty($customer?->company))
                        <div>{{ $customer->company }}</div>
                    @endif
                    @if(!empty($customer?->address))
                        <div>{{ $customer->address }}</div>
                    @endif
                    @if(!empty($customer?->email))
                        <div>{{ $customer->email }}</div>
                    @endif
                    @if(!empty($customer?->phone))
                        <div>{{ $customer->phone }}</div>
                    @endif
                    @if($applyGst && !empty($customer?->gstin))
                        <div>GSTIN: {{ $customer->gstin }}</div>
                    @endif
                </div>
            </td>
            <td style="width: 35%;">
                <div class="info-box">
                    <div class="section-title">Invoice details</div>
                    <div>Number: {{ $payment->invoice_number }}</div>
                    <div>Date: {{ optional($payment->payment_date)->format('d-m-Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Domain/hosting renewal
                    @if($domain?->domain_name)
                        — {{ $domain->domain_name }}
                    @endif
                </td>
                <td>{{ number_format($taxableAmount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="totals-row-label">Subtotal</td>
                <td class="totals-row-value">{{ number_format($taxableAmount, 2) }} {{ $currency }}</td>
            </tr>
            @if($applyGst)
                <tr>
                    <td class="totals-row-label">
                        GST @ {{ $taxRatePercent }}%
                    </td>
                    <td class="totals-row-value">{{ number_format($gstAmount, 2) }} {{ $currency }}</td>
                </tr>
            @endif
            <tr>
                <td class="totals-row-label">Total</td>
                <td class="totals-row-value">{{ number_format($totalAmount, 2) }} {{ $currency }}</td>
            </tr>
        </tfoot>
    </table>

    @if($footerPath && file_exists($footerPath))
        <div class="footer-fixed">
            <div class="footer-image">
                <img src="{{ $footerPath }}" alt="Footer">
            </div>
            <div class="footer-meta">
                <span>{{ $settings['company_name'] ?? config('app.name', 'Host Renewal') }}</span>
                <span>
                    @if(!empty($settings['company_email']))
                        {{ $settings['company_email'] }}
                    @endif
                    @if(!empty($settings['company_phone']))
                        {{ !empty($settings['company_email']) ? ' • ' : '' }}{{ $settings['company_phone'] }}
                    @endif
                </span>
            </div>
        </div>
    @endif
</body>
</html>

