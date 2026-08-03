/**
 * Click Spark Effect - Lightweight HTML5 Canvas Particle Engine
 * Renders high-performance spark bursts on mouse clicks & mobile taps.
 */
(function() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    let canvas, ctx;
    let sparks = [];
    let isAnimating = false;

    function initCanvas() {
        if (!document.body) return;
        canvas = document.createElement('canvas');
        canvas.id = 'click-spark-canvas';
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100vw';
        canvas.style.height = '100vh';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '999999';
        document.body.appendChild(canvas);

        ctx = canvas.getContext('2d');
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas, { passive: true });
    }

    function resizeCanvas() {
        if (!canvas || !ctx) return;
        const dpr = window.devicePixelRatio || 1;
        canvas.width = window.innerWidth * dpr;
        canvas.height = window.innerHeight * dpr;
        ctx.scale(dpr, dpr);
    }

    function createSparkBurst(x, y) {
        const count = 8;
        const isDark = document.documentElement.classList.contains('dark');
        const color = isDark ? '#38BDF8' : '#059669'; // Bright Cyan in Dark Mode, Emerald in Light Mode

        for (let i = 0; i < count; i++) {
            const angle = (i * (2 * Math.PI)) / count;
            sparks.push({
                x: x,
                y: y,
                angle: angle,
                distance: 0,
                maxDistance: 18 + Math.random() * 8,
                length: 6 + Math.random() * 4,
                life: 1.0,
                speed: 1.2 + Math.random() * 0.4,
                color: color
            });
        }

        if (!isAnimating) {
            isAnimating = true;
            requestAnimationFrame(animate);
        }
    }

    function animate() {
        if (!ctx || !canvas) return;

        ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);

        for (let i = sparks.length - 1; i >= 0; i--) {
            const s = sparks[i];

            s.distance += s.speed * 2.5;
            s.life -= 0.045; // Fades in ~350ms

            if (s.life <= 0 || s.distance >= s.maxDistance) {
                sparks.splice(i, 1);
                continue;
            }

            const startX = s.x + Math.cos(s.angle) * s.distance;
            const startY = s.y + Math.sin(s.angle) * s.distance;
            const endX = s.x + Math.cos(s.angle) * (s.distance + s.length);
            const endY = s.y + Math.sin(s.angle) * (s.distance + s.length);

            ctx.beginPath();
            ctx.moveTo(startX, startY);
            ctx.lineTo(endX, endY);
            ctx.strokeStyle = s.color;
            ctx.globalAlpha = Math.max(0, s.life);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();
            ctx.globalAlpha = 1.0;
        }

        if (sparks.length > 0) {
            requestAnimationFrame(animate);
        } else {
            isAnimating = false;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initCanvas();
            window.addEventListener('pointerdown', (e) => {
                if (e.clientX !== undefined && e.clientY !== undefined) {
                    createSparkBurst(e.clientX, e.clientY);
                }
            }, { passive: true });
        });
    } else {
        initCanvas();
        window.addEventListener('pointerdown', (e) => {
            if (e.clientX !== undefined && e.clientY !== undefined) {
                createSparkBurst(e.clientX, e.clientY);
            }
        }, { passive: true });
    }
})();
