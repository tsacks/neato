<?php

namespace App\Service;

class WeatherDecoder
{
    private Array $descriptions = [
        3 => 'Cloudy',
        45 => 'Foggy',
        55 => 'Heavy Drizzle',
        71 => 'Light Snow',
        81 => 'Showers',
        85 => 'Light Snow Showers',
    ];

    private Array $imageSlugs = [
        3 => 'cloudy-2-day',
        45 => 'fog-day',
        55 => 'rainy-3-day',
        71 => 'snowy-1-day',
        81 => 'rainy-3-day',
        85 => 'snowy-1-day',
    ];

    public function getDescription(int $code, bool $night = false)
    {
        return $this->descriptions[$code];
    }

    public function getImageSlug()
    {

    }
}