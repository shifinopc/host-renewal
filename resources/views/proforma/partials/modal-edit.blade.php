<form method="POST" action="{{ route('proforma-invoices.update', $payment) }}" class="space-y-4 js-proforma-edit-form">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Domain</label>
        <p class="text-xs text-slate-600 py-1.5">{{ $payment->domain?->domain_name }} — {{ $payment->domain?->customer?->name }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
            <input name="amount" type="number" step="0.01" value="{{ old('amount', $payment->amount) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice date</label>
            <input name="payment_date" type="date" value="{{ old('payment_date', optional($payment->payment_date)->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div>
        <p class="block text-xs font-semibold text-slate-700 mb-1">Invoice type</p>
        <div class="flex items-center gap-4 text-xs">
            <label class="inline-flex items-center gap-1">
                <input type="radio" name="is_taxable" value="1" @checked($payment->is_taxable !== false)>
                <span>With tax</span>
            </label>
            <label class="inline-flex items-center gap-1">
                <input type="radio" name="is_taxable" value="0" @checked($payment->is_taxable === false)>
                <span>Without tax</span>
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Method</label>
            <input name="method" value="{{ old('method', $payment->method ?? 'Bank Transfer') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Reference No.</label>
            <input name="reference_no" value="{{ old('reference_no', $payment->reference_no) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-edit-proforma'))" class="text-xs text-slate-500">Cancel</button>
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
            Update proforma
        </button>
    </div>
</form>
