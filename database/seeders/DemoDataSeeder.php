<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\Payment;
use App\Models\Server;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Server::exists() || Customer::exists() || Domain::exists()) {
            return;
        }

        $serverA = Server::create([
            'name' => 'Verpex Reseller 1',
            'provider' => 'Verpex',
            'ip_address' => '192.168.10.10',
            'type' => 'Shared',
        ]);

        $serverB = Server::create([
            'name' => 'Vultr VPS 1',
            'provider' => 'Vultr',
            'ip_address' => '10.0.0.5',
            'type' => 'VPS',
        ]);

        $customerA = Customer::create([
            'name' => 'ABC Company',
            'email' => 'abc@example.com',
            'phone' => '1234567890',
            'company' => 'ABC Pvt Ltd',
        ]);

        $customerB = Customer::create([
            'name' => 'XYZ Media',
            'email' => 'xyz@example.com',
            'phone' => '9876543210',
            'company' => 'XYZ Media LLP',
        ]);

        $today = Carbon::today();

        $domain1 = Domain::create([
            'customer_id' => $customerA->id,
            'server_id' => $serverA->id,
            'domain_name' => 'abccompany.com',
            'plan_name' => 'Reseller Basic',
            'price' => 3000,
            'start_date' => $today->copy()->subYear(),
            'expiry_date' => $today->copy()->addDays(20),
        ]);

        $domain2 = Domain::create([
            'customer_id' => $customerB->id,
            'server_id' => $serverB->id,
            'domain_name' => 'xyzmedia.net',
            'plan_name' => 'VPS Plus',
            'price' => 4500,
            'start_date' => $today->copy()->subMonths(8),
            'expiry_date' => $today->copy()->addDays(5),
        ]);

        Payment::create([
            'domain_id' => $domain1->id,
            'amount' => 3000,
            'payment_date' => $today->copy()->subMonths(11),
            'method' => 'Bank Transfer',
            'reference_no' => 'INV-1001',
        ]);

        Payment::create([
            'domain_id' => $domain2->id,
            'amount' => 4500,
            'payment_date' => $today->copy()->subMonths(7),
            'method' => 'UPI',
            'reference_no' => 'INV-1002',
        ]);
    }
}

