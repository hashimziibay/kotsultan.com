<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WallCategorySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $categories = [
            [
                'name_en'       => 'Education',
                'name_ur'       => 'تعلیم و تدریس',
                'slug'          => 'education',
                'icon'          => 'graduation-cap',
                'color'         => 'emerald',
                'display_order' => 1,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Politics',
                'name_ur'       => 'سیاست',
                'slug'          => 'politics',
                'icon'          => 'landmark',
                'color'         => 'blue',
                'display_order' => 2,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Social Workers',
                'name_ur'       => 'سماجی کارکنان',
                'slug'          => 'social-workers',
                'icon'          => 'heart-handshake',
                'color'         => 'rose',
                'display_order' => 3,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Religious Scholars',
                'name_ur'       => 'علمائے کرام',
                'slug'          => 'religious-scholars',
                'icon'          => 'book-open',
                'color'         => 'amber',
                'display_order' => 4,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Doctors & Healthcare',
                'name_ur'       => 'ڈاکٹرز و طبی خدمات',
                'slug'          => 'doctors',
                'icon'          => 'stethoscopes',
                'color'         => 'teal',
                'display_order' => 5,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Business Personalities',
                'name_ur'       => 'کاروباری شخصیات',
                'slug'          => 'business-personalities',
                'icon'          => 'briefcase',
                'color'         => 'indigo',
                'display_order' => 6,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Sports',
                'name_ur'       => 'کھیل و کھلاڑی',
                'slug'          => 'sports',
                'icon'          => 'trophy',
                'color'         => 'orange',
                'display_order' => 7,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Artists & Culture',
                'name_ur'       => 'فنکار و ثقافت',
                'slug'          => 'artists',
                'icon'          => 'palette',
                'color'         => 'purple',
                'display_order' => 8,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Writers & Poets',
                'name_ur'       => 'ادباء و شعراء',
                'slug'          => 'writers-poets',
                'icon'          => 'feather',
                'color'         => 'sky',
                'display_order' => 9,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Government Officers',
                'name_ur'       => 'سرکاری افسران',
                'slug'          => 'government-officers',
                'icon'          => 'award',
                'color'         => 'slate',
                'display_order' => 10,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Freedom Fighters',
                'name_ur'       => 'مجاہدین آزادی',
                'slug'          => 'freedom-fighters',
                'icon'          => 'flag',
                'color'         => 'red',
                'display_order' => 11,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Military & Defense',
                'name_ur'       => 'عسکری و دفاعی شخصیات',
                'slug'          => 'military',
                'icon'          => 'shield',
                'color'         => 'green',
                'display_order' => 12,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Agriculture & Farming',
                'name_ur'       => 'زراعت و باغبانی',
                'slug'          => 'agriculture',
                'icon'          => 'sprout',
                'color'         => 'emerald',
                'display_order' => 13,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Community Leaders',
                'name_ur'       => 'قوم و قبیلہ کے رہنما',
                'slug'          => 'community-leaders',
                'icon'          => 'users',
                'color'         => 'violet',
                'display_order' => 14,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Philanthropists',
                'name_ur'       => 'مخلص مخیرین',
                'slug'          => 'philanthropists',
                'icon'          => 'gift',
                'color'         => 'pink',
                'display_order' => 15,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Historical Personalities',
                'name_ur'       => 'تاریخی شخصیات',
                'slug'          => 'historical-personalities',
                'icon'          => 'history',
                'color'         => 'yellow',
                'display_order' => 16,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Women Achievers',
                'name_ur'       => 'نمایاں خواتین',
                'slug'          => 'women-achievers',
                'icon'          => 'sparkles',
                'color'         => 'fuchsia',
                'display_order' => 17,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Youth Icons',
                'name_ur'       => 'جوان عزم و حوصلے',
                'slug'          => 'youth-icons',
                'icon'          => 'zap',
                'color'         => 'cyan',
                'display_order' => 18,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Journalists & Media',
                'name_ur'       => 'صحافی و میڈیا',
                'slug'          => 'journalists',
                'icon'          => 'newspaper',
                'color'         => 'stone',
                'display_order' => 19,
                'status'        => 'active',
            ],
            [
                'name_en'       => 'Other Personalities',
                'name_ur'       => 'دیگر معروف شخصیات',
                'slug'          => 'other',
                'icon'          => 'user-check',
                'color'         => 'gray',
                'display_order' => 20,
                'status'        => 'active',
            ],
        ];

        $now = date('Y-m-d H:i:s');
        $catMap = [];

        foreach ($categories as $cat) {
            $existing = $db->table('wall_categories')->where('slug', $cat['slug'])->get()->getRowArray();
            if ($existing) {
                $catMap[$cat['slug']] = $existing['id'];
            } else {
                $cat['created_at'] = $now;
                $cat['updated_at'] = $now;
                $db->table('wall_categories')->insert($cat);
                $catMap[$cat['slug']] = $db->insertID();
            }
        }

        // Map existing wall entries to categories and add slugs
        $wallEntries = $db->table('wall_of_kot_sultan')->get()->getResultArray();
        foreach ($wallEntries as $entry) {
            $update = [];
            
            // Slug generation if empty
            if (empty($entry['slug'])) {
                $baseSlug = url_title($entry['name_en'] ?: ('person-' . $entry['id']), '-', true);
                $update['slug'] = $baseSlug;
            }

            // Assign category_id if null
            if (empty($entry['category_id'])) {
                $text = strtolower(($entry['name_en'] ?? '') . ' ' . ($entry['intro_en'] ?? ''));
                if (str_contains($text, 'teacher') || str_contains($text, 'school') || str_contains($text, 'education') || str_contains($text, 'professor')) {
                    $update['category_id'] = $catMap['education'] ?? null;
                    $update['profession_en'] = $entry['profession_en'] ?? 'Educational Leader & Teacher';
                    $update['profession_ur'] = $entry['profession_ur'] ?? 'استاد و تعلیمی رہنما';
                } elseif (str_contains($text, 'doctor') || str_contains($text, 'health') || str_contains($text, 'hospital')) {
                    $update['category_id'] = $catMap['doctors'] ?? null;
                    $update['profession_en'] = $entry['profession_en'] ?? 'Medical Doctor';
                    $update['profession_ur'] = $entry['profession_ur'] ?? 'ڈاکٹر و طبی معالج';
                } elseif (str_contains($text, 'poet') || str_contains($text, 'writer') || str_contains($text, 'author')) {
                    $update['category_id'] = $catMap['writers-poets'] ?? null;
                    $update['profession_en'] = $entry['profession_en'] ?? 'Poet & Writer';
                    $update['profession_ur'] = $entry['profession_ur'] ?? 'شاعر و ادیب';
                } elseif (str_contains($text, 'social') || str_contains($text, 'service') || str_contains($text, 'welfare')) {
                    $update['category_id'] = $catMap['social-workers'] ?? null;
                    $update['profession_en'] = $entry['profession_en'] ?? 'Social Activist & Worker';
                    $update['profession_ur'] = $entry['profession_ur'] ?? 'سماجی ورکر';
                } else {
                    $update['category_id'] = $catMap['other'] ?? null;
                    $update['profession_en'] = $entry['profession_en'] ?? 'Community Personality';
                    $update['profession_ur'] = $entry['profession_ur'] ?? 'معروف مقامی شخصیت';
                }
            }

            if (!empty($update)) {
                $db->table('wall_of_kot_sultan')->where('id', $entry['id'])->update($update);
            }
        }
    }
}
