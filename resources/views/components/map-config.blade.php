<script>
const MAP_CONFIG = {
    maptiler: {
        key: '{{ env('MAPTILER_API_KEY') }}',
        styles: {
            streets: 'streets-v2',
            satellite: 'satellite',
            hybrid: 'hybrid-v4',
            topo: 'topo-v2',
            outdoor: 'outdoor-v4'
        }
    },
    nepal: {
        center: [27.7172, 85.3240], 
        bounds: {
            north: 30.45,
            south: 26.35,
            east: 88.20,
            west: 80.05
        }
    }
};


function createMap(elementId, options = {}) {
    const defaultOptions = {
        center: options.center || MAP_CONFIG.nepal.center,
        zoom: options.zoom || 13,
        zoomControl: options.zoomControl !== false,
        attributionControl: true
    };

    const map = L.map(elementId, {
        center: defaultOptions.center,
        zoom: defaultOptions.zoom,
        zoomControl: defaultOptions.zoomControl,
        attributionControl: defaultOptions.attributionControl
    });

    addTileLayer(map, options.style || 'streets');

    return map;
}


function addTileLayer(map, style = 'satellite') {
    const maptilerKey = MAP_CONFIG.maptiler.key;

    if (maptilerKey && maptilerKey !== '') {
        const styleCode = MAP_CONFIG.maptiler.styles[style] || MAP_CONFIG.maptiler.styles.streets;
        
        L.tileLayer(`https://api.maptiler.com/maps/${styleCode}/{z}/{x}/{y}.png?key=${maptilerKey}`, {
            tileSize: 512,
            zoomOffset: -1,
            minZoom: 1,
            maxZoom: 19,
            attribution: '© <a href="https://www.maptiler.com/">MapTiler</a> © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            crossOrigin: true
        }).addTo(map);

        console.log(`✅ Using MapTiler (${style} style)`);
    } else {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        console.log('⚠️ Using OpenStreetMap (fallback)');
    }
}
</script>

<style>
.leaflet-control-zoom a {
    width: 30px;
    height: 30px;
    line-height: 30px;
    font-size: 18px;
    color: #667eea !important;
}

.leaflet-control-zoom a:hover {
    background: #667eea !important;
    color: white !important;
}

.leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 10px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.leaflet-popup-tip {
    border-radius: 3px;
}

.leaflet-popup-content h4 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 1.1rem;
}

.leaflet-popup-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}
</style>