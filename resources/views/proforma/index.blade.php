@php($pageTitle = 'Proforma invoices')
@php($pageSubtitle = 'View and print proforma invoices for customers')

@extends('layouts.app')

@section('content')
    <div x-data="proformaSidebar()" x-on:keydown.escape.window="if(editOpen) closeEdit(); else close();">
        <div x-data="{ openCreate: false }" @close-proforma-create.window="openCreate = false">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs text-slate-400">Proforma invoices</p>
                    <p class="text-xl font-semibold text-slate-900">{{ $payments->total() }}</p>
                </div>
                <div>
                    <button
                        type="button"
                        @click="openCreate = true"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700"
                    >
                        <span class="material-symbols-rounded text-[16px]">add</span>
                        New proforma
                    </button>
                </div>
            </div>

            <!-- Global create proforma modal -->
            <div
                x-show="openCreate"
                x-cloak
                class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4"
                @click.self="openCreate = false"
            >
                <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-100 max-h-[90vh] flex flex-col" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-slate-100 shrink-0">
                        <h2 class="text-sm font-semibold text-slate-900">Create proforma invoice</h2>
                        <button type="button" @click="openCreate = false" class="h-7 w-7 rounded-full border border-slate-200 text-slate-400 hover:bg-slate-50">✕</button>
                    </div>
                    <div class="p-4 overflow-y-auto flex-1">
                        @include('proforma.partials.modal-create-global')
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit proforma modal --}}
        <div
            x-show="editOpen"
            x-cloak
            x-on:close-edit-proforma.window="closeEdit()"
            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4"
            @click.self="closeEdit()"
        >
            <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-100 max-h-[90vh] flex flex-col" @click.stop>
                <div class="flex items-center justify-between p-4 border-b border-slate-100 shrink-0">
                    <h2 class="text-sm font-semibold text-slate-900">Edit proforma invoice</h2>
                    <button type="button" @click="closeEdit()" class="h-7 w-7 rounded-full border border-slate-200 text-slate-400 hover:bg-slate-50">✕</button>
                </div>
                <div class="p-4 overflow-y-auto flex-1">
                    <template x-if="editLoading">
                        <div class="flex items-center justify-center py-12">
                            <div class="animate-spin w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full"></div>
                        </div>
                    </template>
                    <div x-show="!editLoading" x-html="editContent"></div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('proforma-invoices.index') }}" class="mb-4 p-4 bg-white rounded-2xl border border-slate-100">
            <div class="flex flex-wrap items-end gap-3">
                <div class="min-w-[180px]">
                    <label class="block text-[10px] text-slate-500 mb-1">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Proforma no., domain, customer..."
                        class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">From</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">To</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-[10px] text-slate-500 mb-1">Customer</label>
                    <select name="customer_id" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs">
                        <option value="">All customers</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" @selected(($filters['customer_id'] ?? null) == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">Filter</button>
            </div>
        </form>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Proforma no.</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Domain</th>
                        <th class="px-4 py-3 text-left font-semibold">Amount</th>
                        <th class="px-4 py-3 text-left font-semibold">Tax amount</th>
                        <th class="px-4 py-3 text-left font-semibold">Total</th>
                        <th class="px-4 py-3 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $payment->invoice_number }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ optional($payment->payment_date)->format('d-m-Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $payment->domain?->customer?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $payment->domain?->domain_name ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ number_format((float) ($payment->tax_amount ?? 0), 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ number_format((float) ($payment->total_amount ?? $payment->amount), 2) }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        @click="openEdit({{ $payment->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 text-[11px]"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="open({{ $payment->id }}, '{{ route('proforma-invoices.panel', $payment) }}', '{{ $payment->invoice_number }}', '{{ optional($payment->payment_date)->format('d-m-Y') }}', '{{ addslashes($payment->domain?->customer?->name ?? '') }}', '{{ addslashes($payment->domain?->domain_name ?? '') }}', '{{ number_format($payment->amount, 2) }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 font-semibold hover:bg-indigo-100"
                                    >
                                        <span class="material-symbols-rounded text-[14px]">visibility</span>
                                        View proforma
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                No proforma invoices yet. Add a payment to generate one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-4 py-3 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        </div>

        {{-- Sidebar overlay --}}
        <div
            x-show="isOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/40 z-40"
            @click="close()"
        ></div>

        {{-- Sidebar panel --}}
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 h-full w-full max-w-xl bg-white shadow-2xl z-50 flex flex-col"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Proforma invoice</h2>
                <div class="flex items-center gap-2">
                    <a
                        :href="fullViewUrl + '?download=1'"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200"
                    >
                        <span class="material-symbols-rounded text-[16px]">download</span>
                        Download PDF
                    </a>
                    <button
                        type="button"
                        @click="close()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-slate-100 text-slate-500"
                    >
                        <span class="material-symbols-rounded text-[20px]">close</span>
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div id="proformaPanelContent" class="flex-1 overflow-y-auto">
                <template x-if="loading">
                    <div class="flex items-center justify-center h-64">
                        <div class="animate-spin w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full"></div>
                    </div>
                </template>
                <div x-show="!loading" x-html="content"></div>
            </div>
        </div>
    </div>

    <script>
        function proformaSidebar() {
            return {
                isOpen: false,
                loading: false,
                content: '',
                currentId: null,
                editOpen: false,
                editContent: '',
                editLoading: false,
                fullViewUrl: '',
                currentInvoiceNo: '',
                companyName: '',
                companyAddress: '',
                companyEmail: '',
                companyPhone: '',
                customerName: '',
                customerCompany: '',
                customerAddress: '',
                customerEmail: '',
                customerPhone: '',
                domainName: '',
                serverName: '',
                period: '',
                amount: '',
                invoiceDate: '',

                open(id, url, invoiceNo, date, customer, domain, amount) {
                    this.isOpen = true;
                    this.loading = true;
                    this.currentId = id;
                    this.fullViewUrl = '{{ url('payments') }}/' + id + '/invoice';
                    this.content = '';
                    this.currentInvoiceNo = invoiceNo;
                    this.invoiceDate = date;
                    this.amount = amount;

                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            this.content = html;
                            this.loading = false;
                            this.$nextTick(() => {
                                this.extractData();
                            });
                        })
                        .catch(() => {
                            this.content = '<div class="p-6 text-center text-red-500">Failed to load proforma</div>';
                            this.loading = false;
                        });
                },

                extractData() {
                    const container = document.querySelector('#proformaPanelContent > div:last-child');
                    if (!container) return;

                    const getText = (selector) => {
                        const el = container.querySelector(selector);
                        return el ? el.textContent.trim() : '';
                    };

                    const boxes = container.querySelectorAll('.bg-slate-50.rounded-xl');
                    
                    if (boxes[0]) {
                        const billedTexts = boxes[0].querySelectorAll('p');
                        this.customerName = billedTexts[1]?.textContent.trim() || '';
                        this.customerCompany = billedTexts[2]?.textContent.trim() || '';
                        const remaining = Array.from(billedTexts).slice(3).map(p => p.textContent.trim());
                        remaining.forEach(t => {
                            if (t.includes('@')) this.customerEmail = t;
                            else if (/^\d/.test(t) || t.includes('+')) this.customerPhone = t;
                            else if (t && !this.customerAddress) this.customerAddress = t;
                        });
                    }

                    if (boxes[2]) {
                        const serviceTexts = boxes[2].querySelectorAll('p');
                        this.domainName = serviceTexts[1]?.textContent.trim() || '';
                        serviceTexts.forEach(p => {
                            const t = p.textContent.trim();
                            if (t.startsWith('Server:')) this.serverName = t.replace('Server:', '').trim();
                            if (t.startsWith('Period:')) this.period = t.replace('Period:', '').trim();
                        });
                    }

                    const companyDiv = container.querySelector('.flex.items-center.gap-3 > div');
                    if (companyDiv) {
                        const ps = companyDiv.querySelectorAll('p');
                        this.companyName = ps[0]?.textContent.trim() || '';
                        this.companyAddress = ps[1]?.textContent.trim() || '';
                    }

                    const footerDiv = container.querySelector('.border-t.border-slate-100');
                    if (footerDiv) {
                        const spans = footerDiv.querySelectorAll('span');
                        spans.forEach(s => {
                            const t = s.textContent.trim().replace('•', '').trim();
                            if (t.includes('@')) this.companyEmail = t;
                            else if (t && !this.companyPhone) this.companyPhone = t;
                        });
                    }
                },

                close() {
                    this.isOpen = false;
                },

                openEdit(id) {
                    this.editOpen = true;
                    this.editLoading = true;
                    this.editContent = '';
                    const url = '{{ url('proforma-invoices') }}/' + id + '/edit';
                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            this.editContent = html;
                            this.editLoading = false;
                        })
                        .catch(() => {
                            this.editContent = '<div class="p-4 text-center text-red-500 text-xs">Failed to load form.</div>';
                            this.editLoading = false;
                        });
                },
                closeEdit() {
                    this.editOpen = false;
                },

                printPanel() {
                    const content = document.querySelector('#proformaPanelContent > div:last-child');
                    if (!content) return;

                    const logo = content.querySelector('.logo')?.src || '';
                    const headerImg = content.querySelector('.header-img')?.src || '';
                    const footerImg = content.querySelector('.footer-img')?.src || '';

                    const printWindow = window.open('', '_blank', 'width=800,height=900');
                    printWindow.document.write(`
<!DOCTYPE html>
<html>
<head>
    <title>Proforma Invoice - ${this.currentInvoiceNo}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, sans-serif; 
            background: #fff;
            color: #1a1a2e;
            font-size: 13px;
            line-height: 1.6;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        /* Top accent bar */
        .accent-bar {
            height: 8px;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        }
        
        .content {
            flex: 1;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 50px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .brand img {
            height: 55px;
            width: auto;
        }
        .brand-info h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .brand-info p {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .invoice-info {
            text-align: right;
        }
        .invoice-label {
            font-size: 32px;
            font-weight: 700;
            color: #6366f1;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }
        .invoice-details {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 16px 24px;
            text-align: left;
            min-width: 200px;
        }
        .invoice-details .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12px;
        }
        .invoice-details .row:last-child { margin-bottom: 0; }
        .invoice-details .label { color: #6b7280; }
        .invoice-details .value { font-weight: 600; color: #1a1a2e; }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #059669;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 12px;
        }
        .status::before {
            content: "";
            width: 8px;
            height: 8px;
            background: #059669;
            border-radius: 50%;
        }

        /* Banner images */
        .banner { margin: 0 0 40px 0; }
        .banner img { width: 100%; max-height: 160px; object-fit: contain; border-radius: 8px; }

        /* Parties section */
        .parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .party-card {
            background: linear-gradient(135deg, #f8f9fc 0%, #f3f4f8 100%);
            border-radius: 16px;
            padding: 28px;
            border: 1px solid #e5e7eb;
        }
        .party-card .title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6366f1;
            margin-bottom: 16px;
        }
        .party-card .name {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .party-card .detail {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        /* Table */
        .table-wrapper {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
        }
        thead th {
            padding: 18px 24px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
        }
        thead th:last-child { text-align: right; }
        tbody td {
            padding: 24px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        tbody td:last-child { text-align: right; }
        .item-name {
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .item-desc {
            font-size: 12px;
            color: #6b7280;
        }
        .item-amount {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
        }

        /* Totals */
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .totals-box {
            width: 280px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .totals-row .label { color: #6b7280; }
        .totals-row .value { font-weight: 600; }
        .totals-row.grand {
            border-bottom: none;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            margin: 12px -16px -12px -16px;
            padding: 18px 16px;
            border-radius: 12px;
            color: #fff;
        }
        .totals-row.grand .label { 
            color: rgba(255,255,255,0.85); 
            font-weight: 500;
            font-size: 13px;
        }
        .totals-row.grand .value { 
            font-size: 22px; 
            font-weight: 700;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            padding-top: 30px;
            border-top: 2px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-brand {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 13px;
        }
        .footer-contact {
            color: #6b7280;
            font-size: 12px;
        }
        .footer-accent {
            height: 4px;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            margin-top: 30px;
        }

        @media print {
            .page { width: 100%; min-height: 100vh; }
            .content { padding: 30px 40px; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="content">
            ${headerImg ? '<div class="banner"><img src="' + headerImg + '" /></div>' : ''}

            <!-- Header -->
            <div class="header">
                ${!headerImg ? (
                    '<div class="brand">' +
                        (logo ? '<img src=\"' + logo + '\" alt=\"Logo\" />' : '') +
                        '<div class=\"brand-info\">' +
                            '<h2>' + this.companyName + '</h2>' +
                            '<p>' + this.companyAddress.replace(/\n/g, '<br>') + '</p>' +
                        '</div>' +
                    '</div>'
                ) : ''}
                <div class="invoice-info">
                    <div class="invoice-label">PROFORMA</div>
                    <div class="invoice-details">
                        <div class="row">
                            <span class="label">Invoice No</span>
                            <span class="value">${this.currentInvoiceNo}</span>
                        </div>
                        <div class="row">
                            <span class="label">Date</span>
                            <span class="value">${this.invoiceDate}</span>
                        </div>
                    </div>
                    <div class="status">Paid</div>
                </div>
            </div>

            <!-- Parties -->
            <div class="parties">
                <div class="party-card">
                    <div class="title">Billed To</div>
                    <div class="name">${this.customerName}</div>
                    ${this.customerCompany ? '<div class="detail">' + this.customerCompany + '</div>' : ''}
                    ${this.customerAddress ? '<div class="detail">' + this.customerAddress.replace(/\n/g, '<br>') + '</div>' : ''}
                    ${this.customerEmail ? '<div class="detail">' + this.customerEmail + '</div>' : ''}
                    ${this.customerPhone ? '<div class="detail">' + this.customerPhone + '</div>' : ''}
                </div>
                <div class="party-card">
                    <div class="title">Service</div>
                    <div class="name">${this.domainName}</div>
                    ${this.serverName ? '<div class="detail">Server: ' + this.serverName + '</div>' : ''}
                    ${this.period ? '<div class="detail">Period: ' + this.period + '</div>' : ''}
                </div>
            </div>

            <!-- Items -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="item-name">Domain/Hosting Renewal</div>
                                <div class="item-desc">${this.domainName}${this.period ? ' • ' + this.period : ''}</div>
                            </td>
                            <td><span class="item-amount">${this.amount}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals">
                <div class="totals-box">
                    <div class="totals-row">
                        <span class="label">Subtotal</span>
                        <span class="value">${this.amount}</span>
                    </div>
                    <div class="totals-row grand">
                        <span class="label">Total Amount</span>
                        <span class="value">${this.amount}</span>
                    </div>
                </div>
            </div>

            ${footerImg ? '<div class="banner"><img src="' + footerImg + '" /></div>' : ''}
        </div>
    </div>
</body>
</html>
                    `);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => {
                        printWindow.print();
                    }, 300);
                }
            };
        }
    </script>
@endsection
