<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessModel extends Model
{
    protected $table            = 'businesses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'category_id',
        'area_id',
        'village_id',
        'source_post_id',
        'name_en',
        'name_ur',
        'slug',
        'owner_name',
        'address',
        'address_en',
        'address_ur',
        'description_en',
        'description_ur',
        'phone',
        'whatsapp',
        'website',
        'email',
        'opening_hours',
        'gallery_images',
        'social_links',
        'latitude',
        'longitude',
        'google_map',
        'image',
        'featured',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * One mobile/business account may own at most one listing.
     */
    public function findOwnedByUser(int $userId): ?array
    {
        if ($userId < 1) {
            return null;
        }
        $row = $this->where('user_id', $userId)->orderBy('id', 'ASC')->first();
        return $row ?: null;
    }

    public function userHasBusiness(int $userId): bool
    {
        return $this->findOwnedByUser($userId) !== null;
    }

    public function getRecentBusinesses($limit = 6)
    {
        $rows = $this->baseQuery()
                     ->where('businesses.status', 'active')
                     ->orderBy('businesses.created_at', 'DESC')
                     ->findAll($limit);
        return $this->localizedRows($rows);
    }

    public function searchDirectory($query = '', $categoryId = null, $tagId = null, $page = 1, ?int $perPage = null)
    {
        $locale = $this->locale();
        $builder = $this->baseQuery()->where('businesses.status', 'active');
        $q       = trim((string) ($query ?? ''));
        $joinedBusinessTags = false;

        if (!empty($categoryId)) {
            // Category links may use:
            // - numeric id
            // - short slug (hospitals)
            // - SEO path slug (hospitals-in-kot-sultan)
            if (!ctype_digit((string) $categoryId)) {
                helper('seo');
                $raw      = (string) $categoryId;
                $stripped = seo_strip_place_suffix($raw);
                $cat = $this->db->table('categories')
                                ->select('id')
                                ->groupStart()
                                    ->where('slug', $raw)
                                    ->orWhere('slug', $stripped)
                                ->groupEnd()
                                ->get()
                                ->getRowArray();
                $categoryId = $cat ? (int) $cat['id'] : 0;
            }
            $builder->where('businesses.category_id', (int) $categoryId);
        }

        if (!empty($tagId)) {
            $builder->join('business_tags', 'business_tags.business_id = businesses.id')
                    ->where('business_tags.tag_id', (int) $tagId);
            $joinedBusinessTags = true;
        }

        if ($q !== '') {
            // Include tags in primary fuzzy search so tag keywords match listings.
            if (! $joinedBusinessTags) {
                $builder->join('business_tags', 'business_tags.business_id = businesses.id', 'left');
            }
            $builder->join('tags', 'tags.id = business_tags.tag_id', 'left');

            helper('search');
            apply_fuzzy_search($builder, [
                'businesses.name_en',
                'businesses.name_ur',
                'businesses.owner_name',
                'businesses.address_en',
                'businesses.address_ur',
                'businesses.description_en',
                'businesses.description_ur',
                'categories.name_en',
                'categories.name_ur',
                'areas.name_en',
                'areas.name_ur',
                'villages.name_en',
                'villages.name_ur',
                'businesses.phone',
                'businesses.whatsapp',
                'tags.name_en',
                'tags.name_ur',
                'tags.slug',
            ], $q);
            $builder->groupBy('businesses.id');
        }

        if ($perPage === null) {
            $rows = $builder->orderBy('businesses.featured', 'DESC')
                           ->orderBy("businesses.name_{$locale}", 'ASC')
                           ->findAll();
            return $this->localizedRows($rows);
        }

        $page = max(1, (int) $page);
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $rows = $builder->orderBy('businesses.featured', 'DESC')
                       ->orderBy("businesses.name_{$locale}", 'ASC')
                       ->limit($perPage, $offset)
                       ->findAll();

        $result = [
            'businesses'         => $this->localizedRows($rows),
            'total'              => $total,
            'page'               => $page,
            'perPage'            => $perPage,
            'totalPages'         => (int) ceil($total / max(1, $perPage)),
            'suggestions'        => [],
            'suggested_tags'     => [],
            'suggestion_reason'  => null,
        ];

        // When nothing exact matches, suggest businesses linked to similar tags.
        if ($total === 0 && $q !== '' && empty($tagId)) {
            $suggestedTags = $this->findSimilarTags($q, 8);
            $tagIds        = array_map(static fn ($t) => (int) $t['id'], $suggestedTags);
            $suggestions   = $tagIds !== [] ? $this->findBusinessesByTagIds($tagIds, 12) : [];

            if ($suggestedTags !== [] || $suggestions !== []) {
                $result['suggested_tags']    = $suggestedTags;
                $result['suggestions']       = $suggestions;
                $result['suggestion_reason'] = 'similar_tags';
            }
        }

        return $result;
    }

    /**
     * Fuzzy-match tags for "did you mean" suggestions.
     *
     * @return list<array{id:int,slug:?string,name:string,name_en:string,name_ur:string}>
     */
    public function findSimilarTags(string $query, int $limit = 8): array
    {
        $q = trim($query);
        if ($q === '') {
            return [];
        }

        helper('search');
        $locale  = $this->locale();
        $builder = $this->db->table('tags')->select('tags.id, tags.slug, tags.name_en, tags.name_ur');
        apply_fuzzy_search($builder, ['tags.name_en', 'tags.name_ur', 'tags.slug'], $q);

        $rows = $builder->orderBy("tags.name_{$locale}", 'ASC')
                        ->limit(max(1, $limit))
                        ->get()
                        ->getResultArray();

        return array_map(static function (array $t) use ($locale) {
            $nameEn = trim((string) ($t['name_en'] ?? ''));
            $nameUr = trim((string) ($t['name_ur'] ?? ''));
            $name   = $locale === 'ur' ? ($nameUr !== '' ? $nameUr : $nameEn) : ($nameEn !== '' ? $nameEn : $nameUr);

            return [
                'id'      => (int) $t['id'],
                'slug'    => $t['slug'] ?? null,
                'name'    => $name,
                'name_en' => $nameEn,
                'name_ur' => $nameUr,
            ];
        }, $rows);
    }

    /**
     * Active businesses linked to any of the given tag ids.
     *
     * @param list<int> $tagIds
     */
    public function findBusinessesByTagIds(array $tagIds, int $limit = 12): array
    {
        $tagIds = array_values(array_unique(array_filter(array_map('intval', $tagIds))));
        if ($tagIds === []) {
            return [];
        }

        $locale = $this->locale();
        $rows   = $this->baseQuery()
            ->join('business_tags', 'business_tags.business_id = businesses.id')
            ->where('businesses.status', 'active')
            ->whereIn('business_tags.tag_id', $tagIds)
            ->groupBy('businesses.id')
            ->orderBy('businesses.featured', 'DESC')
            ->orderBy("businesses.name_{$locale}", 'ASC')
            ->limit(max(1, $limit))
            ->findAll();

        return $this->localizedRows($rows);
    }

    public function getLocalizedBusiness($idOrSlug): ?array
    {
        if ($idOrSlug === null || $idOrSlug === '') {
            return null;
        }

        helper('seo');

        $raw = rawurldecode(trim((string) $idOrSlug));
        if ($raw === '') {
            return null;
        }

        // 1) Exact slug match (preferred — avoids collisions with ...-in-kot-sultan-{id} variants)
        $row = $this->baseQuery()
            ->where('businesses.status', 'active')
            ->where('businesses.slug', $raw)
            ->first();
        if ($row) {
            return $this->localizedRow($row);
        }

        // 2) Numeric id
        if (ctype_digit($raw)) {
            $row = $this->baseQuery()
                ->where('businesses.status', 'active')
                ->where('businesses.id', (int) $raw)
                ->first();
            if ($row) {
                return $this->localizedRow($row);
            }
        }

        // 3) Trailing id from SEO slug: name-in-kot-sultan-1512
        $trailingId = seo_extract_trailing_id($raw);
        if ($trailingId && str_contains($raw, seo_place_suffix())) {
            $row = $this->baseQuery()
                ->where('businesses.status', 'active')
                ->where('businesses.id', $trailingId)
                ->first();
            if ($row) {
                return $this->localizedRow($row);
            }
        }

        // 4) Legacy short slug / missing place suffix
        $stripped = seo_strip_place_suffix($raw);
        $withPlace = seo_with_place($stripped);
        foreach (array_unique([$stripped, $withPlace]) as $candidate) {
            if ($candidate === '' || $candidate === $raw) {
                continue;
            }
            $row = $this->baseQuery()
                ->where('businesses.status', 'active')
                ->where('businesses.slug', $candidate)
                ->first();
            if ($row) {
                return $this->localizedRow($row);
            }
        }

        return null;
    }

    private function baseQuery()
    {
        return $this->select('businesses.*, 
                              categories.name_en as category_name_en, categories.name_ur as category_name_ur, categories.icon as category_icon,
                              areas.name_en as area_name_en, areas.name_ur as area_name_ur,
                              villages.name_en as village_name_en, villages.name_ur as village_name_ur')
                    ->join('categories', 'categories.id = businesses.category_id', 'left')
                    ->join('areas', 'areas.id = businesses.area_id', 'left')
                    ->join('villages', 'villages.id = businesses.village_id', 'left');
    }

    private function locale(): string { return service('request')->getLocale() === 'ur' ? 'ur' : 'en'; }

    private function localizedRows(array $rows): array
    {
        return array_values(array_map(fn (array $row) => $this->localizedRow($row), $rows));
    }

    private function localizedRow(array $row): array
    {
        helper('seo');
        $slugOrId = seo_listing_slug_from_row($row);

        // Address resolution: address_en / address_ur first, then legacy address
        $addressVal = $this->localized($row, 'address');
        if (empty($addressVal) && !empty($row['address'])) {
            $addressVal = trim((string)$row['address']);
        }

        return $row + [
            'display_name'          => $this->localized($row, 'name'),
            'display_category_name' => $this->localized($row, 'category_name'),
            'display_area_name'     => $this->localized($row, 'area_name'),
            'display_village_name'  => $this->localized($row, 'village_name'),
            'display_address'       => $addressVal,
            'display_description'   => $this->localized($row, 'description'),
            'seo_slug'              => $slugOrId,
            'url'                   => function_exists('base_url') ? base_url('listing/' . $slugOrId) : '/listing/' . $slugOrId,
        ];
    }

    /**
     * STRICT Localization method.
     * ZERO cross-language fallback mixing!
     */
    private function localized(array $row, string $field): string
    {
        $lang = $this->locale();
        return trim((string) ($row["{$field}_{$lang}"] ?? ''));
    }

    public function getCategoryCounts()
    {
        return $this->select('businesses.category_id, categories.name_en, categories.name_ur, COUNT(businesses.id) as total')
                    ->join('categories', 'categories.id = businesses.category_id', 'left')
                    ->where('businesses.status', 'active')
                    ->groupBy('businesses.category_id, categories.name_en, categories.name_ur')
                    ->findAll();
    }

    /**
     * When a listing is set inactive, also hide active duplicate rows
     * (same English/Urdu name or same phone) so they don't keep showing
     * on the public site after an admin deactivates one copy.
     */
    public function deactivateDuplicates(array $business, int $exceptId): int
    {
        $db = $this->db;
        $updated = 0;

        $nameEn = trim((string) ($business['name_en'] ?? ''));
        $nameUr = trim((string) ($business['name_ur'] ?? ''));
        $phone  = trim((string) ($business['phone'] ?? ''));

        $builder = $db->table($this->table)
            ->where('id !=', $exceptId)
            ->where('status', 'active');

        $parts = [];
        if ($nameEn !== '') {
            $parts[] = ['name_en', $nameEn];
        }
        if ($nameUr !== '') {
            $parts[] = ['name_ur', $nameUr];
        }
        if ($phone !== '') {
            $parts[] = ['phone', $phone];
        }

        if ($parts === []) {
            return 0;
        }

        $builder->groupStart();
        foreach ($parts as $i => [$field, $value]) {
            if ($i === 0) {
                $builder->where($field, $value);
            } else {
                $builder->orWhere($field, $value);
            }
        }
        $builder->groupEnd();

        // Fetch IDs first so we can return a reliable count.
        $ids = array_column(
            $builder->select('id')->get()->getResultArray(),
            'id'
        );

        if ($ids === []) {
            return 0;
        }

        $db->table($this->table)
            ->whereIn('id', $ids)
            ->update([
                'status'     => 'inactive',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return count($ids);
    }
}
