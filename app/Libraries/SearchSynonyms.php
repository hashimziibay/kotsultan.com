<?php

namespace App\Libraries;

/**
 * Shared synonym / fuzzy-term packs for directory search + category tag sync.
 * Covers every business category with English, Roman-Urdu typos, and Urdu.
 */
class SearchSynonyms
{
    /**
     * @return list<array{match:list<string>,terms:list<string>,tags:list<array{en:string,ur:string}>}>
     */
    public static function packs(): array
    {
        return [
            [
                'match' => ['hospital', 'hospitals', 'ہسپتال'],
                'terms' => ['hospital', 'hospitals', 'hasptal', 'haspital', 'haspatal', 'haspataal', 'hospitel', 'haptital', 'hospitaal', 'asptal', 'اسپتال', 'ہسپتال'],
                'tags'  => [
                    ['en' => 'Hospital', 'ur' => 'ہسپتال'],
                    ['en' => 'Hasptal', 'ur' => 'اسپتال'],
                    ['en' => 'Clinic', 'ur' => 'کلینک'],
                    ['en' => 'Medical Centre', 'ur' => 'میڈیکل سینٹر'],
                ],
            ],
            [
                'match' => ['medical store', 'medical-stores', 'pharmacy', 'میڈیکل'],
                'terms' => ['medical store', 'medical stores', 'medicle store', 'medicel store', 'medikal store', 'medicalshop', 'pharmacy', 'farmaci', 'dawakhana', 'dawa khana', 'دواخانہ', 'فارمیسی', 'میڈیکل اسٹور', 'میڈیکل'],
                'tags'  => [
                    ['en' => 'Medical Store', 'ur' => 'میڈیکل اسٹور'],
                    ['en' => 'Pharmacy', 'ur' => 'فارمیسی'],
                    ['en' => 'Dawakhana', 'ur' => 'دواخانہ'],
                ],
            ],
            [
                'match' => ['clinic', 'doctor', 'کلینک', 'ڈاکٹر'],
                'terms' => ['clinic', 'clinics', 'klinic', 'doctor', 'doctors', 'dr', 'dokter', 'docter', 'کلینک', 'ڈاکٹر'],
                'tags'  => [
                    ['en' => 'Clinic', 'ur' => 'کلینک'],
                    ['en' => 'Doctor', 'ur' => 'ڈاکٹر'],
                    ['en' => 'OPD', 'ur' => 'او پی ڈی'],
                ],
            ],
            [
                'match' => ['dental', 'ڈینٹل'],
                'terms' => ['dental', 'dentist', 'dentel', 'dant', 'tooth', 'ڈینٹل', 'دانت'],
                'tags'  => [
                    ['en' => 'Dental', 'ur' => 'ڈینٹل'],
                    ['en' => 'Dentist', 'ur' => 'دانتوں کا ڈاکٹر'],
                    ['en' => 'Tooth Clinic', 'ur' => 'دانت کلینک'],
                ],
            ],
            [
                'match' => ['lab', 'labs', 'لیب'],
                'terms' => ['lab', 'labs', 'laboratory', 'pathology', 'blood test', 'لیب', 'لیبارٹری'],
                'tags'  => [
                    ['en' => 'Lab', 'ur' => 'لیب'],
                    ['en' => 'Laboratory', 'ur' => 'لیبارٹری'],
                    ['en' => 'Blood Test', 'ur' => 'بلڈ ٹیسٹ'],
                ],
            ],
            [
                'match' => ['x-ray', 'xray', 'ایکسرے'],
                'terms' => ['x-ray', 'xray', 'x ray', 'exray', 'ایکسرے', 'ایکس رے'],
                'tags'  => [
                    ['en' => 'X-Ray', 'ur' => 'ایکسرے'],
                    ['en' => 'Digital X Ray', 'ur' => 'ڈیجیٹل ایکسرے'],
                ],
            ],
            [
                'match' => ['ultrasound', 'الٹرا'],
                'terms' => ['ultrasound', 'ultra sound', 'sonography', 'ultasound', 'الٹرا ساؤنڈ', 'سونوگرافی'],
                'tags'  => [
                    ['en' => 'Ultrasound', 'ur' => 'الٹرا ساؤنڈ'],
                    ['en' => 'Sonography', 'ur' => 'سونوگرافی'],
                ],
            ],
            [
                'match' => ['homeopath', 'ہومیو'],
                'terms' => ['homeopath', 'homeopathic', 'homoeopath', 'homeo', 'ہومیوپیتھ', 'ہومیو'],
                'tags'  => [
                    ['en' => 'Homeopathic', 'ur' => 'ہومیوپیتھک'],
                    ['en' => 'Homeopath', 'ur' => 'ہومیوپیتھ'],
                ],
            ],
            [
                'match' => ['veterinary', 'ویٹرنری'],
                'terms' => ['veterinary', 'vet', 'veterinery', 'animal doctor', 'ویٹرنری', 'جانوروں کا ڈاکٹر'],
                'tags'  => [
                    ['en' => 'Veterinary', 'ur' => 'ویٹرنری'],
                    ['en' => 'Animal Doctor', 'ur' => 'جانوروں کا ڈاکٹر'],
                ],
            ],
            [
                'match' => ['herbal', 'desi medicine', 'pansaar', 'دیسی', 'پنسار'],
                'terms' => ['herbal', 'desi medicine', 'desi dawa', 'pansaar', 'pansar', 'hakeem', 'دیسی دوا', 'پنسار', 'حکیم'],
                'tags'  => [
                    ['en' => 'Herbal', 'ur' => 'جڑی بوٹی'],
                    ['en' => 'Desi Medicine', 'ur' => 'دیسی دوا'],
                    ['en' => 'Pansaar', 'ur' => 'پنسار'],
                ],
            ],
            [
                'match' => ['optical', 'آپٹیکل'],
                'terms' => ['optical', 'optician', 'glasses', 'specs', 'ainak', 'آپٹیکل', 'عینک'],
                'tags'  => [
                    ['en' => 'Optical', 'ur' => 'آپٹیکل'],
                    ['en' => 'Glasses', 'ur' => 'عینک'],
                ],
            ],
            [
                'match' => ['school', 'college', 'اسکول', 'کالج'],
                'terms' => ['school', 'schools', 'scool', 'skool', 'college', 'colleg', 'اسکول', 'سکول', 'کالج'],
                'tags'  => [
                    ['en' => 'School', 'ur' => 'اسکول'],
                    ['en' => 'College', 'ur' => 'کالج'],
                ],
            ],
            [
                'match' => ['mosque', 'masjid', 'مسجد'],
                'terms' => ['mosque', 'mosques', 'masjid', 'masjidain', 'jamia', 'مسجد', 'مساجد'],
                'tags'  => [
                    ['en' => 'Mosque', 'ur' => 'مسجد'],
                    ['en' => 'Masjid', 'ur' => 'مسجد'],
                ],
            ],
            [
                'match' => ['vegetable', 'سبزی'],
                'terms' => ['vegetable', 'vegetables', 'veg', 'sabzi', 'sabzi wala', 'سبزی', 'سبزی فروش'],
                'tags'  => [
                    ['en' => 'Vegetable', 'ur' => 'سبزی'],
                    ['en' => 'Sabzi', 'ur' => 'سبزی فروش'],
                    ['en' => 'Fruit & Veg', 'ur' => 'پھل سبزی'],
                ],
            ],
            [
                'match' => ['general store', 'general-stores', 'جنرل'],
                'terms' => ['general store', 'general stores', 'genral store', 'karyana', 'kiryana', 'جنرل اسٹور', 'کریانہ'],
                'tags'  => [
                    ['en' => 'General Store', 'ur' => 'جنرل اسٹور'],
                    ['en' => 'Karyana', 'ur' => 'کریانہ'],
                ],
            ],
            [
                'match' => ['grocery', 'کریانہ'],
                'terms' => ['grocery', 'groceries', 'grocry', 'kiryana', 'karyana store', 'کریانہ اسٹور'],
                'tags'  => [
                    ['en' => 'Grocery', 'ur' => 'کریانہ'],
                    ['en' => 'Kiryana', 'ur' => 'کریانہ اسٹور'],
                ],
            ],
            [
                'match' => ['super store', 'super-stores', 'سپر', 'سپر'],
                'terms' => ['super store', 'superstore', 'supermarket', 'سپر اسٹور', 'سپر مارکیٹ'],
                'tags'  => [
                    ['en' => 'Super Store', 'ur' => 'سپر اسٹور'],
                    ['en' => 'Mart', 'ur' => 'مارٹ'],
                ],
            ],
            [
                'match' => ['cash', 'carry', 'کیش'],
                'terms' => ['cash and carry', 'cash & carry', 'cash n carry', 'wholesale', 'کیش اینڈ کیری', 'ہول سیل'],
                'tags'  => [
                    ['en' => 'Cash & Carry', 'ur' => 'کیش اینڈ کیری'],
                    ['en' => 'Wholesale', 'ur' => 'ہول سیل'],
                ],
            ],
            [
                'match' => ['mart', 'mini market', 'مارٹ'],
                'terms' => ['mart', 'mini market', 'minimart', 'utility store', 'مارٹ', 'منی مارکیٹ', 'یوٹیلٹی'],
                'tags'  => [
                    ['en' => 'Mini Market', 'ur' => 'منی مارکیٹ'],
                    ['en' => 'Mart', 'ur' => 'مارٹ'],
                    ['en' => 'Utility Store', 'ur' => 'یوٹیلٹی اسٹور'],
                ],
            ],
            [
                'match' => ['tuck shop', 'ٹک'],
                'terms' => ['tuck shop', 'tuckshop', 'small store', 'khoka', 'ٹک شاپ', 'کھوکھا'],
                'tags'  => [
                    ['en' => 'Tuck Shop', 'ur' => 'ٹک شاپ'],
                    ['en' => 'Small Store', 'ur' => 'چھوٹی دکان'],
                ],
            ],
            [
                'match' => ['cloth', 'garment', 'کلاتھ', 'گارمنٹ'],
                'terms' => ['cloth', 'cloths', 'clothes', 'garment', 'garments', 'boutique', 'kapra', 'کپڑے', 'کلاتھ', 'گارمنٹس', 'بوٹیک'],
                'tags'  => [
                    ['en' => 'Cloth House', 'ur' => 'کلاتھ ہاؤس'],
                    ['en' => 'Garments', 'ur' => 'کپڑے'],
                    ['en' => 'Boutique', 'ur' => 'بوٹیک'],
                ],
            ],
            [
                'match' => ['shoe', 'شوز', 'چپل'],
                'terms' => ['shoe', 'shoes', 'shooz', 'chappal', 'sandal', 'جوتے', 'شوز', 'چپل'],
                'tags'  => [
                    ['en' => 'Shoes', 'ur' => 'جوتے'],
                    ['en' => 'Shoe Shop', 'ur' => 'شوز شاپ'],
                    ['en' => 'Chappal', 'ur' => 'چپل'],
                ],
            ],
            [
                'match' => ['second hand', 'lunda', 'لنڈا'],
                'terms' => ['second hand', 'secondhand', 'lunda', 'used clothes', 'لنڈا', 'پرانا کپڑا'],
                'tags'  => [
                    ['en' => 'Second Hand Clothes', 'ur' => 'لنڈا'],
                    ['en' => 'Lunda', 'ur' => 'لنڈا شاپ'],
                ],
            ],
            [
                'match' => ['hotel', 'guest', 'ہوٹل'],
                'terms' => ['hotel', 'hotels', 'guest house', 'guesthouse', 'lodge', 'ہوٹل', 'گیسٹ ہاؤس'],
                'tags'  => [
                    ['en' => 'Hotel', 'ur' => 'ہوٹل'],
                    ['en' => 'Guest House', 'ur' => 'گیسٹ ہاؤس'],
                    ['en' => 'Lodge', 'ur' => 'لاج'],
                ],
            ],
            [
                'match' => ['fast food', 'restaurant', 'فاسٹ', 'ریستوران'],
                'terms' => ['fast food', 'fastfood', 'restaurant', 'resturant', 'restoran', 'burger', 'pizza', 'فاسٹ فوڈ', 'ریستوران', 'برگر'],
                'tags'  => [
                    ['en' => 'Fast Food', 'ur' => 'فاسٹ فوڈ'],
                    ['en' => 'Restaurant', 'ur' => 'ریستوران'],
                    ['en' => 'Burger', 'ur' => 'برگر'],
                ],
            ],
            [
                'match' => ['sweet', 'bakery', 'سویٹ', 'بیکر', 'مٹھائی'],
                'terms' => ['sweet', 'sweets', 'bakery', 'baker', 'mithai', 'halwa', 'مٹھائی', 'بیکری', 'سویٹ'],
                'tags'  => [
                    ['en' => 'Sweets', 'ur' => 'مٹھائی'],
                    ['en' => 'Bakery', 'ur' => 'بیکری'],
                    ['en' => 'Mithai', 'ur' => 'مٹھائی'],
                ],
            ],
            [
                'match' => ['biryani', 'بریانی', 'پکوان'],
                'terms' => ['biryani', 'biryani centre', 'pakwan', 'بریانی', 'پکوان'],
                'tags'  => [
                    ['en' => 'Biryani', 'ur' => 'بریانی'],
                    ['en' => 'Pakwan', 'ur' => 'پکوان'],
                ],
            ],
            [
                'match' => ['milk', 'yogurt', 'دودھ', 'دہی'],
                'terms' => ['milk', 'yogurt', 'yoghurt', 'dahi', 'dairy', 'دودھ', 'دہی', 'ڈیری'],
                'tags'  => [
                    ['en' => 'Milk', 'ur' => 'دودھ'],
                    ['en' => 'Yogurt', 'ur' => 'دہی'],
                    ['en' => 'Dairy', 'ur' => 'ڈیری'],
                ],
            ],
            [
                'match' => ['chicken', 'چکن'],
                'terms' => ['chicken', 'poultry', 'murghi', 'چکن', 'مرغی'],
                'tags'  => [
                    ['en' => 'Chicken', 'ur' => 'چکن'],
                    ['en' => 'Poultry', 'ur' => 'مرغی'],
                ],
            ],
            [
                'match' => ['beef', 'meat', 'بیف', 'گوشت'],
                'terms' => ['beef', 'meat', 'butcher', 'qasai', 'گوشت', 'بیف', 'قصائی'],
                'tags'  => [
                    ['en' => 'Meat', 'ur' => 'گوشت'],
                    ['en' => 'Beef', 'ur' => 'بیف'],
                    ['en' => 'Butcher', 'ur' => 'قصائی'],
                ],
            ],
            [
                'match' => ['fish', 'فش'],
                'terms' => ['fish', 'fish shop', 'machli', 'فش', 'مچھلی'],
                'tags'  => [
                    ['en' => 'Fish', 'ur' => 'مچھلی'],
                    ['en' => 'Fish Shop', 'ur' => 'فش شاپ'],
                ],
            ],
            [
                'match' => ['tea', 'چائے', 'ٹی سٹال'],
                'terms' => ['tea', 'tea stall', 'chai', 'چائے', 'ٹی سٹال'],
                'tags'  => [
                    ['en' => 'Tea Stall', 'ur' => 'چائے کی دکان'],
                    ['en' => 'Chai', 'ur' => 'چائے'],
                ],
            ],
            [
                'match' => ['juice', 'جوس'],
                'terms' => ['juice', 'juice corner', 'cold drink', 'جوس', 'ٹھنڈا'],
                'tags'  => [
                    ['en' => 'Juice', 'ur' => 'جوس'],
                    ['en' => 'Cold Drink', 'ur' => 'ٹھنڈا مشروب'],
                ],
            ],
            [
                'match' => ['pan shop', 'پان'],
                'terms' => ['pan shop', 'paan', 'pan', 'پان شاپ', 'پان'],
                'tags'  => [
                    ['en' => 'Pan Shop', 'ur' => 'پان شاپ'],
                    ['en' => 'Paan', 'ur' => 'پان'],
                ],
            ],
            [
                'match' => ['petrol', 'cng', 'پیٹرول', 'سی این جی'],
                'terms' => ['petrol', 'petrol pump', 'cng', 'fuel', 'diesel', 'پیٹرول', 'سی این جی', 'ایندھن'],
                'tags'  => [
                    ['en' => 'Petrol Pump', 'ur' => 'پیٹرول پمپ'],
                    ['en' => 'CNG', 'ur' => 'سی این جی'],
                    ['en' => 'Fuel', 'ur' => 'ایندھن'],
                ],
            ],
            [
                'match' => ['gas station', 'گیس'],
                'terms' => ['gas', 'gas station', 'sui gas', 'گیس', 'سوئی گیس'],
                'tags'  => [
                    ['en' => 'Gas', 'ur' => 'گیس'],
                    ['en' => 'Sui Gas', 'ur' => 'سوئی گیس'],
                ],
            ],
            [
                'match' => ['bus', 'terminal', 'ٹرمینل'],
                'terms' => ['bus', 'bus stand', 'terminal', 'addaa', 'بس', 'بس سٹینڈ', 'ٹرمینل'],
                'tags'  => [
                    ['en' => 'Bus Stand', 'ur' => 'بس سٹینڈ'],
                    ['en' => 'Terminal', 'ur' => 'ٹرمینل'],
                ],
            ],
            [
                'match' => ['flour', 'آٹا', 'چکی'],
                'terms' => ['flour', 'flour mill', 'atta', 'atta chakki', 'آٹا', 'آٹا چکی'],
                'tags'  => [
                    ['en' => 'Flour Mill', 'ur' => 'آٹا چکی'],
                    ['en' => 'Atta Chakki', 'ur' => 'آٹا چکی'],
                ],
            ],
            [
                'match' => ['salt mill', 'نمک'],
                'terms' => ['salt', 'salt mill', 'namak', 'نمک چکی', 'نمک'],
                'tags'  => [
                    ['en' => 'Salt Mill', 'ur' => 'نمک چکی'],
                    ['en' => 'Namak', 'ur' => 'نمک'],
                ],
            ],
            [
                'match' => ['oil trader', 'آئل'],
                'terms' => ['oil', 'oil trader', 'tel', 'آئل', 'تیل'],
                'tags'  => [
                    ['en' => 'Oil Trader', 'ur' => 'آئل ٹریڈر'],
                    ['en' => 'Oil', 'ur' => 'تیل'],
                ],
            ],
            [
                'match' => ['motorcycle', 'honda', 'موٹر سائیکل', 'ہنڈا'],
                'terms' => ['motorcycle', 'motorbike', 'bike', 'honda', 'موٹر سائیکل', 'بائیک', 'ہونڈا'],
                'tags'  => [
                    ['en' => 'Motorcycle', 'ur' => 'موٹر سائیکل'],
                    ['en' => 'Bike Mechanic', 'ur' => 'بائیک مکینک'],
                    ['en' => 'Honda', 'ur' => 'ہونڈا'],
                ],
            ],
            [
                'match' => ['auto exchange', 'car dealer', 'آٹو', 'کار'],
                'terms' => ['auto exchange', 'car dealer', 'car', 'motor dealer', 'آٹو', 'کار ڈیلر', 'موٹر'],
                'tags'  => [
                    ['en' => 'Car Dealer', 'ur' => 'کار ڈیلر'],
                    ['en' => 'Auto Exchange', 'ur' => 'آٹو ایکسچینج'],
                ],
            ],
            [
                'match' => ['mechanic', 'میکینک', 'مکینک'],
                'terms' => ['mechanic', 'mechanics', 'workshop', 'garaj', 'مکینک', 'ورکشاپ'],
                'tags'  => [
                    ['en' => 'Mechanic', 'ur' => 'مکینک'],
                    ['en' => 'Workshop', 'ur' => 'ورکشاپ'],
                ],
            ],
            [
                'match' => ['tyre', 'ٹائر'],
                'terms' => ['tyre', 'tire', 'puncture', 'ٹائر', 'پنکچر'],
                'tags'  => [
                    ['en' => 'Tyre', 'ur' => 'ٹائر'],
                    ['en' => 'Puncture', 'ur' => 'پنکچر'],
                ],
            ],
            [
                'match' => ['battery', 'بیٹری'],
                'terms' => ['battery', 'batteries', 'ups', 'بیٹری', 'یو پی ایس'],
                'tags'  => [
                    ['en' => 'Battery', 'ur' => 'بیٹری'],
                    ['en' => 'UPS Battery', 'ur' => 'یو پی ایس بیٹری'],
                ],
            ],
            [
                'match' => ['cycle', 'سائیکل'],
                'terms' => ['cycle', 'bicycle', 'bike cycle', 'سائیکل', 'بائیسکل'],
                'tags'  => [
                    ['en' => 'Cycle', 'ur' => 'سائیکل'],
                    ['en' => 'Bicycle', 'ur' => 'بائیسکل'],
                ],
            ],
            [
                'match' => ['truck', 'ٹرک'],
                'terms' => ['truck', 'truck service', 'lorry', 'ٹرک', 'لاری'],
                'tags'  => [
                    ['en' => 'Truck Service', 'ur' => 'ٹرک سروس'],
                    ['en' => 'Lorry', 'ur' => 'لاری'],
                ],
            ],
            [
                'match' => ['barber', 'salon', 'حجام', 'سیلون'],
                'terms' => ['barber', 'salon', 'hair', 'hajam', 'حجام', 'سیلون'],
                'tags'  => [
                    ['en' => 'Barber', 'ur' => 'حجام'],
                    ['en' => 'Salon', 'ur' => 'سیلون'],
                ],
            ],
            [
                'match' => ['beauty', 'بیوٹی', 'parlour'],
                'terms' => ['beauty', 'beauty parlour', 'parlor', 'salon beauty', 'بیوٹی پارلر', 'سیلون'],
                'tags'  => [
                    ['en' => 'Beauty Parlour', 'ur' => 'بیوٹی پارلر'],
                    ['en' => 'Salon', 'ur' => 'سیلون'],
                ],
            ],
            [
                'match' => ['tailor', 'ٹیلر', 'درزی'],
                'terms' => ['tailor', 'tailors', 'stitching', 'darzi', 'ٹیلر', 'درزی', 'سلائی'],
                'tags'  => [
                    ['en' => 'Tailor', 'ur' => 'درزی'],
                    ['en' => 'Stitching', 'ur' => 'سلائی'],
                ],
            ],
            [
                'match' => ['embroidery', 'pico', 'کشیدہ', 'پیکو'],
                'terms' => ['embroidery', 'pico', 'kusida', 'کشیدہ', 'پیکو', 'کشیدہ کاری'],
                'tags'  => [
                    ['en' => 'Embroidery', 'ur' => 'کشیدہ کاری'],
                    ['en' => 'Pico', 'ur' => 'پیکو'],
                ],
            ],
            [
                'match' => ['mobile', 'موبائل'],
                'terms' => ['mobile', 'mobile repair', 'phone', 'cellphone', 'موبائل', 'فون'],
                'tags'  => [
                    ['en' => 'Mobile', 'ur' => 'موبائل'],
                    ['en' => 'Mobile Repair', 'ur' => 'موبائل مرمت'],
                    ['en' => 'Phone Shop', 'ur' => 'فون شاپ'],
                ],
            ],
            [
                'match' => ['electric', 'الیکٹری'],
                'terms' => ['electric', 'electrician', 'bijli', 'الیکٹریشین', 'بجلی'],
                'tags'  => [
                    ['en' => 'Electrician', 'ur' => 'الیکٹریشین'],
                    ['en' => 'Bijli', 'ur' => 'بجلی'],
                ],
            ],
            [
                'match' => ['electronic', 'الیکٹرانک'],
                'terms' => ['electronic', 'electronics', 'tv', 'ٹی وی', 'الیکٹرانکس'],
                'tags'  => [
                    ['en' => 'Electronics', 'ur' => 'الیکٹرانکس'],
                    ['en' => 'TV Shop', 'ur' => 'ٹی وی شاپ'],
                ],
            ],
            [
                'match' => ['ac ', 'refrigerat', 'ایئر', 'ریفر'],
                'terms' => ['ac', 'air conditioner', 'fridge', 'refrigerator', 'اے سی', 'فریج', 'ریفریجریٹر'],
                'tags'  => [
                    ['en' => 'AC Repair', 'ur' => 'اے سی مرمت'],
                    ['en' => 'Fridge', 'ur' => 'فریج'],
                ],
            ],
            [
                'match' => ['plumber', 'پلمبر'],
                'terms' => ['plumber', 'plumbing', 'pipe', 'پلمبر', 'پائپ'],
                'tags'  => [
                    ['en' => 'Plumber', 'ur' => 'پلمبر'],
                    ['en' => 'Plumbing', 'ur' => 'پلمبنگ'],
                ],
            ],
            [
                'match' => ['hardware', 'ہارڈ'],
                'terms' => ['hardware', 'tools', 'ہارڈویئر', 'اوزار'],
                'tags'  => [
                    ['en' => 'Hardware', 'ur' => 'ہارڈویئر'],
                    ['en' => 'Tools', 'ur' => 'اوزار'],
                ],
            ],
            [
                'match' => ['building material', 'بلڈنگ'],
                'terms' => ['building material', 'cement', 'sand', 'بلڈنگ', 'سیمنٹ', 'ریت'],
                'tags'  => [
                    ['en' => 'Building Material', 'ur' => 'تعمیراتی سامان'],
                    ['en' => 'Cement', 'ur' => 'سیمنٹ'],
                ],
            ],
            [
                'match' => ['paint', 'پینٹ'],
                'terms' => ['paint', 'painter', 'پینٹ', 'پینٹر'],
                'tags'  => [
                    ['en' => 'Paint', 'ur' => 'پینٹ'],
                    ['en' => 'Painter', 'ur' => 'پینٹر'],
                ],
            ],
            [
                'match' => ['steel', 'سٹیل'],
                'terms' => ['steel', 'welding', 'fabrication', 'سٹیل', 'ویلڈنگ'],
                'tags'  => [
                    ['en' => 'Steel', 'ur' => 'سٹیل'],
                    ['en' => 'Welding', 'ur' => 'ویلڈنگ'],
                ],
            ],
            [
                'match' => ['sanitary', 'سینٹری'],
                'terms' => ['sanitary', 'bathroom fittings', 'سینٹری'],
                'tags'  => [
                    ['en' => 'Sanitary', 'ur' => 'سینٹری'],
                    ['en' => 'Plumber', 'ur' => 'پلمبر'],
                ],
            ],
            [
                'match' => ['fertilizer', 'feed', 'کھل', 'کھاد'],
                'terms' => ['fertilizer', 'fertiliser', 'feed', 'khad', 'کھاد', 'چارہ'],
                'tags'  => [
                    ['en' => 'Fertilizer', 'ur' => 'کھاد'],
                    ['en' => 'Animal Feed', 'ur' => 'جانوروں کا چارہ'],
                ],
            ],
            [
                'match' => ['agro', 'seed', 'ایگرو', 'سیڈ', 'زرعی'],
                'terms' => ['agro', 'seed', 'seeds', 'agriculture', 'ایگرو', 'بیج', 'زراعت'],
                'tags'  => [
                    ['en' => 'Agro', 'ur' => 'ایگرو'],
                    ['en' => 'Seeds', 'ur' => 'بیج'],
                    ['en' => 'Agriculture', 'ur' => 'زراعت'],
                ],
            ],
            [
                'match' => ['commission', 'arthi', 'آڑھتی'],
                'terms' => ['commission', 'arthi', 'aarti', 'آڑھتی', 'کمیشن'],
                'tags'  => [
                    ['en' => 'Arthi', 'ur' => 'آڑھتی'],
                    ['en' => 'Commission Agent', 'ur' => 'کمیشن ایجنٹ'],
                ],
            ],
            [
                'match' => ['tent', 'ٹینٹ'],
                'terms' => ['tent', 'tent service', 'wedding tent', 'ٹینٹ', 'شامیانہ'],
                'tags'  => [
                    ['en' => 'Tent Service', 'ur' => 'ٹینٹ سروس'],
                    ['en' => 'Wedding Tent', 'ur' => 'شادی ٹینٹ'],
                ],
            ],
            [
                'match' => ['photo', 'graphic', 'print', 'فوٹو', 'گرافکس'],
                'terms' => ['photo', 'studio', 'graphics', 'printing', 'photocopy', 'فوٹو', 'گرافکس', 'پرنٹنگ', 'فوٹو کاپی'],
                'tags'  => [
                    ['en' => 'Photo Studio', 'ur' => 'فوٹو سٹوڈیو'],
                    ['en' => 'Printing', 'ur' => 'پرنٹنگ'],
                    ['en' => 'Graphics', 'ur' => 'گرافکس'],
                    ['en' => 'Photocopy', 'ur' => 'فوٹو کاپی'],
                ],
            ],
            [
                'match' => ['furniture', 'sofa', 'فرنیچر', 'صوفہ'],
                'terms' => ['furniture', 'sofa', 'upholstery', 'فرنیچر', 'صوفہ'],
                'tags'  => [
                    ['en' => 'Furniture', 'ur' => 'فرنیچر'],
                    ['en' => 'Sofa', 'ur' => 'صوفہ'],
                ],
            ],
            [
                'match' => ['glass', 'mirror', 'شیشہ'],
                'terms' => ['glass', 'mirror', 'sheesha', 'شیشہ', 'آئینہ'],
                'tags'  => [
                    ['en' => 'Glass', 'ur' => 'شیشہ'],
                    ['en' => 'Mirror', 'ur' => 'آئینہ'],
                ],
            ],
            [
                'match' => ['fan', 'ceiling', 'پنکھا'],
                'terms' => ['fan', 'ceiling fan', 'پنکھا', 'سیلنگ'],
                'tags'  => [
                    ['en' => 'Ceiling Fan', 'ur' => 'سیلنگ پنکھا'],
                    ['en' => 'Fan', 'ur' => 'پنکھا'],
                ],
            ],
            [
                'match' => ['book', 'بک', 'کتاب'],
                'terms' => ['book', 'books', 'stationery', 'old book', 'کتاب', 'کتابیں', 'اسٹیشنری'],
                'tags'  => [
                    ['en' => 'Books', 'ur' => 'کتابیں'],
                    ['en' => 'Stationery', 'ur' => 'اسٹیشنری'],
                    ['en' => 'Old Books', 'ur' => 'پرانی کتابیں'],
                ],
            ],
            [
                'match' => ['jewellery', 'jewel', 'جیول'],
                'terms' => ['jewellery', 'jewelry', 'jeweller', 'gold', 'زیورات', 'سونا', 'جیولر'],
                'tags'  => [
                    ['en' => 'Jewellery', 'ur' => 'زیورات'],
                    ['en' => 'Gold', 'ur' => 'سونا'],
                ],
            ],
            [
                'match' => ['utensil', 'برتن'],
                'terms' => ['utensil', 'utensils', 'kitchenware', 'برتن'],
                'tags'  => [
                    ['en' => 'Utensils', 'ur' => 'برتن'],
                    ['en' => 'Kitchenware', 'ur' => 'باورچی خانہ'],
                ],
            ],
            [
                'match' => ['bangle', 'چوڑی'],
                'terms' => ['bangle', 'bangles', 'choori', 'چوڑی', 'چوڑیاں'],
                'tags'  => [
                    ['en' => 'Bangles', 'ur' => 'چوڑیاں'],
                    ['en' => 'Choori', 'ur' => 'چوڑی سینٹر'],
                ],
            ],
            [
                'match' => ['property', 'پراپرٹی'],
                'terms' => ['property', 'property dealer', 'real estate', 'پراپرٹی', 'رئیل اسٹیٹ'],
                'tags'  => [
                    ['en' => 'Property Dealer', 'ur' => 'پراپرٹی ڈیلر'],
                    ['en' => 'Real Estate', 'ur' => 'رئیل اسٹیٹ'],
                ],
            ],
            [
                'match' => ['laundry', 'dhobhi', 'دھوبی'],
                'terms' => ['laundry', 'dhobi', 'dry clean', 'لانڈری', 'دھوبی'],
                'tags'  => [
                    ['en' => 'Laundry', 'ur' => 'لانڈری'],
                    ['en' => 'Dhobi', 'ur' => 'دھوبی'],
                ],
            ],
            [
                'match' => ['marriage hall', 'میرج'],
                'terms' => ['marriage hall', 'banquet', 'wedding hall', 'میرج ہال', 'بینکوٹ'],
                'tags'  => [
                    ['en' => 'Marriage Hall', 'ur' => 'میرج ہال'],
                    ['en' => 'Banquet', 'ur' => 'بینکوٹ'],
                ],
            ],
            [
                'match' => ['decoration', 'ڈیکور'],
                'terms' => ['decoration', 'decor', 'event decor', 'ڈیکوریشن'],
                'tags'  => [
                    ['en' => 'Decoration', 'ur' => 'ڈیکوریشن'],
                    ['en' => 'Event Decor', 'ur' => 'ایونٹ ڈیکور'],
                ],
            ],
            [
                'match' => ['hammam', 'حمّام', 'حمام'],
                'terms' => ['hammam', 'bath house', 'حمام', 'حمّام'],
                'tags'  => [
                    ['en' => 'Hammam', 'ur' => 'حمّام'],
                    ['en' => 'Bath House', 'ur' => 'غسل خانہ'],
                ],
            ],
            [
                'match' => ['key maker', 'locksmith', 'چابی'],
                'terms' => ['key', 'key maker', 'locksmith', 'chabi', 'چابی', 'تالا'],
                'tags'  => [
                    ['en' => 'Key Maker', 'ur' => 'چابی میکر'],
                    ['en' => 'Locksmith', 'ur' => 'تالا ساز'],
                ],
            ],
            [
                'match' => ['internet', 'cafe', 'نیٹ'],
                'terms' => ['internet', 'internet cafe', 'net cafe', 'wifi', 'نیٹ کیفے'],
                'tags'  => [
                    ['en' => 'Internet Cafe', 'ur' => 'نیٹ کیفے'],
                    ['en' => 'Net Cafe', 'ur' => 'انٹرنیٹ کیفے'],
                ],
            ],
            [
                'match' => ['toy', 'کھلونا'],
                'terms' => ['toy', 'toys', 'کھلونے'],
                'tags'  => [
                    ['en' => 'Toys', 'ur' => 'کھلونے'],
                    ['en' => 'Toy Shop', 'ur' => 'کھلونے کی دکان'],
                ],
            ],
            [
                'match' => ['sports', 'اسپورٹس'],
                'terms' => ['sports', 'sport', 'اسپورٹس'],
                'tags'  => [
                    ['en' => 'Sports', 'ur' => 'اسپورٹس'],
                    ['en' => 'Sports Centre', 'ur' => 'اسپورٹس سینٹر'],
                ],
            ],
            [
                'match' => ['bag', 'luggage', 'بیگ'],
                'terms' => ['bag', 'bags', 'luggage', 'suitcase', 'بیگ', 'سامان'],
                'tags'  => [
                    ['en' => 'Bags', 'ur' => 'بیگ'],
                    ['en' => 'Luggage', 'ur' => 'سامان'],
                ],
            ],
            [
                'match' => ['carpet', 'foam', 'کارپٹ'],
                'terms' => ['carpet', 'foam', 'mattress', 'کارپٹ', 'فوم'],
                'tags'  => [
                    ['en' => 'Carpet', 'ur' => 'کارپٹ'],
                    ['en' => 'Foam', 'ur' => 'فوم'],
                ],
            ],
            [
                'match' => ['bedsheet', 'بیڈشیٹ'],
                'terms' => ['bedsheet', 'bed sheet', 'بیڈشیٹ', 'چادر'],
                'tags'  => [
                    ['en' => 'Bedsheet', 'ur' => 'بیڈشیٹ'],
                    ['en' => 'Chadar', 'ur' => 'چادر'],
                ],
            ],
            [
                'match' => ['charpai', 'چارپائی'],
                'terms' => ['charpai', 'charpoy', 'bed', 'چارپائی'],
                'tags'  => [
                    ['en' => 'Charpai', 'ur' => 'چارپائی'],
                    ['en' => 'Bed Store', 'ur' => 'بستر'],
                ],
            ],
            [
                'match' => ['sewing machine', 'سلائی مشین'],
                'terms' => ['sewing machine', 'silai machine', 'سلائی مشین'],
                'tags'  => [
                    ['en' => 'Sewing Machine', 'ur' => 'سلائی مشین'],
                    ['en' => 'Machine Repair', 'ur' => 'مشین مرمت'],
                ],
            ],
            [
                'match' => ['garland', 'floral', 'ہار'],
                'terms' => ['garland', 'flower', 'floral', 'haar', 'ہار', 'پھول'],
                'tags'  => [
                    ['en' => 'Garland', 'ur' => 'ہار'],
                    ['en' => 'Florist', 'ur' => 'پھول فروش'],
                ],
            ],
            [
                'match' => ['lace', 'لیس'],
                'terms' => ['lace', 'trimming', 'لیس', 'گोटा'],
                'tags'  => [
                    ['en' => 'Lace', 'ur' => 'لیس'],
                    ['en' => 'Trimming', 'ur' => 'گوتا'],
                ],
            ],
            [
                'match' => ['rope', 'ban', 'بان'],
                'terms' => ['rope', 'ban', 'rassi', 'بان', 'رسی'],
                'tags'  => [
                    ['en' => 'Rope', 'ur' => 'رسی'],
                    ['en' => 'Ban', 'ur' => 'بان'],
                ],
            ],
            [
                'match' => ['tobacco', 'تمباکو'],
                'terms' => ['tobacco', 'cigarette', 'تمباکو', 'سگریٹ'],
                'tags'  => [
                    ['en' => 'Tobacco', 'ur' => 'تمباکو'],
                    ['en' => 'Cigarette', 'ur' => 'سگریٹ'],
                ],
            ],
            [
                'match' => ['quail', 'battair', 'بٹیر'],
                'terms' => ['quail', 'battair', 'bater', 'بٹیر'],
                'tags'  => [
                    ['en' => 'Quail', 'ur' => 'بٹیر'],
                    ['en' => 'Battair', 'ur' => 'بٹیر'],
                ],
            ],
            [
                'match' => ['insurance', 'state life', 'انشورنس'],
                'terms' => ['insurance', 'state life', 'بیمہ', 'اسٹیٹ لائف'],
                'tags'  => [
                    ['en' => 'Insurance', 'ur' => 'بیمہ'],
                    ['en' => 'State Life', 'ur' => 'اسٹیٹ لائف'],
                ],
            ],
            [
                'match' => ['weighing', 'scale', 'کانٹا'],
                'terms' => ['weighing', 'scale', 'computer scale', 'کانٹا', 'ترازو'],
                'tags'  => [
                    ['en' => 'Weighing Scale', 'ur' => 'کانٹا'],
                    ['en' => 'Computer Scale', 'ur' => 'کمپیوٹر کانٹا'],
                ],
            ],
            [
                'match' => ['patwari', 'land registry', 'پٹواری'],
                'terms' => ['patwari', 'land', 'registry', 'پٹواری', 'رجسٹری'],
                'tags'  => [
                    ['en' => 'Patwari', 'ur' => 'پٹواری'],
                    ['en' => 'Land Registry', 'ur' => 'رجسٹری'],
                ],
            ],
            [
                'match' => ['stamp', 'اسٹام'],
                'terms' => ['stamp', 'stamp paper', 'اسٹامپ', 'اسٹام'],
                'tags'  => [
                    ['en' => 'Stamp Vendor', 'ur' => 'اسٹام فروش'],
                    ['en' => 'Stamp Paper', 'ur' => 'اسٹام پیپر'],
                ],
            ],
            [
                'match' => ['press', 'media', 'news', 'پریس', 'نیوز'],
                'terms' => ['press club', 'media', 'news agency', 'پریس', 'میڈیا', 'نیوز'],
                'tags'  => [
                    ['en' => 'Press Club', 'ur' => 'پریس کلب'],
                    ['en' => 'News Agency', 'ur' => 'نیوز ایجنسی'],
                ],
            ],
            [
                'match' => ['trunk', 'wardrobe', 'الماری'],
                'terms' => ['trunk', 'wardrobe', 'almirah', 'ٹرنک', 'الماری'],
                'tags'  => [
                    ['en' => 'Trunk', 'ur' => 'ٹرنک'],
                    ['en' => 'Wardrobe', 'ur' => 'الماری'],
                ],
            ],
            // Emergency / civic (also used by emergency search)
            [
                'match' => ['police', 'پولیس'],
                'terms' => ['police', 'polis', 'polise', 'pulice', 'poilce', 'پولیس'],
                'tags'  => [
                    ['en' => 'Police', 'ur' => 'پولیس'],
                ],
            ],
            [
                'match' => ['rescue', '1122', 'ریسکیو'],
                'terms' => ['rescue', 'riscue', '1122', 'ریسکیو'],
                'tags'  => [
                    ['en' => 'Rescue 1122', 'ur' => 'ریسکیو 1122'],
                ],
            ],
            [
                'match' => ['ambulance', 'امبولینس'],
                'terms' => ['ambulance', 'ambulence', 'ambulanc', 'امبولینس', 'ایمبولینس'],
                'tags'  => [
                    ['en' => 'Ambulance', 'ur' => 'امبولینس'],
                ],
            ],
            [
                'match' => ['nadra', 'نادرا'],
                'terms' => ['nadra', 'نادرا'],
                'tags'  => [
                    ['en' => 'NADRA', 'ur' => 'نادرا'],
                ],
            ],
            [
                'match' => ['court', 'kachehri', 'عدالت'],
                'terms' => ['court', 'kachehri', 'عدالت', 'کچہری'],
                'tags'  => [
                    ['en' => 'Court', 'ur' => 'عدالت'],
                ],
            ],
            [
                'match' => ['railway', 'ریلوے'],
                'terms' => ['railway', 'railways', 'ریلوے'],
                'tags'  => [
                    ['en' => 'Railway', 'ur' => 'ریلوے'],
                ],
            ],
            [
                'match' => ['post office', 'ڈاکخانہ'],
                'terms' => ['post office', 'dakkhana', 'ڈاکخانہ'],
                'tags'  => [
                    ['en' => 'Post Office', 'ur' => 'ڈاکخانہ'],
                ],
            ],
            [
                'match' => ['fire', 'آگ'],
                'terms' => ['fire', 'fire brigade', 'آگ', 'فائر بریگیڈ'],
                'tags'  => [
                    ['en' => 'Fire Brigade', 'ur' => 'فائر بریگیڈ'],
                ],
            ],
            [
                'match' => ['bank', 'بینک'],
                'terms' => ['bank', 'بنک', 'بینک'],
                'tags'  => [
                    ['en' => 'Bank', 'ur' => 'بینک'],
                ],
            ],
            [
                'match' => ['market', 'bazaar', 'مارکیٹ'],
                'terms' => ['market', 'mandi', 'bazaar', 'bazar', 'مارکیٹ', 'بازار', 'منڈی'],
                'tags'  => [
                    ['en' => 'Market', 'ur' => 'مارکیٹ'],
                    ['en' => 'Bazaar', 'ur' => 'بازار'],
                ],
            ],
        ];
    }

