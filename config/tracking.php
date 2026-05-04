<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GPS Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Configure GPS tracking behavior and limits
    |
    */

    // Location History Settings
    'history' => [
        'retention_days' => env('TRACKING_HISTORY_RETENTION', 30),
        'max_points_per_user' => env('TRACKING_MAX_POINTS', 100000),
        'cleanup_enabled' => env('TRACKING_CLEANUP_ENABLED', true),
    ],

    // Live Tracking Settings
    'live' => [
        'update_interval' => env('TRACKING_UPDATE_INTERVAL', 5), // seconds
        'high_accuracy' => env('TRACKING_HIGH_ACCURACY', true),
        'timeout' => env('TRACKING_TIMEOUT', 5000), // milliseconds
        'maximum_age' => env('TRACKING_MAXIMUM_AGE', 0), // milliseconds
    ],

    // Geofence Settings
    'geofence' => [
        'min_radius' => env('GEOFENCE_MIN_RADIUS', 10), // meters
        'max_radius' => env('GEOFENCE_MAX_RADIUS', 10000), // meters
        'max_per_user' => env('GEOFENCE_MAX_PER_USER', 50),
        'check_interval' => env('GEOFENCE_CHECK_INTERVAL', 10), // seconds
    ],

    // Location Marker Settings
    'markers' => [
        'max_per_user' => env('MARKERS_MAX_PER_USER', 500),
        'types' => [
            'marker' => ['icon' => 'map-marker-alt', 'color' => '#3388ff'],
            'checkpoint' => ['icon' => 'flag-checkered', 'color' => '#43e97b'],
            'danger' => ['icon' => 'exclamation-triangle', 'color' => '#ee0979'],
            'favorite' => ['icon' => 'star', 'color' => '#f093fb'],
        ],
        'default_type' => 'marker',
        'default_color' => '#3388ff',
    ],

    // Map Settings
    'map' => [
        'default_center' => [
            'lat' => env('MAP_DEFAULT_LAT', 27.7172),
            'lng' => env('MAP_DEFAULT_LNG', 85.3240),
        ],
        'default_zoom' => env('MAP_DEFAULT_ZOOM', 13),
        'max_zoom' => env('MAP_MAX_ZOOM', 18),
        'min_zoom' => env('MAP_MIN_ZOOM', 3),
    ],

    // Sharing Settings
    'sharing' => [
        'public_locations_enabled' => env('SHARING_PUBLIC_LOCATIONS', true),
        'live_user_tracking_enabled' => env('SHARING_LIVE_USERS', true),
        'sharing_timeout' => env('SHARING_TIMEOUT', 5), // minutes
    ],

    // Statistics Settings
    'stats' => [
        'cache_duration' => env('STATS_CACHE_DURATION', 300), // seconds
        'distance_unit' => env('STATS_DISTANCE_UNIT', 'km'), // km or miles
    ],

    // Performance Settings
    'performance' => [
        'enable_query_caching' => env('TRACKING_QUERY_CACHE', true),
        'cache_ttl' => env('TRACKING_CACHE_TTL', 600), // seconds
        'batch_size' => env('TRACKING_BATCH_SIZE', 100),
    ],

    // Rate Limiting
    'rate_limit' => [
        'location_updates' => env('RATE_LIMIT_LOCATION_UPDATES', 60), // per minute
        'api_requests' => env('RATE_LIMIT_API_REQUESTS', 120), // per minute
    ],
];
