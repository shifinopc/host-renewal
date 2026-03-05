<p class="text-xs text-slate-500 mb-3">Payment history for <strong>{{ $domain->domain_name }}</strong></p>
<div class="rounded-xl border border-slate-100 overflow-hidden">
    <table class="min-w-full text-xs">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="px-3 py-2 text-left font-semibold">Date</th>
                <th class="px-3 py-2 text-left font-semibold">Amount</th>
                <th class="px-3 py-2 text-left font-semibold">Method</th>
                <th class="px-3 py-2 text-left font-semibold">Reference</th>
                <th class="px-3 py-2 text-right font-semibold">Options</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($payments as $payment)
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2 text-slate-800">{{ optional($payment->payment_date)->format('d-m-Y') }}</td>
                    <td class="px-3 py-2 text-slate-800">{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-3 py-2 text-slate-500">{{ $payment->method ?? '—' }}</td>
                    <td class="px-3 py-2 text-slate-500">{{ $payment->reference_no ?? '—' }}</td>
                    <td class="px-3 py-2 text-right space-x-2">
                        <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="text-[11px] text-sky-500 font-semibold">
                            Proforma
                        </a>
                        <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-[11px] text-emerald-500 font-semibold">
                            Receipt
                        </a>
                        <form method="POST" action="{{ route('payments.destroy', [$domain, $payment]) }}" class="inline js-stacked-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11px] text-rose-500 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-slate-400">No payments recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($payments->hasPages())
        <div class="px-3 py-2 border-t border-slate-100">{{ $payments->links() }}</div>
    @endif
</div>
