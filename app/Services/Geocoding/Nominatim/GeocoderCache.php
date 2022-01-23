<?php

namespace App\Services\Geocoding\Nominatim;

use App\Services\Geocoding\Geocoder;
use Illuminate\Contracts\Cache\Repository;

class GeocoderCache implements Geocoder
{

    private Geocoder $geocoder;
    private Repository $cache;

    public function __construct(Geocoder $geocoder, Repository $cache)
    {
        $this->geocoder = $geocoder;
        $this->cache = $cache;
    }

    public function getPlaceSummaryFromPosition(float $latitude, float $longitude): ?string
    {
        return $this->cache->rememberForever(
            sprintf('getPlaceSummaryFromPosition@%s:%s', $latitude, $longitude),
            fn() => $this->geocoder->getPlaceSummaryFromPosition($latitude, $longitude)
        );
    }

}