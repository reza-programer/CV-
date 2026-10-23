<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Local development accounts. Both use the factory password "password".
     * CV templates are inserted by their migration, not here.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@cvforge.test',
        ]);

        User::factory()->create([
            'name' => 'Pengguna Uji',
            'email' => 'user@cvforge.test',
        ]);
    }
}
