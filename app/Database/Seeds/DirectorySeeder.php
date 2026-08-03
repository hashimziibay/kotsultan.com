<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DirectorySeeder extends Seeder
{
    public function run()
    {
        // 1. Categories
        $categories = [
            [
                'name_en'       => 'Barbers & Salons',
                'name_ur'       => 'حجام اور سیلون',
                'slug'          => 'barbers-salons',
                'icon'          => 'scissors',
                'display_order' => 1,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Schools & Colleges',
                'name_ur'       => 'اسکول اور کالج',
                'slug'          => 'schools-colleges',
                'icon'          => 'graduation-cap',
                'display_order' => 2,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Clinics & Doctors',
                'name_ur'       => 'کلینک اور ڈاکٹرز',
                'slug'          => 'clinics-doctors',
                'icon'          => 'stethoscope',
                'display_order' => 3,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Mosques & Religious Places',
                'name_ur'       => 'مساجد اور مذہبی مقامات',
                'slug'          => 'mosques',
                'icon'          => 'building-2',
                'display_order' => 4,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Restaurants & Sweets',
                'name_ur'       => 'ریسٹورنٹس اور مٹھائیاں',
                'slug'          => 'restaurants-sweets',
                'icon'          => 'utensils',
                'display_order' => 5,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Medical Stores',
                'name_ur'       => 'میڈیکل اسٹورز',
                'slug'          => 'medical-stores',
                'icon'          => 'pill',
                'display_order' => 6,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Tailors & Cloth',
                'name_ur'       => 'درزی اور کپڑا',
                'slug'          => 'tailors-cloth',
                'icon'          => 'shirt',
                'display_order' => 7,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name_en'       => 'Mechanics & Autos',
                'name_ur'       => 'مکینک اور آٹو',
                'slug'          => 'mechanics-autos',
                'icon'          => 'wrench',
                'display_order' => 8,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($categories);

        // 2. Tags
        $tags = [
            ['name_en' => 'Emergency', 'name_ur' => 'ہنگامی', 'slug' => 'emergency', 'created_at' => date('Y-m-d H:i:s')],
            ['name_en' => '24/7 Open', 'name_ur' => '24 گھنٹے کھلا', 'slug' => '24-7', 'created_at' => date('Y-m-d H:i:s')],
            ['name_en' => 'Home Delivery', 'name_ur' => 'ہوم ڈیلیوری', 'slug' => 'home-delivery', 'created_at' => date('Y-m-d H:i:s')],
            ['name_en' => 'Government', 'name_ur' => 'سرکاری', 'slug' => 'government', 'created_at' => date('Y-m-d H:i:s')],
            ['name_en' => 'Private Specialist', 'name_ur' => 'پرائیویٹ ماہر', 'slug' => 'private-specialist', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('tags')->insertBatch($tags);

        // 3. Businesses
        $businesses = [
            [
                'category_id' => 3, // Clinics & Doctors
                'name_en'     => 'Al-Shafi Hospital & Clinic',
                'name_ur'     => 'الشافی ہسپتال و کلینک',
                'slug'        => 'al-shafi-hospital-clinic',
                'owner_name'  => 'Dr. Muhammad Aslam',
                'address'     => 'Main Multan Road, Near General Bus Stand, Kot Sultan',
                'phone'       => '0305-6660169',
                'whatsapp'    => '923056660169',
                'latitude'    => '30.7725',
                'longitude'   => '70.8524',
                'google_map'  => 'https://maps.google.com/?q=Kot+Sultan',
                'image'       => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=600',
                'featured'    => 1,
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 2, // Schools & Colleges
                'name_en'     => 'Government Higher Secondary School',
                'name_ur'     => 'گورنمنٹ ہائر سیکنڈری اسکول',
                'slug'        => 'govt-higher-secondary-school-kot-sultan',
                'owner_name'  => 'Govt. of Punjab',
                'address'     => 'College Road, Kot Sultan',
                'phone'       => '0301-7654321',
                'whatsapp'    => '923017654321',
                'latitude'    => '30.7730',
                'longitude'   => '70.8530',
                'google_map'  => 'https://maps.google.com/?q=Kot+Sultan',
                'image'       => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=600',
                'featured'    => 1,
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 5, // Restaurants & Sweets
                'name_en'     => 'Bismillah Bakers & Sweet House',
                'name_ur'     => 'بسم اللہ بیکرز اینڈ سویٹ ہاؤس',
                'slug'        => 'bismillah-bakers-sweet-house',
                'owner_name'  => 'Haji Rashid Ahmad',
                'address'     => 'Main Bazaar Market, Kot Sultan',
                'phone'       => '0302-8877665',
                'whatsapp'    => '923028877665',
                'latitude'    => '30.7718',
                'longitude'   => '70.8510',
                'google_map'  => 'https://maps.google.com/?q=Kot+Sultan',
                'image'       => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&q=80&w=600',
                'featured'    => 1,
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4, // Mosques
                'name_en'     => 'Jamia Masjid Ghausia',
                'name_ur'     => 'جامع مسجد غوثیہ',
                'slug'        => 'jamia-masjid-ghausia',
                'owner_name'  => 'Anjuman-e-Tajiran',
                'address'     => 'Near Old Station Road, Kot Sultan',
                'phone'       => '0303-1234567',
                'whatsapp'    => null,
                'latitude'    => '30.7740',
                'longitude'   => '70.8540',
                'google_map'  => 'https://maps.google.com/?q=Kot+Sultan',
                'image'       => 'https://images.unsplash.com/photo-1591604466107-ec97de577aff?auto=format&fit=crop&q=80&w=600',
                'featured'    => 0,
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 6, // Medical Stores
                'name_en'     => 'Kot Sultan Pharmacy & Medical Store',
                'name_ur'     => 'کوٹ سلطان فارمیسی اینڈ میڈیکل اسٹور',
                'slug'        => 'kot-sultan-pharmacy-medical-store',
                'owner_name'  => 'Tariq Mehmood',
                'address'     => 'Opposite THQ Hospital Road, Kot Sultan',
                'phone'       => '0304-9988776',
                'whatsapp'    => '923049988776',
                'latitude'    => '30.7720',
                'longitude'   => '70.8515',
                'google_map'  => 'https://maps.google.com/?q=Kot+Sultan',
                'image'       => 'https://images.unsplash.com/photo-1576602976047-174e57a47881?auto=format&fit=crop&q=80&w=600',
                'featured'    => 1,
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 8, // Mechanics
                'name_en'     => 'Master Tariq Auto Workshop',
                'name_ur'     => 'ماسٹر طارق آٹو ورکشاپ',
                'slug'        => 'master-tariq-auto-workshop',
                'owner_name'  => 'Ustad Tariq Hussain',
                'address'     => 'Bypass Road, Kot Sultan',
                'phone'       => '0305-4433221',
                'whatsapp'    => '923054433221',
                'latitude'    => '30.7750',
                'longitude'   => '70.8560',
                'google_map'  => 'https://maps.google.com/?q=Kot+Sultan',
                'image'       => 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&q=80&w=600',
                'featured'    => 0,
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('businesses')->insertBatch($businesses);

        // 4. Wall of Kot Sultan
        $wallEntries = [
            [
                'photo'            => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=400',
                'name_en'          => 'Haji Ghulam Qadir',
                'name_ur'          => 'حاجی غلام قادر',
                'intro_en'         => 'Prominent social worker and founder of free drinking water plants across Kot Sultan bazaars.',
                'intro_ur'         => 'معروف سماجی کارکن اور کوٹ سلطان کے بازاروں میں مفت پینے کے پانی کے پلانٹس کے بانی۔',
                'years_of_service' => '1975 - 2020',
                'display_order'    => 1,
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'photo'            => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=400',
                'name_en'          => 'Master Allah Ditta',
                'name_ur'          => 'ماسٹر اللہ دتہ',
                'intro_en'         => 'Devoted teacher who educated thousands of students in Kot Sultan over 40 honorable years.',
                'intro_ur'         => 'مخلص معلم جنہوں نے 40 سالہ شاندار تدریسی خدمات کے دوران کوٹ سلطان کے ہزاروں طلباء کی تربیت کی۔',
                'years_of_service' => '1980 - 2022',
                'display_order'    => 2,
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'photo'            => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=400',
                'name_en'          => 'Dr. Abdul Rehman',
                'name_ur'          => 'ڈاکٹر عبدالرحمٰن',
                'intro_en'         => 'Pioneer healthcare practitioner who served the poor and needy patients of Kot Sultan selflessly.',
                'intro_ur'         => 'صحت کے شعبے کے پائلٹ جنہوں نے کوٹ سلطان کے غریب اور مستحق مریضوں کی بلا معاوضہ خدمت کی۔',
                'years_of_service' => '1985 - Present',
                'display_order'    => 3,
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('wall_of_kot_sultan')->insertBatch($wallEntries);
    }
}
