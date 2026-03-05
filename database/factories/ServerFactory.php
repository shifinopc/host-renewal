<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Server>
 */
class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->domainWord().' Server',
            'provider' => $this->faker->company(),
            'ip_address' => $this->faker->ipv4(),
            'type' => $this->faker->randomElement(['Shared', 'VPS', 'Cloud']),
            'notes' => null,
        ];
    }
}

