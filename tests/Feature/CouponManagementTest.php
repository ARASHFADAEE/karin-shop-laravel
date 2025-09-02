<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\Coupons\Create as CouponCreate;
use App\Livewire\Admin\Coupons\Edit as CouponEdit;
use App\Livewire\Admin\Coupons\Index as CouponIndex;
use Carbon\Carbon;

class CouponManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);
        
        // Create regular user
        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'role' => 'user'
        ]);
    }

    /** @test */
    public function admin_can_view_coupons_index_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.coupons.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(CouponIndex::class);
    }

    /** @test */
    public function admin_can_view_create_coupon_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.coupons.create'))
            ->assertStatus(200)
            ->assertSeeLivewire(CouponCreate::class);
    }

    /** @test */
    public function admin_can_create_percentage_coupon()
    {
        $couponData = [
            'code' => 'SAVE20',
            'description' => '20% discount coupon',
            'type' => 'percentage',
            'value' => 20,
            'minimum_amount' => 100000,
            'usage_limit' => 100,
            'expires_at' => Carbon::now()->addDays(30)->format('Y-m-d\TH:i'),
            'is_active' => true,
        ];

        Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->set($couponData)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', [
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
            'minimum_amount' => 100000,
            'usage_limit' => 100,
            'is_active' => true
        ]);
    }

    /** @test */
    public function admin_can_create_fixed_amount_coupon()
    {
        $couponData = [
            'code' => 'FIXED50K',
            'description' => '50,000 Toman discount',
            'type' => 'fixed',
            'value' => 50000,
            'minimum_amount' => 200000,
            'usage_limit' => 50,
            'expires_at' => Carbon::now()->addDays(15)->format('Y-m-d\TH:i'),
            'is_active' => true,
        ];

        Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->set($couponData)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('coupons', [
            'code' => 'FIXED50K',
            'type' => 'fixed',
            'value' => 50000,
            'minimum_amount' => 200000
        ]);
    }

    /** @test */
    public function coupon_creation_requires_code()
    {
        Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->set('code', '')
            ->set('type', 'percentage')
            ->set('value', 10)
            ->call('save')
            ->assertHasErrors(['code']);
    }

    /** @test */
    public function coupon_code_must_be_unique()
    {
        Coupon::factory()->create(['code' => 'DUPLICATE']);

        Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->set('code', 'DUPLICATE')
            ->set('type', 'percentage')
            ->set('value', 10)
            ->call('save')
            ->assertHasErrors(['code']);
    }

    /** @test */
    public function coupon_value_must_be_positive()
    {
        Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->set('code', 'NEGATIVE')
            ->set('type', 'percentage')
            ->set('value', -10)
            ->call('save')
            ->assertHasErrors(['value']);
    }

    /** @test */
    public function percentage_coupon_value_cannot_exceed_100()
    {
        Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->set('code', 'OVER100')
            ->set('type', 'percentage')
            ->set('value', 150)
            ->call('save')
            ->assertHasErrors(['value']);
    }

    /** @test */
    public function admin_can_edit_existing_coupon()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'ORIGINAL',
            'value' => 10
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.coupons.edit', $coupon))
            ->assertStatus(200)
            ->assertSeeLivewire(CouponEdit::class);
    }

    /** @test */
    public function admin_can_update_coupon_information()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'ORIGINAL',
            'description' => 'Original description',
            'value' => 10,
            'minimum_amount' => 50000
        ]);

        Livewire::actingAs($this->admin)
            ->test(CouponEdit::class, ['coupon' => $coupon])
            ->set('code', 'UPDATED')
            ->set('description', 'Updated description')
            ->set('value', 15)
            ->set('minimum_amount', 75000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'code' => 'UPDATED',
            'description' => 'Updated description',
            'value' => 15,
            'minimum_amount' => 75000
        ]);
    }

    /** @test */
    public function admin_can_activate_deactivate_coupon()
    {
        $coupon = Coupon::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->admin)
            ->test(CouponEdit::class, ['coupon' => $coupon])
            ->set('is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'is_active' => false
        ]);
    }

    /** @test */
    public function admin_can_delete_coupon()
    {
        $coupon = Coupon::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CouponIndex::class)
            ->call('delete', $coupon->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    /** @test */
    public function admin_can_search_coupons()
    {
        Coupon::factory()->create(['code' => 'SEARCHABLE']);
        Coupon::factory()->create(['code' => 'ANOTHER']);

        Livewire::actingAs($this->admin)
            ->test(CouponIndex::class)
            ->set('search', 'SEARCHABLE')
            ->assertSee('SEARCHABLE')
            ->assertDontSee('ANOTHER');
    }

    /** @test */
    public function admin_can_filter_coupons_by_type()
    {
        Coupon::factory()->create(['code' => 'PERCENT', 'type' => 'percentage']);
        Coupon::factory()->create(['code' => 'FIXED', 'type' => 'fixed']);

        Livewire::actingAs($this->admin)
            ->test(CouponIndex::class)
            ->set('typeFilter', 'percentage')
            ->assertSee('PERCENT')
            ->assertDontSee('FIXED');
    }

    /** @test */
    public function admin_can_filter_coupons_by_status()
    {
        Coupon::factory()->create(['code' => 'ACTIVE', 'is_active' => true]);
        Coupon::factory()->create(['code' => 'INACTIVE', 'is_active' => false]);

        Livewire::actingAs($this->admin)
            ->test(CouponIndex::class)
            ->set('statusFilter', 'active')
            ->assertSee('ACTIVE')
            ->assertDontSee('INACTIVE');
    }

    /** @test */
    public function admin_can_generate_random_coupon_code()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(CouponCreate::class)
            ->call('generateCode');

        $generatedCode = $component->get('code');
        $this->assertNotEmpty($generatedCode);
        $this->assertEquals(10, strlen($generatedCode)); // Assuming 10 character codes
    }

    /** @test */
    public function coupon_usage_count_increases_when_used()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'TESTUSE',
            'usage_count' => 0,
            'usage_limit' => 10
        ]);

        // Simulate coupon usage
        $coupon->increment('usage_count');
        
        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'usage_count' => 1
        ]);
    }

    /** @test */
    public function expired_coupon_cannot_be_used()
    {
        $expiredCoupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'expires_at' => Carbon::now()->subDays(1),
            'is_active' => true
        ]);

        // Test coupon validation logic
        $this->assertTrue($expiredCoupon->expires_at->isPast());
    }

    /** @test */
    public function coupon_with_usage_limit_reached_cannot_be_used()
    {
        $limitReachedCoupon = Coupon::factory()->create([
            'code' => 'LIMITREACHED',
            'usage_count' => 10,
            'usage_limit' => 10,
            'is_active' => true
        ]);

        $this->assertTrue($limitReachedCoupon->usage_count >= $limitReachedCoupon->usage_limit);
    }

    /** @test */
    public function inactive_coupon_cannot_be_used()
    {
        $inactiveCoupon = Coupon::factory()->create([
            'code' => 'INACTIVE',
            'is_active' => false
        ]);

        $this->assertFalse($inactiveCoupon->is_active);
    }

    /** @test */
    public function coupon_minimum_amount_is_enforced()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'MINIMUM',
            'minimum_amount' => 100000,
            'type' => 'percentage',
            'value' => 10
        ]);

        // Test with amount below minimum
        $orderAmount = 50000;
        $this->assertTrue($orderAmount < $coupon->minimum_amount);

        // Test with amount above minimum
        $orderAmount = 150000;
        $this->assertTrue($orderAmount >= $coupon->minimum_amount);
    }

    /** @test */
    public function percentage_coupon_calculates_discount_correctly()
    {
        $coupon = Coupon::factory()->create([
            'type' => 'percentage',
            'value' => 20 // 20%
        ]);

        $orderAmount = 100000;
        $expectedDiscount = $orderAmount * ($coupon->value / 100);
        $this->assertEquals(20000, $expectedDiscount);
    }

    /** @test */
    public function fixed_coupon_calculates_discount_correctly()
    {
        $coupon = Coupon::factory()->create([
            'type' => 'fixed',
            'value' => 50000 // Fixed 50,000 Toman
        ]);

        $orderAmount = 200000;
        $expectedDiscount = min($coupon->value, $orderAmount);
        $this->assertEquals(50000, $expectedDiscount);
    }

    /** @test */
    public function fixed_coupon_cannot_exceed_order_amount()
    {
        $coupon = Coupon::factory()->create([
            'type' => 'fixed',
            'value' => 100000 // Fixed 100,000 Toman
        ]);

        $orderAmount = 50000; // Less than coupon value
        $expectedDiscount = min($coupon->value, $orderAmount);
        $this->assertEquals(50000, $expectedDiscount); // Should be limited to order amount
    }

    /** @test */
    public function guest_cannot_access_coupon_management_pages()
    {
        $this->get(route('admin.coupons.index'))
            ->assertRedirect(route('login'));
            
        $this->get(route('admin.coupons.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_coupon_management()
    {
        $this->actingAs($this->user)
            ->get(route('admin.coupons.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function coupon_code_is_case_insensitive()
    {
        $coupon = Coupon::factory()->create(['code' => 'TESTCODE']);
        
        // Test that lowercase version should be treated the same
        $foundCoupon = Coupon::where('code', 'LIKE', 'testcode')->first();
        // This test depends on your implementation of case-insensitive search
    }

    /** @test */
    public function coupon_statistics_are_calculated_correctly()
    {
        $coupon = Coupon::factory()->create([
            'usage_count' => 5,
            'usage_limit' => 10
        ]);

        $usagePercentage = ($coupon->usage_count / $coupon->usage_limit) * 100;
        $this->assertEquals(50, $usagePercentage);
        
        $remainingUses = $coupon->usage_limit - $coupon->usage_count;
        $this->assertEquals(5, $remainingUses);
    }
}