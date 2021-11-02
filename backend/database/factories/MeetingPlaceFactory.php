<?php

namespace Database\Factories;

use App\Models\MeetingPlace;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

class MeetingPlaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MeetingPlace::class;

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
            'prefecture_code' => $this->faker->numberBetween(0, 47),
            'address' => $this->faker->streetAddress(),
            'publish_status' => $this->faker->numberBetween(0, 2),
        ];
    }
}
