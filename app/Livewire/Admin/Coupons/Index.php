<?php

namespace App\Livewire\Admin\Coupons;

use App\Models\Coupon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function delete($couponId)
    {
        $coupon = Coupon::find($couponId);
        if ($coupon) {
            $coupon->delete();
            session()->flash('success', 'کوپن با موفقیت حذف شد.');
        }
    }

    public function toggleStatus($couponId)
    {
        $coupon = Coupon::find($couponId);
        if ($coupon) {
            $coupon->update([
                'is_active' => !$coupon->is_active
            ]);
            $status = $coupon->is_active ? 'فعال' : 'غیرفعال';
            session()->flash('success', "وضعیت کوپن به {$status} تغییر کرد.");
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $coupons = Coupon::query()
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_active', $this->status === 'active');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.coupons.index', compact('coupons'));
    }
}
