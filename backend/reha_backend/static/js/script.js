/* ============================================================
   REHA ORGANIZATION — MAIN JAVASCRIPT
   ============================================================ */

(function () {
    'use strict';

    /* ──────────────────────────────────────────
       UTILITY: DOM helpers
    ────────────────────────────────────────── */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

    /* ──────────────────────────────────────────
       1. THEME TOGGLE  (light ↔ dark)
    ────────────────────────────────────────── */
    function initTheme() {
        // Inject toggle into navbar if not already present
        const navbar = $('.navbar');
        if (!navbar) return;

        // Build toggle HTML
        const controls = document.createElement('div');
        controls.className = 'navbar-controls';
        controls.innerHTML = `
      <label class="theme-toggle" aria-label="Toggle dark mode">
        <input type="checkbox" id="themeCheck">
        <div class="theme-toggle-track">
          <div class="theme-toggle-thumb">
            <span class="sun-icon">☀️</span>
            <span class="moon-icon">🌙</span>
          </div>
        </div>
      </label>
      <a href="/donate/" class="navbar-donate">Donate</a>
    `;

        // Insert before hamburger (or at end)
        const hamburger = $('.hamburger', navbar);
        hamburger
            ? navbar.insertBefore(controls, hamburger)
            : navbar.appendChild(controls);

        const checkbox = $('#themeCheck');

        // Persist preference
        const saved = localStorage.getItem('reha-theme') || 'light';
        applyTheme(saved, checkbox);

        checkbox.addEventListener('change', () => {
            const next = checkbox.checked ? 'dark' : 'light';
            applyTheme(next, checkbox);
            localStorage.setItem('reha-theme', next);
        });
    }

    function applyTheme(theme, checkbox) {
        document.documentElement.setAttribute('data-theme', theme);
        if (checkbox) checkbox.checked = theme === 'dark';
    }

    /* ──────────────────────────────────────────
       2. NAVBAR: scroll effect + hamburger
    ────────────────────────────────────────── */
    function initNavbar() {
        const navbar = $('.navbar');
        if (!navbar) return;

        // Scroll state
        const onScroll = () => {
            navbar.classList.toggle('scrolled', window.scrollY > 60);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        // Hamburger
        const hamburger = $('.hamburger', navbar);
        if (hamburger) {
            let menu = $('.mobile-menu');
            if (!menu) {
                menu = document.createElement('div');
                menu.className = 'mobile-menu';
                const links = $$('.navbar-links a').map(a =>
                    `<li><a href="${a.href}">${a.textContent}</a></li>`
                ).join('');
                menu.innerHTML = `<ul>${links}<li><a href="/donate/" style="color:var(--gold)">Donate Now</a></li></ul>`;
                navbar.insertAdjacentElement('afterend', menu);
            }
            hamburger.addEventListener('click', () => menu.classList.toggle('open'));
        }

        // Active link
        $$('.navbar-links a').forEach(a => {
            if (a.href === location.href) a.classList.add('active');
        });
    }

    /* ──────────────────────────────────────────
       3. BACK TO TOP
    ────────────────────────────────────────── */
    function initBackToTop() {
        const btn = $('#back-to-top');
        if (!btn) return;
        window.addEventListener('scroll', () => {
            btn.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });
        btn.addEventListener('click', e => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ──────────────────────────────────────────
       4. HERO SLIDER  (3-slide ken-burns)
    ────────────────────────────────────────── */
    function initHeroSlider() {
        const slides = $$('.hero .slide');
        const dots = $$('.hero-dot');
        if (!slides.length) return;

        let current = 0;
        let timer;

        const goTo = idx => {
            slides[current].classList.remove('active');
            dots[current]?.classList.remove('active');
            current = (idx + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current]?.classList.add('active');
            // restart ken-burns
            const img = slides[current].querySelector('img');
            if (img) {
                img.style.animation = 'none';
                void img.offsetHeight; // reflow
                img.style.animation = '';
            }
        };

        const next = () => goTo(current + 1);

        const start = () => { timer = setInterval(next, 5500); };
        const stop = () => clearInterval(timer);

        start();

        // Dot clicks
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => { stop(); goTo(i); start(); });
        });

    }

    /* ──────────────────────────────────────────
       5. BUILD AUTO-SCROLL SLIDERS
          Converts existing Django-rendered lists
          into infinite auto-scroll tracks
    ────────────────────────────────────────── */
    function buildAutoSlider(section) {
        if (!section) return;

        // Find existing slider-track (Django output)
        let track = section.querySelector('.slider-track, #projectSlider, #newsSlider');
        if (!track) return;

        // Grab cards
        const cards = $$('.project-card, .news-card', track);
        if (!cards.length) return;

        // Remove old slider scaffolding (prev/next buttons)
        const oldSlider = track.closest('.slider');
        const parent = oldSlider || section;

        // Wrap in new auto-slider structure
        const wrapper = document.createElement('div');
        wrapper.className = 'auto-slider-wrapper';

        const newTrack = document.createElement('div');
        newTrack.className = 'auto-slider-track';

        // Clone cards for seamless loop (duplicate once)
        const allCards = [...cards, ...cards.map(c => c.cloneNode(true))];
        allCards.forEach(c => newTrack.appendChild(c));

        wrapper.appendChild(newTrack);

        // Replace old content
        if (oldSlider) {
            oldSlider.replaceWith(wrapper);
        } else {
            track.replaceWith(wrapper);
        }
    }

    function initAutoSliders() {
        buildAutoSlider($('.projects'));
        buildAutoSlider($('.news'));
    }

    /* ──────────────────────────────────────────
       6. PARTNERS SECTION  (two-column logo sliders)
    ────────────────────────────────────────── */
    function initPartners() {
        const section = $('.partners');
        if (!section) return;

        // Get existing partner logos
        const logos = $$('.partner-logo img', section);
        if (!logos.length) return;

        // Split roughly in half: left = national, right = international
        const half = Math.ceil(logos.length / 2);
        const national = logos.slice(0, half);
        const international = logos.slice(half);

        // Clear section body content (keep section-label, h2, p)
        const container = section.querySelector('.partners-container');
        if (container) container.remove();

        // Build two-column layout
        const grid = document.createElement('div');
        grid.className = 'partners-grid';

        grid.innerHTML = `
      <div class="partners-col" id="partners-national"></div>
      <div class="partners-divider"></div>
      <div class="partners-col" id="partners-intl"></div>
    `;

        section.appendChild(grid);

        buildPartnerColumn($('#partners-national'), national, 'National Partners', 'partnerSlide');
        buildPartnerColumn($('#partners-intl'), international, 'International Partners', 'partnerSlide2');
    }

    function buildPartnerColumn(col, imgs, label, animName) {
        if (!col) return;

        // Label
        const lbl = document.createElement('div');
        lbl.className = 'partners-col-label';
        lbl.textContent = label;
        col.appendChild(lbl);

        // Wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'partner-logos-track-wrapper';

        const track = document.createElement('div');
        track.className = 'partner-logos-track';
        track.style.animationName = animName;

        // Duplicate for seamless loop
        const allImgs = [...imgs, ...imgs.map(img => img.cloneNode(true))];
        allImgs.forEach(img => {
            const item = document.createElement('div');
            item.className = 'partner-logo-item';
            item.appendChild(img.cloneNode ? img : img);
            track.appendChild(item);
        });

        wrapper.appendChild(track);
        col.appendChild(wrapper);
    }

    /* ──────────────────────────────────────────
       7. COUNTER ANIMATION
    ────────────────────────────────────────── */
    function initCounters() {
        const counters = $$('.counter[data-target]');
        if (!counters.length) return;

        const animateCounter = el => {
            const target = parseInt(el.dataset.target, 10);
            const duration = 1800;
            const step = target / (duration / 16);
            let current = 0;
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = Math.floor(current);
                if (current >= target) {
                    el.textContent = target + (target >= 100 ? '+' : '');
                    clearInterval(timer);
                }
            }, 16);
        };

        const obs = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });

        counters.forEach(c => obs.observe(c));
    }

    /* ──────────────────────────────────────────
       8. SCROLL REVEAL
    ────────────────────────────────────────── */
    function initScrollReveal() {
        // Add reveal class to key elements
        const targets = [
            '.who-container > *',
            '.value-card',
            '.program-card',
            '.stat-box',
            '.section-header',
            '.map-container',
            '.who-content h2',
            '.partners-col',
        ];

        targets.forEach((sel, si) => {
            $$(sel).forEach((el, i) => {
                el.classList.add('reveal');
                if (i > 0 && i <= 4) el.classList.add(`reveal-delay-${i}`);
            });
        });

        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('in-view');
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });

        $$('.reveal').forEach(el => obs.observe(el));
    }

    /* ── NETWORK CANVAS (vision/mission) ── */
    function initNetworkCanvas() {
        const canvas = document.getElementById('networkCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let W, H, nodes;

        const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';

        const resize = () => {
            W = canvas.width = canvas.offsetWidth;
            H = canvas.height = canvas.offsetHeight;
            nodes = Array.from({ length: 48 }, () => ({
                x: Math.random() * W,
                y: Math.random() * H,
                vx: (Math.random() - 0.5) * 0.45,
                vy: (Math.random() - 0.5) * 0.45,
                r: Math.random() * 2.8 + 1.2,
            }));
        };

        const draw = () => {
            ctx.clearRect(0, 0, W, H);

            const dark = isDark();
            // Blue dots — deeper blue on light bg, lighter on dark bg
            const dotColor = dark ? 'rgba(62, 139, 228, 0.99)' : 'rgb(15, 99, 226)';
            const lineBase = dark ? '147,197,253' : '30,90,200';
            const lineMaxAlpha = dark ? 0.20 : 0.22;
            const connectDist = 160;

            nodes.forEach(n => {
                n.x += n.vx; n.y += n.vy;
                if (n.x < 0 || n.x > W) n.vx *= -1;
                if (n.y < 0 || n.y > H) n.vy *= -1;
                ctx.beginPath();
                ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
                ctx.fillStyle = dotColor;
                ctx.fill();
            });

            for (let i = 0; i < nodes.length; i++) {
                for (let j = i + 1; j < nodes.length; j++) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const d = Math.sqrt(dx * dx + dy * dy);
                    if (d < connectDist) {
                        const alpha = lineMaxAlpha * (1 - d / connectDist);
                        ctx.beginPath();
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                        ctx.strokeStyle = `rgba(${lineBase},${alpha})`;
                        ctx.lineWidth = 3;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(draw);
        };

        resize();
        window.addEventListener('resize', resize);
        draw();
    }

    /* ── STATS CANVAS (blue particles) ── */
    function initStatsCanvas() {
        const canvas = document.getElementById('statsCanvas');
        if (!canvas) return;
        const section = canvas.closest('.statistics');
        const container = section ? section.querySelector('.stats-container') : null;
        const ctx = canvas.getContext('2d');
        let W, H, pts;


        const palette = [
            { r: 184, g: 137, b: 42 },
            { r: 45, g: 90, b: 61 },
            { r: 192, g: 57, b: 43 },
            { r: 52, g: 152, b: 219 },
        ];

        const zoneCount = container ? container.children.length : palette.length;

        const resize = () => {
            W = canvas.width = canvas.offsetWidth;
            H = canvas.height = canvas.offsetHeight;

            // Map zones to the actual stats-container position within the canvas,
            // since the container is centered and narrower than the full section.
            let originX = 0;
            let spanW = W;
            if (container) {
                const cRect = container.getBoundingClientRect();
                const kRect = canvas.getBoundingClientRect();
                originX = cRect.left - kRect.left;
                spanW = cRect.width;
            }
            const zoneWidth = spanW / Math.max(zoneCount, 1);

            pts = [];
            for (let z = 0; z < zoneCount; z++) {
                const color = palette[z % palette.length];
                const zoneX = originX + z * zoneWidth;
                const count = 26;

                for (let i = 0; i < count; i++) {
                    pts.push({
                        x: zoneX + Math.random() * zoneWidth,
                        y: Math.random() * H,
                        baseX: zoneX + Math.random() * zoneWidth,
                        r: Math.random() * 3.2 + 1,
                        o: Math.random() * 0.30 + 0.10,
                        phase: Math.random() * Math.PI * 2,
                        speedY: -(Math.random() * 0.35 + 0.08),
                        driftAmp: Math.random() * 18 + 6,
                        driftSpeed: Math.random() * 0.012 + 0.004,
                        pulseSpeed: Math.random() * 0.02 + 0.008,
                        color,
                    });
                }
            }
        };

        let t = 0;
        const draw = () => {
            t += 1;
            ctx.clearRect(0, 0, W, H);

            pts.forEach(p => {
                // Drift upward, wrap around
                p.y += p.speedY;
                if (p.y < -10) {
                    p.y = H + 10;
                    p.baseX = p.x; // reset drift anchor on respawn
                }

                // Gentle horizontal sway (organic, not linear)
                p.x = p.baseX + Math.sin(t * p.driftSpeed + p.phase) * p.driftAmp;

                // Soft pulsing opacity for a living, breathing feel
                const pulse = (Math.sin(t * p.pulseSpeed + p.phase) + 1) / 2; // 0..1
                const alpha = p.o * (0.55 + pulse * 0.45);

                ctx.beginPath();
                ctx.ellipse(p.x, p.y, p.r, p.r * (0.8 + pulse * 0.3), 0, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${p.color.r},${p.color.g},${p.color.b},${alpha})`;
                ctx.fill();
            });

            requestAnimationFrame(draw);
        };

        resize();
        window.addEventListener('resize', resize);
        draw();
    }
    /* ──────────────────────────────────────────
       11. CUSTOM CURSOR (subtle dot)
    ────────────────────────────────────────── */
    function initCursor() {
        if (window.matchMedia('(pointer: coarse)').matches) return; // skip touch devices

        const dot = document.createElement('div');
        dot.style.cssText = `
      position:fixed; width:8px; height:8px; border-radius:50%;
      background:var(--gold); pointer-events:none; z-index:9999;
      top:0; left:0; transform:translate(-50%,-50%);
      transition:transform 0.15s ease, opacity 0.3s;
      mix-blend-mode:difference;
    `;

        const ring = document.createElement('div');
        ring.style.cssText = `
      position:fixed; width:32px; height:32px; border-radius:50%;
      border:1.5px solid rgba(184,137,42,0.55); pointer-events:none; z-index:9998;
      top:0; left:0; transform:translate(-50%,-50%);
      transition:top 0.12s ease, left 0.12s ease;
    `;

        document.body.appendChild(dot);
        document.body.appendChild(ring);

        let mx = 0, my = 0;

        document.addEventListener('mousemove', e => {
            mx = e.clientX; my = e.clientY;
            dot.style.top = my + 'px';
            dot.style.left = mx + 'px';
            ring.style.top = my + 'px';
            ring.style.left = mx + 'px';
        });

        document.addEventListener('mousedown', () => {
            dot.style.transform = 'translate(-50%,-50%) scale(1.8)';
            ring.style.transform = 'translate(-50%,-50%) scale(0.7)';
        });
        document.addEventListener('mouseup', () => {
            dot.style.transform = 'translate(-50%,-50%) scale(1)';
            ring.style.transform = 'translate(-50%,-50%) scale(1)';
        });
    }

    /* ──────────────────────────────────────────
       12. INIT ALL
    ────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        initTheme();
        initNavbar();
        initBackToTop();
        initHeroSlider();
        initAutoSliders();
        initPartners();
        initCounters();
        initScrollReveal();
        initNetworkCanvas();
        initStatsCanvas();
        initCursor();
    });

})();
