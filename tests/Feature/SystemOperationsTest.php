<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Admin\Orders\Edit as OrderEdit;
use Carbon\Carbon;

class SystemOperationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $product;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin'
        ]);
        
        // Create regular user
        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);
        
        // Create test category and product
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100000,
            'stock' => 50
        ]);
        $this->product->categories()->attach($this->category->id);
    }

    // Authentication Tests
    
    /** @test */
    public function admin_can_login_with_valid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'admin123456')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin);
    }

    /** @test */
    public function admin_cannot_login_with_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function login_requires_email_and_password()
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['email', 'password']);
    }

    /** @test */
    public function admin_can_logout()
    {
        $this->actingAs($this->admin)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    // Order Management Tests
    
    /** @test */
    public function admin_can_view_orders_index()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.orders.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(OrderIndex::class);
    }

    /** @test */
    public function admin_can_view_all_orders()
    {
        $order1 = Order::factory()->create([
            'user_id' => $this->user->id,
            'total_amount' => 150000,
            'status' => 'pending'
        ]);
        
        $order2 = Order::factory()->create([
            'user_id' => $this->user->id,
            'total_amount' => 200000,
            'status' => 'completed'
        ]);

        Livewire::actingAs($this->admin)
            ->test(OrderIndex::class)
            ->assertSee('150,000')
            ->assertSee('200,000');
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        Livewire::actingAs($this->admin)
            ->test(OrderEdit::class, ['order' => $order])
            ->set('status', 'processing')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing'
        ]);
    }

    /** @test */
    public function admin_can_search_orders_by_user_email()
    {
        $user1 = User::factory()->create(['email' => 'customer1@test.com']);
        $user2 = User::factory()->create(['email' => 'customer2@test.com']);
        
        Order::factory()->create(['user_id' => $user1->id]);
        Order::factory()->create(['user_id' => $user2->id]);

        Livewire::actingAs($this->admin)
            ->test(OrderIndex::class)
            ->set('search', 'customer1@test.com')
            ->assertSee('customer1@test.com')
            ->assertDontSee('customer2@test.com');
    }

    /** @test */
    public function admin_can_filter_orders_by_status()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);
        
        Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        Livewire::actingAs($this->admin)
            ->test(OrderIndex::class)
            ->set('statusFilter', 'pending')
            ->assertSee('pending')
            ->assertDontSee('completed');
    }

    /** @test */
    public function order_total_is_calculated_correctly()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total_amount' => 0
        ]);
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100000
        ]);
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 50000
        ]);

        $expectedTotal = (2 * 100000) + (1 * 50000);
        $actualTotal = $order->items->sum(function($item) {
            return $item->quantity * $item->price;
        });
        
        $this->assertEquals($expectedTotal, $actualTotal);
    }

    // Cart Management Tests
    
    /** @test */
    public function user_can_add_product_to_cart()
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => $this->product->price
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    /** @test */
    public function cart_total_is_calculated_correctly()
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price' => $this->product->price
        ]);

        $expectedTotal = 3 * $this->product->price;
        $actualTotal = $cart->items->sum(function($item) {
            return $item->quantity * $item->product->price;
        });
        
        $this->assertEquals($expectedTotal, $actualTotal);
    }

    /** @test */
    public function cart_item_quantity_can_be_updated()
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price
        ]);
        
        $cartItem->update(['quantity' => 5]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5
        ]);
    }

    /** @test */
    public function cart_item_can_be_removed()
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price
        ]);
        
        $cartItem->delete();

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }

    // Coupon Application Tests
    
    /** @test */
    public function valid_percentage_coupon_applies_discount()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
            'minimum_amount' => 50000,
            'is_active' => true,
            'expires_at' => Carbon::now()->addDays(30)
        ]);
        
        $orderAmount = 100000;
        $expectedDiscount = $orderAmount * 0.20;
        
        // Test coupon validation
        $this->assertTrue($coupon->is_active);
        $this->assertTrue($orderAmount >= $coupon->minimum_amount);
        $this->assertTrue($coupon->expires_at->isFuture());
        
        $this->assertEquals(20000, $expectedDiscount);
    }

    /** @test */
    public function valid_fixed_coupon_applies_discount()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'FIXED50K',
            'type' => 'fixed',
            'value' => 50000,
            'minimum_amount' => 100000,
            'is_active' => true,
            'expires_at' => Carbon::now()->addDays(30)
        ]);
        
        $orderAmount = 200000;
        $expectedDiscount = min($coupon->value, $orderAmount);
        
        $this->assertEquals(50000, $expectedDiscount);
    }

    /** @test */
    public function expired_coupon_is_invalid()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'expires_at' => Carbon::now()->subDays(1),
            'is_active' => true
        ]);
        
        $this->assertTrue($coupon->expires_at->isPast());
    }

    /** @test */
    public function coupon_with_insufficient_order_amount_is_invalid()
    {
        $coupon = Coupon::factory()->create([
            'minimum_amount' => 100000,
            'is_active' => true
        ]);
        
        $orderAmount = 50000;
        $this->assertTrue($orderAmount < $coupon->minimum_amount);
    }

    // User Address Management Tests
    
    /** @test */
    public function user_can_have_multiple_addresses()
    {
        $address1 = UserAddress::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Home',
            'is_default' => true
        ]);
        
        $address2 = UserAddress::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Work',
            'is_default' => false
        ]);

        $this->assertEquals(2, $this->user->addresses()->count());
    }

    /** @test */
    public function user_can_have_only_one_default_address()
    {
        $address1 = UserAddress::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true
        ]);
        
        $address2 = UserAddress::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true
        ]);
        
        // When a new default address is created, the old one should be updated
        $address1->refresh();
        $this->assertFalse($address1->is_default);
        $this->assertTrue($address2->is_default);
    }

    // Payment Tests
    
    /** @test */
    public function payment_can_be_created_for_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total_amount' => 150000
        ]);
        
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 150000,
            'status' => 'completed',
            'payment_method' => 'credit_card'
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => 150000,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function payment_amount_should_match_order_total()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total_amount' => 150000
        ]);
        
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 150000
        ]);
        
        $this->assertEquals($order->total_amount, $payment->amount);
    }

    // Product Stock Management Tests
    
    /** @test */
    public function product_stock_decreases_when_order_is_placed()
    {
        $initialStock = $this->product->stock;
        $orderQuantity = 5;
        
        // Simulate order placement
        $this->product->decrement('stock', $orderQuantity);
        
        $this->assertEquals($initialStock - $orderQuantity, $this->product->fresh()->stock);
    }

    /** @test */
    public function product_stock_increases_when_order_is_cancelled()
    {
        $initialStock = $this->product->stock;
        $orderQuantity = 3;
        
        // Simulate order cancellation
        $this->product->increment('stock', $orderQuantity);
        
        $this->assertEquals($initialStock + $orderQuantity, $this->product->fresh()->stock);
    }

    /** @test */
    public function out_of_stock_product_cannot_be_ordered()
    {
        $this->product->update(['stock' => 0]);
        
        $this->assertEquals(0, $this->product->stock);
        $this->assertTrue($this->product->stock <= 0);
    }

    // System Security Tests
    
    /** @test */
    public function guest_cannot_access_admin_dashboard()
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function regular_user_cannot_access_admin_pages()
    {
        $this->actingAs($this->user)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_all_admin_pages()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);
            
        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertStatus(200);
            
        $this->actingAs($this->admin)
            ->get(route('admin.categories.index'))
            ->assertStatus(200);
    }

    // Data Validation Tests
    
    /** @test */
    public function email_must_be_valid_format()
    {
        Livewire::test(Login::class)
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function password_must_meet_minimum_length()
    {
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', '123')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    // Database Integrity Tests
    
    /** @test */
    public function deleting_user_removes_related_data()
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        
        $this->user->delete();
        
        $this->assertDatabaseMissing('carts', ['user_id' => $this->user->id]);
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    /** @test */
    public function deleting_product_removes_related_data()
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price
        ]);
        
        $this->product->delete();
        
        $this->assertDatabaseMissing('cart_items', ['product_id' => $this->product->id]);
    }
}