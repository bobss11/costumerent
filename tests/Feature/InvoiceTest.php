<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Costume;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\ReturnInspection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role).' '.uniqid(),
            'email' => $role.'-'.uniqid().'@costumerent.local',
            'password' => bcrypt('password123'),
            'phone_number' => '0812-0000-0000',
            'identity_type' => 'KTP',
            'identity_number' => '357100000',
            'address' => 'Jl. Contoh No. 5',
            'role' => $role,
        ]);
    }

    private function costume(): Costume
    {
        $category = Category::create([
            'name' => 'Kategori '.uniqid(),
            'slug' => 'kategori-'.uniqid(),
        ]);

        return Costume::create([
            'category_id' => $category->id,
            'code' => 'CST-INV-'.strtoupper(substr(uniqid(), -3)),
            'name' => 'Kostum Nota Uji',
            'size' => 'M',
            'gender' => 'Unisex',
            'rental_price_per_day' => 150000,
            'deposit_amount' => 100000,
            'late_fee_per_day' => 25000,
            'stock_total' => 5,
            'stock_available' => 5,
            'accessories_included' => 'Blangkon, Keris',
            'image_url' => 'https://example.com/x.jpg',
            'description' => 'Deskripsi.',
            'is_active' => true,
        ]);
    }

    private function rental(User $owner, Costume $costume, array $overrides = []): Rental
    {
        return Rental::create(array_merge([
            'rental_code' => 'RNT-INV-'.strtoupper(substr(uniqid(), -4)),
            'user_id' => $owner->id,
            'costume_id' => $costume->id,
            'qty' => 1,
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addDays(2)->toDateString(),
            'total_days' => 3,
            'rental_subtotal' => 450000,
            'deposit_total' => 100000,
            'grand_total' => 550000,
            'status' => 'picked_up',
            'payment_status' => 'paid',
        ], $overrides));
    }

    public function test_owner_customer_can_view_their_invoice(): void
    {
        $owner = $this->user('customer');
        $costume = $this->costume();
        $rental = $this->rental($owner, $costume);

        Invoice::create([
            'invoice_number' => 'INV-'.Carbon::now()->format('Ym').'-001',
            'rental_id' => $rental->id,
            'payment_method' => 'cash',
            'amount_paid' => 600000,
            'change_amount' => 50000,
            'issued_at' => Carbon::now(),
        ]);

        $this->actingAs($owner)
            ->get(route('invoice.show', $rental->rental_code))
            ->assertOk()
            ->assertSee($rental->rental_code)
            ->assertSee('CostumeRent')
            ->assertSee('Kostum Nota Uji')
            ->assertSee('Cetak Nota');
    }

    public function test_invoice_renders_return_inspection_when_present(): void
    {
        $owner = $this->user('customer');
        $costume = $this->costume();
        $rental = $this->rental($owner, $costume, ['status' => 'returned']);

        ReturnInspection::create([
            'rental_id' => $rental->id,
            'checked_by_user_id' => $this->user('staff')->id,
            'actual_return_date' => Carbon::today()->addDays(3)->toDateString(),
            'late_days' => 1,
            'late_fee_amount' => 25000,
            'condition_status' => 'minor_stain',
            'inspection_notes' => 'Noda ringan.',
            'damage_penalty_fee' => 0,
            'total_deduction' => 25000,
            'deposit_refunded' => 75000,
            'extra_charge' => 0,
        ]);

        $this->actingAs($owner)
            ->get(route('invoice.show', $rental->rental_code))
            ->assertOk()
            ->assertSee('Hasil Inspeksi Pengembalian')
            ->assertSee('Noda ringan.');
    }

    public function test_a_different_customer_cannot_view_the_invoice(): void
    {
        $owner = $this->user('customer');
        $other = $this->user('customer');
        $rental = $this->rental($owner, $this->costume());

        $this->actingAs($other)
            ->get(route('invoice.show', $rental->rental_code))
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_view_any_invoice(): void
    {
        $owner = $this->user('customer');
        $rental = $this->rental($owner, $this->costume());

        $this->actingAs($this->user('admin'))
            ->get(route('invoice.show', $rental->rental_code))
            ->assertOk()
            ->assertSee($rental->rental_code);

        $this->actingAs($this->user('staff'))
            ->get(route('invoice.show', $rental->rental_code))
            ->assertOk()
            ->assertSee($rental->rental_code);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $rental = $this->rental($this->user('customer'), $this->costume());

        $this->get(route('invoice.show', $rental->rental_code))
            ->assertRedirect(route('login'));
    }

    public function test_unknown_rental_code_returns_404(): void
    {
        $this->actingAs($this->user('admin'))
            ->get(route('invoice.show', 'RNT-DOES-NOT-EXIST'))
            ->assertNotFound();
    }
}
