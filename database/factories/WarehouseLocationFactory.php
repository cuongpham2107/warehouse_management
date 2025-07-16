<?php

namespace Database\Factories;

use App\Models\WarehouseLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseLocationFactory extends Factory
{
    protected $model = WarehouseLocation::class;

    public function definition(): array
    {
        $statuses = ['available', 'occupied', 'maintenance'];
        return [
            'location_code' => $this->faker->unique()->bothify('LOC-###'),
            'zone' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'rack' => $this->faker->randomElement(['R1', 'R2', 'R3', 'R4']),
            'level' => $this->faker->numberBetween(1, 5),
            'position' => $this->faker->numberBetween(1, 20),
            'max_weight' => $this->faker->randomFloat(2, 500, 5000),
            'max_volume' => $this->faker->randomFloat(2, 5, 100),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
} 