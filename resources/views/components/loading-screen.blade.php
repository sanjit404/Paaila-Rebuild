<div id="paailaLoader" style="display:none">
    <div class="loader-inner">
        <img
            src="{{ asset('images/paailaLogo.png') }}"
            alt="Paaila"
            class="loader-logo"
        >
        <div class="loader-text">Paaila</div>
        <div class="loader-line">
            <span id="loaderLineFill"></span>
        </div>
        <p class="loader-status" id="loaderStatus">
            Preparing your adventure...
        </p>
    </div>
</div>

<style>

body.loader-active {
    overflow: hidden;
    pointer-events: none;
}

#paailaLoader {
    position: fixed;
    inset: 0;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.82);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: all;
}

#paailaLoader.visible {
    opacity: 1;
}

#paailaLoader.hidden {
    opacity: 0;
    pointer-events: none;
}

.loader-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    animation: fadeUp 0.5s ease;
}

.loader-logo {
    width: 74px;
    height: auto;
    animation: logoFloat 2.4s ease-in-out infinite;
    filter: drop-shadow(0 4px 14px rgba(0,0,0,0.08));
}

.loader-text {
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.4px;
    color: #17351f;
}

.loader-line {
    width: 120px;
    height: 2px;
    background: rgba(0,0,0,0.08);
    overflow: hidden;
    border-radius: 999px;
    position: relative;
}

.loader-line span {
    position: absolute;
    left: -40%;
    width: 40%;
    height: 100%;
    background: linear-gradient(90deg, #1f7a45, #f6b73c);
    border-radius: 999px;
    animation: lineMove 1s ease-in-out infinite;
}

.loader-status {
    font-size: 13px;
    font-weight: 500;
    color: rgba(0,0,0,0.5);
    margin: 0;
    min-height: 18px;
    transition: opacity 0.25s ease;
}

@keyframes lineMove {
    0%   { left: -40%; }
    100% { left: 100%; }
}

@keyframes logoFloat {
    0%, 100% { transform: translateY(0px); }
    50%      { transform: translateY(-5px); }
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

</style>

<script>

(function () {

    const loader   = document.getElementById('paailaLoader');
    const status   = document.getElementById('loaderStatus');

    const messages = [
        'Preparing your adventure...',
        'Loading trek routes...',
        'Exploring the mountains...',
        'Almost there...',
        'Ready!'
    ];

    let current         = 0;
    let loaderVisible   = false;
    let messageInterval = null;
    let showTimer       = null;
    let activeRequests  = 0;

    function startTimer() {
        if (showTimer) return;

        showTimer = setTimeout(() => {
            loaderVisible = true;
            loader.style.display = 'flex';

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    loader.classList.add('visible');
                    document.body.classList.add('loader-active');
                });
            });

            current = 0;
            messageInterval = setInterval(() => {
                current++;
                if (current < messages.length) {
                    status.style.opacity = 0;
                    setTimeout(() => {
                        status.textContent = messages[current];
                        status.style.opacity = 1;
                    }, 180);
                }
            }, 900);

        }, 2000);
    }

    function cancelTimer() {
        if (showTimer) {
            clearTimeout(showTimer);
            showTimer = null;
        }
    }

    function showLoader() {
        activeRequests++;
        startTimer();
    }

    function hideLoader() {
        activeRequests = Math.max(0, activeRequests - 1);
        if (activeRequests > 0) return;

        cancelTimer();

        if (!loaderVisible) return;

        loaderVisible = false;
        clearInterval(messageInterval);
        messageInterval = null;
        current = 0;
        status.textContent = messages[0];

        loader.classList.remove('visible');
        loader.classList.add('hidden');
        document.body.classList.remove('loader-active');

        setTimeout(() => {
            loader.classList.remove('hidden');
            loader.style.display = 'none';
        }, 350);
    }

    window.PaailaLoader = { show: showLoader, hide: hideLoader };


    window.addEventListener('load', () => {
        showLoader();
        hideLoader();
    });


    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');

        if (!href || href.startsWith('#') || href.startsWith('javascript') || href.startsWith('mailto') || href.startsWith('tel')) return;

        if (link.target === '_blank') return;

        const isSameDomain =
            href.startsWith('/') ||
            href.startsWith(window.location.origin);

        if (!isSameDomain) return;

        showLoader();
    });


    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        showLoader();
    });


    window.addEventListener('load', hideLoader);
    window.addEventListener('pageshow', hideLoader);


    const _fetch = window.fetch;
    window.fetch = function (...args) {
        showLoader();
        return _fetch.apply(this, args).finally(() => {
            hideLoader();
        });
    };

    const _open = XMLHttpRequest.prototype.open;
    const _send = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function (...args) {
        this._paailaTracked = true;
        return _open.apply(this, args);
    };

    XMLHttpRequest.prototype.send = function (...args) {
        if (this._paailaTracked) {
            showLoader();
            this.addEventListener('loadend', () => hideLoader());
        }
        return _send.apply(this, args);
    };


    setTimeout(hideLoader, 8000);

})();

</script>