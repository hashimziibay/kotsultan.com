<?php
/**
 * Lightweight Alpine.js BorderGlow Component Snippet
 * 
 * Replaces heavy React hooks (useRef, useEffect, useCallback) with a clean,
 * hardware-accelerated Alpine.js directive tracking mouse proximity (--x, --y).
 * Designed for low-end mobile devices & fast performance.
 * 
 * Usage in CodeIgniter Views:
 * <?= $this->include('components/border_glow', ['slot' => '...html...']) ?>
 */
?>
<div x-data="{
        x: 50,
        y: 50,
        isHovered: false,
        onMouseMove(e) {
            const rect = $el.getBoundingClientRect();
            this.x = ((e.clientX - rect.left) / rect.width) * 100;
            this.y = ((e.clientY - rect.top) / rect.height) * 100;
        }
     }"
     @mousemove="onMouseMove($event)"
     @mouseenter="isHovered = true"
     @mouseleave="isHovered = false"
     class="relative p-[1.5px] rounded-xl overflow-hidden transition-all duration-200 group"
     :style="`--x: ${x}%; --y: ${y}%;`">

    <!-- Lightweight Proximity Glow Layer (CSS Radial Gradient driven by Alpine.js) -->
    <div class="absolute inset-0 rounded-xl pointer-events-none transition-opacity duration-300"
         :class="isHovered ? 'opacity-100' : 'opacity-0'"
         :style="`background: radial-gradient(300px circle at var(--x) var(--y), rgba(5, 150, 105, 0.4), transparent 80%);`"></div>

    <!-- Fallback Border for Non-Hover State -->
    <div class="absolute inset-0 rounded-xl border border-slate-300 dark:border-slate-700 pointer-events-none transition-opacity duration-300"
         :class="isHovered ? 'opacity-0' : 'opacity-100'"></div>

    <!-- Inner Content Wrapper -->
    <div class="relative bg-white dark:bg-slate-800 rounded-[10px] z-10 w-full h-full">
        <?= $slot ?? '' ?>
    </div>
</div>
