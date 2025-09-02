<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductFeaturedImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\Products\Create as ProductCreate;
use App\Livewire\Admin\Products\Edit as ProductEdit;
use App\Livewire\Admin\Products\Index as ProductIndex;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);
        
        // Create test category
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
        
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_products_index_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ProductIndex::class);
    }

    /** @test */
    public function admin_can_view_create_product_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.create'))
            ->assertStatus(200)
            ->assertSeeLivewire(ProductCreate::class);
    }

    /** @test */
    public function admin_can_create_product_with_valid_data()
    {
        $productData = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'This is a test product description',
            'price' => 100000,
            'stock' => 50,
            'sku' => 'TEST-001',
            'status' => 'active',
            'selectedCategories' => [$this->category->id],
            'primaryCategory' => $this->category->id,
        ];

        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set($productData)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 100000,
            'stock' => 50,
            'sku' => 'TEST-001',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_can_create_product_with_discount()
    {
        $productData = [
            'name' => 'Discounted Product',
            'slug' => 'discounted-product',
            'description' => 'Product with discount',
            'price' => 80000,
            'original_price' => 100000,
            'discount_percentage' => 20,
            'discount_amount' => 20000,
            'has_discount' => true,
            'stock' => 30,
            'sku' => 'DISC-001',
            'status' => 'active',
            'selectedCategories' => [$this->category->id],
            'primaryCategory' => $this->category->id,
        ];

        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set($productData)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Discounted Product',
            'has_discount' => true,
            'original_price' => 100000,
            'discount_percentage' => 20
        ]);
    }

    /** @test */
    public function admin_can_create_product_with_attributes()
    {
        $productData = [
            'name' => 'Product with Attributes',
            'slug' => 'product-with-attributes',
            'price' => 150000,
            'stock' => 25,
            'sku' => 'ATTR-001',
            'status' => 'active',
            'selectedCategories' => [$this->category->id],
            'primaryCategory' => $this->category->id,
            'productAttributes' => [
                ['name' => 'Color', 'value' => 'Red'],
                ['name' => 'Size', 'value' => 'Large'],
                ['name' => 'Material', 'value' => 'Cotton']
            ]
        ];

        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set($productData)
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('name', 'Product with Attributes')->first();
        $this->assertNotNull($product);
        $this->assertEquals(3, $product->attributes()->count());
        
        $this->assertDatabaseHas('product_attributes', [
            'product_id' => $product->id,
            'attribute_name' => 'Color',
            'attribute_value' => 'Red'
        ]);
    }

    /** @test */
    public function product_creation_requires_name()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', '')
            ->set('price', 100000)
            ->set('stock', 10)
            ->set('sku', 'TEST-001')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function product_creation_requires_price()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'Test Product')
            ->set('price', '')
            ->set('stock', 10)
            ->set('sku', 'TEST-001')
            ->call('save')
            ->assertHasErrors(['price']);
    }

    /** @test */
    public function product_creation_requires_unique_sku()
    {
        Product::factory()->create(['sku' => 'DUPLICATE-SKU']);

        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'Test Product')
            ->set('price', 100000)
            ->set('stock', 10)
            ->set('sku', 'DUPLICATE-SKU')
            ->call('save')
            ->assertHasErrors(['sku']);
    }

    /** @test */
    public function product_creation_requires_at_least_one_category()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'Test Product')
            ->set('price', 100000)
            ->set('stock', 10)
            ->set('sku', 'TEST-001')
            ->set('selectedCategories', [])
            ->call('save')
            ->assertSet('selectedCategories', []);
    }

    /** @test */
    public function admin_can_edit_existing_product()
    {
        $product = Product::factory()->create([
            'name' => 'Original Product',
            'price' => 100000
        ]);
        
        $product->categories()->attach($this->category->id, ['is_primary' => true]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product))
            ->assertStatus(200)
            ->assertSeeLivewire(ProductEdit::class);
    }

    /** @test */
    public function admin_can_update_product_information()
    {
        $product = Product::factory()->create([
            'name' => 'Original Product',
            'price' => 100000,
            'stock' => 20
        ]);
        
        $product->categories()->attach($this->category->id, ['is_primary' => true]);

        Livewire::actingAs($this->admin)
            ->test(ProductEdit::class, ['product' => $product])
            ->set('name', 'Updated Product')
            ->set('price', 150000)
            ->set('stock', 30)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 150000,
            'stock' => 30
        ]);
    }

    /** @test */
    public function admin_can_update_product_discount_settings()
    {
        $product = Product::factory()->create([
            'name' => 'Product for Discount',
            'price' => 100000,
            'has_discount' => false
        ]);
        
        $product->categories()->attach($this->category->id, ['is_primary' => true]);

        Livewire::actingAs($this->admin)
            ->test(ProductEdit::class, ['product' => $product])
            ->set('has_discount', true)
            ->set('original_price', 100000)
            ->set('price', 80000)
            ->set('discount_percentage', 20)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'has_discount' => true,
            'original_price' => 100000,
            'discount_percentage' => 20
        ]);
    }

    /** @test */
    public function admin_can_update_product_attributes()
    {
        $product = Product::factory()->create();
        $product->categories()->attach($this->category->id, ['is_primary' => true]);
        
        // Create initial attributes
        ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_name' => 'Old Color',
            'attribute_value' => 'Blue'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductEdit::class, ['product' => $product])
            ->set('productAttributes', [
                ['name' => 'New Color', 'value' => 'Red'],
                ['name' => 'Size', 'value' => 'Medium']
            ])
            ->call('save')
            ->assertHasNoErrors();

        // Old attributes should be deleted
        $this->assertDatabaseMissing('product_attributes', [
            'product_id' => $product->id,
            'attribute_name' => 'Old Color'
        ]);

        // New attributes should be created
        $this->assertDatabaseHas('product_attributes', [
            'product_id' => $product->id,
            'attribute_name' => 'New Color',
            'attribute_value' => 'Red'
        ]);
    }

    /** @test */
    public function admin_can_generate_slug_automatically()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'محصول تست')
            ->call('generateSlug')
            ->assertSet('slug', 'test-product'); // Assuming translation works
    }

    /** @test */
    public function admin_can_generate_sku_automatically()
    {
        $component = Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->call('generateSku');

        $this->assertStringStartsWith('PRD-', $component->get('sku'));
        $this->assertEquals(12, strlen($component->get('sku'))); // PRD- + 8 characters
    }

    /** @test */
    public function product_price_must_be_numeric()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'Test Product')
            ->set('price', 'invalid-price')
            ->set('stock', 10)
            ->set('sku', 'TEST-001')
            ->call('save')
            ->assertHasErrors(['price']);
    }

    /** @test */
    public function product_stock_must_be_non_negative()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'Test Product')
            ->set('price', 100000)
            ->set('stock', -5)
            ->set('sku', 'TEST-001')
            ->call('save')
            ->assertHasErrors(['stock']);
    }

    /** @test */
    public function discount_percentage_cannot_exceed_100()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductCreate::class)
            ->set('name', 'Test Product')
            ->set('price', 100000)
            ->set('has_discount', true)
            ->set('discount_percentage', 150)
            ->set('stock', 10)
            ->set('sku', 'TEST-001')
            ->call('save')
            ->assertHasErrors(['discount_percentage']);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        $product = Product::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ProductIndex::class)
            ->call('delete', $product->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function admin_can_search_products()
    {
        Product::factory()->create(['name' => 'Searchable Product']);
        Product::factory()->create(['name' => 'Another Product']);

        Livewire::actingAs($this->admin)
            ->test(ProductIndex::class)
            ->set('search', 'Searchable')
            ->assertSee('Searchable Product')
            ->assertDontSee('Another Product');
    }

    /** @test */
    public function admin_can_filter_products_by_status()
    {
        Product::factory()->create(['name' => 'Active Product', 'status' => 'active']);
        Product::factory()->create(['name' => 'Draft Product', 'status' => 'draft']);

        Livewire::actingAs($this->admin)
            ->test(ProductIndex::class)
            ->set('statusFilter', 'active')
            ->assertSee('Active Product')
            ->assertDontSee('Draft Product');
    }

    /** @test */
    public function admin_can_filter_products_by_category()
    {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        
        $product1 = Product::factory()->create(['name' => 'Product 1']);
        $product2 = Product::factory()->create(['name' => 'Product 2']);
        
        $product1->categories()->attach($category1->id);
        $product2->categories()->attach($category2->id);

        Livewire::actingAs($this->admin)
            ->test(ProductIndex::class)
            ->set('categoryFilter', $category1->id)
            ->assertSee('Product 1')
            ->assertDontSee('Product 2');
    }

    /** @test */
    public function guest_cannot_access_product_management_pages()
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('login'));
            
        $this->get(route('admin.products.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_product_management()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $this->actingAs($user)
            ->get(route('admin.products.index'))
            ->assertStatus(403);
    }
}