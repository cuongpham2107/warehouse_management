<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vehicle;
use App\Models\WarehouseLocation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // // Thêm dữ liệu mẫu cho Vendor và Vehicle
        Vendor::factory(5)->create();
        Vehicle::factory(5)->create();
        WarehouseLocation::factory(5)->create();
    }
}
