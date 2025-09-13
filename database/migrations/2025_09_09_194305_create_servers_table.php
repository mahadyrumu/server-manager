<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address')->unique();
            $table->enum('provider', ['aws', 'digitalocean', 'vultr', 'other']);
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('inactive');
            $table->integer('cpu_cores');
            $table->integer('ram_mb');
            $table->integer('storage_gb');
            $table->timestamps();

            $table->unique(['name', 'provider']);
            $table->index('ip_address');
            $table->index('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
