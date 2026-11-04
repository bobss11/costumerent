<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Costume;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminViewsRenderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-'.uniqid().'@costumerent.local',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    private function seedRental(string $status): Rental
    {
        $category = Category::create(['name' => 'Kat '.uniqid(), 'slug' => 'kat-'.uniqid()]);
        $costume = Costume::create([
            'category_id' => $category->id,
            'code' => 'CST-'.strtoupper(substr(uniqid(), -4)),
            'name' => 'Kostum',
            'size' => 'L',
            'gender' => 'Unisex',
            'rental_price_per_day' => 150000,
            'deposit_amount' => 100000,
            'late_fee_per_day' => 25000,
            'stock_total' => 6,
            'stock_available' => 6,
            'accessories_included' => 'A, B',
            'image_url' => 'https://example.com/x.jpg',
            'is_active' => true,
        ]);
        $customer = User::create([
            'name' => 'Cust', 'email' => 'c-'.uniqid().'@x.test',
            'password' => bcrypt('password123'), 'role' => 'customer',
        ]);

        return Rental::create([
            'rental_code' => 'RNT-'.strtoupper(substr(uniqid(), -4)),
            'user_id' => $customer->id,
            'costume_id' => $costume->id,
            'qty' => 1,
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addDays(2)->toDateString(),
            'total_days' => 3,
            'rental_subtotal' => 450000,
            'deposit_total' => 100000,
            'grand_total' => 550000,
            'status' => $status,
            'payment_status' => $status === 'pending' ? 'unpaid' : 'paid',
        ]);
    }

    public function test_all_admin_get_pages_render(): void
    {
        $admin = $this->admin();
        $pending = $this->seedRental('pending');
        $pickedUp = $this->seedRental('picked_up');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pickups.edit', $pending))->assertOk();
        $this->actingAs($admin)->get(route('admin.returns.create', $pickedUp))->assertOk();
        $this->actingAs($admin)->get(route('admin.costumes.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.costumes.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.categories.create'))->assertOk();
    }

    public function test_costume_crud_create_update_delete(): void
    {
        $admin = $this->admin();
        $category = Category::create(['name' => 'Kat CRUD', 'slug' => 'kat-crud']);

        $this->actingAs($admin)->post(route('admin.costumes.store'), [
            'category_id' => $category->id,
            'code' => 'CST-NEW-01',
            'name' => 'Kostum Baru',
            'size' => 'M',
            'gender' => 'Pria',
            'rental_price_per_day' => 100000,
            'deposit_amount' => 80000,
            'late_fee_per_day' => 20000,
            'stock_total' => 5,
            'accessories_included' => 'X, Y',
            'image_url' => 'https://example.com/n.jpg',
        ])->assertRedirect(route('admin.costumes.index'));

        $costume = Costume::where('code', 'CST-NEW-01')->firstOrFail();
        // stock_available defaults to stock_total when not provided.
        $this->assertSame(5, $costume->stock_available);

        $this->actingAs($admin)->put(route('admin.costumes.update', $costume), [
            'category_id' => $category->id,
            'code' => 'CST-NEW-01',
            'name' => 'Kostum Diperbarui',
            'size' => 'L',
            'gender' => 'Unisex',
            'rental_price_per_day' => 120000,
            'deposit_amount' => 90000,
            'late_fee_per_day' => 25000,
            'stock_total' => 8,
            'stock_available' => 8,
            'accessories_included' => 'X, Y, Z',
            'image_url' => 'https://example.com/n.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.costumes.index'));

        $this->assertSame('Kostum Diperbarui', $costume->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.costumes.destroy', $costume))
            ->assertRedirect(route('admin.costumes.index'));
        $this->assertDatabaseMissing('costumes', ['code' => 'CST-NEW-01']);
    }

    public function test_category_crud_autogenerates_unique_slug(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Kostum Karnaval & Tari',
            'description' => 'Aneka karnaval.',
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Kostum Karnaval & Tari')->firstOrFail();
        $this->assertSame('kostum-karnaval-tari', $category->slug);

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Karnaval Nusantara',
            'description' => 'Diperbarui.',
        ])->assertRedirect(route('admin.categories.index'));
        $this->assertSame('karnaval-nusantara', $category->fresh()->slug);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
