@php($pageTitle = 'Payments')
@php($pageSubtitle = 'Payment history for ' . $domain->domain_name)

@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs text-slate-400">Domain</p>
            <p class="text-sm font-semibold text-slate-900">{{ $domain->domain_name }}</p>
        </div>
        <a href="{{ route('payments.create', $domain) }}" class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600">
            + Add Payment
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Date</th>
                    <th class="px-4 py-3 text-left font-semibold">Amount</th>
                    <th class="px-4 py-3 text-left font-semibold">Method</th>
                    <th class="px-4 py-3 text-left font-semibold">Reference</th>
                    <th class="px-4 py-3 text-right font-semibold">Options</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">
                            {{ optional($payment->payment_date)->format('d-m-Y') }}
                        </td>
                        <td class="px-4 py-3 text-slate-800">
                            {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->method ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->reference_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('payments.invoice', $payment) }}" class="text-[11px] text-sky-500 font-semibold">
                                Proforma
                            </a>
                            <a href="{{ route('payments.receipt', $payment) }}" class="text-[11px] text-emerald-500 font-semibold">
                                Receipt
                            </a>
                            <form method="POST" action="{{ route('payments.destroy', [$domain, $payment]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Delete this payment?');"
                                    class="text-[11px] text-rose-500 font-semibold"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                            No payments recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
    </div>
@endsection

