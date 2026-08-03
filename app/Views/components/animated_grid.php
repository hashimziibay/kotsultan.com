<?php
/**
 * Global Magic UI Style Animated Grid Background Component
 * 
 * Includes:
 * 1. Crisp SVG Grid Lines with Radial Fade Mask
 * 2. Alpine.js Organic Random Square Cell Animations (Soft Blue/Green in Light Mode, Soft Cyan/Emerald in Dark Mode)
 * 3. GPU Accelerated, Zero Layout Reflows
 * 4. Automatic Motion-Reduction Bypass
 */
?>
<div id="global-animated-grid" 
     class="fixed inset-0 pointer-events-none z-0 overflow-hidden opacity-80 dark:opacity-60 transition-opacity duration-300"
     x-data="{
        squares: [],
        cols: 16,
        rows: 12,
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this.generateGrid();
            this.startCellAnimation();
        },
        generateGrid() {
            const arr = [];
            const count = (window.innerWidth < 768) ? 4 : 8; // Reduce density for mobile performance
            for (let i = 0; i < count; i++) {
                arr.push({
                    id: i,
                    col: Math.floor(Math.random() * this.cols),
                    row: Math.floor(Math.random() * this.rows),
                    opacity: 0,
                    active: false
                });
            }
            this.squares = arr;
        },
        startCellAnimation() {
            setInterval(() => {
                if (!this.squares.length) return;
                const randomIndex = Math.floor(Math.random() * this.squares.length);
                const sq = this.squares[randomIndex];
                sq.col = Math.floor(Math.random() * this.cols);
                sq.row = Math.floor(Math.random() * this.rows);
                sq.active = true;
                setTimeout(() => {
                    sq.active = false;
                }, 2500);
            }, 1200);
        }
     }">

    <!-- SVG Square Grid with Central Radial Fade Mask -->
    <svg class="w-full h-full text-slate-300/80 dark:text-slate-700/60" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="global-grid-pattern" width="48" height="48" patternUnits="userSpaceOnUse">
                <path d="M 48 0 L 0 0 0 48" fill="none" stroke="currentColor" stroke-width="1" opacity="0.6" />
            </pattern>
            <radialGradient id="global-grid-fade" cx="50%" cy="40%" r="70%">
                <stop offset="0%" stop-color="#fff" stop-opacity="1" />
                <stop offset="60%" stop-color="#fff" stop-opacity="0.6" />
                <stop offset="100%" stop-color="#fff" stop-opacity="0.05" />
            </radialGradient>
            <mask id="global-grid-mask">
                <rect width="100%" height="100%" fill="url(#global-grid-fade)" />
            </mask>
        </defs>
        <rect width="100%" height="100%" fill="url(#global-grid-pattern)" mask="url(#global-grid-mask)" />
    </svg>

    <!-- Animated Highlighting Grid Cells (Magic UI Style Organic Movement) -->
    <div class="absolute inset-0 grid grid-cols-12 md:grid-cols-16 grid-rows-12 gap-0 max-w-7xl mx-auto p-4 opacity-75">
        <template x-for="sq in squares" :key="sq.id">
            <div class="w-full h-full transition-all duration-1000 ease-in-out rounded-sm"
                 :style="`grid-column-start: ${sq.col + 1}; grid-row-start: ${sq.row + 1};`"
                 :class="sq.active 
                    ? 'bg-gradient-to-br from-emerald-400/20 to-sky-400/20 dark:from-emerald-500/25 dark:to-cyan-400/25 border border-emerald-400/30 dark:border-cyan-400/30 opacity-100 scale-100' 
                    : 'opacity-0 scale-95'"
            ></div>
        </template>
    </div>
</div>
