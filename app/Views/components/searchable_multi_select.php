<?php
/**
 * Searchable multi-select (Alpine.js).
 * Posts selected values as name[] hidden inputs (selection order preserved).
 *
 * @var string $name  e.g. category_ids  (rendered as category_ids[])
 * @var array<int,array{id:int|string,label:string,label_alt?:string}> $options
 * @var list<int|string> $selected
 * @var bool $required
 * @var string $placeholder
 * @var string $searchPlaceholder
 * @var string $inputClass
 */
$name              = $name ?? 'category_ids';
$options           = $options ?? [];
$selected          = array_values(array_map('strval', $selected ?? []));
$required          = ! empty($required);
$placeholder       = $placeholder ?? 'Select…';
$searchPlaceholder = $searchPlaceholder ?? 'Search…';
$inputClass        = $inputClass ?? 'w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-medium';

$items = [];
foreach ($options as $opt) {
    $items[] = [
        'id'    => (string) ($opt['id'] ?? ''),
        'label' => (string) ($opt['label'] ?? ''),
        'alt'   => (string) ($opt['label_alt'] ?? ''),
    ];
}
$fieldName = rtrim($name, '[]') . '[]';
?>
<div class="relative"
     x-data='{
        open: false,
        q: "",
        selected: <?= json_encode($selected) ?>,
        placeholder: <?= json_encode($placeholder) ?>,
        items: <?= json_encode($items, JSON_UNESCAPED_UNICODE) ?>,
        get filtered() {
            const needle = this.q.trim().toLowerCase();
            if (!needle) return this.items;
            return this.items.filter(i =>
                (i.label || "").toLowerCase().includes(needle) ||
                (i.alt || "").toLowerCase().includes(needle)
            );
        },
        get selectedItems() {
            return this.selected
                .map(id => this.items.find(i => i.id === id))
                .filter(Boolean);
        },
        get triggerLabel() {
            if (this.selected.length === 0) return this.placeholder;
            if (this.selected.length === 1) {
                return this.selectedItems[0]?.label || this.placeholder;
            }
            return this.selected.length + " selected";
        },
        isSelected(id) {
            return this.selected.includes(id);
        },
        toggle(item) {
            if (this.isSelected(item.id)) {
                this.selected = this.selected.filter(id => id !== item.id);
            } else {
                this.selected = [...this.selected, item.id];
            }
            this.q = "";
        },
        remove(id) {
            this.selected = this.selected.filter(s => s !== id);
        }
     }'
     @keydown.escape.window="open = false">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="<?= esc($fieldName) ?>" :value="id">
    </template>
    <?php if ($required): ?>
        <input type="text" class="sr-only" tabindex="-1" :value="selected.length ? '1' : ''" required aria-hidden="true">
    <?php endif; ?>

    <button type="button"
            @click="open = !open; if (open) $nextTick(() => $refs.search && $refs.search.focus())"
            class="<?= esc($inputClass) ?> text-start flex items-center justify-between gap-2">
        <span class="truncate" :class="selected.length ? 'text-slate-900 dark:text-white' : 'text-slate-400'" x-text="triggerLabel"></span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0"></i>
    </button>

    <div x-show="selectedItems.length"
         class="mt-2 flex flex-wrap gap-1.5">
        <template x-for="item in selectedItems" :key="item.id">
            <span class="inline-flex items-center gap-1 max-w-full px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 text-[11px] font-bold">
                <span class="truncate" x-text="item.label"></span>
                <button type="button"
                        class="shrink-0 rounded px-0.5 leading-none text-emerald-700/70 hover:text-emerald-900 dark:hover:text-emerald-100"
                        @click.stop="remove(item.id)"
                        aria-label="Remove">&times;</button>
            </span>
        </template>
    </div>

    <div x-show="open"
         x-cloak
         @click.outside="open = false"
         class="absolute z-40 mt-1 inset-x-0 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg overflow-hidden">
        <div class="p-2 border-b border-slate-100 dark:border-slate-800">
            <input type="text"
                   x-ref="search"
                   x-model="q"
                   placeholder="<?= esc($searchPlaceholder) ?>"
                   class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs outline-none focus:border-emerald-500">
        </div>
        <div class="max-h-56 overflow-y-auto py-1">
            <template x-if="filtered.length === 0">
                <p class="px-3 py-2 text-xs text-slate-400"><?= esc(lang('App.no_results') ?: 'No results') ?></p>
            </template>
            <template x-for="item in filtered" :key="item.id">
                <button type="button"
                        class="w-full text-start px-3 py-2 text-xs flex items-center gap-2 hover:bg-emerald-50 dark:hover:bg-emerald-950/40"
                        :class="isSelected(item.id) ? 'bg-emerald-50 dark:bg-emerald-950/40 font-bold text-emerald-700 dark:text-emerald-300' : 'text-slate-700 dark:text-slate-200'"
                        @click="toggle(item)">
                    <span class="w-4 h-4 rounded border flex items-center justify-center shrink-0"
                          :class="isSelected(item.id) ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 dark:border-slate-600'">
                        <span x-show="isSelected(item.id)">✓</span>
                    </span>
                    <span x-text="item.label"></span>
                </button>
            </template>
        </div>
    </div>
</div>
