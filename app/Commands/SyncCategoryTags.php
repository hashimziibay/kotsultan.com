<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Create bilingual tags from every category (+ synonyms) and link them
 * to businesses in that category so directory search suggestions work.
 *
 * Synonym packs: App\Libraries\SearchSynonyms (shared with fuzzy search).
 *
 * Usage: php spark tags:sync-categories
 */
class SyncCategoryTags extends BaseCommand
{
    protected $group       = 'Tags';
    protected $name        = 'tags:sync-categories';
    protected $description = 'Sync English/Urdu tags from categories and attach them to businesses.';
    protected $usage       = 'tags:sync-categories';

    public function run(array $params)
    {
        helper('seo');
        $db = Database::connect();

        $categories = $db->table('categories')
            ->orderBy('display_order', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->get()
            ->getResultArray();

        if ($categories === []) {
            CLI::error('No categories found.');
            return;
        }

        $synonymGroups = \App\Libraries\SearchSynonyms::forTagSync();
        $tagsCreated   = 0;
        $tagsReused    = 0;
        $linksCreated  = 0;
        $now           = date('Y-m-d H:i:s');

        foreach ($categories as $cat) {
            $catId    = (int) $cat['id'];
            $nameEn   = trim((string) ($cat['name_en'] ?? ''));
            $nameUr   = trim((string) ($cat['name_ur'] ?? ''));
            $slug     = trim((string) ($cat['slug'] ?? ''));
            $haystack = mb_strtolower($nameEn . ' ' . $nameUr . ' ' . $slug, 'UTF-8');

            $tagDefs = [];
            if ($nameEn !== '' || $nameUr !== '') {
                $tagDefs[] = [
                    'name_en' => $nameEn !== '' ? $nameEn : $nameUr,
                    'name_ur' => $nameUr !== '' ? $nameUr : $nameEn,
                    'slug'    => $this->uniqueSlug($db, $slug !== '' ? $slug : seo_base_slug($nameEn !== '' ? $nameEn : $nameUr), $catId),
                ];
            }

            foreach ($synonymGroups as $group) {
                if (! $this->groupMatches($haystack, $group['match'])) {
                    continue;
                }
                foreach ($group['tags'] as $syn) {
                    $en = trim((string) ($syn['en'] ?? ''));
                    $ur = trim((string) ($syn['ur'] ?? ''));
                    if ($en === '' && $ur === '') {
                        continue;
                    }
                    $tagDefs[] = [
                        'name_en' => $en !== '' ? $en : $ur,
                        'name_ur' => $ur !== '' ? $ur : $en,
                        'slug'    => $this->uniqueSlug($db, seo_base_slug($en !== '' ? $en : $ur), $catId),
                    ];
                }
            }

            // De-dupe tag defs by lowercase English name.
            $seen       = [];
            $uniqueDefs = [];
            foreach ($tagDefs as $def) {
                $key = mb_strtolower($def['name_en'], 'UTF-8');
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key]   = true;
                $uniqueDefs[] = $def;
            }

            $tagIds = [];
            foreach ($uniqueDefs as $def) {
                $existing = $db->table('tags')
                    ->groupStart()
                        ->where('slug', $def['slug'])
                        ->orWhere('name_en', $def['name_en'])
                    ->groupEnd()
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    $tagId = (int) $existing['id'];
                    $db->table('tags')->where('id', $tagId)->update([
                        'name_en' => $def['name_en'] !== '' ? $def['name_en'] : ($existing['name_en'] ?? ''),
                        'name_ur' => $def['name_ur'] !== '' ? $def['name_ur'] : ($existing['name_ur'] ?? ''),
                    ]);
                    $tagsReused++;
                } else {
                    $db->table('tags')->insert([
                        'name_en'    => $def['name_en'],
                        'name_ur'    => $def['name_ur'],
                        'slug'       => $def['slug'],
                        'created_at' => $now,
                    ]);
                    $tagId = (int) $db->insertID();
                    $tagsCreated++;
                }
                $tagIds[] = $tagId;
            }

            if ($tagIds === []) {
                continue;
            }

            $businesses = $db->table('businesses')
                ->select('id')
                ->where('category_id', $catId)
                ->where('status', 'active')
                ->get()
                ->getResultArray();

            foreach ($businesses as $biz) {
                $businessId = (int) $biz['id'];
                foreach ($tagIds as $tagId) {
                    $exists = $db->table('business_tags')
                        ->where('business_id', $businessId)
                        ->where('tag_id', $tagId)
                        ->countAllResults();
                    if ($exists > 0) {
                        continue;
                    }
                    $db->table('business_tags')->insert([
                        'business_id' => $businessId,
                        'tag_id'      => $tagId,
                    ]);
                    $linksCreated++;
                }
            }

            CLI::write(sprintf(
                'Category #%d %s → %d tags, %d businesses',
                $catId,
                $nameEn !== '' ? $nameEn : $nameUr,
                count($tagIds),
                count($businesses)
            ));
        }

        CLI::newLine();
        CLI::write('Done.', 'green');
        CLI::write("Tags created: {$tagsCreated}");
        CLI::write("Tags reused/updated: {$tagsReused}");
        CLI::write("Business-tag links created: {$linksCreated}");
    }

    /**
     * @param list<string> $needles
     */
    private function groupMatches(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            $n = mb_strtolower(trim((string) $needle), 'UTF-8');
            if ($n !== '' && str_contains($haystack, $n)) {
                return true;
            }
        }
        return false;
    }

    private function uniqueSlug($db, string $base, int $catId): string
    {
        $base = trim($base, '-');
        if ($base === '') {
            $base = 'tag-' . $catId;
        }
        $slug = $base;
        $i    = 0;
        while (true) {
            $row = $db->table('tags')->select('id')->where('slug', $slug)->get()->getRowArray();
            if (! $row) {
                return $slug;
            }
            if ($i === 0) {
                return $slug;
            }
            $i++;
            $slug = $base . '-' . $i;
        }
    }
}
