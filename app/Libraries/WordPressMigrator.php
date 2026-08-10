<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Idempotently synchronises only the legacy ATBD directory records.
 *
 * The source connection is deliberately separate from the application DB.  It
 * must point at a database restored from the supplied SQL dump; it is never
 * written to by this class.
 */
class WordPressMigrator
{
    private BaseConnection $db;
    private BaseConnection $source;
    private string $uploadsSource;
    private string $uploadsTarget;
    private array $stats;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $config = config('Database')->default;
        // kts_temp_migration is a correct restore of wordpress-35303337d41d.sql with
        // real Urdu intact (kts_migration_source was imported with a broken charset
        // and its text is corrupted to '?').
        $config['database'] = getenv('MIGRATION_SOURCE_DATABASE') ?: 'kts_temp_migration';
        $config['DBDebug'] = true;
        $this->source = \Config\Database::connect($config, false);
        $this->uploadsSource = ROOTPATH . 'kts data base/kts web data base pics/public_html/kts/wp-content/uploads/';
        $this->uploadsTarget = FCPATH . 'uploads/businesses/';
        $this->stats = array_fill_keys([
            'businesses_found_old', 'businesses_current', 'businesses_merged', 'businesses_new', 'businesses_skipped',
            'categories_imported', 'areas_imported', 'villages_imported', 'tags_imported', 'images_referenced',
            'images_linked', 'images_missing_file', 'images_missing_ref', 'businesses_english', 'businesses_urdu',
            'businesses_bilingual', 'rows_updated', 'rows_inserted'
        ], 0);
        $this->stats['errors'] = [];
    }

    public function migrate(): array
    {
        $this->assertSource();
        $this->stats['businesses_current'] = $this->db->table('businesses')->countAllResults();
        $listings = $this->source->table('9b_posts')->where('post_type', 'at_biz_dir')->get()->getResultArray();
        $this->stats['businesses_found_old'] = count($listings);

        foreach ($listings as $listing) {
            try {
                $this->syncListing($listing);
            } catch (\Throwable $e) {
                $this->stats['errors'][] = 'Post ' . $listing['ID'] . ': ' . $e->getMessage();
            }
        }
        return $this->stats;
    }

    private function assertSource(): void
    {
        foreach (['9b_posts', '9b_postmeta', '9b_terms', '9b_term_taxonomy', '9b_term_relationships'] as $table) {
            if (!$this->source->tableExists($table)) {
                throw new \RuntimeException("Migration source table {$table} is unavailable.");
            }
        }
        if (!is_dir($this->uploadsSource)) {
            throw new \RuntimeException('Legacy uploads directory is unavailable.');
        }
    }

    private function syncListing(array $post): void
    {
        $meta = $this->metadata((int) $post['ID']);
        $terms = $this->terms((int) $post['ID']);
        $title = $this->sourceText($post['post_title'], $post['post_name']);
        [$nameEn, $nameUr] = $this->languageValues($title);
        [$descriptionEn, $descriptionUr] = $this->languageValues($post['post_content']);
        [$addressEn, $addressUr] = $this->languageValues($meta['_address'] ?? '');
        $existing = $this->findExisting((int) $post['ID'], $meta['_phone'] ?? '', $nameEn, $nameUr);
        $categoryId = $this->categoryId($terms['at_biz_dir-category'] ?? null);
        if (!$categoryId && !$existing) {
            // Never invent a category merely to satisfy the target FK.
            $this->stats['businesses_skipped']++;
            return;
        }
        $villageId = $this->villageId($terms['at_biz_dir-location'] ?? null);
        $image = $this->imageFor($meta['_thumbnail_id'] ?? $meta['_listing_prv_img'] ?? null);
        $incoming = [
            'category_id' => $categoryId, 'village_id' => $villageId,
            'name_en' => $nameEn, 'name_ur' => $nameUr, 'description_en' => $descriptionEn, 'description_ur' => $descriptionUr,
            'address_en' => $addressEn, 'address_ur' => $addressUr, 'address' => $addressEn ?: $addressUr,
            'phone' => $meta['_phone'] ?? '', 'image' => $image, 'source_post_id' => $post['ID'],
            'slug' => $post['post_name'] ?: $this->slug($title, (int) $post['ID']),
            'featured' => empty($meta['_featured']) ? 0 : 1,
            'status' => $post['post_status'] === 'publish' ? 'active' : 'pending',
        ];
        if ($existing) {
            $updates = $this->emptyOnly($existing, $incoming);
            if ($updates) {
                $this->db->table('businesses')->where('id', $existing['id'])->update($updates);
                $this->stats['businesses_merged']++;
                $this->stats['rows_updated']++;
            } else {
                $this->stats['businesses_skipped']++;
            }
        } else {
            $this->db->table('businesses')->insert($incoming);
            $this->stats['businesses_new']++;
            $this->stats['rows_inserted']++;
        }
        if ($nameEn) $this->stats['businesses_english']++;
        if ($nameUr) $this->stats['businesses_urdu']++;
        if ($nameEn && $nameUr) $this->stats['businesses_bilingual']++;
    }

    private function metadata(int $postId): array
    {
        $rows = $this->source->table('9b_postmeta')->where('post_id', $postId)->get()->getResultArray();
        return array_column($rows, 'meta_value', 'meta_key');
    }

    /** Source IDs win; otherwise only a uniquely matching legacy phone may merge records. */
    private function findExisting(int $sourceId, string $phone, string $nameEn, string $nameUr): ?array
    {
        $row = $this->db->table('businesses')->where('source_post_id', $sourceId)->get()->getRowArray();
        if ($row) return $row;
        if ($phone !== '') {
            $matches = $this->db->table('businesses')->where('phone', $phone)->get()->getResultArray();
            if (count($matches) === 1) return $matches[0];
        }
        if ($nameEn !== '') return $this->db->table('businesses')->where('name_en', $nameEn)->get()->getRowArray();
        if ($nameUr !== '') return $this->db->table('businesses')->where('name_ur', $nameUr)->get()->getRowArray();
        return null;
    }

    private function terms(int $postId): array
    {
        $rows = $this->source->table('9b_term_relationships tr')->select('tt.taxonomy,t.name,t.slug')
            ->join('9b_term_taxonomy tt', 'tt.term_taxonomy_id = tr.term_taxonomy_id')
            ->join('9b_terms t', 't.term_id = tt.term_id')->where('tr.object_id', $postId)->get()->getResultArray();
        return array_column($rows, null, 'taxonomy');
    }

    private function categoryId(?array $term): ?int
    {
        if (!$term) return null;
        [$en, $ur] = $this->languageValues($this->sourceText($term['name'], $term['slug']));
        $row = $this->db->table('categories')->groupStart()->where('name_en', $en)->orWhere('name_ur', $ur)->groupEnd()->get()->getRowArray();
        return $row ? (int) $row['id'] : null;
    }

    private function villageId(?array $term): ?int
    {
        if (!$term || !$this->db->tableExists('villages')) return null;
        [$en, $ur] = $this->languageValues($this->sourceText($term['name'], $term['slug']));
        $row = $this->db->table('villages')->groupStart()->where('name_en', $en)->orWhere('name_ur', $ur)->groupEnd()->get()->getRowArray();
        if ($row) return (int) $row['id'];
        $this->db->table('villages')->insert(['name_en' => $en, 'name_ur' => $ur, 'slug' => $term['slug'] ?: $this->slug($term['name'], 0)]);
        $this->stats['villages_imported']++; $this->stats['rows_inserted']++;
        return (int) $this->db->insertID();
    }

    private function imageFor(?string $attachmentId): string
    {
        if (!$attachmentId) { $this->stats['images_missing_ref']++; return ''; }
        $this->stats['images_referenced']++;
        $file = $this->source->table('9b_postmeta')->select('meta_value')->where(['post_id' => $attachmentId, 'meta_key' => '_wp_attached_file'])->get()->getRow('meta_value');
        if (!$file) { $this->stats['images_missing_ref']++; return ''; }
        $file = ltrim(str_replace('\\', '/', $file), '/');
        $source = $this->uploadsSource . $file;
        if (!is_file($source)) { $this->stats['images_missing_file']++; return ''; }
        $target = $this->uploadsTarget . $file;
        if (!is_dir(dirname($target))) mkdir(dirname($target), 0755, true);
        if (!is_file($target) && !copy($source, $target)) throw new \RuntimeException("Could not copy listing image {$file}");
        $this->stats['images_linked']++;
        return 'uploads/businesses/' . $file;
    }

    private function languageValues(?string $value): array
    {
        $value = trim((string) $value);
        return preg_match('/[\x{0600}-\x{06FF}]/u', $value) ? ['', $value] : [$value, ''];
    }

    /** Uses the actual URL-encoded WP slug only where the dump lost its text to question marks. */
    private function sourceText(?string $value, ?string $slug): string
    {
        $value = trim((string) $value);
        if ($value !== '' && !preg_match('/^\?+(?:\s+\?+)*$/u', $value)) return $value;
        $decoded = rawurldecode((string) $slug);
        return $decoded !== '' ? $decoded : $value;
    }

    private function emptyOnly(array $existing, array $incoming): array
    {
        $updates = [];
        foreach ($incoming as $key => $value) {
            if ($key === 'category_id' && !$value) continue;
            if ($value !== '' && $value !== null && (!isset($existing[$key]) || $existing[$key] === '' || $existing[$key] === null)) $updates[$key] = $value;
        }
        return $updates;
    }

    private function slug(string $value, int $id): string
    {
        $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $value)), '-');
        return $slug ?: 'legacy-listing-' . $id;
    }

    public function generateReport(): string { return json_encode($this->stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); }
}
