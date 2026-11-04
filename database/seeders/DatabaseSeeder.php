<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Costume;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\ReturnInspection;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
        $categories = $this->seedCategories();
        $costumes = $this->seedCostumes($categories);
        $this->seedRentals($costumes);
    }

    /**
     * Seed the three baseline accounts (admin, staff, customer).
     *
     * @return array<string, User>
     */
    protected function seedUsers(): array
    {
        $admin = User::create([
            'name' => 'Administrator CostumeRent',
            'email' => 'admin@costumerent.local',
            'password' => Hash::make('password123'),
            'phone_number' => '081200000001',
            'identity_type' => 'KTP',
            'identity_number' => '3201010101010001',
            'address' => 'Jl. Melati No. 1, Jakarta Pusat',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $staff = User::create([
            'name' => 'Staff Operasional',
            'email' => 'staff@costumerent.local',
            'password' => Hash::make('password123'),
            'phone_number' => '081200000002',
            'identity_type' => 'SIM',
            'identity_number' => '3201010101010002',
            'address' => 'Jl. Kenanga No. 2, Jakarta Selatan',
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        $customer = User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@costumerent.local',
            'password' => Hash::make('password123'),
            'phone_number' => '081200000003',
            'identity_type' => 'Kartu Pelajar',
            'identity_number' => '3201010101010003',
            'address' => 'Jl. Anggrek No. 3, Depok',
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        return [
            'admin' => $admin,
            'staff' => $staff,
            'customer' => $customer,
        ];
    }

    /**
     * Seed the four costume categories.
     *
     * @return array<string, Category>
     */
    protected function seedCategories(): array
    {
        $names = [
            'adat' => 'Pakaian Adat Nusantara',
            'karnaval' => 'Kostum Karnaval & Tari',
            'cosplay' => 'Kostum Karakter / Cosplay',
            'profesi' => 'Busana Profesi & Sejarah',
        ];

        $descriptions = [
            'adat' => 'Koleksi pakaian adat dan busana tradisional dari berbagai daerah di Nusantara.',
            'karnaval' => 'Kostum meriah untuk karnaval, pawai, dan pertunjukan tari.',
            'cosplay' => 'Kostum karakter populer untuk cosplay dan acara bertema.',
            'profesi' => 'Busana profesi serta pakaian bernuansa sejarah untuk edukasi dan drama.',
        ];

        $categories = [];

        foreach ($names as $key => $name) {
            $categories[$key] = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $descriptions[$key],
            ]);
        }

        return $categories;
    }

    /**
     * Seed six costumes spread across the categories.
     *
     * @param  array<string, Category>  $categories
     * @return array<string, Costume>
     */
    protected function seedCostumes(array $categories): array
    {
        $data = [
            // 1. Set Beskap & Kebaya Adat Jawa Solo (spec item 1).
            'jawa' => [
                'category' => 'adat',
                'code' => 'CST-JWA-01',
                'name' => 'Set Beskap & Kebaya Adat Jawa Solo',
                'size' => 'L',
                'gender' => 'Unisex',
                'rental_price_per_day' => 150000.00,
                'deposit_amount' => 100000.00,
                'late_fee_per_day' => 25000.00,
                'stock_total' => 6,
                'stock_available' => 6,
                'accessories_included' => 'Beskap, Kebaya Beludru, Kain Jarik, Blangkon, Keris Properti',
                'image_url' => 'https://images.unsplash.com/photo-1601933470928-c6ecbf0f43f3?auto=format&fit=crop&w=800&q=80',
                'description' => 'Set busana adat Jawa Solo lengkap untuk pernikahan tradisional maupun acara budaya.',
            ],
            // 2. Pakaian Adat Koto Gadang Minangkabau (spec item 2).
            'minang' => [
                'category' => 'adat',
                'code' => 'CST-MNG-02',
                'name' => 'Pakaian Adat Koto Gadang Minangkabau',
                'size' => 'M',
                'gender' => 'Wanita',
                'rental_price_per_day' => 220000.00,
                'deposit_amount' => 150000.00,
                'late_fee_per_day' => 35000.00,
                'stock_total' => 4,
                'stock_available' => 4,
                'accessories_included' => 'Baju Kurung, Tingkuluak Talakuang, Songket, Kalung Gadang',
                'image_url' => 'https://images.unsplash.com/photo-1555212697-194d092e3b8f?auto=format&fit=crop&w=800&q=80',
                'description' => 'Busana adat Koto Gadang khas Minangkabau dengan songket dan tingkuluak khas.',
            ],
            // 3. Baju Bodo Adat Bugis Makassar (spec item 3).
            'bodo' => [
                'category' => 'adat',
                'code' => 'CST-BGS-03',
                'name' => 'Baju Bodo Adat Bugis Makassar',
                'size' => 'All Size',
                'gender' => 'Wanita',
                'rental_price_per_day' => 135000.00,
                'deposit_amount' => 100000.00,
                'late_fee_per_day' => 25000.00,
                'stock_total' => 8,
                'stock_available' => 8,
                'accessories_included' => 'Baju Bodo, Sarung Sutra, Mahkota Saloko, Gelang Panjang',
                'image_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                'description' => 'Busana adat Baju Bodo khas Bugis Makassar dengan sarung sutra dan mahkota saloko.',
            ],
            // 4. Kostum Karnaval Garuda Nusantara Gold (spec item 4).
            'garuda' => [
                'category' => 'karnaval',
                'code' => 'CST-KRN-04',
                'name' => 'Kostum Karnaval Garuda Nusantara Gold',
                'size' => 'L',
                'gender' => 'Unisex',
                'rental_price_per_day' => 350000.00,
                'deposit_amount' => 250000.00,
                'late_fee_per_day' => 50000.00,
                'stock_total' => 3,
                'stock_available' => 3,
                'accessories_included' => 'Sayap Punggung, Mahkota Garuda, Chestplate, Pelindung Lengan',
                'image_url' => 'https://images.unsplash.com/photo-1514446268004-2d0b9e9d2d5c?auto=format&fit=crop&w=800&q=80',
                'description' => 'Kostum karnaval Garuda Nusantara emas yang megah untuk pawai dan pertunjukan.',
            ],
            // 5. Kostum Pejuang Kemerdekaan / Bung Tomo (spec item 5).
            'bungtomo' => [
                'category' => 'profesi',
                'code' => 'CST-PRF-05',
                'name' => 'Kostum Pejuang Kemerdekaan / Bung Tomo',
                'size' => 'XL',
                'gender' => 'Pria',
                'rental_price_per_day' => 95000.00,
                'deposit_amount' => 75000.00,
                'late_fee_per_day' => 20000.00,
                'stock_total' => 10,
                'stock_available' => 10,
                'accessories_included' => 'Kemeja Safari, Celana, Sabuk Kulit, Topi Peci Pejuang, Pin Garuda',
                'image_url' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=800&q=80',
                'description' => 'Kostum pejuang kemerdekaan bergaya Bung Tomo untuk drama sejarah dan peringatan kemerdekaan.',
            ],
            // 6. Kimono Haori Tradisional Jepang (spec item 6).
            'kimono' => [
                'category' => 'cosplay',
                'code' => 'CST-CSP-06',
                'name' => 'Kimono Haori Tradisional Jepang',
                'size' => 'M',
                'gender' => 'Unisex',
                'rental_price_per_day' => 125000.00,
                'deposit_amount' => 100000.00,
                'late_fee_per_day' => 25000.00,
                'stock_total' => 5,
                'stock_available' => 5,
                'accessories_included' => 'Kimono, Obi Belt, Geta Sandals, Kipas Lipat',
                'image_url' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&w=800&q=80',
                'description' => 'Kimono Haori tradisional Jepang untuk cosplay, foto bertema, dan acara budaya Jepang.',
            ],
        ];

        $costumes = [];

        foreach ($data as $key => $row) {
            $category = $categories[$row['category']];
            unset($row['category']);
            $row['category_id'] = $category->id;
            $row['is_active'] = true;
            $costumes[$key] = Costume::create($row);
        }

        return $costumes;
    }

    /**
     * Seed three dummy rentals: picked up, pending, and returned.
     *
     * @param  array<string, Costume>  $costumes
     */
    protected function seedRentals(array $costumes): void
    {
        $admin = User::where('email', 'admin@costumerent.local')->firstOrFail();
        $staff = User::where('email', 'staff@costumerent.local')->firstOrFail();
        $customer = User::where('email', 'customer@costumerent.local')->firstOrFail();

        $period = now()->format('Ym');

        // 1. Picked up rental (paid, guarantee card held, stock decremented).
        $pickedCostume = $costumes['jawa'];
        $pickedStart = now()->subDays(1)->startOfDay();
        $pickedEnd = now()->addDays(2)->startOfDay();
        $pickedDays = $pickedStart->diffInDays($pickedEnd) + 1;
        $pickedQty = 1;
        $pickedSubtotal = $pickedCostume->rental_price_per_day * $pickedDays * $pickedQty;
        $pickedDeposit = $pickedCostume->deposit_amount * $pickedQty;
        $pickedGrand = $pickedSubtotal + $pickedDeposit;

        $pickedRental = Rental::create([
            'rental_code' => 'RNT-'.$period.'-001',
            'user_id' => $customer->id,
            'costume_id' => $pickedCostume->id,
            'qty' => $pickedQty,
            'start_date' => $pickedStart->toDateString(),
            'end_date' => $pickedEnd->toDateString(),
            'total_days' => $pickedDays,
            'rental_subtotal' => $pickedSubtotal,
            'deposit_total' => $pickedDeposit,
            'grand_total' => $pickedGrand,
            'guarantee_card_held' => 'KTP - 3201010101010003',
            'pickup_condition_notes' => 'Kostum lengkap dengan seluruh aksesoris, kondisi baik saat diambil.',
            'status' => 'picked_up',
            'payment_status' => 'paid',
        ]);

        $pickedCostume->decrement('stock_available', $pickedQty);

        Invoice::create([
            'invoice_number' => 'INV-'.$period.'-001',
            'rental_id' => $pickedRental->id,
            'payment_method' => 'cash',
            'amount_paid' => $pickedGrand,
            'change_amount' => 0,
            'issued_at' => $pickedStart->copy()->setTime(9, 30),
        ]);

        // 2. Pending rental (unpaid, awaiting confirmation).
        $pendingCostume = $costumes['bodo'];
        $pendingStart = now()->addDays(5)->startOfDay();
        $pendingEnd = now()->addDays(7)->startOfDay();
        $pendingDays = $pendingStart->diffInDays($pendingEnd) + 1;
        $pendingQty = 2;
        $pendingSubtotal = $pendingCostume->rental_price_per_day * $pendingDays * $pendingQty;
        $pendingDeposit = $pendingCostume->deposit_amount * $pendingQty;
        $pendingGrand = $pendingSubtotal + $pendingDeposit;

        Rental::create([
            'rental_code' => 'RNT-'.$period.'-002',
            'user_id' => $customer->id,
            'costume_id' => $pendingCostume->id,
            'qty' => $pendingQty,
            'start_date' => $pendingStart->toDateString(),
            'end_date' => $pendingEnd->toDateString(),
            'total_days' => $pendingDays,
            'rental_subtotal' => $pendingSubtotal,
            'deposit_total' => $pendingDeposit,
            'grand_total' => $pendingGrand,
            'guarantee_card_held' => null,
            'pickup_condition_notes' => null,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // 3. Returned rental (with return inspection + invoice, includes late + damage calc).
        $returnedCostume = $costumes['bungtomo'];
        $returnedStart = now()->subDays(10)->startOfDay();
        $returnedEnd = now()->subDays(7)->startOfDay();
        $returnedDays = $returnedStart->diffInDays($returnedEnd) + 1;
        $returnedQty = 1;
        $returnedSubtotal = $returnedCostume->rental_price_per_day * $returnedDays * $returnedQty;
        $returnedDeposit = $returnedCostume->deposit_amount * $returnedQty;
        $returnedGrand = $returnedSubtotal + $returnedDeposit;

        $returnedRental = Rental::create([
            'rental_code' => 'RNT-'.$period.'-003',
            'user_id' => $customer->id,
            'costume_id' => $returnedCostume->id,
            'qty' => $returnedQty,
            'start_date' => $returnedStart->toDateString(),
            'end_date' => $returnedEnd->toDateString(),
            'total_days' => $returnedDays,
            'rental_subtotal' => $returnedSubtotal,
            'deposit_total' => $returnedDeposit,
            'grand_total' => $returnedGrand,
            'guarantee_card_held' => 'KTP - 3201010101010003',
            'pickup_condition_notes' => 'Kostum lengkap saat pengambilan.',
            'status' => 'returned',
            'payment_status' => 'paid',
        ]);

        // Returned 2 days late with a minor stain penalty.
        $actualReturn = $returnedEnd->copy()->addDays(2);
        $lateDays = 2;
        $lateFee = $returnedCostume->late_fee_per_day * $lateDays * $returnedQty;
        $damagePenalty = 50000.00;
        $totalDeduction = $lateFee + $damagePenalty;
        $depositRefunded = max($returnedDeposit - $totalDeduction, 0);
        $extraCharge = max($totalDeduction - $returnedDeposit, 0);

        ReturnInspection::create([
            'rental_id' => $returnedRental->id,
            'checked_by_user_id' => $staff->id,
            'actual_return_date' => $actualReturn->toDateString(),
            'late_days' => $lateDays,
            'late_fee_amount' => $lateFee,
            'condition_status' => 'minor_stain',
            'inspection_notes' => 'Terdapat noda ringan pada kemeja safari, dikenakan denda kebersihan.',
            'damage_penalty_fee' => $damagePenalty,
            'total_deduction' => $totalDeduction,
            'deposit_refunded' => $depositRefunded,
            'extra_charge' => $extraCharge,
        ]);

        Invoice::create([
            'invoice_number' => 'INV-'.$period.'-002',
            'rental_id' => $returnedRental->id,
            'payment_method' => 'transfer',
            'amount_paid' => $returnedGrand,
            'change_amount' => 0,
            'issued_at' => $returnedStart->copy()->setTime(10, 0),
        ]);
    }
}
