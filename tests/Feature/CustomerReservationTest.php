<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Costume;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCostume(array $overrides = []): Costume
    {
        $category = Category::create([
            'name' => 'Pakaian Adat Nusantara',
            'slug' => 'pakaian-adat-nusantara-'.uniqid(),
            'description' => 'Koleksi adat.',
        ]);

        return Costume::create(array_merge([
            'category_id' => $category->id,
            'code' => 'CST-TST-01',
            'name' => 'Set Beskap & Kebaya Adat Jawa Solo',
            'size' => 'L',
            'gender' => 'Unisex',
            'rental_price_per_day' => 150000,
            'deposit_amount' => 100000,
            'late_fee_per_day' => 25000,
            'stock_total' => 6,
            'stock_available' => 6,
            'accessories_included' => 'Beskap, Kebaya, Blangkon',
            'image_url' => 'https://example.com/jawa.jpg',
            'description' => 'Busana adat Jawa.',
            'is_active' => true,
        ], $overrides));
    }

    protected function customer(): User
    {
        return User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);
    }

    public function test_catalog_lists_active_costumes_and_filters(): void
    {
        $costume = $this->makeCostume();
        $this->makeCostume(['code' => 'CST-OFF-02', 'name' => 'Nonaktif', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Set Beskap &amp; Kebaya Adat Jawa Solo', false)
            ->assertDontSee('Nonaktif');

        // Search filter.
        $this->get('/catalog?search=Beskap')
            ->assertOk()
            ->assertSee('Set Beskap', false);

        $this->get('/catalog?search=Tidak Ada')
            ->assertOk()
            ->assertDontSee('Set Beskap', false);

        // Size filter.
        $this->get('/catalog?size=XL')
            ->assertOk()
            ->assertDontSee('Set Beskap', false);

        // Category filter.
        $this->get('/catalog?category_id='.$costume->category_id)
            ->assertOk()
            ->assertSee('Set Beskap', false);
    }

    public function test_authenticated_customer_can_create_reservation_with_correct_totals(): void
    {
        $costume = $this->makeCostume();
        $user = $this->customer();

        $response = $this->actingAs($user)->post(route('customer.rentals.store'), [
            'costume_id' => $costume->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(), // 3 inclusive days
            'qty' => 2,
        ]);

        $response->assertRedirect(route('customer.rentals.index'));

        $rental = Rental::first();
        $this->assertNotNull($rental);
        $this->assertSame(3, $rental->total_days);
        // subtotal = 150000 * 3 * 2 = 900000
        $this->assertEquals(900000, (float) $rental->rental_subtotal);
        // deposit = 100000 * 2 = 200000
        $this->assertEquals(200000, (float) $rental->deposit_total);
        $this->assertEquals(1100000, (float) $rental->grand_total);
        $this->assertSame('pending', $rental->status);
        $this->assertSame('unpaid', $rental->payment_status);
        $this->assertMatchesRegularExpression('/^RNT-\d{6}-\d{3}$/', $rental->rental_code);

        // Stock must NOT be decremented at reservation time.
        $this->assertSame(6, $costume->fresh()->stock_available);
    }

    public function test_reservation_rejected_when_qty_exceeds_stock(): void
    {
        $costume = $this->makeCostume(['stock_available' => 1]);
        $user = $this->customer();

        $response = $this->actingAs($user)->post(route('customer.rentals.store'), [
            'costume_id' => $costume->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'qty' => 5,
        ]);

        $response->assertSessionHasErrors('qty');
        $this->assertSame(0, Rental::count());
    }

    public function test_rental_code_is_unique_and_increments(): void
    {
        $costume = $this->makeCostume();
        $user = $this->customer();

        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($user)->post(route('customer.rentals.store'), [
                'costume_id' => $costume->id,
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addDays(2)->toDateString(),
                'qty' => 1,
            ]);
        }

        $codes = Rental::pluck('rental_code');
        $this->assertCount(3, $codes);
        $this->assertSame($codes->count(), $codes->unique()->count());
    }

    public function test_guest_cannot_create_reservation(): void
    {
        $costume = $this->makeCostume();

        $this->post(route('customer.rentals.store'), [
            'costume_id' => $costume->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'qty' => 1,
        ])->assertRedirect(route('login'));

        $this->assertSame(0, Rental::count());
    }

    public function test_customer_rentals_page_shows_their_bookings(): void
    {
        $costume = $this->makeCostume();
        $user = $this->customer();

        $this->actingAs($user)->post(route('customer.rentals.store'), [
            'costume_id' => $costume->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'qty' => 1,
        ]);

        $rental = Rental::first();

        $this->actingAs($user)->get(route('customer.rentals.index'))
            ->assertOk()
            ->assertSee($rental->rental_code)
            ->assertSee('Set Beskap', false);
    }
}
