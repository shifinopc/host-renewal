<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\Payment;
use App\Models\Server;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalServers = Server::count();
        $totalCustomers = Customer::count();
        $totalDomains = Domain::count();

        $totalActiveDomains = Domain::query()
            ->where(function ($query) {
                $today = Carbon::today();

                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', $today->addDays(31));
            })
            ->count();

        $expiringIn30Days = Domain::expiringInDays(30)->count();

        $expiringIn7Days = Domain::expiringInDays(7)->count();
        $expiredCount = Domain::expired()->count();

        $revenueThisYear = Payment::query()
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // Domains per month for the current year (created_at)
        $monthlyDomains = Domain::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthlyDomains as $m => $total) {
            $monthlyData[$m] = (int) $total;
        }

        $upcomingExpiries = Domain::with('customer')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', Carbon::today())
            ->orderBy('expiry_date')
            ->paginate(2, ['*'], 'renewals_page');

        $recentPayments = Payment::with(['domain.customer'])
            ->latest('payment_date')
            ->paginate(2, ['*'], 'payments_page');

        $customersForModal = Customer::orderBy('name')->get();
        $serversForModal = Server::orderBy('name')->get();

        return view('dashboard.index', [
            'totalServers' => $totalServers,
            'totalCustomers' => $totalCustomers,
            'totalDomains' => $totalDomains,
            'totalActiveDomains' => $totalActiveDomains,
            'expiringIn7Days' => $expiringIn7Days,
            'expiringIn30Days' => $expiringIn30Days,
            'expiredCount' => $expiredCount,
            'revenueThisYear' => $revenueThisYear,
            'monthlyData' => array_values($monthlyData),
            'upcomingExpiries' => $upcomingExpiries,
            'recentPayments' => $recentPayments,
            'customersForModal' => $customersForModal,
            'serversForModal' => $serversForModal,
        ]);
    }
}

