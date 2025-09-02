<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\Categories\Create as CategoryCreate;
use App\Livewire\Admin\Categories\Edit as CategoryEdit;
use App\Livewire\Admin\Categories\Index as CategoryIndex;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function admin_can_view_categories_index_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.categories.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(CategoryIndex::class);
    }

    /** @test */
    public function admin_can_view_create_category_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.categories.create'))
            ->assertStatus(200)
            ->assertSeeLivewire(CategoryCreate::class);
    }

    /** @test */
    public function admin_can_create_category_with_valid_data()
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category description',
            'status' => 'active',
        ];

        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set($categoryData)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category description',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_can_create_parent_category()
    {
        $categoryData = [
            'name' => 'Parent Category',
            'slug' => 'parent-category',
            'description' => 'This is a parent category',
            'status' => 'active',
            'parent_id' => null,
        ];

        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set($categoryData)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Parent Category',
            'parent_id' => null
        ]);
    }

    /** @test */
    public function admin_can_create_child_category()
    {
        $parentCategory = Category::factory()->create([
            'name' => 'Parent Category',
            'parent_id' => null
        ]);

        $categoryData = [
            'name' => 'Child Category',
            'slug' => 'child-category',
            'description' => 'This is a child category',
            'status' => 'active',
            'parent_id' => $parentCategory->id,
        ];

        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set($categoryData)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Child Category',
            'parent_id' => $parentCategory->id
        ]);
    }

    /** @test */
    public function category_creation_requires_name()
    {
        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set('name', '')
            ->set('slug', 'test-slug')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function category_creation_requires_unique_slug()
    {
        Category::factory()->create(['slug' => 'duplicate-slug']);

        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set('name', 'Test Category')
            ->set('slug', 'duplicate-slug')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    /** @test */
    public function category_name_must_be_unique()
    {
        Category::factory()->create(['name' => 'Duplicate Name']);

        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set('name', 'Duplicate Name')
            ->set('slug', 'unique-slug')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function admin_can_edit_existing_category()
    {
        $category = Category::factory()->create([
            'name' => 'Original Category',
            'slug' => 'original-category'
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $category))
            ->assertStatus(200)
            ->assertSeeLivewire(CategoryEdit::class);
    }

    /** @test */
    public function admin_can_update_category_information()
    {
        $category = Category::factory()->create([
            'name' => 'Original Category',
            'slug' => 'original-category',
            'description' => 'Original description'
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryEdit::class, ['category' => $category])
            ->set('name', 'Updated Category')
            ->set('slug', 'updated-category')
            ->set('description', 'Updated description')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'Updated description'
        ]);
    }

    /** @test */
    public function admin_can_change_category_parent()
    {
        $parentCategory = Category::factory()->create(['name' => 'New Parent']);
        $category = Category::factory()->create([
            'name' => 'Child Category',
            'parent_id' => null
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryEdit::class, ['category' => $category])
            ->set('parent_id', $parentCategory->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $parentCategory->id
        ]);
    }

    /** @test */
    public function category_cannot_be_its_own_parent()
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CategoryEdit::class, ['category' => $category])
            ->set('parent_id', $category->id)
            ->call('save')
            ->assertHasErrors(['parent_id']);
    }

    /** @test */
    public function admin_can_delete_category_without_products()
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    /** @test */
    public function admin_cannot_delete_category_with_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->call('delete', $category->id)
            ->assertHasErrors();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function admin_can_search_categories()
    {
        Category::factory()->create(['name' => 'Searchable Category']);
        Category::factory()->create(['name' => 'Another Category']);

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->set('search', 'Searchable')
            ->assertSee('Searchable Category')
            ->assertDontSee('Another Category');
    }

    /** @test */
    public function admin_can_filter_categories_by_status()
    {
        Category::factory()->create(['name' => 'Active Category', 'status' => 'active']);
        Category::factory()->create(['name' => 'Inactive Category', 'status' => 'inactive']);

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->set('statusFilter', 'active')
            ->assertSee('Active Category')
            ->assertDontSee('Inactive Category');
    }

    /** @test */
    public function admin_can_filter_parent_categories()
    {
        $parentCategory = Category::factory()->create(['name' => 'Parent Category', 'parent_id' => null]);
        $childCategory = Category::factory()->create(['name' => 'Child Category', 'parent_id' => $parentCategory->id]);

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->set('parentFilter', 'parent')
            ->assertSee('Parent Category')
            ->assertDontSee('Child Category');
    }

    /** @test */
    public function admin_can_filter_child_categories()
    {
        $parentCategory = Category::factory()->create(['name' => 'Parent Category', 'parent_id' => null]);
        $childCategory = Category::factory()->create(['name' => 'Child Category', 'parent_id' => $parentCategory->id]);

        Livewire::actingAs($this->admin)
            ->test(CategoryIndex::class)
            ->set('parentFilter', 'child')
            ->assertSee('Child Category')
            ->assertDontSee('Parent Category');
    }

    /** @test */
    public function admin_can_generate_slug_from_name()
    {
        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set('name', 'Test Category Name')
            ->call('generateSlug')
            ->assertSet('slug', 'test-category-name');
    }

    /** @test */
    public function category_slug_is_automatically_generated_if_empty()
    {
        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set('name', 'Auto Slug Category')
            ->set('slug', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Auto Slug Category',
            'slug' => 'auto-slug-category'
        ]);
    }

    /** @test */
    public function category_status_defaults_to_active()
    {
        Livewire::actingAs($this->admin)
            ->test(CategoryCreate::class)
            ->set('name', 'Default Status Category')
            ->set('slug', 'default-status-category')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Default Status Category',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function guest_cannot_access_category_management_pages()
    {
        $this->get(route('admin.categories.index'))
            ->assertRedirect(route('login'));
            
        $this->get(route('admin.categories.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_category_management()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $this->actingAs($user)
            ->get(route('admin.categories.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function category_hierarchy_is_maintained_correctly()
    {
        $grandParent = Category::factory()->create(['name' => 'Grand Parent', 'parent_id' => null]);
        $parent = Category::factory()->create(['name' => 'Parent', 'parent_id' => $grandParent->id]);
        $child = Category::factory()->create(['name' => 'Child', 'parent_id' => $parent->id]);

        $this->assertEquals($grandParent->id, $parent->parent_id);
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertNull($grandParent->parent_id);
    }

    /** @test */
    public function category_can_have_multiple_children()
    {
        $parent = Category::factory()->create(['name' => 'Parent Category']);
        $child1 = Category::factory()->create(['name' => 'Child 1', 'parent_id' => $parent->id]);
        $child2 = Category::factory()->create(['name' => 'Child 2', 'parent_id' => $parent->id]);
        $child3 = Category::factory()->create(['name' => 'Child 3', 'parent_id' => $parent->id]);

        $parent->refresh();
        $this->assertEquals(3, $parent->children()->count());
    }

    /** @test */
    public function deleting_parent_category_updates_children()
    {
        $parent = Category::factory()->create(['name' => 'Parent Category']);
        $child = Category::factory()->create(['name' => 'Child Category', 'parent_id' => $parent->id]);

        $parent->delete();
        $child->refresh();

        $this->assertNull($child->parent_id);
    }
}