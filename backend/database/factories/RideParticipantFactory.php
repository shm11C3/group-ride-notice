<?php

namespace Database\Factories;

use App\Models\RideParticipant;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Seeders\DatabaseSeeder;

class RideParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RideParticipant::class;

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
            'ride_uuid' => Ride::find($this->faker->numberBetween(1, $seeder->numberOfRides))->uuid,
            'comment' => $this->faker->sentence(),
        ];
    }
}
