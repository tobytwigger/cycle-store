<?php

namespace Tests\Utils;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

trait ConnectsToStrava
{

    public function connectToStrava(User $user)
    {
        $client = StravaClient::factory()->create(['user_id' => $user->id]);
        $token = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client->id]);

        return $client;
    }

}
