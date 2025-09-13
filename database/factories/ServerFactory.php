<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition(): array
    {
        $providers = ['aws', 'digitalocean', 'vultr', 'other'];
        $statuses = ['active', 'inactive', 'maintenance'];

        return [
            'name' => $this->faker->unique()->domainWord . '-' . $this->faker->numberBetween(1, 10000),
            'ip_address' => $this->faker->unique()->ipv4,
            'provider' => $this->faker->randomElement($providers),
            'status' => $this->faker->randomElement($statuses),
            'cpu_cores' => $this->faker->numberBetween(1, 128),
            'ram_mb' => $this->faker->numberBetween(512, 1048576),
            'storage_gb' => $this->faker->numberBetween(10, 1048576),
        ];
    }
}
