<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Payment;
use App\Models\Server;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function expiring()
    {
        $domains = Domain::with(['customer', 'server'])
            ->expiringInDays(30)
            ->orderBy('expiry_date')
            ->paginate(20);

        return view('reports.expiring', compact('domains'));
    }

    public function expiringCsv()
    {
        $domains = Domain::with(['customer', 'server'])
            ->expiringInDays(30)
            ->orderBy('expiry_date')
            ->get();

        return response()->streamDownload(function () use ($domains) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Domain', 'Customer', 'Server', 'Expiry Date']);
            foreach ($domains as $domain) {
                fputcsv($handle, [
                    $domain->domain_name,
                    $domain->customer?->name,
                    $domain->server?->name,
                    optional($domain->expiry_date)->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        }, 'expiring-domains.csv');
    }

    public function revenue()
    {
        $year = (int) request('year', now()->year);

        $monthly = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.revenue', compact('monthly', 'year'));
    }

    public function revenueCsv()
    {
        $year = (int) request('year', now()->year);

        $monthly = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->streamDownload(function () use ($monthly) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Month', 'Total']);
            foreach ($monthly as $row) {
                fputcsv($handle, [$row->month, $row->total]);
            }
            fclose($handle);
        }, "revenue-{$year}.csv");
    }

    public function serverRevenue()
    {
        $rows = Server::withSum(['domains.payments as revenue' => function ($query) {
            $query->whereYear('payment_date', now()->year);
        }], 'amount')->get();

        return view('reports.server_revenue', compact('rows'));
    }

    public function serverRevenueCsv()
    {
        $rows = Server::withSum(['domains.payments as revenue' => function ($query) {
            $query->whereYear('payment_date', now()->year);
        }], 'amount')->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Server', 'Provider', 'Type', 'Revenue']);
            foreach ($rows as $server) {
                fputcsv($handle, [
                    $server->name,
                    $server->provider,
                    $server->type,
                    $server->revenue ?? 0,
                ]);
            }
            fclose($handle);
        }, 'server-revenue.csv');
    }
}

