<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private const CODES = [
        0  => ['label' => 'Clear sky',        'icon' => 'fa-sun'],
        1  => ['label' => 'Mainly clear',     'icon' => 'fa-sun'],
        2  => ['label' => 'Partly cloudy',    'icon' => 'fa-cloud-sun'],
        3  => ['label' => 'Overcast',         'icon' => 'fa-cloud'],
        45 => ['label' => 'Foggy',            'icon' => 'fa-smog'],
        48 => ['label' => 'Rime fog',         'icon' => 'fa-smog'],
        51 => ['label' => 'Light drizzle',    'icon' => 'fa-cloud-rain'],
        53 => ['label' => 'Drizzle',          'icon' => 'fa-cloud-rain'],
        55 => ['label' => 'Dense drizzle',    'icon' => 'fa-cloud-rain'],
        61 => ['label' => 'Slight rain',      'icon' => 'fa-cloud-showers-heavy'],
        63 => ['label' => 'Rain',             'icon' => 'fa-cloud-showers-heavy'],
        65 => ['label' => 'Heavy rain',       'icon' => 'fa-cloud-showers-heavy'],
        71 => ['label' => 'Slight snow',      'icon' => 'fa-snowflake'],
        73 => ['label' => 'Snow',             'icon' => 'fa-snowflake'],
        75 => ['label' => 'Heavy snow',       'icon' => 'fa-snowflake'],
        80 => ['label' => 'Rain showers',     'icon' => 'fa-cloud-showers-heavy'],
        81 => ['label' => 'Rain showers',     'icon' => 'fa-cloud-showers-heavy'],
        82 => ['label' => 'Violent showers',  'icon' => 'fa-cloud-showers-heavy'],
        85 => ['label' => 'Snow showers',     'icon' => 'fa-snowflake'],
        86 => ['label' => 'Snow showers',     'icon' => 'fa-snowflake'],
        95 => ['label' => 'Thunderstorm',     'icon' => 'fa-bolt'],
        96 => ['label' => 'Thunderstorm/hail','icon' => 'fa-bolt'],
        99 => ['label' => 'Thunderstorm/hail','icon' => 'fa-bolt'],
    ];

    public static function current(?float $lat, ?float $lng): ?array
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        $key = 'weather:' . round($lat, 3) . ',' . round($lng, 3);

        return Cache::remember($key, now()->addMinutes(30), function () use ($lat, $lng) {
            try {
                $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'current'   => 'temperature_2m,weather_code,wind_speed_10m,relative_humidity_2m',
                    'timezone'  => 'auto',
                ]);

                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json('current');

                if (!$data) {
                    return null;
                }

                $code = $data['weather_code'] ?? 0;
                $info = self::CODES[$code] ?? ['label' => 'Unknown', 'icon' => 'fa-question'];

                return [
                    'temperature' => round($data['temperature_2m'] ?? 0),
                    'humidity'    => $data['relative_humidity_2m'] ?? null,
                    'wind_speed'  => round($data['wind_speed_10m'] ?? 0),
                    'label'       => $info['label'],
                    'icon'        => $info['icon'],
                ];
            } catch (\Throwable $e) {
                Log::warning('Weather fetch failed', [
                    'lat' => $lat, 'lng' => $lng, 'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * @param array<string, array{lat: float|null, lng: float|null}> $locations
     * @return array<string, array|null>
     */
    public static function forLocations(array $locations): array
    {
        $results = [];

        foreach ($locations as $key => $loc) {
            $results[$key] = self::current($loc['lat'] ?? null, $loc['lng'] ?? null);
        }

        return $results;
    }
}