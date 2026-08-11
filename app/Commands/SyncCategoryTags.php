<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Create bilingual tags from every category (+ synonyms) and link them
 * to businesses in that category so directory search suggestions work.
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

        $synonymGroups = $this->synonymGroups();
        $tagsCreated   = 0;
        $tagsReused    = 0;
        $linksCreated  = 0;
        $now           = date('Y-m-d H:i:s');

        foreach ($categories as $cat) {
            $catId   = (int) $cat['id'];
            $nameEn  = trim((string) ($cat['name_en'] ?? ''));
            $nameUr  = trim((string) ($cat['name_ur'] ?? ''));
            $slug    = trim((string) ($cat['slug'] ?? ''));
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
            $seen = [];
            $uniqueDefs = [];
            foreach ($tagDefs as $def) {
                $key = mb_strtolower($def['name_en'], 'UTF-8');
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
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
                    // Keep bilingual names filled in.
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
            // Same slug already exists — reuse that tag; caller matches by slug first.
            if ($i === 0) {
                return $slug;
            }
            $i++;
            $slug = $base . '-' . $i;
        }
    }

    /**
     * Synonym packs matched against category EN/UR/slug.
     *
     * @return list<array{match:list<string>,tags:list<array{en:string,ur:string}>}>
     */
    private function synonymGroups(): array
    {
        return [
            [
                'match' => ['hospital', 'hospitals', 'ہسپتال'],
                'tags'  => [
                    ['en' => 'Hospital', 'ur' => 'ہسپتال'],
                    ['en' => 'Hasptal', 'ur' => 'اسپتال'],
                    ['en' => 'Clinic', 'ur' => 'کلینک'],
                    ['en' => 'Medical Centre', 'ur' => 'میڈیکل سینٹر'],
                ],
            ],
            [
                'match' => ['medical store', 'medical-stores', 'pharmacy', 'میڈیکل'],
                'tags'  => [
                    ['en' => 'Medical Store', 'ur' => 'میڈیکل اسٹور'],
                    ['en' => 'Pharmacy', 'ur' => 'فارمیسی'],
                    ['en' => 'Dawakhana', 'ur' => 'دواخانہ'],
                ],
            ],
            [
                'match' => ['dental', 'ڈینٹل'],
                'tags'  => [
                    ['en' => 'Dental', 'ur' => 'ڈینٹل'],
                    ['en' => 'Dentist', 'ur' => 'دانتوں کا ڈاکٹر'],
                    ['en' => 'Tooth Clinic', 'ur' => 'دانت کلینک'],
                ],
            ],
            [
                'match' => ['lab', 'labs', 'لیب'],
                'tags'  => [
                    ['en' => 'Lab', 'ur' => 'لیب'],
                    ['en' => 'Laboratory', 'ur' => 'لیبارٹری'],
                    ['en' => 'Blood Test', 'ur' => 'بلڈ ٹیسٹ'],
                ],
            ],
            [
                'match' => ['x-ray', 'xray', 'ایکسرے'],
                'tags'  => [
                    ['en' => 'X-Ray', 'ur' => 'ایکسرے'],
                    ['en' => 'Digital X Ray', 'ur' => 'ڈیجیٹل ایکسرے'],
                ],
            ],
            [
                'match' => ['ultrasound', 'الٹرا'],
                'tags'  => [
                    ['en' => 'Ultrasound', 'ur' => 'الٹرا ساؤنڈ'],
                    ['en' => 'Sonography', 'ur' => 'سونوگرافی'],
                ],
            ],
            [
                'match' => ['vegetable', 'سبزی'],
                'tags'  => [
                    ['en' => 'Vegetable', 'ur' => 'سبزی'],
                    ['en' => 'Sabzi', 'ur' => 'سبزی فروش'],
                    ['en' => 'Fruit & Veg', 'ur' => 'پھل سبزی'],
                ],
            ],
            [
                'match' => ['general store', 'general-stores', 'جنرل'],
                'tags'  => [
                    ['en' => 'General Store', 'ur' => 'جنرل اسٹور'],
                    ['en' => 'Karyana', 'ur' => 'کریانہ'],
                ],
            ],
            [
                'match' => ['grocery', 'کریانہ'],
                'tags'  => [
                    ['en' => 'Grocery', 'ur' => 'کریانہ'],
                    ['en' => 'Kiryana', 'ur' => 'کریانہ اسٹور'],
                ],
            ],
            [
                'match' => ['super store', 'super-stores', 'سپر'],
                'tags'  => [
                    ['en' => 'Super Store', 'ur' => 'سپر اسٹور'],
                    ['en' => 'Mart', 'ur' => 'مارٹ'],
                ],
            ],
            [
                'match' => ['cash', 'کیش'],
                'tags'  => [
                    ['en' => 'Cash & Carry', 'ur' => 'کیش اینڈ کیری'],
                    ['en' => 'Wholesale', 'ur' => 'ہول سیل'],
                ],
            ],
            [
                'match' => ['mart', 'mini market', 'مارٹ'],
                'tags'  => [
                    ['en' => 'Mini Market', 'ur' => 'منی مارکیٹ'],
                    ['en' => 'Mart', 'ur' => 'مارٹ'],
                ],
            ],
            [
                'match' => ['cloth', 'garment', 'کلاتھ', 'گارمنٹ'],
                'tags'  => [
                    ['en' => 'Cloth House', 'ur' => 'کلاتھ ہاؤس'],
                    ['en' => 'Garments', 'ur' => 'کپڑے'],
                    ['en' => 'Boutique', 'ur' => 'بوٹیک'],
                ],
            ],
            [
                'match' => ['shoe', 'شوز', 'چپل'],
                'tags'  => [
                    ['en' => 'Shoes', 'ur' => 'جوتے'],
                    ['en' => 'Shoe Shop', 'ur' => 'شوز شاپ'],
                    ['en' => 'Chappal', 'ur' => 'چپل'],
                ],
            ],
            [
                'match' => ['hotel', 'guest', 'ہوٹل'],
                'tags'  => [
                    ['en' => 'Hotel', 'ur' => 'ہوٹل'],
                    ['en' => 'Guest House', 'ur' => 'گیسٹ ہاؤس'],
                    ['en' => 'Lodge', 'ur' => 'لاج'],
                ],
            ],
            [
                'match' => ['fast food', 'restaurant', 'فاسٹ', 'ریستوران'],
                'tags'  => [
                    ['en' => 'Fast Food', 'ur' => 'فاسٹ فوڈ'],
                    ['en' => 'Restaurant', 'ur' => 'ریستوران'],
                    ['en' => 'Burger', 'ur' => 'برگر'],
                ],
            ],
            [
                'match' => ['sweet', 'bakery', 'سویٹ', 'بیکر'],
                'tags'  => [
                    ['en' => 'Sweets', 'ur' => 'مٹھائی'],
                    ['en' => 'Bakery', 'ur' => 'بیکری'],
                    ['en' => 'Mithai', 'ur' => 'مٹھائی'],
                ],
            ],
            [
                'match' => ['biryani', 'بریانی'],
                'tags'  => [
                    ['en' => 'Biryani', 'ur' => 'بریانی'],
                    ['en' => 'Pakwan', 'ur' => 'پکوان'],
                ],
            ],
            [
                'match' => ['milk', 'yogurt', 'دودھ', 'دہی'],
                'tags'  => [
                    ['en' => 'Milk', 'ur' => 'دودھ'],
                    ['en' => 'Yogurt', 'ur' => 'دہی'],
                    ['en' => 'Dairy', 'ur' => 'ڈیری'],
                ],
            ],
            [
                'match' => ['chicken', 'چکن'],
                'tags'  => [
                    ['en' => 'Chicken', 'ur' => 'چکن'],
                    ['en' => 'Poultry', 'ur' => 'مرغی'],
                ],
            ],
            [
                'match' => ['beef', 'meat', 'بیف', 'گوشت'],
                'tags'  => [
                    ['en' => 'Meat', 'ur' => 'گوشت'],
                    ['en' => 'Beef', 'ur' => 'بیف'],
                    ['en' => 'Butcher', 'ur' => 'قصائی'],
                ],
            ],
            [
                'match' => ['fish', 'فش'],
                'tags'  => [
                    ['en' => 'Fish', 'ur' => 'مچھلی'],
                    ['en' => 'Fish Shop', 'ur' => 'فش شاپ'],
                ],
            ],
            [
                'match' => ['petrol', 'cng', 'پیٹرول', 'سی این جی'],
                'tags'  => [
                    ['en' => 'Petrol Pump', 'ur' => 'پیٹرول پمپ'],
                    ['en' => 'CNG', 'ur' => 'سی این جی'],
                    ['en' => 'Fuel', 'ur' => 'ایندھن'],
                ],
            ],
            [
                'match' => ['gas station', 'گیس'],
                'tags'  => [
                    ['en' => 'Gas', 'ur' => 'گیس'],
                    ['en' => 'Sui Gas', 'ur' => 'سوئی گیس'],
                ],
            ],
            [
                'match' => ['bus', 'terminal', 'ٹرمینل'],
                'tags'  => [
                    ['en' => 'Bus Stand', 'ur' => 'بس سٹینڈ'],
                    ['en' => 'Terminal', 'ur' => 'ٹرمینل'],
                ],
            ],
            [
                'match' => ['flour', 'آٹا', 'چکی'],
                'tags'  => [
                    ['en' => 'Flour Mill', 'ur' => 'آٹا چکی'],
                    ['en' => 'Atta Chakki', 'ur' => 'آٹا چکی'],
                ],
            ],
            [
                'match' => ['motorcycle', 'honda', 'موٹر سائیکل', 'ہنڈا'],
                'tags'  => [
                    ['en' => 'Motorcycle', 'ur' => 'موٹر سائیکل'],
                    ['en' => 'Bike Mechanic', 'ur' => 'بائیک مکینک'],
                    ['en' => 'Honda', 'ur' => 'ہونڈا'],
                ],
            ],
            [
                'match' => ['auto exchange', 'car dealer', 'آٹو', 'کار'],
                'tags'  => [
                    ['en' => 'Car Dealer', 'ur' => 'کار ڈیلر'],
                    ['en' => 'Auto Exchange', 'ur' => 'آٹو ایکسچینج'],
                ],
            ],
            [
                'match' => ['mechanic', 'میکینک', 'مکینک'],
                'tags'  => [
                    ['en' => 'Mechanic', 'ur' => 'مکینک'],
                    ['en' => 'Workshop', 'ur' => 'ورکشاپ'],
                ],
            ],
            [
                'match' => ['homeopath', 'ہومیو'],
                'tags'  => [
                    ['en' => 'Homeopathic', 'ur' => 'ہومیوپیتھک'],
                    ['en' => 'Homeopath', 'ur' => 'ہومیوپیتھ'],
                ],
            ],
            [
                'match' => ['veterinary', 'ویٹرنری'],
                'tags'  => [
                    ['en' => 'Veterinary', 'ur' => 'ویٹرنری'],
                    ['en' => 'Animal Doctor', 'ur' => 'جانوروں کا ڈاکٹر'],
                ],
            ],
            [
                'match' => ['tea', 'ٹی سٹال'],
                'tags'  => [
                    ['en' => 'Tea Stall', 'ur' => 'چائے کی دکان'],
                    ['en' => 'Chai', 'ur' => 'چائے'],
                ],
            ],
            [
                'match' => ['juice', 'جوس'],
                'tags'  => [
                    ['en' => 'Juice', 'ur' => 'جوس'],
                    ['en' => 'Cold Drink', 'ur' => 'ٹھنڈا مشروب'],
                ],
            ],
            [
                'match' => ['beauty', 'بیوٹی', 'parlour'],
                'tags'  => [
                    ['en' => 'Beauty Parlour', 'ur' => 'بیوٹی پارلر'],
                    ['en' => 'Salon', 'ur' => 'سیلون'],
                ],
            ],
            [
                'match' => ['tailor', 'ٹیلر'],
                'tags'  => [
                    ['en' => 'Tailor', 'ur' => 'درزی'],
                    ['en' => 'Stitching', 'ur' => 'سلائی'],
                ],
            ],
            [
                'match' => ['mobile', 'موبائل'],
                'tags'  => [
                    ['en' => 'Mobile', 'ur' => 'موبائل'],
                    ['en' => 'Mobile Repair', 'ur' => 'موبائل مرمت'],
                    ['en' => 'Phone Shop', 'ur' => 'فون شاپ'],
                ],
            ],
            [
                'match' => ['electric', 'الیکٹری'],
                'tags'  => [
                    ['en' => 'Electrician', 'ur' => 'الیکٹریشین'],
                    ['en' => 'Bijli', 'ur' => 'بجلی'],
                ],
            ],
            [
                'match' => ['electronic', 'الیکٹرانک'],
                'tags'  => [
                    ['en' => 'Electronics', 'ur' => 'الیکٹرانکس'],
                    ['en' => 'TV Shop', 'ur' => 'ٹی وی شاپ'],
                ],
            ],
            [
                'match' => ['optical', 'آپٹیکل'],
                'tags'  => [
                    ['en' => 'Optical', 'ur' => 'آپٹیکل'],
                    ['en' => 'Glasses', 'ur' => 'عینک'],
                ],
            ],
            [
                'match' => ['ac ', 'refrigerat', 'ایئر', 'ریفر'],
                'tags'  => [
                    ['en' => 'AC Repair', 'ur' => 'اے سی مرمت'],
                    ['en' => 'Fridge', 'ur' => 'فریج'],
                ],
            ],
            [
                'match' => ['tyre', 'ٹائر'],
                'tags'  => [
                    ['en' => 'Tyre', 'ur' => 'ٹائر'],
                    ['en' => 'Puncture', 'ur' => 'پنکچر'],
                ],
            ],
            [
                'match' => ['battery', 'بیٹری'],
                'tags'  => [
                    ['en' => 'Battery', 'ur' => 'بیٹری'],
                    ['en' => 'UPS Battery', 'ur' => 'یو پی ایس بیٹری'],
                ],
            ],
            [
                'match' => ['hardware', 'ہارڈ'],
                'tags'  => [
                    ['en' => 'Hardware', 'ur' => 'ہارڈویئر'],
                    ['en' => 'Tools', 'ur' => 'اوزار'],
                ],
            ],
            [
                'match' => ['building material', 'بلڈنگ'],
                'tags'  => [
                    ['en' => 'Building Material', 'ur' => 'تعمیراتی سامان'],
                    ['en' => 'Cement', 'ur' => 'سیمنٹ'],
                ],
            ],
            [
                'match' => ['paint', 'پینٹ'],
                'tags'  => [
                    ['en' => 'Paint', 'ur' => 'پینٹ'],
                    ['en' => 'Painter', 'ur' => 'پینٹر'],
                ],
            ],
            [
                'match' => ['steel', 'سٹیل'],
                'tags'  => [
                    ['en' => 'Steel', 'ur' => 'سٹیل'],
                    ['en' => 'Welding', 'ur' => 'ویلڈنگ'],
                ],
            ],
            [
                'match' => ['sanitary', 'سینٹری'],
                'tags'  => [
                    ['en' => 'Sanitary', 'ur' => 'سینٹری'],
                    ['en' => 'Bathroom', 'ur' => 'پلمبر'],
                ],
            ],
            [
                'match' => ['cycle', 'سائیکل'],
                'tags'  => [
                    ['en' => 'Cycle', 'ur' => 'سائیکل'],
                    ['en' => 'Bicycle', 'ur' => 'بائیسکل'],
                ],
            ],
            [
                'match' => ['fertilizer', 'feed', 'کھل', 'کھاد'],
                'tags'  => [
                    ['en' => 'Fertilizer', 'ur' => 'کھاد'],
                    ['en' => 'Animal Feed', 'ur' => 'جانوروں کا چارہ'],
                ],
            ],
            [
                'match' => ['agro', 'seed', 'ایگرو', 'سیڈ', 'زرعی'],
                'tags'  => [
                    ['en' => 'Agro', 'ur' => 'ایگرو'],
                    ['en' => 'Seeds', 'ur' => 'بیج'],
                    ['en' => 'Agriculture', 'ur' => 'زراعت'],
                ],
            ],
            [
                'match' => ['commission', 'arthi', 'آڑھتی'],
                'tags'  => [
                    ['en' => 'Arthi', 'ur' => 'آڑھتی'],
                    ['en' => 'Commission Agent', 'ur' => 'کمیشن ایجنٹ'],
                ],
            ],
            [
                'match' => ['tent', 'ٹینٹ'],
                'tags'  => [
                    ['en' => 'Tent Service', 'ur' => 'ٹینٹ سروس'],
                    ['en' => 'Wedding Tent', 'ur' => 'شادی ٹینٹ'],
                ],
            ],
            [
                'match' => ['photo', 'graphic', 'print', 'فوٹو', 'گرافکس'],
                'tags'  => [
                    ['en' => 'Photo Studio', 'ur' => 'فوٹو سٹوڈیو'],
                    ['en' => 'Printing', 'ur' => 'پرنٹنگ'],
                    ['en' => 'Graphics', 'ur' => 'گرافکس'],
                ],
            ],
            [
                'match' => ['furniture', 'sofa', 'فرنیچر', 'صوفہ'],
                'tags'  => [
                    ['en' => 'Furniture', 'ur' => 'فرنیچر'],
                    ['en' => 'Sofa', 'ur' => 'صوفہ'],
                ],
            ],
            [
                'match' => ['glass', 'mirror', 'شیشہ'],
                'tags'  => [
                    ['en' => 'Glass', 'ur' => 'شیشہ'],
                    ['en' => 'Mirror', 'ur' => 'آئینہ'],
                ],
            ],
            [
                'match' => ['book', 'بک', 'کتاب'],
                'tags'  => [
                    ['en' => 'Books', 'ur' => 'کتابیں'],
                    ['en' => 'Stationery', 'ur' => 'اسٹیشنری'],
                ],
            ],
            [
                'match' => ['jewellery', 'jewel', 'جیول'],
                'tags'  => [
                    ['en' => 'Jewellery', 'ur' => 'زیورات'],
                    ['en' => 'Gold', 'ur' => 'سونا'],
                ],
            ],
            [
                'match' => ['utensil', 'برتن'],
                'tags'  => [
                    ['en' => 'Utensils', 'ur' => 'برتن'],
                    ['en' => 'Kitchenware', 'ur' => 'باورچی خانہ'],
                ],
            ],
            [
                'match' => ['property', 'پراپرٹی'],
                'tags'  => [
                    ['en' => 'Property Dealer', 'ur' => 'پراپرٹی ڈیلر'],
                    ['en' => 'Real Estate', 'ur' => 'رئیل اسٹیٹ'],
                ],
            ],
            [
                'match' => ['laundry', 'dhobhi', 'دھوبی'],
                'tags'  => [
                    ['en' => 'Laundry', 'ur' => 'لانڈری'],
                    ['en' => 'Dhobi', 'ur' => 'دھوبی'],
                ],
            ],
            [
                'match' => ['embroidery', 'pico', 'کشیدہ', 'پیکو'],
                'tags'  => [
                    ['en' => 'Embroidery', 'ur' => 'کشیدہ کاری'],
                    ['en' => 'Pico', 'ur' => 'پیکو'],
                ],
            ],
            [
                'match' => ['marriage hall', 'میرج'],
                'tags'  => [
                    ['en' => 'Marriage Hall', 'ur' => 'میرج ہال'],
                    ['en' => 'Banquet', 'ur' => 'بینکوٹ'],
                ],
            ],
            [
                'match' => ['decoration', 'ڈیکور'],
                'tags'  => [
                    ['en' => 'Decoration', 'ur' => 'ڈیکوریشن'],
                    ['en' => 'Event Decor', 'ur' => 'ایونٹ ڈیکور'],
                ],
            ],
            [
                'match' => ['bangle', 'چوڑی'],
                'tags'  => [
                    ['en' => 'Bangles', 'ur' => 'چوڑیاں'],
                    ['en' => 'Choori', 'ur' => 'چوڑی سینٹر'],
                ],
            ],
            [
                'match' => ['hammam', 'حمّام', 'حمام'],
                'tags'  => [
                    ['en' => 'Hammam', 'ur' => 'حمّام'],
                    ['en' => 'Bath House', 'ur' => 'غسل خانہ'],
                ],
            ],
            [
                'match' => ['pan shop', 'پان'],
                'tags'  => [
                    ['en' => 'Pan Shop', 'ur' => 'پان شاپ'],
                    ['en' => 'Paan', 'ur' => 'پان'],
                ],
            ],
        ];
    }
}
