<?php

namespace App\Models;

use CodeIgniter\Model;

class WallModel extends Model
{
    protected $table            = 'wall_of_kot_sultan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'category_id',
        'slug',
        'photo',
        'external_links',
        'name_en',
        'name_ur',
        'profession_en',
        'profession_ur',
        'intro_en',
        'intro_ur',
        'achievements_en',
        'achievements_ur',
        'awards_en',
        'awards_ur',
        'years_of_service',
        'birth_date',
        'death_date',
        'featured',
        'views',
        'display_order',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function searchWallEntries($query = '', $category = '', $sort = 'newest', $page = 1, ?int $perPage = 18)
    {
        $locale = $this->locale();
        $isUrdu = ($locale === 'ur');

        $builder = $this->select('wall_of_kot_sultan.*, wall_categories.name_en as category_name_en, wall_categories.name_ur as category_name_ur, wall_categories.slug as category_slug, wall_categories.icon as category_icon, wall_categories.color as category_color')
                        ->join('wall_categories', 'wall_categories.id = wall_of_kot_sultan.category_id', 'left')
                        ->where('wall_of_kot_sultan.status', 'active');

        // Category filter
        if (!empty($category) && $category !== 'all') {
            $builder->groupStart()
                ->where('wall_categories.id', $category)
                ->orWhere('wall_categories.slug', $category);
            if ($isUrdu) {
                $builder->orWhere('wall_categories.name_ur', $category);
            } else {
                $builder->orWhere('wall_categories.name_en', $category);
            }
            $builder->groupEnd();
        }

        // Live search filter strictly based on locale
        if ($query !== null && trim((string)$query) !== '') {
            $needle = trim((string)$query);
            $builder->groupStart();
            if ($isUrdu) {
                $builder->like('wall_of_kot_sultan.name_ur', $needle)
                        ->orLike('wall_of_kot_sultan.profession_ur', $needle)
                        ->orLike('wall_of_kot_sultan.intro_ur', $needle)
                        ->orLike('wall_of_kot_sultan.achievements_ur', $needle)
                        ->orLike('wall_categories.name_ur', $needle);
            } else {
                $builder->like('wall_of_kot_sultan.name_en', $needle)
                        ->orLike('wall_of_kot_sultan.profession_en', $needle)
                        ->orLike('wall_of_kot_sultan.intro_en', $needle)
                        ->orLike('wall_of_kot_sultan.achievements_en', $needle)
                        ->orLike('wall_categories.name_en', $needle);
            }
            $builder->groupEnd();
        }

        // Sorting
        switch ($sort) {
            case 'oldest':
                $builder->orderBy('wall_of_kot_sultan.created_at', 'ASC');
                break;
            case 'alphabetical':
                $builder->orderBy("wall_of_kot_sultan.name_{$locale}", 'ASC');
                break;
            case 'featured':
                $builder->orderBy('wall_of_kot_sultan.featured', 'DESC')
                        ->orderBy('wall_of_kot_sultan.display_order', 'ASC')
                        ->orderBy('wall_of_kot_sultan.created_at', 'DESC');
                break;
            case 'views':
                $builder->orderBy('wall_of_kot_sultan.views', 'DESC')
                        ->orderBy('wall_of_kot_sultan.display_order', 'ASC');
                break;
            case 'newest':
            default:
                $builder->orderBy('wall_of_kot_sultan.featured', 'DESC')
                        ->orderBy('wall_of_kot_sultan.display_order', 'ASC')
                        ->orderBy('wall_of_kot_sultan.created_at', 'DESC');
                break;
        }

        if ($perPage === null) {
            $rows = $builder->findAll();
            return $this->localizedRows($rows, $isUrdu);
        }

        $page = max(1, (int)$page);
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $rows = $builder->limit($perPage, $offset)->findAll();

        return [
            'entries'    => $this->localizedRows($rows, $isUrdu),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    public function getWallPersonality($idOrSlug): ?array
    {
        if (empty($idOrSlug)) return null;

        $builder = $this->select('wall_of_kot_sultan.*, wall_categories.name_en as category_name_en, wall_categories.name_ur as category_name_ur, wall_categories.slug as category_slug, wall_categories.icon as category_icon, wall_categories.color as category_color')
                        ->join('wall_categories', 'wall_categories.id = wall_of_kot_sultan.category_id', 'left')
                        ->where('wall_of_kot_sultan.status', 'active');

        if (is_numeric($idOrSlug)) {
            $builder->where('wall_of_kot_sultan.id', (int) $idOrSlug);
        } else {
            $builder->where('wall_of_kot_sultan.slug', $idOrSlug);
        }

        $row = $builder->first();
        if (!$row && is_string($idOrSlug) && ctype_digit($idOrSlug)) {
            $row = $this->select('wall_of_kot_sultan.*, wall_categories.name_en as category_name_en, wall_categories.name_ur as category_name_ur, wall_categories.slug as category_slug, wall_categories.icon as category_icon, wall_categories.color as category_color')
                        ->join('wall_categories', 'wall_categories.id = wall_of_kot_sultan.category_id', 'left')
                        ->where('wall_of_kot_sultan.status', 'active')
                        ->where('wall_of_kot_sultan.id', (int) $idOrSlug)
                        ->first();
        }

        if (!$row) return null;

        $this->where('id', $row['id'])->increment('views', 1);

        $isUrdu = ($this->locale() === 'ur');
        return $this->localizedRow($row, $isUrdu);
    }

    public function getRelatedPersonalities($categoryId, $currentId, $limit = 3): array
    {
        $isUrdu = ($this->locale() === 'ur');
        $builder = $this->select('wall_of_kot_sultan.*, wall_categories.name_en as category_name_en, wall_categories.name_ur as category_name_ur, wall_categories.icon as category_icon')
                        ->join('wall_categories', 'wall_categories.id = wall_of_kot_sultan.category_id', 'left')
                        ->where('wall_of_kot_sultan.status', 'active')
                        ->where('wall_of_kot_sultan.id !=', $currentId);

        if (!empty($categoryId)) {
            $builder->where('wall_of_kot_sultan.category_id', $categoryId);
        }

        $rows = $builder->orderBy('wall_of_kot_sultan.featured', 'DESC')
                        ->orderBy('wall_of_kot_sultan.display_order', 'ASC')
                        ->findAll($limit);

        return $this->localizedRows($rows, $isUrdu);
    }

    public function getActiveWallEntries()
    {
        $isUrdu = ($this->locale() === 'ur');
        $entries = $this->select('wall_of_kot_sultan.*, wall_categories.name_en as category_name_en, wall_categories.name_ur as category_name_ur')
                        ->join('wall_categories', 'wall_categories.id = wall_of_kot_sultan.category_id', 'left')
                        ->where('wall_of_kot_sultan.status', 'active')
                        ->orderBy('wall_of_kot_sultan.display_order', 'ASC')
                        ->orderBy('wall_of_kot_sultan.created_at', 'DESC')
                        ->findAll();
        return $this->localizedRows($entries, $isUrdu);
    }

    private function locale(): string
    {
        return service('request')->getLocale() === 'ur' ? 'ur' : 'en';
    }

    private function localizedRows(array $rows, bool $isUrdu): array
    {
        return array_values(array_map(fn (array $row) => $this->localizedRow($row, $isUrdu), $rows));
    }

    /**
     * STRICT Localization method.
     * ZERO cross-language fallback mixing!
     */
    private function localizedRow(array $row, bool $isUrdu): array
    {
        $slugOrId = !empty($row['slug']) ? $row['slug'] : ($row['id'] ?? '');

        if ($isUrdu) {
            $row['display_name']         = trim((string)($row['name_ur'] ?? ''));
            $row['display_intro']        = trim((string)($row['intro_ur'] ?? ''));
            $row['display_profession']   = trim((string)($row['profession_ur'] ?? ''));
            $row['display_category']     = trim((string)($row['category_name_ur'] ?? ''));
            $row['display_achievements'] = trim((string)($row['achievements_ur'] ?? ''));
            $row['display_awards']       = trim((string)($row['awards_ur'] ?? ''));
        } else {
            $row['display_name']         = trim((string)($row['name_en'] ?? ''));
            $row['display_intro']        = trim((string)($row['intro_en'] ?? ''));
            $row['display_profession']   = trim((string)($row['profession_en'] ?? ''));
            $row['display_category']     = trim((string)($row['category_name_en'] ?? ''));
            $row['display_achievements'] = trim((string)($row['achievements_en'] ?? ''));
            $row['display_awards']       = trim((string)($row['awards_en'] ?? ''));
        }

        $row['url']       = base_url('wall-of-kot-sultan/' . $slugOrId);

        // Photos may be absolute URLs (e.g. Unsplash) or local relative paths.
        // base_url() would mangle an absolute URL, so only wrap relative paths.
        $photo = trim((string)($row['photo'] ?? ''));
        if ($photo !== '') {
            $row['photo_url'] = (preg_match('#^(https?:)?//#i', $photo) || stripos($photo, 'data:') === 0)
                ? $photo
                : base_url($photo);
        } else {
            $row['photo_url'] = base_url('images/placeholder-person.jpg');
        }

        return $row;
    }

    /**
     * Social networks only (not generic website / news links).
     *
     * @return array<string, array{label:string,icon:string}>
     */
    public static function socialPlatforms(): array
    {
        return [
            'facebook'  => ['label' => 'Facebook',  'icon' => 'facebook'],
            'instagram' => ['label' => 'Instagram', 'icon' => 'instagram'],
            'x'         => ['label' => 'X (Twitter)', 'icon' => 'twitter'],
            'youtube'   => ['label' => 'YouTube',   'icon' => 'youtube'],
            'linkedin'  => ['label' => 'LinkedIn',  'icon' => 'linkedin'],
            'tiktok'    => ['label' => 'TikTok',    'icon' => 'music-2'],
            'whatsapp'  => ['label' => 'WhatsApp',  'icon' => 'message-circle'],
            'telegram'  => ['label' => 'Telegram',  'icon' => 'send'],
            'threads'   => ['label' => 'Threads',   'icon' => 'at-sign'],
            'snapchat'  => ['label' => 'Snapchat',  'icon' => 'ghost'],
            'pinterest' => ['label' => 'Pinterest', 'icon' => 'pin'],
            'github'    => ['label' => 'GitHub',    'icon' => 'github'],
        ];
    }

    public static function isSocialPlatform(?string $platform): bool
    {
        $key = self::normalizePlatform($platform, false);
        return $key !== '' && isset(self::socialPlatforms()[$key]);
    }

    public static function normalizePlatform(?string $platform, bool $fallbackOther = true): string
    {
        $key = strtolower(trim((string) $platform));
        if ($key === 'twitter') {
            $key = 'x';
        }
        if ($key === 'website' || $key === 'other' || $key === 'external') {
            return $key === 'external' ? 'website' : $key;
        }
        if (isset(self::socialPlatforms()[$key])) {
            return $key;
        }
        return $fallbackOther ? 'other' : '';
    }

    /**
     * @return list<array{url:string,title:string,platform:string,icon:string,label:string,kind:string}>
     */
    public static function decodeExternalLinks(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return [];
        }
        $social = self::socialPlatforms();
        $out    = [];
        foreach ($decoded as $row) {
            if (is_string($row)) {
                $url = trim($row);
                if ($url === '') {
                    continue;
                }
                $out[] = [
                    'url'      => $url,
                    'title'    => $url,
                    'platform' => 'website',
                    'icon'     => 'globe',
                    'label'    => 'Website',
                    'kind'     => 'external',
                ];
                continue;
            }
            if (! is_array($row)) {
                continue;
            }
            $url = trim((string) ($row['url'] ?? $row['link'] ?? ''));
            if ($url === '') {
                continue;
            }

            $kindRaw = strtolower(trim((string) ($row['kind'] ?? $row['type'] ?? '')));
            $platformRaw = trim((string) ($row['platform'] ?? ''));

            if ($kindRaw === 'social' || ($platformRaw !== '' && self::isSocialPlatform($platformRaw))) {
                $platform = self::normalizePlatform($platformRaw !== '' ? $platformRaw : self::inferPlatformFromUrl($url));
                if (! self::isSocialPlatform($platform)) {
                    // Forced social kind but unknown platform — still social with generic icon.
                    $platform = $platformRaw !== '' ? self::normalizePlatform($platformRaw) : 'other';
                    $meta = ['label' => 'Social', 'icon' => 'share-2'];
                    if (isset($social[$platform])) {
                        $meta = $social[$platform];
                    }
                } else {
                    $meta = $social[$platform];
                }
                $title = trim((string) ($row['title'] ?? $row['name'] ?? ''));
                if ($title === '') {
                    $title = $meta['label'];
                }
                $out[] = [
                    'url'      => $url,
                    'title'    => $title,
                    'platform' => $platform,
                    'icon'     => $meta['icon'],
                    'label'    => $meta['label'],
                    'kind'     => 'social',
                ];
                continue;
            }

            // External / website / news / other non-social links
            if ($kindRaw === 'external' || $platformRaw === '' || in_array($platformRaw, ['website', 'other', 'external'], true)) {
                // If URL is clearly a social network and no explicit external kind, treat as social.
                if ($kindRaw === '' && $platformRaw === '') {
                    $inferred = self::inferPlatformFromUrl($url);
                    if (self::isSocialPlatform($inferred)) {
                        $meta  = $social[$inferred];
                        $title = trim((string) ($row['title'] ?? $row['name'] ?? ''));
                        if ($title === '') {
                            $title = $meta['label'];
                        }
                        $out[] = [
                            'url'      => $url,
                            'title'    => $title,
                            'platform' => $inferred,
                            'icon'     => $meta['icon'],
                            'label'    => $meta['label'],
                            'kind'     => 'social',
                        ];
                        continue;
                    }
                }

                $title = trim((string) ($row['title'] ?? $row['name'] ?? ''));
                if ($title === '' || $title === 'Website' || $title === 'Other / Custom') {
                    // Prefer host as readable title when old rows used platform label or URL-as-title.
                    $host = (string) (parse_url($url, PHP_URL_HOST) ?? '');
                    $title = $host !== '' ? preg_replace('/^www\./', '', $host) : $url;
                }
                $out[] = [
                    'url'      => $url,
                    'title'    => $title,
                    'platform' => 'website',
                    'icon'     => 'globe',
                    'label'    => 'Website',
                    'kind'     => 'external',
                ];
                continue;
            }

            // Unknown platform key → external
            $title = trim((string) ($row['title'] ?? $row['name'] ?? $url));
            $out[] = [
                'url'      => $url,
                'title'    => $title !== '' ? $title : $url,
                'platform' => 'website',
                'icon'     => 'globe',
                'label'    => 'Website',
                'kind'     => 'external',
            ];
        }
        return $out;
    }

    /**
     * @return array{external:list<array>,social:list<array>}
     */
    public static function partitionLinks(?string $json): array
    {
        $all = self::decodeExternalLinks($json);
        $external = [];
        $social   = [];
        foreach ($all as $link) {
            if (($link['kind'] ?? '') === 'social') {
                $social[] = $link;
            } else {
                $external[] = $link;
            }
        }
        return ['external' => $external, 'social' => $social];
    }

    public static function inferPlatformFromUrl(string $url): string
    {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        $host = preg_replace('/^www\./', '', $host) ?? $host;
        $map  = [
            'facebook.com' => 'facebook',
            'fb.com'       => 'facebook',
            'fb.me'        => 'facebook',
            'instagram.com'=> 'instagram',
            'twitter.com'  => 'x',
            'x.com'        => 'x',
            'youtube.com'  => 'youtube',
            'youtu.be'     => 'youtube',
            'linkedin.com' => 'linkedin',
            'tiktok.com'   => 'tiktok',
            'wa.me'        => 'whatsapp',
            'whatsapp.com' => 'whatsapp',
            'api.whatsapp.com' => 'whatsapp',
            't.me'         => 'telegram',
            'telegram.me'  => 'telegram',
            'threads.net'  => 'threads',
            'snapchat.com' => 'snapchat',
            'pinterest.com'=> 'pinterest',
            'pin.it'       => 'pinterest',
            'github.com'   => 'github',
        ];
        foreach ($map as $domain => $platform) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return $platform;
            }
        }
        return 'website';
    }
}
