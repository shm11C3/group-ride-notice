<?php

namespace Database\Factories;

use App\Models\Ride;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Seeders\DatabaseSeeder;

class RideFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ride::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $order = 0;

        $seeder = new DatabaseSeeder;

        $a = $order++;
        $publish_status = $a%3;

        return [
            'uuid' => $this->faker->uuid(),
            'host_user_uuid' => User::find($this->faker->numberBetween(1, $seeder->numberOfUsers))->uuid,
            'meeting_places_uuid' => $seeder->meeting_place_uuid,
            'ride_routes_uuid' => $seeder->ride_route_uuid,
            'name' => $this->faker->word(),
            'time_appoint' => $this->faker->dateTimeBetween('-1 months', '+3 months')->format('Y-m-d H:i:s'),
            'intensity' => $this->faker->numberBetween(0, 10),
            'num_of_laps' => $this->faker->numberBetween(0, 30),
            'comment' => $this->faker->sentence(),
            'publish_status' => $publish_status,
        ];
    }
}
