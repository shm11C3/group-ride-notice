<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $order = 2;

        return [
            'user_uuid' => User::find($order++)->uuid,
            'user_profile_img_path' => '',
            'user_intro' => $this->faker->text(100),
            'user_url' => $this->faker->url(),
            'fb_username' => substr($this->faker->userName(), 0, 10),
            'tw_username' => substr($this->faker->userName(), 0, 10),
            'ig_username' => substr($this->faker->userName(), 0, 10),
        ];
    }
}
