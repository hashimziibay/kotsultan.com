<?php
/**
 * Custom Dropdown / Select Component using Alpine.js and Tailwind CSS
 * 
 * @param string $name         The name attribute for the hidden input
 * @param array  $options      Array of options [['value' => '', 'label' => '', 'icon' => '']]
 * @param string $selected     The currently selected value
 * @param string $placeholder  The placeholder text
 * @param bool   $searchable   Whether the dropdown should include a search input
 * @param string $class        Extra CSS classes for the container button
 */

$name       = $name ?? 'select';
$options    = $options ?? [];
$selected   = $selected ?? '';
$placeholder= $placeholder ?? (lang('App.select_option') !== 'App.select_option' ? lang('App.select_option') : 'Select an option');
$searchable = $searchable ?? false;
$class      = $class ?? '';

// Ensure options are properly formatted for JSON
$formattedOptions = array_values($options); // Re-index array for JSON array []
$optionsJson = json_encode($formattedOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<div class="relative w-full"
     x-data="{
        open: false,
        options: <?= htmlspecialchars($optionsJson, ENT_QUOTES, 'UTF-8') ?>,
        selected: '<?= esc($selected) ?>',
        searchable: <?= $searchable ? 'true' : 'false' ?>,
        search: '',
        highlightedIndex: -1,

        get filteredOptions() {
            if (this.search === '') return this.options;
            const query = this.search.toLowerCase();
            return this.options.filter(opt => opt.label.toLowerCase().includes(query));
        },

        get selectedOption() {
            return this.options.find(opt => String(opt.value) === String(this.selected));
        },

        toggle() {
            if (this.open) {
                this.close();
            } else {
                this.open = true;
                this.search = '';
                if (this.searchable) {
                    this.$nextTick(() => { this.$refs.searchInput.focus(); });
                }
                const selIndex = this.filteredOptions.findIndex(opt => String(opt.value) === String(this.selected));
                this.highlightedIndex = selIndex >= 0 ? selIndex : 0;
                this.scrollToHighlighted();
                this.updateIcons();
            }
        },

        close() {
            this.open = false;
            this.search = '';
            this.highlightedIndex = -1;
        },

        select(val) {
            this.selected = val;
            this.close();
        },

        selectHighlighted() {
            if (this.open && this.filteredOptions.length > 0 && this.highlightedIndex >= 0) {
                this.select(this.filteredOptions[this.highlightedIndex].value);
            } else if (this.open) {
                this.close();
            } else {
                this.toggle();
            }
        },

        navigate(step) {
            if (!this.open) {
                this.toggle();
                return;
            }
            const count = this.filteredOptions.length;
            if (count === 0) return;
            this.highlightedIndex += step;
            if (this.highlightedIndex < 0) this.highlightedIndex = count - 1;
            else if (this.highlightedIndex >= count) this.highlightedIndex = 0;
            this.scrollToHighlighted();
        },
        
        scrollToHighlighted() {
            this.$nextTick(() => {
                const listbox = this.$refs.listbox;
                if (!listbox) return;
                const items = listbox.querySelectorAll('li[role=\'option\']');
                if (items[this.highlightedIndex]) {
                    items[this.highlightedIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            });
        },

        updateIcons() {
            this.$nextTick(() => { 
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons({ root: this.$el });
                }
            });
        },
        
        init() {
            this.$watch('filteredOptions', () => this.updateIcons());
            this.$watch('selected', () => this.updateIcons());
            // Initial icon render
            this.updateIcons();
        }
     }"
     @click.away="close()"
     @keydown.escape.prevent="close()"
     @keydown.arrow-down.prevent="navigate(1)"
     @keydown.arrow-up.prevent="navigate(-1)"
     @keydown.enter.prevent="selectHighlighted()">
    
    <!-- Hidden input for standard form submission -->
    <input type="hidden" name="<?= esc($name) ?>" :value="selected">

    <!-- Select Button -->
    <button type="button"
            @click="toggle()"
            class="w-full flex items-center justify-between px-4 h-[54px] bg-slate-50 hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white text-base leading-normal font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm cursor-pointer <?= esc($class) ?>"
            :class="open ? 'ring-2 ring-emerald-500/20 border-emerald-500 bg-white dark:bg-slate-800' : ''"
            aria-haspopup="listbox"
            :aria-expanded="open">
        
        <div class="flex items-center gap-2.5 truncate">
            <template x-if="selectedOption && selectedOption.icon">
                <i :data-lucide="selectedOption.icon" class="w-[20px] h-[20px] text-emerald-600 dark:text-emerald-500 flex-shrink-0"></i>
            </template>
            <span class="truncate" x-text="selectedOption ? selectedOption.label : '<?= esc($placeholder) ?>'"></span>
        </div>

        <i data-lucide="chevron-down" 
           class="w-[20px] h-[20px] text-slate-400 transition-transform duration-200 flex-shrink-0"
           :class="open ? 'rotate-180 text-emerald-500' : ''"></i>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-50 w-full mt-2 bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl overflow-hidden flex flex-col"
         style="display: none;"
         role="listbox">
        
        <!-- Search Input -->
        <?php if ($searchable): ?>
        <div class="p-1.5 border-b border-slate-100 dark:border-slate-700/50 relative">
            <i data-lucide="search" class="w-[20px] h-[20px] text-slate-400 absolute left-3.5 rtl:right-3.5 rtl:left-auto top-1/2 -translate-y-1/2"></i>
            <input type="text"
                   x-ref="searchInput"
                   x-model="search"
                   @keydown.arrow-down.stop.prevent="navigate(1)"
                   @keydown.arrow-up.stop.prevent="navigate(-1)"
                   @keydown.enter.stop.prevent="selectHighlighted()"
                   class="w-full pl-10 pr-3 rtl:pr-10 rtl:pl-3 h-12 bg-slate-50 dark:bg-slate-900/50 border border-transparent rounded-xl text-base leading-normal text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 transition-colors"
                   placeholder="<?= lang('App.search_placeholder') ?? 'Search...' ?>"
                   autocomplete="off">
        </div>
        <?php endif; ?>

        <!-- Options List -->
        <ul class="max-h-[360px] overflow-y-auto p-1.5 custom-scrollbar" x-ref="listbox">
            
            <template x-if="filteredOptions.length === 0">
                <li class="py-4 px-4 text-center text-sm text-slate-500 dark:text-slate-400">
                    <?= lang('App.no_results_found') ?? 'No results found' ?>
                </li>
            </template>

            <template x-for="(option, index) in filteredOptions" :key="option.value">
                <li @click="select(option.value)"
                    @mousemove="highlightedIndex = index"
                    :class="{
                        'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-900 dark:text-emerald-100': highlightedIndex === index || String(selected) === String(option.value),
                        'text-slate-700 dark:text-slate-300': highlightedIndex !== index && String(selected) !== String(option.value)
                    }"
                    class="flex items-center justify-between px-3.5 min-h-[46px] rounded-xl cursor-pointer text-base leading-normal transition-colors group"
                    role="option"
                    :aria-selected="String(selected) === String(option.value)">
                    
                    <div class="flex items-center gap-2.5">
                        <template x-if="option.icon">
                            <i :data-lucide="option.icon" 
                               class="w-[20px] h-[20px] flex-shrink-0"
                               :class="String(selected) === String(option.value) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 group-hover:text-emerald-500 transition-colors'"></i>
                        </template>
                        <span x-text="option.label" class="font-medium" :class="String(selected) === String(option.value) ? 'font-bold' : ''"></span>
                    </div>

                    <!-- Check Icon for selected item -->
                    <template x-if="String(selected) === String(option.value)">
                        <i data-lucide="check" class="w-[20px] h-[20px] text-emerald-600 dark:text-emerald-400 flex-shrink-0"></i>
                    </template>
                </li>
            </template>
        </ul>
    </div>
</div>
