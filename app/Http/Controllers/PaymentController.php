<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function all(Request $request)
    {
        $query = Payment::with(['domain.customer'])
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', 'invoice');
            });

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('method', 'like', "%{$search}%")
                    ->orWhereHas('domain', fn ($d) => $d->where('domain_name', 'like', "%{$search}%"))
                    ->orWhereHas('domain.customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }
        if ($customerId = $request->query('customer_id')) {
            $query->whereHas('domain', fn ($q) => $q->where('customer_id', $customerId));
        }
        if ($domainId = $request->query('domain_id')) {
            $query->where('domain_id', $domainId);
        }

        $payments = $query->latest('payment_date')->paginate(15)->appends($request->query());

        $customers = Customer::orderBy('name')->get();
        $domains = Domain::with('customer')->orderBy('domain_name')->get();

        return view('payments.all', [
            'payments' => $payments,
            'customers' => $customers,
            'domains' => $domains,
            'filters' => $request->only(['q', 'date_from', 'date_to', 'customer_id', 'domain_id']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Payment::with(['domain.customer'])
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', 'invoice');
            });

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('method', 'like', "%{$search}%")
                    ->orWhereHas('domain', fn ($d) => $d->where('domain_name', 'like', "%{$search}%"))
                    ->orWhereHas('domain.customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }
        if ($customerId = $request->query('customer_id')) {
            $query->whereHas('domain', fn ($q) => $q->where('customer_id', $customerId));
        }
        if ($domainId = $request->query('domain_id')) {
            $query->where('domain_id', $domainId);
        }

        $payments = $query->latest('payment_date')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . date('Y-m-d') . '.csv"',
        ];

        return new StreamedResponse(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Domain', 'Customer', 'Amount', 'Method', 'Reference']);

            foreach ($payments as $p) {
                fputcsv($handle, [
                    optional($p->payment_date)->format('Y-m-d'),
                    $p->domain?->domain_name ?? '—',
                    $p->domain?->customer?->name ?? '—',
                    $p->amount,
                    $p->method ?? '—',
                    $p->reference_no ?? '—',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'method' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
        ], [], [
            'domain_id' => 'domain',
        ]);

        $domain = Domain::findOrFail($data['domain_id']);
        $domain->payments()->create([
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'method' => $data['method'] ?? null,
            'reference_no' => $data['reference_no'] ?? null,
            'invoice_number' => Payment::generateNextInvoiceNumber(),
            'type' => 'invoice',
            'status' => 'paid',
        ]);

        return redirect()->route('payments.all')->with('status', 'Payment added successfully.');
    }

    public function reports()
    {
        $queryInvoices = Payment::where(function ($q) {
            $q->whereNull('type')->orWhere('type', 'invoice');
        });

        $totalRevenue = (clone $queryInvoices)->sum('amount');
        $thisMonth = (clone $queryInvoices)->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $thisYear = (clone $queryInvoices)->whereYear('payment_date', now()->year)->sum('amount');

        $paymentsThisYear = (clone $queryInvoices)->whereYear('payment_date', now()->year)->get();
        $byMonth = $paymentsThisYear->groupBy(fn ($p) => (int) $p->payment_date->format('n'));
        $monthlyValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyValues[] = (float) ($byMonth->get($m)?->sum('amount') ?? 0);
        }

        $recentPayments = Payment::with(['domain.customer'])->latest('payment_date')->take(10)->get();

        return view('payments.reports', [
            'totalRevenue' => $totalRevenue,
            'thisMonth' => $thisMonth,
            'thisYear' => $thisYear,
            'monthlyData' => $monthlyValues,
            'recentPayments' => $recentPayments,
        ]);
    }

    public function proformaIndex(Request $request)
    {
        $query = Payment::with(['domain.customer', 'domainRenewal'])
            ->where('type', 'proforma');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhereHas('domain', fn ($d) => $d->where('domain_name', 'like', "%{$search}%"))
                    ->orWhereHas('domain.customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }
        if ($customerId = $request->query('customer_id')) {
            $query->whereHas('domain', fn ($q) => $q->where('customer_id', $customerId));
        }

        // Order newest first by ID so listing matches creation order
        $payments = $query->latest('id')->paginate(15)->appends($request->query());
        $customers = Customer::orderBy('name')->get();
        $domains = Domain::with('customer')->orderBy('domain_name')->get();

        $settings = Setting::getMany([
            'company_tax_id',
            'company_gstin',
        ]);

        $companyHasTax = ! empty($settings['company_tax_id'] ?? '') || ! empty($settings['company_gstin'] ?? '');
        $taxRatePercent = 18;

        $payments->getCollection()->transform(function (Payment $payment) use ($companyHasTax, $taxRatePercent) {
            $customer = $payment->domain?->customer;
            $customerIsTaxable = ($customer?->tax_preference ?? 'taxable') === 'taxable';

            $invoiceTaxableFlag = $payment->is_taxable;
            $isTaxableInvoice = $invoiceTaxableFlag !== null ? (bool) $invoiceTaxableFlag : $customerIsTaxable;
            $applyGst = $companyHasTax && $isTaxableInvoice;

            $baseAmount = (float) $payment->amount;
            $taxAmount = $applyGst ? round($baseAmount * ($taxRatePercent / 100), 2) : 0.0;
            $totalAmount = $baseAmount + $taxAmount;

            $payment->setAttribute('tax_amount', $taxAmount);
            $payment->setAttribute('total_amount', $totalAmount);
            $payment->setAttribute('apply_gst', $applyGst);

            return $payment;
        });

        return view('proforma.index', [
            'payments' => $payments,
            'customers' => $customers,
            'domains' => $domains,
            'filters' => $request->only(['q', 'date_from', 'date_to', 'customer_id']),
            'settings' => $settings,
        ]);
    }

    public function createProforma()
    {
        $domains = Domain::with('customer')
            ->orderBy('domain_name')
            ->get();
        $settings = Setting::getMany([
            'company_tax_id',
            'company_gstin',
        ]);

        return view('proforma.partials.modal-create-global', [
            'domains' => $domains,
            'settings' => $settings,
        ]);
    }

    public function storeProforma(Request $request)
    {
        $data = $request->validate([
            'domain_id' => ['required', 'exists:domains,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:255'],
        ], [], [
            'domain_id' => 'domain',
        ]);

        $domain = Domain::with('customer')->findOrFail($data['domain_id']);

        $companyHasTax = ! empty(Setting::get('company_tax_id')) || ! empty(Setting::get('company_gstin'));
        $customerIsTaxable = ($domain->customer?->tax_preference ?? 'taxable') === 'taxable';
        $defaultTaxable = $companyHasTax && $customerIsTaxable;

        // Respect explicit radio choice; fall back to default when not present (e.g. old data)
        $isTaxableInput = $request->input('is_taxable');
        $isTaxable = $isTaxableInput !== null ? $request->boolean('is_taxable') : $defaultTaxable;

        $domain->payments()->create([
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'method' => $data['method'] ?? null,
            'reference_no' => $data['reference_no'] ?? null,
            'invoice_number' => Payment::generateNextInvoiceNumber(),
            'type' => 'proforma',
            'status' => 'draft',
            'is_taxable' => $isTaxable,
        ]);

        return redirect()
            ->route('proforma-invoices.index')
            ->with('status', 'Proforma invoice created successfully.');
    }

    public function proformaPanel(Payment $payment)
    {
        $payment->load(['domain.customer', 'domain.server', 'domainRenewal']);

        $settings = Setting::getMany([
            'company_name',
            'company_address',
            'company_email',
            'company_phone',
            'company_website',
            'company_tax_id',
            'company_gstin',
            'company_logo',
            'invoice_header',
            'invoice_footer',
        ]);

        return view('proforma.panel', [
            'payment' => $payment,
            'settings' => $settings,
        ]);
    }

    public function editProformaModal(Payment $payment)
    {
        if (($payment->type ?? null) !== 'proforma') {
            abort(404);
        }
        $payment->load('domain.customer');
        return view('proforma.partials.modal-edit', compact('payment'));
    }

    public function updateProforma(Request $request, Payment $payment)
    {
        if (($payment->type ?? null) !== 'proforma') {
            abort(404);
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:255'],
        ], [], []);

        $isTaxableInput = $request->input('is_taxable');
        $isTaxable = $isTaxableInput !== null ? $request->boolean('is_taxable') : $payment->is_taxable;

        $payment->update([
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'method' => $data['method'] ?? null,
            'reference_no' => $data['reference_no'] ?? null,
            'is_taxable' => $isTaxable,
        ]);

        return redirect()
            ->route('proforma-invoices.index')
            ->with('status', 'Proforma invoice updated successfully.');
    }

    public function indexModal(Domain $domain)
    {
        $payments = $domain->payments()
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', 'invoice');
            })
            ->latest()
            ->paginate(10);

        return view('payments.partials.modal-index', compact('domain', 'payments'));
    }

    public function index(Domain $domain)
    {
        $payments = $domain->payments()
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', 'invoice');
            })
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('domain', 'payments'));
    }

    public function createModal(Domain $domain)
    {
        return view('payments.partials.modal-create', compact('domain'));
    }

    public function create(Domain $domain)
    {
        return view('payments.create', compact('domain'));
    }

    public function createProformaModal(Domain $domain)
    {
        $settings = Setting::getMany([
            'company_tax_id',
            'company_gstin',
        ]);

        return view('proforma.partials.modal-create', compact('domain', 'settings'));
    }

    public function store(Request $request, Domain $domain)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'method' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
        ]);

        $domain->payments()->create(array_merge($data, [
            'invoice_number' => Payment::generateNextInvoiceNumber(),
            'type' => 'invoice',
            'status' => 'paid',
        ]));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Payment added successfully.',
                'details_url' => route('domains.details', $domain),
            ]);
        }

        return redirect()->route('payments.index', $domain)->with('status', 'Payment added successfully.');
    }

    public function destroy(Domain $domain, Payment $payment)
    {
        $payment->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'message' => 'Payment deleted.',
                'list_url' => route('payments.index.modal', $domain),
            ]);
        }

        return redirect()->route('payments.index', $domain)->with('status', 'Payment deleted.');
    }

    public function invoice(Request $request, Payment $payment)
    {
        $payment->load(['domain.customer', 'domain.server', 'domainRenewal']);

        $settings = Setting::getMany([
            'company_name',
            'company_address',
            'company_email',
            'company_phone',
            'company_website',
            'company_tax_id',
            'company_gstin',
            'company_logo',
            'invoice_header',
            'invoice_footer',
        ]);

        $viewData = [
            'payment' => $payment,
            'settings' => $settings,
            'type' => $payment->type ?? 'invoice',
        ];

        if ($request->boolean('download') && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $fileName = 'proforma-' . ($payment->invoice_number ?? 'invoice') . '.pdf';

            return Pdf::loadView('proforma.pdf', $viewData)
                ->setPaper('a4')
                ->download($fileName);
        }

        return view('payments.document', $viewData);
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['domain.customer']);

        $settings = Setting::getMany([
            'company_name',
            'company_address',
            'company_email',
            'company_phone',
            'company_website',
            'company_tax_id',
            'company_gstin',
            'company_logo',
            'invoice_header',
            'invoice_footer',
        ]);

        return view('payments.document', [
            'payment' => $payment,
            'settings' => $settings,
            'type' => 'receipt',
        ]);
    }
}

