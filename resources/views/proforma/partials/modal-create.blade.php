<p class="text-xs text-slate-500 mb-3">Create a proforma invoice for <strong>{{ $domain->domain_name }}</strong></p>
<form method="POST" action="{{ route('proforma-invoices.store') }}" class="space-y-4 js-stacked-modal-form">
    @csrf
    <input type="hidden" name="domain_id" value="{{ $domain->id }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
            <input name="amount" type="number" step="0.01" value="{{ old('amount', $domain->price) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice date</label>
            <input name="payment_date" type="date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div>
        <p class="block text-xs font-semibold text-slate-700 mb-1">Invoice type</p>
        <div class="flex items-center gap-4 text-xs">
            <label class="inline-flex items-center gap-1">
                <input type="radio" name="is_taxable" value="1" checked>
                <span>With tax</span>
            </label>
            <label class="inline-flex items-center gap-1">
                <input type="radio" name="is_taxable" value="0">
                <span>Without tax</span>
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Method</label>
            <input name="method" value="{{ old('method', 'Bank Transfer') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Reference No.</label>
            <input name="reference_no" value="{{ old('reference_no') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" class="js-close-stacked-modal text-xs text-slate-500">Cancel</button>
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
            Save proforma
        </button>
    </div>
</form>

