@props(['package'])

<a
style="text-decoration:none; position: relative;"
href="{{ route('tours.show', $package) }}"
class="offer-card package-item"
id="offerCard"
data-type="{{ $package->trek_type }}">


<!-- <canvas class="oc-snow-canvas" id="snowCanvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 999; pointer-events: none;"></canvas> -->


@if($package->trek_type ?? false)
<div style="position: absolute; top: 10px; right: 10px; z-index: 50;">
    <br>
    <span style="background: rgba(255,255,255,0.95); color: #1a5f7a;
                    padding: 3px 10px; border-radius: 20px;
                    font-size: 11px; font-weight: 700;
                    font-family:'Astloch',system-ui;
                    text-transform: capitalize;
                    box-shadow: 0 2px 8px rgba(26, 95, 122, 0.3);">
        Luxury
    </span>
</div>
@endif


@if($package->image == null)
<img
    src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&h=400&fit=crop"
    alt="{{ $package->name }}"
    class="offer-card-image"
>
@else
<div style="position: relative; width: 100%; height: 200px; overflow: hidden;">
    <x-prayer-flags />
    <img
        src="{{ $package->image }}"
        alt="{{ $package->name }}"
        class="offer-card-image"
        style="width: 100%; height: 100%; object-fit: cover;"
    >
</div>
@endif


<div class="offer-card-body" id="offerCardBody">
    <br>
    <div style="display:flex; margin-bottom: var(--space-md); position: relative; z-index: 10;">
        @if($package->difficulty_level === 'easy')
            <span class="badgee badgee-success">
                <i class="fas fa-circle"></i> Easy
            </span>
        @elseif($package->difficulty_level === 'moderate')
            <span class="badgee badgee-warning">
                <i class="fas fa-circle"></i> Moderate
            </span>
        @else
            <span class="badgee badgee-error">
                <i class="fas fa-circle"></i> Hard
            </span>
        @endif
        @if(($package->rating_count ?? 0) > 0)
        <div style="
        color: white; padding: 3px 8px; border-radius: 12px;
        font-size: 12px; font-weight: 600;
        display: flex; align-items: center; gap: 4px; backdrop-filter: blur(4px);"
        >
        <span class="badgee badgee-success" style="opacity: 0.75; font-size: 11px;">
        <i class="fas fa-star" style="color: #FFC107; font-size: 11px;"></i>
        {{ number_format($package->rating_avg, 1) }}
        ({{ $package->rating_count }})</span>
        </div>
        @endif
    </div>


    <h3 class="offer-card-title" style="position: relative; z-index: 10;">{{ $package->name }}</h3>


    <p class="offer-card-text" style="position: relative; z-index: 10;">{{ Str::limit(strip_tags(Str::markdown($package->description)), 100) }}</p>


    <div style="display: flex; gap: var(--space-lg); margin-bottom: var(--space-md); font-size: 14px; color: var(--color-text-light); position: relative; z-index: 10;">
        <div class="flex" style="align-items: center; gap: var(--space-xs);">
            <i class="fas fa-calendar" style="color: #d4a020;"></i>
            <span style="color: #e8f4f8;">{{ $package->duration_days }} {{ Str::plural('Day', $package->duration_days) }}</span>
        </div>
        <div class="flex" style="align-items: center; gap: var(--space-xs);">
            <i class="fas fa-map-marker-alt" style="color: #d4a020;"></i>
            <span style="color: #e8f4f8;">{{ $package->checkpoints->count() }} Stops</span>
        </div>
        @if($package->region ?? false)
        <span style="color: #d4a020; text-transform: capitalize; position: relative; z-index: 10;">
        <i class="fas fa-map-pin"></i>
        {{ $package->region }}
        </span>
        @endif
    </div>


    <div style="position: relative; z-index: 10; margin-top: auto;">
        <hr class="oc-divider">
        <div class="oc-price-section">
            <div>
            <div class="oc-old-price">NPR 85,000</div>
            <div class="oc-new-price">Rs. <span style="font-family:'Astloch',system-ui;">{{ number_format($package->price, 0) }} </span><sub>/ person</sub></div>
            </div>
            <div class="oc-discount-pill">90% OFF</div>
        </div>
    </div>
</div>
</a>