    /**
     * Flat term groups for fuzzy LIKE / SOUNDEX expansion.
     *
     * @return list<list<string>>
     */
    public static function forSearch(): array
    {
        $groups = [];
        foreach (self::packs() as $pack) {
            $terms = array_merge($pack['match'] ?? [], $pack['terms'] ?? []);
            foreach ($pack['tags'] ?? [] as $tag) {
                $terms[] = (string) ($tag['en'] ?? '');
                $terms[] = (string) ($tag['ur'] ?? '');
            }
            $terms = array_values(array_unique(array_filter(array_map(static fn ($t) => trim((string) $t), $terms))));
            if ($terms !== []) {
                $groups[] = $terms;
            }
        }

        foreach (self::categoryTermGroups() as $dynamic) {
            $groups[] = $dynamic;
        }

        return $groups;
    }

    /**
     * Tag sync packs (match needles + bilingual tags).
     *
     * @return list<array{match:list<string>,tags:list<array{en:string,ur:string}>}>
     */
    public static function forTagSync(): array
    {
        $out = [];
        foreach (self::packs() as $pack) {
            $out[] = [
                'match' => $pack['match'],
                'tags'  => $pack['tags'],
            ];
        }
        return $out;
    }

    /**
     * Add every live category name/slug as its own fuzzy group so search
     * works even when a pack was not hand-written for that category.
     *
     * @return list<list<string>>
     */
    public static function categoryTermGroups(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $cache = [];
        try {
            $rows = \Config\Database::connect()
                ->table('categories')
                ->select('name_en, name_ur, slug')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            return $cache;
        }

        foreach ($rows as $row) {
            $en   = trim((string) ($row['name_en'] ?? ''));
            $ur   = trim((string) ($row['name_ur'] ?? ''));
            $slug = trim((string) ($row['slug'] ?? ''));
            $terms = [];

            if ($en !== '') {
                $terms[] = $en;
                $terms[] = mb_strtolower($en, 'UTF-8');
                // Split compound names: "Medical Stores" → medical, stores
                foreach (preg_split('/[\s\/&,]+/u', $en) ?: [] as $part) {
                    $part = trim($part);
                    if (mb_strlen($part) >= 3) {
                        $terms[] = $part;
                        $terms[] = mb_strtolower($part, 'UTF-8');
                    }
                }
            }
            if ($ur !== '') {
                $terms[] = $ur;
            }
            if ($slug !== '') {
                $terms[] = str_replace('-', ' ', $slug);
                $terms[] = $slug;
            }

            $terms = array_values(array_unique(array_filter($terms)));
            if (count($terms) >= 1) {
                $cache[] = $terms;
            }
        }

        return $cache;
    }
}
