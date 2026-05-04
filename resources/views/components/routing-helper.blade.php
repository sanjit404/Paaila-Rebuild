<script>
// Routing Configuration
const ROUTING_CONFIG = {
    openroute: '{{ env('OPENROUTE_API_KEY') }}',
};

/**
 * Smart Route Drawing with OpenRouteService + OSRM fallback
 */
async function drawSmartRoute(waypoints, map) {
    console.log(`🗺️ Drawing route with ${waypoints.length} waypoints...`);

    let result = null;

    // Try OpenRouteService first (supports up to 50 waypoints)
    if (ROUTING_CONFIG.openroute && ROUTING_CONFIG.openroute !== '' && waypoints.length <= 50) {
        result = await getOpenRouteServiceRoute(waypoints, map);
        if (result && result.success) return result;
    }

    // Fallback to OSRM
    result = await getOSRMRoute(waypoints, map);
    if (result && result.success) return result;

    // Last resort: straight lines
    return drawStraightLine(waypoints, map);
}

/**
 * OpenRouteService Routing (BEST - up to 50 waypoints)
 */
async function getOpenRouteServiceRoute(waypoints, map) {
    const API_KEY = ROUTING_CONFIG.openroute;
    
    try {
        // Build coordinates array [[lng, lat], [lng, lat], ...]
        const coordinates = waypoints.map(w => [w.lng, w.lat]);

        const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
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
            throw new Error(`OpenRouteService error: ${response.status}`);
        }

        const data = await response.json();

        if (data.features && data.features.length > 0) {
            const route = data.features[0];
            const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            
            // Draw beautiful route
            const routeLine = L.polyline(routeCoordinates, {
                color: '#667eea',
                weight: 5,
                opacity: 0.8,
                lineJoin: 'round',
                lineCap: 'round',
                className: 'route-line'
            }).addTo(map);

            // Fit map to route
            map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });

            // Extract stats
            const distance = (route.properties.segments.reduce((sum, seg) => sum + seg.distance, 0) / 1000).toFixed(1);
            const duration = Math.round(route.properties.segments.reduce((sum, seg) => sum + seg.duration, 0) / 60);

            console.log(`✅ OpenRouteService: ${distance} km, ${duration} min`);

            return {
                success: true,
                provider: 'OpenRouteService (Real roads)',
                distance: distance,
                duration: duration,
                coordinates: routeCoordinates
            };
        }
    } catch (error) {
        console.error('❌ OpenRouteService error:', error);
        return null;
    }
}

/**
 * OSRM Routing (Fallback - unlimited but may need segmentation)
 */
async function getOSRMRoute(waypoints, map) {
    try {
        // OSRM has URL length limits, split if too many waypoints
        if (waypoints.length > 25) {
            return await getOSRMRouteSegmented(waypoints, map);
        }

        const coordinates = waypoints.map(w => `${w.lng},${w.lat}`).join(';');
        
        const response = await fetch(
            `https://router.project-osrm.org/route/v1/driving/${coordinates}?overview=full&geometries=geojson`
        );
        const data = await response.json();

        if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
            const route = data.routes[0];
            const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            
            const routeLine = L.polyline(routeCoordinates, {
                color: '#667eea',
                weight: 5,
                opacity: 0.8,
                lineJoin: 'round'
            }).addTo(map);

            map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });

            const distance = (route.distance / 1000).toFixed(1);
            const duration = Math.round(route.duration / 60);

            console.log(`✅ OSRM: ${distance} km, ${duration} min`);

            return {
                success: true,
                provider: 'OSRM (Real roads)',
                distance: distance,
                duration: duration,
                coordinates: routeCoordinates
            };
        }
    } catch (error) {
        console.error('❌ OSRM error:', error);
        return null;
    }
}

/**
 * OSRM Segmented (for >25 waypoints)
 */
async function getOSRMRouteSegmented(waypoints, map) {
    console.log(`🔄 Splitting ${waypoints.length} waypoints into segments...`);
    
    const segments = [];
    const chunkSize = 20;

    // Split waypoints into chunks with overlap
    for (let i = 0; i < waypoints.length - 1; i += chunkSize - 1) {
        const chunk = waypoints.slice(i, Math.min(i + chunkSize, waypoints.length));
        segments.push(chunk);
    }

    const allCoordinates = [];
    let totalDistance = 0;
    let totalDuration = 0;

    for (const segment of segments) {
        const coordinates = segment.map(w => `${w.lng},${w.lat}`).join(';');
        
        try {
            const response = await fetch(
                `https://router.project-osrm.org/route/v1/driving/${coordinates}?overview=full&geometries=geojson`
            );
            const data = await response.json();

            if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                
                allCoordinates.push(...routeCoordinates);
                totalDistance += route.distance;
                totalDuration += route.duration;
            }
        } catch (error) {
            console.error('Segment error:', error);
        }
    }

    if (allCoordinates.length > 0) {
        const routeLine = L.polyline(allCoordinates, {
            color: '#667eea',
            weight: 5,
            opacity: 0.8
        }).addTo(map);

        map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });

        const distance = (totalDistance / 1000).toFixed(1);
        const duration = Math.round(totalDuration / 60);

        console.log(`✅ OSRM (segmented): ${distance} km, ${duration} min`);

        return {
            success: true,
            provider: 'OSRM (Multi-segment)',
            distance: distance,
            duration: duration,
            coordinates: allCoordinates
        };
    }

    return null;
}

/**
 * Straight Line Fallback
 */
function drawStraightLine(waypoints, map) {
    const routePoints = waypoints.map(w => [w.lat, w.lng]);
    
    L.polyline(routePoints, {
        color: '#667eea',
        weight: 4,
        opacity: 0.7,
        dashArray: '10, 10'
    }).addTo(map);

    map.fitBounds(routePoints, { padding: [50, 50] });

    console.log('⚠️ Using straight line fallback');

    return {
        success: true,
        provider: 'Direct Line',
        coordinates: routePoints
    };
}
</script>

<style>
/* Animated route line */
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