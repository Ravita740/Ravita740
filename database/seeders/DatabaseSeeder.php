<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'sonika',
            'email' => 'sonika@example.com',
        ]);

        Category::factory()->count(2)->sequence(
            ['name' => 'mobile'],
            ['name' => 'laptop']
        )->create();

        $this->call(ProductSeeder::class);
        // $this->call(CategorySeeder::class);
        
        // Invoice Management System Seeders
        $this->call(CustomerSeeder::class);
        $this->call(InvoiceSeeder::class);
    }
}
