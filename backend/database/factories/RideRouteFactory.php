<?php

namespace Database\Factories;

use App\Models\RideRoute;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

class RideRouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RideRoute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $seeder = new DatabaseSeeder;

        return [
            'uuid' => $this->faker->uuid(),
            'user_uuid' => User::find($this->faker->numberBetween(1, $seeder->numberOfUsers))->uuid,
            'name' => $this->faker->word(),
            'elevation' => $this->faker->numberBetween(0, 1000),
            'distance' => $this->faker->numberBetween(1, 100),
            'lap_status' => $this->faker->numberBetween(0, 1),
            'comment' => $this->faker->sentence(),
            'publish_status' => $this->faker->numberBetween(0, 2),
        ];
    }
}
