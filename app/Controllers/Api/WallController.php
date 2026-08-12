<?php

namespace App\Controllers\Api;

use App\Models\WallCategoryModel;
use App\Models\WallModel;

class WallController extends BaseApiController
{
    public function index()
    {
        $this->applyLocale();

        $q        = $this->request->getGet('q');
        $category = $this->request->getGet('category');
        $sort     = $this->request->getGet('sort') ?: 'newest';
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = min(50, max(1, (int) ($this->request->getGet('per_page') ?? 18)));

        $result = (new WallModel())->searchWallEntries($q, $category ?: '', $sort, $page, $perPage);
        $cats   = (new WallCategoryModel())->orderBy('display_order', 'ASC')->where('status', 'active')->findAll();

        return $this->jsonOk([
            'items' => array_map([$this, 'mapEntry'], $result['entries'] ?? []),
            'categories' => array_map(static function ($c) {
                return [
                    'id'      => (int) $c['id'],
                    'slug'    => $c['slug'] ?? null,
                    'name_en' => $c['name_en'] ?? '',
                    'name_ur' => $c['name_ur'] ?? '',
                    'icon'    => $c['icon'] ?? 'user',
                    'color'   => $c['color'] ?? null,
                ];
            }, $cats),
            'total'       => $result['total'] ?? 0,
            'page'        => $result['page'] ?? $page,
            'per_page'    => $result['perPage'] ?? $perPage,
            'total_pages' => $result['totalPages'] ?? 1,
        ]);
    }

    public function show($idOrSlug = null)
    {
        $this->applyLocale();
        if ($idOrSlug === null || $idOrSlug === '') {
            return $this->jsonError('Not found', 404);
        }

        $model = new WallModel();
        $entry = $model->getWallPersonality($idOrSlug);
        if (!$entry) {
            return $this->jsonError('Not found', 404);
        }

        $related = $model->getRelatedPersonalities(
            $entry['category_ids'] ?? ($entry['category_id'] ?? null),
            $entry['id'],
            4
        );
        $attachments = [];
        try {
            $attachments = (new \App\Models\WallAttachmentModel())->getForWall((int) $entry['id']);
        } catch (\Throwable $e) {
            $attachments = [];
        }

        $partitioned = \App\Models\WallModel::partitionLinks($entry['external_links'] ?? null);

        return $this->jsonOk([
            'entry'       => $this->mapEntry($entry, true) + [
                'external_links' => array_map([$this, 'mapOneLink'], $partitioned['external']),
                'social_links'   => array_map([$this, 'mapOneLink'], $partitioned['social']),
                'attachments' => array_map(function (array $a) {
                    return [
                        'id'            => (int) ($a['id'] ?? 0),
                        'url'           => $this->absoluteUrl($a['file_path'] ?? '') ?: ($a['url'] ?? ''),
                        'original_name' => $a['original_name'] ?? '',
                        'mime_type'     => $a['mime_type'] ?? '',
                        'file_type'     => $a['file_type'] ?? 'other',
                        'file_size'     => (int) ($a['file_size'] ?? 0),
                    ];
                }, $attachments),
            ],
            'related'     => array_map(fn ($e) => $this->mapEntry($e), $related),
        ]);
    }

    /**
     * @param array{url?:string,title?:string,platform?:string,icon?:string,label?:string,kind?:string} $link
     * @return array{platform:string,label:string,title:string,icon:string,url:string,kind:string}
     */
    private function mapOneLink(array $link): array
    {
        return [
            'platform' => $link['platform'] ?? 'website',
            'label'    => $link['label'] ?? ($link['title'] ?? 'Link'),
            'title'    => $link['title'] ?? ($link['label'] ?? 'Link'),
            'icon'     => $link['icon'] ?? 'link-2',
            'url'      => $link['url'] ?? '',
            'kind'     => $link['kind'] ?? 'external',
        ];
    }

    /**
     * @return list<array{platform:string,label:string,title:string,icon:string,url:string,kind:string}>
     */
    private function mapExternalLinks($json): array
    {
        return array_map([$this, 'mapOneLink'], \App\Models\WallModel::decodeExternalLinks(
            $json === null ? null : (string) $json
        ));
    }

    private function mapEntry(array $e, bool $detail = false): array
    {
        $photo = $this->absoluteUrl($e['photo'] ?? '') ?: ($e['photo_url'] ?? null);

        $base = [
            'id'            => (int) ($e['id'] ?? 0),
            'slug'          => $e['slug'] ?? null,
            'name'          => $e['display_name'] ?? ($e['name'] ?? ($e['name_en'] ?? '')),
            'name_en'       => $e['name_en'] ?? '',
            'name_ur'       => $e['name_ur'] ?? '',
            'profession'    => $e['display_profession'] ?? ($e['profession'] ?? ($e['profession_en'] ?? '')),
            'category'      => $e['display_category'] ?? ($e['category_name_en'] ?? ($e['category_name'] ?? '')),
            'category_id'   => isset($e['category_id']) ? (int) $e['category_id'] : null,
            'category_slug' => $e['category_slug'] ?? null,
            'categories'    => array_map(static function ($c) {
                return [
                    'id'      => (int) ($c['id'] ?? 0),
                    'slug'    => $c['slug'] ?? null,
                    'name'    => $c['name'] ?? '',
                    'name_en' => $c['name_en'] ?? '',
                    'name_ur' => $c['name_ur'] ?? '',
                    'icon'    => $c['icon'] ?? 'user',
                    'color'   => $c['color'] ?? null,
                ];
            }, $e['categories'] ?? []),
            'photo'         => $photo,
            'featured'      => (bool) ($e['featured'] ?? false),
            'views'         => (int) ($e['views'] ?? 0),
        ];

        if (!$detail) {
            return $base;
        }

        return $base + [
            'intro'            => $e['display_intro'] ?? ($e['intro'] ?? ($e['intro_en'] ?? '')),
            'achievements'     => $e['display_achievements'] ?? ($e['achievements'] ?? ''),
            'awards'           => $e['display_awards'] ?? ($e['awards'] ?? ''),
            'years_of_service' => $e['years_of_service'] ?? '',
            'birth_date'       => $e['birth_date'] ?? '',
            'death_date'       => $e['death_date'] ?? '',
        ];
    }
}
