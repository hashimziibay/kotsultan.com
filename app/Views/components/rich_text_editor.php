<?php
/**
 * TinyMCE rich-text field (years of service / short HTML).
 *
 * @var string $name
 * @var string $value
 * @var string $label
 * @var string $hint
 * @var string $placeholder
 * @var string $textareaClass
 * @var int    $height
 * @var int    $maxChars  approximate text limit (tags may add more)
 */
$name           = $name ?? 'years_of_service';
$value          = (string) ($value ?? '');
$label          = $label ?? '';
$hint           = $hint ?? '';
$placeholder    = $placeholder ?? '';
$textareaClass  = $textareaClass ?? 'w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium';
$height         = (int) ($height ?? 220);
$maxChars       = (int) ($maxChars ?? 2000);
$editorId       = 'rte_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . substr(md5($name . mt_rand()), 0, 6);
?>
<div class="space-y-1">
    <?php if ($label !== ''): ?>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1" for="<?= esc($editorId) ?>">
            <?= esc($label) ?>
        </label>
    <?php endif; ?>
    <textarea id="<?= esc($editorId) ?>" name="<?= esc($name) ?>"
              class="<?= esc($textareaClass) ?> js-rich-text"
              data-rte-height="<?= $height ?>"
              data-rte-max="<?= $maxChars ?>"
              data-rte-placeholder="<?= esc($placeholder) ?>"><?= esc($value) ?></textarea>
    <?php if ($hint !== ''): ?>
        <p class="mt-1 text-[10px] text-slate-400"><?= esc($hint) ?></p>
    <?php endif; ?>
</div>
