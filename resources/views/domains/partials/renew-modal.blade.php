<p class="text-xs text-slate-500 mb-4">Set the new start and end dates for the renewal period. Optionally add the payment received.</p>
<form method="POST" action="{{ route('domains.renew', $domain) }}" class="space-y-4 js-stacked-modal-form" data-refresh-details="true">
    @csrf
    <input type="hidden" name="_renew" value="1">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">New start date</label>
            <input name="start_date" type="date" value="{{ old('start_date', $domain->expiry_date ? $domain->expiry_date->copy()->addDay()->format('Y-m-d') : now()->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">New end date</label>
            <input name="end_date" type="date" value="{{ old('end_date') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
    </div>
    <div class="pt-2 border-t border-slate-100">
        <p class="text-[11px] text-slate-500 mb-2">Optional: record payment for this renewal</p>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Amount</label>
                <input name="amount" type="number" step="0.01" value="{{ old('amount', $domain->price) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Payment date</label>
                <input name="payment_date" type="date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Method</label>
                <input name="method" value="{{ old('method', 'Bank Transfer') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Reference</label>
                <input name="reference_no" value="{{ old('reference_no') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('notes') }}</textarea>
    </div>
    <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" class="js-close-stacked-modal text-xs text-slate-500">Cancel</button>
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-full bg-amber-500 text-white text-xs font-semibold hover:bg-amber-600">
            Renew domain
        </button>
    </div>
</form>
