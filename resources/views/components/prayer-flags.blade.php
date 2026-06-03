@props(['color1' => '#003366', 'color2' => '#ffffff', 'color3' => '#c41e3a', 'color4' => '#1a4d2e', 'color5' => '#d4a020'])

<svg class="oc-flags" viewBox="0 0 320 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" 
        style="position: absolute; top: 0; left: 0; width: 100%; height: 60px; z-index: 999;">
    
        <path d="M0,12 Q40,8 80,14 Q120,20 160,12 Q200,4 240,14 Q280,22 320,12" stroke="#d4a020" stroke-width="0.8" fill="none" opacity="0.7"/>
        
        <g style="transform-origin:40px 12px;animation:flag-wave 2.1s ease-in-out infinite">
            <rect x="20" y="0" width="40" height="24" fill="{{ $color1 }}" opacity="0.95" rx="3"/>
            <text x="40" y="18" font-size="14" fill="rgba(255,255,255,0.95)" text-anchor="middle" font-family="Arial, sans-serif">ཨོཾ</text>
        </g>
        
        <g style="transform-origin:100px 12px;animation:flag-wave 2.4s 0.3s ease-in-out infinite">
            <rect x="80" y="0" width="40" height="24" fill="{{ $color2 }}" opacity="0.95" rx="3"/>
            <text x="100" y="18" font-size="14" fill="#006600" text-anchor="middle" font-family="Arial, sans-serif">མ</text>
        </g>
    
        <g style="transform-origin:160px 12px;animation:flag-wave 2.2s 0.6s ease-in-out infinite">
        <rect x="140" y="0" width="40" height="24" fill="{{ $color3 }}" opacity="0.95" rx="3"/>
        <text x="160" y="18" font-size="14" fill="rgb(242, 255, 0)" text-anchor="middle" font-family="Arial, sans-serif">ཎི</text>
        </g>
    
        <g style="transform-origin:220px 12px;animation:flag-wave 2.5s 0.2s ease-in-out infinite">
        <rect x="200" y="0" width="40" height="24" fill="{{ $color4 }}" opacity="0.95" rx="3"/>
        <text x="220" y="18" font-size="14" fill="rgb(233, 132, 1)" text-anchor="middle" font-family="Arial, sans-serif">པདྨེ</text>
        </g>
    
        <g style="transform-origin:280px 12px;animation:flag-wave 2.3s 0.5s ease-in-out infinite">
        <rect x="260" y="0" width="40" height="24" fill="{{ $color5 }}" opacity="0.95" rx="3"/>
        <text x="280" y="18" font-size="14" fill="#003366" text-anchor="middle" font-family="Arial, sans-serif">ཧཱུྂ</text>
        </g>
</svg>

<style>
    @keyframes flag-wave {
        0%, 100% { transform: rotate(-3deg); }
        50% { transform: rotate(3deg); }
    }
</style>