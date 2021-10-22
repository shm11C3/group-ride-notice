<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Ride;
use App\Models\RideParticipant;

class DatabaseSeeder extends Seeder
{
    public $user_uuid = '241cec9d-7d70-64e2-827e-379efdff0a66';

    public $ride_uuid = 'c29754dc-46df-2630-2307-786c062d4b8b';

    public $meeting_place_uuid = '5e1d62fa-8abc-7250-1255-f342b5b87b0f';

    public $ride_route_uuid = '7ddc539b-7dca-3284-1b2b-513406f47115';

    public $ride_participant_uuid = 'c36727e4-bb22-0724-a847-b11dac7f0e19';

    public $numberOfUsers = 100;

    public $numberOfRides = 1000;

    public $numberOfRideParticipants = 3000;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminTablesSeeder::class,
            ConstantDatabaseValue::class,
        ]);


        User::factory($this->numberOfUsers)->create();
        UserProfile::factory($this->numberOfUsers)->create();
        Ride::factory($this->numberOfRides)->create();
        RideParticipant::factory($this->numberOfRideParticipants)->create();
    }
}
