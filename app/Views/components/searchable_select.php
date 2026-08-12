<?php
/**
 * Searchable select (Alpine.js).
 *
 * @var string $name
 * @var array<int,array{id:int|string,label:string,label_alt?:string}> $options
 * @var string|int|null $selected
 * @var bool $required
 * @var string $placeholder
 * @var string $searchPlaceholder
 * @var string $inputClass
 * @var string $extraInputAttrs
 */
$name              = $name ?? 'category_id';
$options           = $options ?? [];
$selected          = $selected ?? '';
$required          = ! empty($required);
$placeholder       = $placeholder ?? 'Select…';
$searchPlaceholder = $searchPlaceholder ?? 'Search…';
$inputClass        = $inputClass ?? 'w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-medium';
$extraInputAttrs   = $extraInputAttrs ?? '';

$items = [];
foreach ($options as $opt) {
    $items[] = [
        'id'    => (string) ($opt['id'] ?? ''),
        'label' => (string) ($opt['label'] ?? ''),
        'alt'   => (string) ($opt['label_alt'] ?? ''),
    ];
}
$selectedLabel = $placeholder;
foreach ($items as $item) {
    if ($item['id'] !== '' && (string) $selected === $item['id']) {
        $selectedLabel = $item['label'];
        break;
    }
}
?>
<div class="relative"
     x-data='{
        open: false,
        q: "",
        selected: <?= json_encode((string) $selected) ?>,
        label: <?= json_encode($selectedLabel) ?>,
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
        pick(item) {
            this.selected = item.id;
            this.label = item.label;
            this.open = false;
            this.q = "";
        }
     }'
     @keydown.escape.window="open = false">
    <input type="hidden" name="<?= esc($name) ?>" :value="selected" <?= $required ? 'required data-biz-field' : '' ?> <?= $extraInputAttrs ?>>
    <button type="button"
            @click="open = !open; if (open) $nextTick(() => $refs.search && $refs.search.focus())"
            class="<?= esc($inputClass) ?> text-start flex items-center justify-between gap-2">
        <span class="truncate" :class="selected ? 'text-slate-900 dark:text-white' : 'text-slate-400'" x-text="label"></span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0"></i>
    </button>
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
                        class="w-full text-start px-3 py-2 text-xs hover:bg-emerald-50 dark:hover:bg-emerald-950/40"
                        :class="selected === item.id ? 'bg-emerald-50 dark:bg-emerald-950/40 font-bold text-emerald-700' : 'text-slate-700 dark:text-slate-200'"
                        @click="pick(item)"
                        x-text="item.label"></button>
            </template>
        </div>
    </div>
</div>
