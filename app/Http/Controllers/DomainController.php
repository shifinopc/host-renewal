<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\DomainRenewal;
use App\Models\Server;
use App\Models\Payment;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $query = Domain::with(['customer', 'server'])->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('domain_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->query('status')) {
            $today = now()->startOfDay();
            if ($status === 'active') {
                $query->where(function ($q) use ($today) {
                    $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>', $today->copy()->addDays(30));
                });
            } elseif ($status === 'expiring') {
                $query->whereBetween('expiry_date', [$today, $today->copy()->addDays(30)]);
            } elseif ($status === 'expired') {
                $query->where('expiry_date', '<', $today);
            }
        }

        if ($serverId = $request->query('server_id')) {
            $query->where('server_id', $serverId);
        }

        if ($customerId = $request->query('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        $domains = $query->paginate(10)->appends($request->query());

        $servers = Server::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('domains.index', [
            'domains' => $domains,
            'servers' => $servers,
            'customers' => $customers,
            'filters' => [
                'q' => $request->query('q'),
                'status' => $status ?? null,
                'server_id' => $serverId ?? null,
                'customer_id' => $customerId ?? null,
            ],
        ]);
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $servers = Server::orderBy('name')->get();

        return view('domains.create', compact('customers', 'servers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'server_id' => 'nullable|exists:servers,id',
            'domain_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
            ],
            'plan_name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ], [
            'domain_name.regex' => 'The domain name must be valid (e.g. example.com).',
        ]);

        $domain = Domain::create($data);

        if ($domain->start_date && $domain->expiry_date) {
            \App\Models\DomainRenewal::create([
                'domain_id' => $domain->id,
                'start_date' => $domain->start_date,
                'end_date' => $domain->expiry_date,
                'payment_id' => null,
            ]);
        }

        return redirect()->route('domains.index')->with('status', 'Domain created successfully.');
    }

    public function show(Domain $domain)
    {
        $domain->load(['customer', 'server', 'payments.domainRenewal', 'renewals.payment']);

        return view('domains.show', compact('domain'));
    }

    public function details(Domain $domain)
    {
        $domain->load(['customer', 'server', 'payments.domainRenewal', 'renewals.payment']);

        return view('domains.partials.detail-modal', compact('domain'));
    }

    public function renewModal(Domain $domain)
    {
        return view('domains.partials.renew-modal', compact('domain'));
    }

    public function renew(Request $request, Domain $domain)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'method' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment = null;
        if (! empty($data['amount']) && $data['amount'] > 0) {
            $payment = $domain->payments()->create([
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'] ?? $data['start_date'],
                'method' => $data['method'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'invoice_number' => Payment::generateNextInvoiceNumber(),
            ]);
        }

        DomainRenewal::create([
            'domain_id' => $domain->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'payment_id' => $payment?->id,
            'notes' => $data['notes'] ?? null,
        ]);

        $domain->update([
            'start_date' => $data['start_date'],
            'expiry_date' => $data['end_date'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Domain renewed successfully.',
                'details_url' => route('domains.details', $domain),
            ]);
        }

        return redirect()->route('domains.show', $domain)->with('status', 'Domain renewed successfully.');
    }

    public function edit(Domain $domain)
    {
        $customers = Customer::orderBy('name')->get();
        $servers = Server::orderBy('name')->get();

        return view('domains.edit', compact('domain', 'customers', 'servers'));
    }

    public function update(Request $request, Domain $domain)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'server_id' => 'nullable|exists:servers,id',
            'domain_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-\.]*[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
            ],
            'plan_name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ], [
            'domain_name.regex' => 'The domain name must be valid (e.g. example.com).',
        ]);

        $domain->update($data);

        return redirect()->route('domains.index')->with('status', 'Domain updated successfully.');
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('domains.index')->with('status', 'Domain deleted.');
    }
}