<style>
    @import url('https://fonts.googleapis.com/css2?family=Astloch:wght@400;700&family=Rye&display=swap');
    .offer-card {
        position: relative;
        border-radius: 16px;
        overflow: visible;
        background: linear-gradient(180deg, #287a1a 20%, #355f2d 60%, #175412 100%);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        text-decoration: none;
    }

    .offer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
    }

    .offer-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        position: relative;
        border-radius: 16px 16px 0 0;
        z-index: 2;
        filter: brightness(1.05) contrast(1.05);
    }

    .offer-card-body {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        background: linear-gradient(180deg, #0f2a14 0%, #1a3a22 100%);
        border-radius: 0 0 16px 16px;
        overflow: hidden;
    }

    .offer-card-title {
        font-family: "Rye", serif;
        font-size: 18px;
        font-weight: 700;
        color: #e8f4f8;
        margin: 0 0 8px;
        line-height: 1.3;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .offer-card-text {
        font-size: 13px;
        color: #b8d4e8;
        margin: 0 0 12px;
        line-height: 1.5;
        flex-shrink: 0;
    }

    .badgee {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        margin-right: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .badgee-success {
        background: linear-gradient(135deg, #2d5016, #3d6b22);
        color: #e8f8e0;
    }

    .badgee-warning {
        background: linear-gradient(135deg, #d4a020, #b88a10);
        color: #fff8e0;
    }

    .badgee-error {
        background: linear-gradient(135deg, #c41e3a, #a01830);
        color: #ffe0e0;
    }

    .oc-divider {
        border: none;
        border-top: 1px solid rgba(212, 160, 32, 0.4);
        margin: 12px 0;
        width: 100%;
    }

    .oc-price-section {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
    }

    .oc-old-price {
        font-size: 12px;
        color: #7a9ab8;
        text-decoration: line-through;
        margin-bottom: 4px;
    }

    .oc-new-price {
        font-size: 20px;
        font-weight: 700;
        color: #d4a020;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(212, 160, 32, 0.4);
    }

    .oc-new-price sub {
        font-size: 11px;
        font-weight: 400;
        color: #7a9ab8;
        bottom: 0;
    }

    .oc-discount-pill {
        background: linear-gradient(135deg, #c41e3a, #8b1528);
        color: white;
        font-size: 11px;
        font-weight: 800;
        padding: 6px 10px;
        border-radius: 16px;
        letter-spacing: 0.5px;
        animation: oc-wiggle 3s ease-in-out infinite;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(196, 30, 58, 0.5);
    }

    @keyframes oc-wiggle {
        0%, 100% { transform: rotate(-2deg); }
        50% { transform: rotate(2deg); }
    }

    @keyframes flag-wave {
        0%, 100% { transform: rotate(-3deg); }
        50% { transform: rotate(3deg); }
    }
</style>

<!-- 
<script>
document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        const canvas = document.getElementById('snowCanvas');
        const card = document.getElementById('offerCard');
        
        if (!canvas || !card) return;

        const ctx = canvas.getContext('2d');
        if (!ctx) return;
        
        const rect = card.getBoundingClientRect();
        const width = Math.floor(rect.width);
        const height = Math.floor(rect.height);
        
        canvas.width = width;
        canvas.height = height;

        if (width === 0 || height === 0) return;

        const GROUND_Y_RATIO = 1.0;
        const COLS = 90;
        const MAX_PILE = 30;
        const pile = new Array(COLS).fill(0);
        
        const FLAKE_COUNT = 85;
        const flakes = Array.from({ length: FLAKE_COUNT }, () => ({
            x: Math.random() * width,
            y: -Math.random() * height,
            r: 0.7 + Math.random() * 2.3,
            speed: 0.28 + Math.random() * 0.72,
            drift: (Math.random() - 0.5) * 0.35,
            alpha: 0.45 + Math.random() * 0.55,
            wobble: Math.random() * Math.PI * 2,
            wobbleSpd: 0.018 + Math.random() * 0.032,
            isCrystal: Math.random() < 0.3
        }));

        const sparkles = [];
        let sparkleTimer = 0;

        function groundY() { return height * GROUND_Y_RATIO; }

        function drawPile() {
            const gY = groundY();
            const colW = width / COLS;
            
            ctx.beginPath();
            ctx.moveTo(0, height);
            ctx.lineTo(0, gY - pile[0]);
            
            for (let i = 1; i < COLS; i++) {
                const x0 = (i - 1) * colW;
                const x1 = i * colW;
                const h0 = pile[i - 1];
                const h1 = pile[i];
                ctx.quadraticCurveTo(x0 + colW * 0.5, gY - (h0 + h1) / 2 - 2, x1, gY - h1);
            }
            
            ctx.lineTo(width, height);
            ctx.closePath();
            
            const grad = ctx.createLinearGradient(0, gY - MAX_PILE, 0, height);
            grad.addColorStop(0, 'rgba(235,248,255,0.98)');
            grad.addColorStop(0.3, 'rgba(215,232,248,0.98)');
            grad.addColorStop(1, 'rgba(185,218,238,1)');
            
            ctx.fillStyle = grad;
            ctx.fill();
            
            ctx.beginPath();
            ctx.moveTo(0, gY - pile[0]);
            for (let i = 1; i < COLS; i++) {
                const x1 = i * (width / COLS);
                ctx.lineTo(x1, gY - pile[i]);
            }
            
            ctx.strokeStyle = 'rgba(255,255,255,0.7)';
            ctx.lineWidth = 0.8;
            ctx.stroke();
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            const gY = groundY();
            const colW = width / COLS;
            
            drawPile();
            
            flakes.forEach(f => {
                f.wobble += f.wobbleSpd;
                const wx = f.x + Math.sin(f.wobble) * 1.8;
                const col = Math.min(COLS - 1, Math.max(0, Math.floor(wx / colW)));
                const landY = gY - pile[col];
                
                if (f.y + f.r >= landY) {
                    if (pile[col] < MAX_PILE) {
                        pile[col] = Math.min(MAX_PILE, pile[col] + 0.11);
                        if (col > 0) pile[col - 1] = Math.min(MAX_PILE, pile[col - 1] + 0.04);
                        if (col < COLS - 1) pile[col + 1] = Math.min(MAX_PILE, pile[col + 1] + 0.04);
                    }
                    f.x = Math.random() * width;
                    f.y = -f.r - Math.random() * 20;
                    f.speed = 0.28 + Math.random() * 0.72;
                    f.drift = (Math.random() - 0.5) * 0.35;
                    return;
                }
                
                f.y += f.speed;
                f.x += f.drift;
                
                if (f.x < -5) f.x = width + 5;
                if (f.x > width + 5) f.x = -5;
                
                ctx.save();
                ctx.globalAlpha = f.alpha;
                if (f.isCrystal && f.r > 1.4) {
                    ctx.strokeStyle = 'rgba(255,255,255,0.95)';
                    ctx.lineWidth = f.r * 0.45;
                    ctx.translate(wx, f.y);
                    for (let a = 0; a < 3; a++) {
                        ctx.beginPath();
                        ctx.moveTo(-f.r * 1.2, 0);
                        ctx.lineTo(f.r * 1.2, 0);
                        ctx.stroke();
                        ctx.rotate(Math.PI / 3);
                    }
                } else {
                    ctx.beginPath();
                    ctx.arc(wx, f.y, f.r, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(255,255,255,0.92)';
                    ctx.fill();
                }
                ctx.restore();
            });
            
            sparkleTimer++;
            if (sparkleTimer % 6 === 0 && sparkles.length < 8) {
                const sx = Math.random() * width;
                const sc = Math.min(COLS - 1, Math.max(0, Math.floor(sx / colW)));
                if (pile[sc] > 3) {
                    sparkles.push({
                        x: sx,
                        y: gY - pile[sc] - Math.random() * 3,
                        life: 1,
                        decay: 0.03 + Math.random() * 0.04,
                        r: 1 + Math.random() * 1.5
                    });
                }
            }
            
            for (let i = sparkles.length - 1; i >= 0; i--) {
                const sp = sparkles[i];
                sp.life -= sp.decay;
                if (sp.life <= 0) { sparkles.splice(i, 1); continue; }
                ctx.save();
                ctx.globalAlpha = sp.life * 0.85;
                ctx.fillStyle = 'rgba(255,255,255,0.98)';
                ctx.translate(sp.x, sp.y);
                for (let a = 0; a < 4; a++) {
                    ctx.beginPath();
                    ctx.ellipse(0, 0, sp.r * 0.3, sp.r, 0, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.rotate(Math.PI / 4);
                }
                ctx.restore();
            }
            
            requestAnimationFrame(animate);
        }
        
        animate();
    }, 500);
});
</script> -->