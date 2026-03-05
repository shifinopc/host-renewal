<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected function businessTypes(): array
    {
        return [
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
    }

    protected function indianStates(): array
    {
        return [
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
    }

    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        $businessTypes = $this->businessTypes();
        $indianStates = $this->indianStates();

        return view('customers.index', compact('customers', 'businessTypes', 'indianStates'));
    }

    public function create()
    {
        return view('customers.create', [
            'businessTypes' => $this->businessTypes(),
            'indianStates' => $this->indianStates(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tax_preference' => 'required|in:taxable,exempt',
            'business_type' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:30',
            'place_of_supply' => 'nullable|string|max:255',
            'tax_exempt_reason' => 'nullable|string',
        ]);

        if (($data['tax_preference'] ?? 'taxable') === 'taxable') {
            $data['tax_exempt_reason'] = null;
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('status', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load('domains');

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer,
            'businessTypes' => $this->businessTypes(),
            'indianStates' => $this->indianStates(),
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tax_preference' => 'required|in:taxable,exempt',
            'business_type' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:30',
            'place_of_supply' => 'nullable|string|max:255',
            'tax_exempt_reason' => 'nullable|string',
        ]);

        if (($data['tax_preference'] ?? 'taxable') === 'taxable') {
            $data['tax_exempt_reason'] = null;
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('status', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer deleted.');
    }
}

