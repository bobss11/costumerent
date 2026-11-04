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

class AdminPickupReturnTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::create([
            'name' => 'Staff Operasional',
            'email' => 'staff-'.uniqid().'@costumerent.local',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);
    }

    private function customer(): User
    {
        return User::create([
            'name' => 'Pelanggan Setia',
            'email' => 'cust-'.uniqid().'@costumerent.local',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);
    }

    private function costume(array $overrides = []): Costume
    {
        $category = Category::create([
            'name' => 'Kategori '.uniqid(),
            'slug' => 'kategori-'.uniqid(),
        ]);

        return Costume::create(array_merge([
            'category_id' => $category->id,
            'code' => 'CST-TST-'.strtoupper(substr(uniqid(), -3)),
            'name' => 'Kostum Uji',
            'size' => 'L',
            'gender' => 'Unisex',
            'rental_price_per_day' => 150000,
            'deposit_amount' => 100000,
            'late_fee_per_day' => 25000,
            'stock_total' => 6,
            'stock_available' => 6,
            'accessories_included' => 'Blangkon, Keris',
            'image_url' => 'https://example.com/x.jpg',
            'description' => 'Deskripsi.',
            'is_active' => true,
        ], $overrides));
    }

    private function rental(User $customer, Costume $costume, array $overrides = []): Rental
    {
        return Rental::create(array_merge([
            'rental_code' => 'RNT-TST-'.strtoupper(substr(uniqid(), -4)),
            'user_id' => $customer->id,
            'costume_id' => $costume->id,
            'qty' => 1,
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addDays(2)->toDateString(),
            'total_days' => 3,
            'rental_subtotal' => 450000,
            'deposit_total' => 100000,
            'grand_total' => 550000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ], $overrides));
    }

    public function test_pickup_marks_paid_decrements_stock_and_creates_invoice_with_change(): void
    {
        $staff = $this->staff();
        $costume = $this->costume(['stock_available' => 6]);
        $rental = $this->rental($this->customer(), $costume, ['qty' => 2, 'grand_total' => 550000]);

        $response = $this->actingAs($staff)->put(route('admin.pickups.update', $rental), [
            'guarantee_card_held' => 'KTP Asli a.n Pelanggan Setia',
            'pickup_condition_notes' => 'Lengkap dan rapi.',
            'payment_method' => 'cash',
            'amount_paid' => 600000,
        ]);

        $response->assertRedirect();

        $rental->refresh();
        $costume->refresh();

        $this->assertSame('picked_up', $rental->status);
        $this->assertSame('paid', $rental->payment_status);
        $this->assertSame('KTP Asli a.n Pelanggan Setia', $rental->guarantee_card_held);

        // Stock decremented by qty (6 - 2 = 4).
        $this->assertSame(4, $costume->stock_available);

        $invoice = Invoice::where('rental_id', $rental->id)->first();
        $this->assertNotNull($invoice);
        $this->assertSame('cash', $invoice->payment_method);
        $this->assertEquals(600000, (float) $invoice->amount_paid);
        // change = 600000 - 550000 = 50000.
        $this->assertEquals(50000, (float) $invoice->change_amount);
        $this->assertStringStartsWith('INV-'.Carbon::now()->format('Ym').'-', $invoice->invoice_number);
    }

    public function test_pickup_rejects_payment_below_grand_total(): void
    {
        $staff = $this->staff();
        $costume = $this->costume(['stock_available' => 6]);
        $rental = $this->rental($this->customer(), $costume, ['grand_total' => 550000]);

        $response = $this->actingAs($staff)->put(route('admin.pickups.update', $rental), [
            'guarantee_card_held' => 'KTP Asli',
            'payment_method' => 'cash',
            'amount_paid' => 100000,
        ]);

        $response->assertSessionHasErrors('amount_paid');
        $rental->refresh();
        $costume->refresh();
        $this->assertSame('pending', $rental->status);
        $this->assertSame(6, $costume->stock_available);
        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_return_inspection_settles_deposit_and_restores_stock(): void
    {
        $staff = $this->staff();
        // stock_available 4 reflects 2 units already out at pickup.
        $costume = $this->costume(['stock_available' => 4, 'late_fee_per_day' => 25000]);
        $rental = $this->rental($this->customer(), $costume, [
            'qty' => 2,
            'end_date' => Carbon::today()->toDateString(),
            'deposit_total' => 200000,
            'status' => 'picked_up',
            'payment_status' => 'paid',
        ]);

        // Returned 1 day late, Rp 30.000 damage.
        $actualReturn = Carbon::today()->addDay()->toDateString();

        $response = $this->actingAs($staff)->post(route('admin.returns.store', $rental), [
            'actual_return_date' => $actualReturn,
            'condition_status' => 'minor_stain',
            'damage_penalty_fee' => 30000,
            'inspection_notes' => 'Ada noda ringan.',
        ]);

        $response->assertRedirect();

        $rental->refresh();
        $costume->refresh();
        $inspection = ReturnInspection::where('rental_id', $rental->id)->firstOrFail();

        // late_days = 1, late_fee = 1 * 25000 * 2 = 50000; total_deduction = 80000.
        $this->assertSame(1, $inspection->late_days);
        $this->assertEquals(50000, (float) $inspection->late_fee_amount);
        $this->assertEquals(80000, (float) $inspection->total_deduction);
        // deposit 200000 - 80000 = 120000 refunded, no extra charge.
        $this->assertEquals(120000, (float) $inspection->deposit_refunded);
        $this->assertEquals(0, (float) $inspection->extra_charge);
        $this->assertSame($staff->id, $inspection->checked_by_user_id);

        $this->assertSame('returned', $rental->status);
        // Stock restored by qty (4 + 2 = 6).
        $this->assertSame(6, $costume->stock_available);
    }

    public function test_return_inspection_produces_extra_charge_when_deductions_exceed_deposit(): void
    {
        $staff = $this->staff();
        $costume = $this->costume(['stock_available' => 5, 'late_fee_per_day' => 25000]);
        $rental = $this->rental($this->customer(), $costume, [
            'qty' => 1,
            'end_date' => Carbon::today()->toDateString(),
            'deposit_total' => 100000,
            'status' => 'picked_up',
            'payment_status' => 'paid',
        ]);

        // 3 days late + Rp 100.000 damage → deductions exceed the deposit.
        $actualReturn = Carbon::today()->addDays(3)->toDateString();

        $this->actingAs($staff)->post(route('admin.returns.store', $rental), [
            'actual_return_date' => $actualReturn,
            'condition_status' => 'damaged',
            'damage_penalty_fee' => 100000,
            'inspection_notes' => 'Sobek di bagian lengan.',
        ])->assertRedirect();

        $inspection = ReturnInspection::where('rental_id', $rental->id)->firstOrFail();

        // late_fee = 3 * 25000 * 1 = 75000; total_deduction = 175000.
        $this->assertSame(3, $inspection->late_days);
        $this->assertEquals(75000, (float) $inspection->late_fee_amount);
        $this->assertEquals(175000, (float) $inspection->total_deduction);
        // deposit 100000 fully consumed → refund 0, extra_charge 75000.
        $this->assertEquals(0, (float) $inspection->deposit_refunded);
        $this->assertEquals(75000, (float) $inspection->extra_charge);
    }

    public function test_customer_cannot_access_admin_pickup(): void
    {
        $customer = $this->customer();
        $costume = $this->costume();
        $rental = $this->rental($customer, $costume);

        $this->actingAs($customer)
            ->get(route('admin.pickups.edit', $rental))
            ->assertForbidden();
    }

    public function test_guest_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
}
