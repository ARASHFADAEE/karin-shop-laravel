<?php

namespace App\Livewire\Admin\Reviews;

use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $rating = '';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingRating()
    {
        $this->resetPage();
    }

    public function approve($reviewId)
    {
        $review = Review::find($reviewId);
        if ($review) {
            $review->update(['status' => 'approved']);
            session()->flash('success', 'نظر تایید شد.');
        }
    }

    public function reject($reviewId)
    {
        $review = Review::find($reviewId);
        if ($review) {
            $review->update(['status' => 'rejected']);
            session()->flash('success', 'نظر رد شد.');
        }
    }

    public function delete($reviewId)
    {
        $review = Review::find($reviewId);
        if ($review) {
            $review->delete();
            session()->flash('success', 'نظر حذف شد.');
        }
    }

    public function reply($reviewId, $replyText)
    {
        $review = Review::find($reviewId);
        if ($review && !empty($replyText)) {
            $review->update([
                'admin_reply' => $replyText,
                'replied_at' => now()
            ]);
            session()->flash('success', 'پاسخ ثبت شد.');
        }
    }

    public function replyToReview($reviewId, $reply)
    {
        if (!empty($reply)) {
            $review = Review::find($reviewId);
            if ($review) {
                $review->update([
                    'admin_reply' => $reply,
                    'replied_at' => now()
                ]);
                session()->flash('success', 'پاسخ ثبت شد.');
            }
        }
    }
    
    public function getReviewsProperty()
    {
        return Review::with(['user', 'product'])
            ->when($this->search, function ($query) {
                $query->where('comment', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('product', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->rating !== '', function ($query) {
                $query->where('rating', $this->rating);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $reviews = Review::with(['user', 'product'])
            ->when($this->search, function ($query) {
                $query->where('comment', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('product', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->rating !== '', function ($query) {
                $query->where('rating', $this->rating);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.reviews.index', compact('reviews'));
    }
}
