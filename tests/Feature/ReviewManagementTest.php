<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Admin\Reviews\Index as ReviewIndex;
use Carbon\Carbon;

class ReviewManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $product;

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
        
        // Create test product
        $category = Category::factory()->create();
        $this->product = Product::factory()->create(['name' => 'Test Product']);
        $this->product->categories()->attach($category->id);
    }

    /** @test */
    public function admin_can_view_reviews_index_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reviews.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ReviewIndex::class);
    }

    /** @test */
    public function admin_can_view_all_reviews()
    {
        $review1 = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Great product!',
            'rating' => 5
        ]);
        
        $review2 = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Not bad',
            'rating' => 3
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->assertSee('Great product!')
            ->assertSee('Not bad');
    }

    /** @test */
    public function admin_can_approve_pending_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'pending'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('approve', $review->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function admin_can_reject_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'pending'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('reject', $review->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'rejected'
        ]);
    }

    /** @test */
    public function admin_can_reply_to_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'admin_reply' => null
        ]);

        $replyText = 'Thank you for your feedback!';

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('reply', $review->id, $replyText)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => $replyText
        ]);
        
        $review->refresh();
        $this->assertNotNull($review->replied_at);
    }

    /** @test */
    public function admin_can_update_existing_reply()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'admin_reply' => 'Original reply',
            'replied_at' => Carbon::now()->subHour()
        ]);

        $newReplyText = 'Updated reply text';

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('reply', $review->id, $newReplyText)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => $newReplyText
        ]);
    }

    /** @test */
    public function admin_can_delete_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('delete', $review->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }

    /** @test */
    public function admin_can_search_reviews_by_content()
    {
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'This product is amazing!'
        ]);
        
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Not satisfied with quality'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->set('search', 'amazing')
            ->assertSee('This product is amazing!')
            ->assertDontSee('Not satisfied with quality');
    }

    /** @test */
    public function admin_can_search_reviews_by_user_name()
    {
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);
        
        Review::factory()->create([
            'user_id' => $user1->id,
            'product_id' => $this->product->id,
            'comment' => 'Review by John'
        ]);
        
        Review::factory()->create([
            'user_id' => $user2->id,
            'product_id' => $this->product->id,
            'comment' => 'Review by Jane'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->set('search', 'John')
            ->assertSee('Review by John')
            ->assertDontSee('Review by Jane');
    }

    /** @test */
    public function admin_can_filter_reviews_by_status()
    {
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Approved review',
            'status' => 'approved'
        ]);
        
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Pending review',
            'status' => 'pending'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->set('status', 'approved')
            ->assertSee('Approved review')
            ->assertDontSee('Pending review');
    }

    /** @test */
    public function admin_can_filter_reviews_by_rating()
    {
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Five star review',
            'rating' => 5
        ]);
        
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Three star review',
            'rating' => 3
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->set('rating', '5')
            ->assertSee('Five star review')
            ->assertDontSee('Three star review');
    }

    /** @test */
    public function admin_can_change_pagination_per_page()
    {
        // Create 15 reviews
        Review::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->set('perPage', '10')
            ->assertSet('perPage', '10');
    }

    /** @test */
    public function review_statistics_are_displayed_correctly()
    {
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'approved'
        ]);
        
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'pending'
        ]);
        
        Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'rejected'
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class);

        // Check that statistics are calculated correctly
        $reviews = $component->get('reviews');
        $this->assertEquals(3, $reviews->total());
    }

    /** @test */
    public function admin_cannot_approve_already_approved_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'approved'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('approve', $review->id);

        // Status should remain approved
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function admin_cannot_reject_already_rejected_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'status' => 'rejected'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('reject', $review->id);

        // Status should remain rejected
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'status' => 'rejected'
        ]);
    }

    /** @test */
    public function empty_reply_is_not_saved()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'admin_reply' => null
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->call('reply', $review->id, '')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => null
        ]);
    }

    /** @test */
    public function review_with_admin_reply_shows_reply_date()
    {
        $replyDate = Carbon::now();
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'admin_reply' => 'Test reply',
            'replied_at' => $replyDate
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->assertSee($replyDate->format('Y/m/d H:i'));
    }

    /** @test */
    public function guest_cannot_access_review_management()
    {
        $this->get(route('admin.reviews.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_cannot_access_review_management()
    {
        $this->actingAs($this->user)
            ->get(route('admin.reviews.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function review_displays_product_information()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Test review'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->assertSee($this->product->name);
    }

    /** @test */
    public function review_displays_user_information()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'comment' => 'Test review'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->assertSee($this->user->name);
    }

    /** @test */
    public function review_rating_is_displayed_correctly()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Four star review'
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class)
            ->assertSee('(4/5)');
    }

    /** @test */
    public function reviews_are_paginated()
    {
        // Create more reviews than the default per page
        Review::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(ReviewIndex::class);

        $reviews = $component->get('reviews');
        $this->assertTrue($reviews->hasPages());
    }
}