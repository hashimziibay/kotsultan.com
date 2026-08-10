<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Rebuild listing/category slugs for SEO: {name}-in-kot-sultan
 */
class RebuildSeoSlugs extends BaseCommand
{
    protected $group       = 'SEO';
    protected $name        = 'seo:rebuild-slugs';
    protected $description = 'Regenerate ASCII SEO slugs (...-in-kot-sultan) for businesses and categories.';

    public function run(array $params)
    {
        helper(['url', 'seo']);
        $db = \Config\Database::connect();

        $bizUpdated = 0;
        $catUpdated = 0;

        $businesses = $db->table('businesses')->select('id, name_en, slug')->get()->getResultArray();
        foreach ($businesses as $row) {
            $id      = (int) $row['id'];
            $newSlug = make_unique_listing_seo_slug((string) ($row['name_en'] ?? ''), $id, $db);
            if ($newSlug !== (string) ($row['slug'] ?? '')) {
                $db->table('businesses')->where('id', $id)->update([
                    'slug'       => $newSlug,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $bizUpdated++;
            }
        }

        $categories = $db->table('categories')->select('id, name_en, slug')->get()->getResultArray();
        foreach ($categories as $row) {
            $id      = (int) $row['id'];
            $newSlug = make_unique_category_seo_slug((string) ($row['name_en'] ?? ''), $id, $db);
            if ($newSlug !== (string) ($row['slug'] ?? '')) {
                $db->table('categories')->where('id', $id)->update([
                    'slug'       => $newSlug,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $catUpdated++;
            }
        }

        CLI::write("Businesses updated: {$bizUpdated}");
        CLI::write("Categories updated: {$catUpdated}");
        CLI::write('Done. Listing URLs look like /listing/name-in-kot-sultan');
        CLI::write('Category URLs look like /directory/name-in-kot-sultan');
    }
}
