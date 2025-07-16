<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $vehicleTypes = ['truck', 'container', 'van'];
        $vehicleStatuses = ['available', 'loading', 'in_transit', 'maintenance'];
        return [
            'vehicle_code' => $this->faker->unique()->bothify('VEH-####'),
            'vehicle_type' => $this->faker->randomElement($vehicleTypes),
            'license_plate' => $this->faker->unique()->bothify('##A-####'),
            'driver_name' => $this->faker->name(),
            'driver_phone' => $this->faker->phoneNumber(),
            'capacity_weight' => $this->faker->randomFloat(2, 1000, 20000),
            'capacity_volume' => $this->faker->randomFloat(2, 10, 100),
            'status' => $this->faker->randomElement($vehicleStatuses),
        ];
    }
} 