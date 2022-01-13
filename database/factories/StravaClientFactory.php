<?php

namespace Database\Factories;

use App\Integrations\Strava\Models\StravaClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StravaClientFactory extends Factory
{
    protected $model = StravaClient::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => fn() => User::factory(),
            'client_id' => $this->faker->numberBetween(1000, 9999999),
            'client_secret' => Str::random(40),
            'enabled' => true,
            'public' => false,
            'webhook_verify_token' => Str::random(20),
            'invitation_link_uuid' => null,
            'used_15_min_calls' => 0,
            'used_daily_calls' => 0
        ];
    }
}
