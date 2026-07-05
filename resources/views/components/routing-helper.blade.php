<script>
const ROUTING_CONFIG = {
    openroute: '{{ env('OPENROUTE_API_KEY') }}',
};


async function drawSmartRoute(waypoints, map) {
    console.trace("drawSmartRoute called");
    console.log(`🗺️ Drawing route with ${waypoints.length} waypoints...`);

    let result = null;

    // Route draw (Only if available in Open Route Service ko API)
    if (ROUTING_CONFIG.openroute && ROUTING_CONFIG.openroute !== '' && waypoints.length <= 50) {
        result = await getOpenRouteServiceRoute(waypoints, map);
        if (result && result.success) return result;
    }

    //Remote area ko route find navako thau ko lagi drawing dashed lines for reference (May use GPX data later but less chance)
    return drawStraightLine(waypoints, map);
}


async function getOpenRouteServiceRoute(waypoints, map) {
    const API_KEY = ROUTING_CONFIG.openroute;
    
    try {
        // Build coordinates array [[lng, lat], [lng, lat], ...]
        const coordinates = waypoints.map(w => [w.lng, w.lat]);

        const response = await fetch('https://api.openrouteservice.org/v2/directions/foot-hiking/geojson', {
            method: 'POST',
            headers: {
                'Authorization': API_KEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                coordinates: coordinates,
                instructions: false,
                preference: 'recommended'
            })
        });

        if (!response.ok) {
            console.log(await response.text());
            throw new Error(`OpenRouteService error: ${response.status}`);
        }

        const data = await response.json();

        if (data.features && data.features.length > 0) {
            const route = data.features[0];
            const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            
            
            const routeLine = L.polyline(routeCoordinates, {
                color: '#2E7D32',
                weight: 5,
                opacity: 0.8,
                lineJoin: 'round',
                lineCap: 'round',
                className: 'route-line'
            }).addTo(map);

            map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });

let distance = 0;
let duration = 0;

if (route.properties.summary) {
    distance = (route.properties.summary.distance / 1000).toFixed(1);
    duration = Math.round(route.properties.summary.duration / 60);
} else if (route.properties.segments) {
    distance = (
        route.properties.segments.reduce((s, seg) => s + seg.distance, 0) / 1000
    ).toFixed(1);

    duration = Math.round(
        route.properties.segments.reduce((s, seg) => s + seg.duration, 0) / 60
    );
}

console.log(`OpenRouteService: ${distance} km, ${duration} min`);

            return {
                success: true,
                provider: 'OpenRouteService (Real roads)',
                distance: distance,
                duration: duration,
                coordinates: routeCoordinates,
                line: routeLine
            };
        }
    } catch (error) {
        console.error('OpenRouteService error:', error);
        return null;
    }
}



function drawStraightLine(waypoints, map) {
    const routePoints = waypoints.map(w => [w.lat, w.lng]);

    const line = L.polyline(routePoints, {
        color: '#2E7D32',
        weight: 4,
        opacity: 0.7,
        dashArray: '10,10'
    }).addTo(map);

    return {
        success: true,
        provider: 'Direct Line',
        coordinates: routePoints,
        line: line
    };
}
</script>

<style>
@keyframes routeAppear {
    from {
        stroke-dashoffset: 1000;
    }
    to {
        stroke-dashoffset: 0;
    }
}

.route-line {
    stroke-dasharray: 1000;
    animation: routeAppear 2s ease-in-out forwards;
}
</style>