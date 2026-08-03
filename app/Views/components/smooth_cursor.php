<?php
/**
 * Magic UI Smooth Cursor Component
 * 
 * Includes:
 * 1. Hardware accelerated smooth lerp cursor dot & trailing ring.
 * 2. Theme-aware colors (Emerald/Sky in Light Mode, Emerald/Cyan in Dark Mode).
 * 3. Automatic touch device detection (disabled on phones/tablets).
 * 4. Automatic prefers-reduced-motion bypass.
 */
?>
<div id="magic-smooth-cursor"
     class="hidden md:block pointer-events-none fixed inset-0 z-50 overflow-hidden"
     x-data="{
        mouseX: -100,
        mouseY: -100,
        ringX: -100,
        ringY: -100,
        isHovered: false,
        isClicking: false,
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if ('ontouchstart' in window || navigator.maxTouchPoints > 0) return;

            window.addEventListener('mousemove', (e) => {
                this.mouseX = e.clientX;
                this.mouseY = e.clientY;
            });

            window.addEventListener('mousedown', () => this.isClicking = true);
            window.addEventListener('mouseup', () => this.isClicking = false);

            document.addEventListener('mouseover', (e) => {
                if (e.target.closest('a, button, input, select, textarea, [role=button]')) {
                    this.isHovered = true;
                } else {
                    this.isHovered = false;
                }
            });

            this.animateRing();
        },
        animateRing() {
            // Smooth linear interpolation (lerp) for springy trailing ring
            const lerp = (start, end, factor) => start + (end - start) * factor;
            const loop = () => {
                this.ringX = lerp(this.ringX, this.mouseX, 0.18);
                this.ringY = lerp(this.ringY, this.mouseY, 0.18);
                requestAnimationFrame(loop);
            };
            requestAnimationFrame(loop);
        }
     }">

    <!-- Inner Core Cursor Dot -->
    <div class="fixed top-0 left-0 w-2.5 h-2.5 rounded-full bg-emerald-600 dark:bg-emerald-400 pointer-events-none transition-transform duration-75 ease-out shadow-xs z-50"
         :style="`transform: translate3d(${mouseX - 5}px, ${mouseY - 5}px, 0) scale(${isClicking ? 0.7 : isHovered ? 1.5 : 1});`">
    </div>

    <!-- Outer Smooth Trailing Ring (Magic UI SmoothCursor Effect) -->
    <div class="fixed top-0 left-0 w-8 h-8 rounded-full border border-emerald-500/60 dark:border-cyan-400/60 bg-emerald-500/10 dark:bg-cyan-400/10 pointer-events-none transition-all duration-300 ease-out z-40"
         :style="`transform: translate3d(${ringX - 16}px, ${ringY - 16}px, 0) scale(${isClicking ? 0.8 : isHovered ? 1.8 : 1});`"
         :class="isHovered ? 'border-emerald-600 dark:border-cyan-300 bg-emerald-500/20 dark:bg-cyan-400/20' : ''">
    </div>
</div>
