<?php
/**
 * Shared TinyMCE bootstrap — include once per page that uses components/rich_text_editor.
 */
?>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(() => {
  if (!window.tinymce) return;

  const isDark = document.documentElement.classList.contains('dark');

  document.querySelectorAll('textarea.js-rich-text').forEach((el) => {
    if (el.dataset.rteReady === '1') return;
    el.dataset.rteReady = '1';

    const height = parseInt(el.dataset.rteHeight || '220', 10) || 220;
    const maxChars = parseInt(el.dataset.rteMax || '2000', 10) || 2000;
    const placeholder = el.dataset.rtePlaceholder || '';

    tinymce.init({
      target: el,
      menubar: false,
      branding: false,
      promotion: false,
      height,
      placeholder,
      skin: isDark ? 'oxide-dark' : 'oxide',
      content_css: isDark ? 'dark' : 'default',
      plugins: 'lists link autolink charmap code',
      toolbar: 'undo redo | styles | bold italic underline strikethrough | forecolor | alignleft aligncenter alignright | bullist numlist | link | removeformat | code',
      style_formats: [
        { title: 'Paragraph', format: 'p' },
        { title: 'Heading', format: 'h3' },
        { title: 'Subheading', format: 'h4' },
      ],
      valid_elements: 'p,br,strong/b,em/i,u,s,ul,ol,li,span[style],a[href|target|rel|title],h3,h4,blockquote,div[style]',
      extended_valid_elements: 'span[style],p[style],div[style]',
      convert_urls: false,
      relative_urls: false,
      setup(editor) {
        editor.on('init', () => {
          // Soft limit on plain text length (HTML tags excluded from count).
          const enforce = () => {
            const text = editor.getContent({ format: 'text' }) || '';
            if (text.length > maxChars) {
              // Keep HTML but warn via status — avoid aggressive truncation mid-edit.
              editor.getContainer()?.classList.add('rte-over-limit');
            } else {
              editor.getContainer()?.classList.remove('rte-over-limit');
            }
          };
          editor.on('keyup change SetContent', enforce);
          enforce();
        });
      },
    });
  });

  // Ensure TinyMCE writes back before native form submit.
  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', () => {
      if (window.tinymce) window.tinymce.triggerSave();
    });
  });
})();
</script>
<style>
  .tox-tinymce { border-radius: 0.75rem !important; border-color: #e2e8f0 !important; }
  .dark .tox-tinymce { border-color: #334155 !important; }
  .tox.rte-over-limit { outline: 2px solid #f43f5e; }
  .wall-years-html p { margin: 0 0 0.35em; }
  .wall-years-html p:last-child { margin-bottom: 0; }
  .wall-years-html ul, .wall-years-html ol { margin: 0.25em 0 0.25em 1.1em; padding: 0; }
  .wall-years-html a { color: #059669; text-decoration: underline; }
  .wall-years-html h3, .wall-years-html h4 { font-weight: 800; margin: 0.2em 0; font-size: 1em; }
</style>
