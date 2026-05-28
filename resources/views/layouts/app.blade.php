<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Paaila') - Trek with Confidence</title>
    <link rel="icon" type="image/png" href="{{ asset('images/paailaLogo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400;1,700&family=Jim+Nightshade&family=Tangerine&display=swap');

        :root {
            --color-primary: #1B5E20;
            --color-primary-dark: #144416;
            --color-primary-light: #2E7D32;
            --color-accent: #F57C00;
            --color-accent-dark: #E65100;
            --color-bg: #F5F5F5;
            --color-white: #FFFFFF;
            --color-text: #263238;
            --color-text-light: #546E7A;
            --color-text-light-booking: #ffffff;
            --color-success: #2E7D32;
            --color-warning: #ED6C02;
            --color-error: #D32F2F;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --space-xs: 4px;
            --space-sm: 8px;
            --space-md: 16px;
            --space-lg: 24px;
            --space-xl: 32px;
            --space-2xl: 48px;
            --font-heading: 'Poppins', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * { 
        margin: 0; padding: 0; box-sizing: border-box; 
        }
        
        html {
        scroll-behavior: smooth; 
        }
        
        html{
            scroll-behavior:smooth;
        }
        
        body {
            font-family: var(--font-body);
            font-size: 16px;
            line-height: 1.6;
            color: var(--color-text);
            background: var(--color-bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            line-height: 1.2;
            color: var(--color-text);
        }

        h1 { font-size: clamp(22px, 5vw, 32px); }
        h2 { font-size: clamp(18px, 4vw, 24px); }
        h3 { font-size: clamp(15px, 3vw, 18px); }
        h4 { font-size: 16px; }

        p {
            margin-bottom: var(--space-md);
            color: var(--color-text-light);
        }

        .tangerine-regular {
        font-family: "Tangerine", cursive;
        font-weight: 400; 
        }
        
        .almendra-regular {
        font-family: "Almendra", serif;
        font-weight: 400; 
        }
        
        .almendra-bold { 
        font-family: "Almendra", serif;
        font-weight: 700;
        }
        
        .almendra-regular-italic { 
        font-family: "Almendra", serif;
        font-weight: 400; 
        font-style: italic;
        }
        
        .almendra-bold-italic { 
        font-family: "Almendra", serif;
        font-weight: 700; 
        font-style: italic; 
        }
        
        .jim-nightshade-regular {
        font-family: "Jim Nightshade", cursive;
        font-weight: 400; 
        }

        .hero-logo-row { 
        display: flex;
        align-items: flex-end;
        gap: 14px;
        margin-bottom: 4px; 
        }
        
        .hero-logo-img { 
        height: 64px; 
        width: 46px; 
        object-fit: contain;
        flex-shrink: 0; 
        margin-bottom: 6px; 
        opacity: 0.92; 
        }

        .navbar {
            background: var(--color-white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--space-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            text-decoration: none;
            flex-shrink: 0;
        }

        .navbar-logo-img {
            height: 45px;
            width: auto;
        }

        .navbar-logo-text {
            font-family: "Almendra", serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--color-primary-light);
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: var(--space-xl);
            list-style: none;
        }

        .navbar-link {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-text);
            text-decoration: none;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .navbar-link:hover { 
        color: var(--color-primary);
        }
        
        .navbar-link.active { 
        color: var(--color-primary); 
        font-weight: 600; 
        }

        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: var(--color-text);
            font-size: 22px;
            line-height: 1;
            border-radius: var(--radius-sm);
            transition: background 0.2s;
        }

        .navbar-toggle:hover { 
        background: #F0F0F0; 
        }

        .navbar-mobile-menu {
            display: none;
            flex-direction: column;
            background: var(--color-white);
            border-top: 1px solid #E0E0E0;
            padding: var(--space-md) var(--space-lg);
            gap: 4px;
            box-shadow: var(--shadow-md);
        }

        .navbar-mobile-menu.open { 
        display: flex; 
        }

        .navbar-mobile-menu li {
        list-style: none; 
        }

        .navbar-mobile-menu .navbar-link {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #F5F5F5;
            font-size: 15px;
        }

        .navbar-mobile-menu .navbar-link:last-child { border-bottom: none; }

        .navbar-mobile-menu .btn {
            margin-top: 8px;
            width: 100%;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: 12px 24px;
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn:disabled {
        			opacity: 0.5; 
              cursor: not-allowed; 
        }

        .btn-primary {
        	background: var(--color-primary); 
          color: var(--color-white); 
          }
          
        .btn-primary:hover:not(:disabled) {
            background: var(--color-primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            background: var(--color-white);
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
        }
        .btn-secondary:hover:not(:disabled) { background: var(--color-primary); color: var(--color-white); }

        .btn-cta {
            background: var(--color-accent);
            color: var(--color-white);
            font-size: 16px;
            padding: 14px 32px;
        }
        .btn-cta:hover:not(:disabled) {
            background: var(--color-accent-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-lg { padding: 16px 32px; font-size: 16px; }
        .btn-block { width: 100%; }

        .card {
            background: var(--color-white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all 0.3s ease;
            animation: appear linear;
            animation-timeline: view();
            animation-range: entry -20% cover 60%;
        }

        @keyframes appear {
            0% { opacity: 0.2; scale: 0.2; }
            45% { opacity: 1; scale: 1; }
        }

        .card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .card-image { width: 100%; height: 200px; object-fit: cover; }
        .card-body { padding: var(--space-lg); }
        .card-title { font-size: 18px; font-weight: 600; margin-bottom: var(--space-sm); color: var(--color-text); }
        .card-text { font-size: 14px; color: var(--color-text-light); margin-bottom: var(--space-md); }
        .card-footer {
            padding: var(--space-md) var(--space-lg);
            border-top: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--space-sm);
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 var(--space-lg); }
        .container-fluid { width: 100%; padding: 0 var(--space-lg); }
        .section { padding: var(--space-2xl) 0; }

        .grid { display: grid; gap: var(--space-lg); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        .badge { display: inline-flex; align-items: center; gap: var(--space-xs); padding: 4px 12px; font-size: 12px; font-weight: 600; border-radius: 12px; }
        .badge-success { background: #E8F5E9; color: var(--color-success); }
        .badge-warning { background: #FFF3E0; color: var(--color-warning); }
        .badge-error { background: #FFEBEE; color: var(--color-error); }
        .badge-primary { background: #E8F5E9; color: var(--color-primary); }

        .alert { padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md); margin-bottom: var(--space-md); display: flex; align-items: center; gap: var(--space-md); flex-wrap: wrap; }
        .alert-success { background: #E8F5E9; color: var(--color-success); border-left: 4px solid var(--color-success); }
        .alert-error { background: #FFEBEE; color: var(--color-error); border-left: 4px solid var(--color-error); }
        .alert-warning { background: #FFF3E0; color: var(--color-warning); border-left: 4px solid var(--color-warning); }

        .form-group { margin-bottom: var(--space-lg); }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--color-text); margin-bottom: var(--space-sm); }
        .form-label .required { color: var(--color-error); }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--color-text);
            background: var(--color-white);
            border: 2px solid #E0E0E0;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.1);
        }

        .form-input::placeholder { color: #B0BEC5; }
        .form-textarea { min-height: 120px; resize: vertical; }
        .form-helper { font-size: 13px; color: var(--color-text-light); margin-top: var(--space-xs); }
        .form-error { font-size: 13px; color: var(--color-error); margin-top: var(--space-xs); }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .mt-xs { margin-top: var(--space-xs); }
        .mt-sm { margin-top: var(--space-sm); }
        .mt-md { margin-top: var(--space-md); }
        .mt-lg { margin-top: var(--space-lg); }
        .mt-xl { margin-top: var(--space-xl); }
        .mb-xs { margin-bottom: var(--space-xs); }
        .mb-sm { margin-bottom: var(--space-sm); }
        .mb-md { margin-bottom: var(--space-md); }
        .mb-lg { margin-bottom: var(--space-lg); }
        .mb-xl { margin-bottom: var(--space-xl); }
        .flex { display: flex; }
        .flex-center { display: flex; justify-content: center; align-items: center; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .flex-wrap { flex-wrap: wrap; }
        .gap-sm { gap: var(--space-sm); }
        .gap-md { gap: var(--space-md); }
        .gap-lg { gap: var(--space-lg); }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #F5F5F5; }
        ::-webkit-scrollbar-thumb { background: #B0BEC5; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #90A4AE; }

        @media (max-width: 900px) {
            .navbar-menu { display: none; }
            .navbar-toggle { display: flex; align-items: center; justify-content: center; }
        }

        @media (max-width: 900px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 640px) {
            :root {
                --space-lg: 16px;
                --space-xl: 20px;
                --space-2xl: 32px;
            }

            .navbar-container { padding: 0 var(--space-md); height: 60px; }
            .navbar-logo-text { font-size: 20px; }
            .navbar-logo-img { height: 36px; }

            .grid-2,
            .grid-3,
            .grid-4 { grid-template-columns: 1fr; }

            .section { padding: var(--space-xl) 0; }

            .btn-cta { font-size: 14px; padding: 12px 24px; }

            .container { padding: 0 var(--space-md); }

            .card-image { height: 180px; }

            footer .grid-3 { grid-template-columns: 1fr !important; gap: var(--space-lg); }
        }

        @media (max-width: 400px) {
            .navbar-logo-text { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img src="{{ asset('images/paailaLogo.png') }}" alt="Paaila logo" class="navbar-logo-img">
                <span class="navbar-logo-text almendra-bold">Paaila</span>
            </a>

            <ul class="navbar-menu">
                <li><a href="{{ route('feed.index') }}" class="navbar-link {{ request()->routeIs('feed.*') ? 'active' : '' }}">Feed</a></li>
                <li><a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">Treks</a></li>
                <li><a href="#footer" class="navbar-link">About Us</a></li>

                @auth
                    <li><a href="{{ route('tour.foryou') }}" class="navbar-link {{ request()->routeIs('tour.*') ? 'active' : '' }}">For You <sup><i class="fa-solid fa-heart fa-beat-fade fa-2xs"></i></sup></a></li>
                    <li><a href="{{ route('bookings.index') }}" class="navbar-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
                    <li><a href="{{ route('profile.show') }}" class="navbar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">My Profile</a></li>

                    @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}" class="navbar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a></li>
                    @endif

                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Login</a></li>
                    <li><a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a></li>
                @endauth
            </ul>

            <button class="navbar-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="navbar-mobile-menu" id="mobileMenu" role="navigation" aria-label="Mobile navigation">
            <ul style="list-style:none; display:contents;">
                <li><a href="{{ route('feed.index') }}" class="navbar-link {{ request()->routeIs('feed.*') ? 'active' : '' }}">Feed</a></li>
                <li><a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">Treks</a></li>
                <li><a href="#footer" class="navbar-link">About Us</a></li>

                @auth
                    <li><a href="{{ route('tour.foryou') }}" class="navbar-link {{ request()->routeIs('tour.*') ? 'active' : '' }}">For You <sup><i class="fa-solid fa-heart fa-beat-fade fa-2xs"></i></sup></a></li>
                    <li><a href="{{ route('bookings.index') }}" class="navbar-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
                    <li><a href="{{ route('profile.show') }}" class="navbar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">My Profile</a></li>

                    @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}" class="navbar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a></li>
                    @endif

                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:block; margin-top:8px;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-block">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="btn btn-secondary btn-block" style="margin-top:8px;">Login</a></li>
                    <li><a href="{{ route('register') }}" class="btn btn-primary btn-block" style="margin-top:8px;">Sign Up</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-md">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-md">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer id="footer" style="background: linear-gradient(135deg, rgba(4,11,4,0.95) 0%, rgba(1,9,1,0.3) 100%), url('{{ asset('images/TrekWall.jpg') }}') center/cover; padding: var(--space-2xl) 0;">
        <div class="container">
            <div class="grid grid-3" style="gap: var(--space-xl);">
                <div>
                    <h3 style="color: var(--color-white); margin-bottom: var(--space-md);">Paaila</h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px;">
                        Trek with confidence.<br>
                        सर्वे भवन्तु सुखिनः । सर्वे सन्तु निरामयाः ॥<br>
                        𑐫𑐵𑐬𑐸 𑐳𑐸𑐏𑐸𑐫𑑂 𑐥𑐵𑐫𑑂:
                    </p>
                    <div style="display:flex; align-items:center; gap:12px; margin-top: var(--space-sm);">
                        <i class="fas fa-solid fa-flip"><img src="{{ asset('images/paailaLogo.png') }}" alt="Paaila logo" class="hero-logo-img"></i>
                        <img src="{{ asset('images/Flag_of_Nepal.gif') }}" alt="Flag of Nepal" class="hero-logo-img">
                    </div>
                </div>
                <div>
                    <h4 style="color: var(--color-white); margin-bottom: var(--space-md); font-size: 16px;">Quick Links</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: var(--space-sm);"><a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px;">Browse Treks</a></li>
                        <li style="margin-bottom: var(--space-sm);"><a href="{{ route('tracking.pin.entry') }}" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px;">Track Someone</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: var(--color-white); margin-bottom: var(--space-md); font-size: 16px;">Contact</h4>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px;">Email: info@paaila.com<br>Phone: +977-123-4567</p>
                </div>
            </div>
            <div style="margin-top: var(--space-xl); padding-top: var(--space-lg); border-top: 1px solid rgba(255,255,255,0.3); text-align: center;">
                <p style="color: rgba(255,255,255,0.88); font-size: 13px; margin: 0;">© 2026 Paaila | पाइला | 𑐥𑐵𑐂𑐮𑑂𑐴</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        (function () {
            var toggle = document.getElementById('navToggle');
            var menu   = document.getElementById('mobileMenu');
            var icon   = toggle.querySelector('i');

            toggle.addEventListener('click', function () {
                var open = menu.classList.toggle('open');
                toggle.setAttribute('aria-expanded', open);
                icon.className = open ? 'fas fa-times' : 'fas fa-bars';
            });

            menu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    menu.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                    icon.className = 'fas fa-bars';
                });
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
{{-- SUCCESS MESSAGE SECTION --}}
{{-- Checks if a success message exists in session --}}
{{-- If yes, displays a green success alert box --}}
{{-- Icon: check-circle --}}
{{-- Shows session('success') text --}}

{{-- ERROR MESSAGE SECTION --}}
{{-- Checks if an error message exists in session --}}
{{-- If yes, displays a red error alert box --}}
{{-- Icon: exclamation-circle --}}
{{-- Shows session('error') text --}}

{{-- MAIN CONTENT AREA --}}
{{-- This is where child pages inject their content using @yield('content') --}}

{{-- FOOTER SECTION --}}
{{-- Main footer container with primary dark background --}}

{{-- FOOTER COLUMN 1: BRAND INFO --}}
{{-- Displays app name "Paaila" --}}
{{-- Short description about GPS trekking safety system --}}

{{-- FOOTER COLUMN 2: QUICK LINKS --}}
{{-- Link to Browse Treks (route: home) --}}
{{-- Link to Track Someone (route: tracking.pin.entry) --}}

{{-- FOOTER COLUMN 3: CONTACT INFO --}}
{{-- Email: info@paaila.com --}}
{{-- Phone: +977-123-4567 --}}

{{-- FOOTER BOTTOM BAR --}}
{{-- Copyright text for 2026 Paaila --}}
{{-- Tagline: Trek safely with GPS tracking --}}

{{-- LEAFLET JS LIBRARY --}}
{{-- External script for map functionality --}}
{{-- https://unpkg.com/leaflet@1.9.4/dist/leaflet.js --}}

{{-- STACKED SCRIPTS --}}
{{-- Allows pushing page-specific scripts from child views --}}

{{-- END OF LAYOUT FILE --}}