<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProductSeeder::class);
        $this->call(DiscountSeeder::class);

        \App\Models\User::factory()->create([
            'name' => 'Oğuzcan Özdemir',
            'username' => 'oguzcan',
            'password' => Hash::make('oguzcan'),
            'revenue' => 0
        ]);
    }
}
