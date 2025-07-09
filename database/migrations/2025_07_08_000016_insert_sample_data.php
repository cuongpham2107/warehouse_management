<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Note: Skip inserting users as the users table already exists
        // You can manually create users or use your existing user management system

        // Insert sample vendors
        DB::table('vendors')->insert([
            [
                'vendor_code' => 'VND001',
                'vendor_name' => 'ABC Trading Company',
                'contact_person' => 'John Doe',
                'phone' => '0987654321',
                'email' => 'john@abc-trading.com',
                'address' => '123 Main St, Ho Chi Minh City',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'vendor_code' => 'VND002',
                'vendor_name' => 'XYZ Import Export',
                'contact_person' => 'Jane Smith',
                'phone' => '0987654322',
                'email' => 'jane@xyz-import.com',
                'address' => '456 Business Ave, Hanoi',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert sample warehouse locations
        $locations = [];
        $zones = ['A', 'B', 'C'];
        $racks = ['01', '02', '03', '04', '05'];
        $levels = [1, 2, 3, 4];
        $positions = ['A', 'B', 'C', 'D'];

        foreach ($zones as $zone) {
            foreach ($racks as $rack) {
                foreach ($levels as $level) {
                    foreach ($positions as $position) {
                        $locations[] = [
                            'location_code' => $zone . $rack . sprintf('%02d', $level) . $position,
                            'zone' => $zone,
                            'rack' => $rack,
                            'level' => $level,
                            'position' => $position,
                            'max_weight' => 1000.00,
                            'max_volume' => 10.00,
                            'status' => 'available',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        }

        DB::table('warehouse_locations')->insert($locations);

        // Insert sample devices
        DB::table('devices')->insert([
            [
                'device_code' => 'PDA001',
                'device_type' => 'pda',
                'device_name' => 'Handheld Scanner 1',
                'mac_address' => '00:11:22:33:44:55',
                'ip_address' => '192.168.1.101',
                'status' => 'active',
                'assigned_to' => null, // Will be assigned later
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'device_code' => 'PDA002',
                'device_type' => 'pda',
                'device_name' => 'Handheld Scanner 2',
                'mac_address' => '00:11:22:33:44:56',
                'ip_address' => '192.168.1.102',
                'status' => 'active',
                'assigned_to' => null, // Will be assigned later
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'device_code' => 'FLT001',
                'device_type' => 'forklift_computer',
                'device_name' => 'Forklift Computer 1',
                'mac_address' => '00:11:22:33:44:57',
                'ip_address' => '192.168.1.201',
                'status' => 'active',
                'assigned_to' => null, // Will be assigned later
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'device_code' => 'FLT002',
                'device_type' => 'forklift_computer',
                'device_name' => 'Forklift Computer 2',
                'mac_address' => '00:11:22:33:44:58',
                'ip_address' => '192.168.1.202',
                'status' => 'active',
                'assigned_to' => null, // Will be assigned later
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert sample vehicles
        DB::table('vehicles')->insert([
            [
                'vehicle_code' => 'TRK001',
                'vehicle_type' => 'truck',
                'license_plate' => '29A-12345',
                'driver_name' => 'Nguyen Van A',
                'driver_phone' => '0912345678',
                'capacity_weight' => 5000.00,
                'capacity_volume' => 50.00,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'vehicle_code' => 'TRK002',
                'vehicle_type' => 'truck',
                'license_plate' => '29A-67890',
                'driver_name' => 'Tran Van B',
                'driver_phone' => '0912345679',
                'capacity_weight' => 7000.00,
                'capacity_volume' => 70.00,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'vehicle_code' => 'CNT001',
                'vehicle_type' => 'container',
                'license_plate' => '29C-11111',
                'driver_name' => 'Le Van C',
                'driver_phone' => '0912345680',
                'capacity_weight' => 20000.00,
                'capacity_volume' => 200.00,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert sample receiving plan
        DB::table('receiving_plans')->insert([
            [
                'plan_code' => 'RCP001',
                'vendor_id' => 1,
                'plan_date' => now()->addDays(1),
                'total_crates' => 0,
                'total_pieces' => 0,
                'total_weight' => 0,
                'status' => 'pending',
                'notes' => 'First receiving plan for ABC Trading',
                'created_by' => null, // Will be set by actual user
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'plan_code' => 'RCP002',
                'vendor_id' => 2,
                'plan_date' => now()->addDays(2),
                'total_crates' => 0,
                'total_pieces' => 0,
                'total_weight' => 0,
                'status' => 'pending',
                'notes' => 'Electronics shipment from XYZ Import Export',
                'created_by' => null, // Will be set by actual user
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert sample crates
        DB::table('crates')->insert([
            [
                'crate_id' => 'CRT001',
                'receiving_plan_id' => 1,
                'description' => 'Electronic components - resistors and capacitors',
                'pieces' => 1000,
                'gross_weight' => 25.50,
                'dimensions_length' => 60.00,
                'dimensions_width' => 40.00,
                'dimensions_height' => 30.00,
                'status' => 'planned',
                'barcode' => '1234567890123',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'crate_id' => 'CRT002',
                'receiving_plan_id' => 1,
                'description' => 'PCB boards for manufacturing',
                'pieces' => 500,
                'gross_weight' => 15.75,
                'dimensions_length' => 50.00,
                'dimensions_width' => 30.00,
                'dimensions_height' => 20.00,
                'status' => 'planned',
                'barcode' => '1234567890124',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'crate_id' => 'CRT003',
                'receiving_plan_id' => 2,
                'description' => 'Laptops and accessories',
                'pieces' => 50,
                'gross_weight' => 75.00,
                'dimensions_length' => 80.00,
                'dimensions_width' => 60.00,
                'dimensions_height' => 40.00,
                'status' => 'planned',
                'barcode' => '1234567890125',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert sample shipping request
        DB::table('shipping_requests')->insert([
            [
                'request_code' => 'SHR001',
                'customer_name' => 'Tech Solutions Co.',
                'customer_contact' => 'contact@techsolutions.com',
                'delivery_address' => '789 Technology Park, District 7, Ho Chi Minh City',
                'requested_date' => now()->addDays(5),
                'priority' => 'high',
                'status' => 'pending',
                'notes' => 'Urgent delivery for new product launch',
                'created_by' => null, // Will be set by actual user
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all sample data (except users table)
        DB::table('shipping_requests')->truncate();
        DB::table('crates')->truncate();
        DB::table('receiving_plans')->truncate();
        DB::table('vehicles')->truncate();
        DB::table('devices')->truncate();
        DB::table('warehouse_locations')->truncate();
        DB::table('vendors')->truncate();
        // DB::table('users')->truncate(); // Skip users table
    }
};
