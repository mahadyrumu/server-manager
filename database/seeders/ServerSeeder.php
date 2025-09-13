<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Server;

class ServerSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 5000 servers
        $providers = ['aws', 'digitalocean', 'vultr', 'other'];

        for ($i = 1; $i <= 5000; $i++) {
            Server::create([
                'name' => "Server-$i",
                'ip_address' => "192.168." . floor($i / 255) . "." . ($i % 255),
                'provider' => $providers[array_rand($providers)],
                'status' => ['active', 'inactive', 'maintenance'][array_rand(['active', 'inactive', 'maintenance'])],
                'cpu_cores' => rand(1, 128),
                'ram_mb' => rand(512, 1048576),
                'storage_gb' => rand(10, 1048576)
            ]);
        }
    }
}
