<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_domain(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $server = Server::factory()->create();

        $response = $this->actingAs($user)->post('/domains', [
            'customer_id' => $customer->id,
            'server_id' => $server->id,
            'domain_name' => 'example.com',
            'plan_name' => 'Test Plan',
            'price' => 1000,
        ]);

        $response->assertRedirect('/domains');
        $this->assertDatabaseHas('domains', [
            'domain_name' => 'example.com',
            'customer_id' => $customer->id,
        ]);
    }
}

