<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $perPage = 10;

    protected $queryString = ['search', 'role'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRole()
    {
        $this->resetPage();
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            // جلوگیری از حذف ادمین فعلی
            if ($user->id === auth()->user()->id) {
                session()->flash('error', 'نمی‌توانید خودتان را حذف کنید.');
                return;
            }
            
            // بررسی اینکه آیا کاربر سفارش دارد
            if ($user->orders()->count() > 0) {
                session()->flash('error', 'این کاربر دارای سفارش است و قابل حذف نیست.');
                return;
            }
            
            // حذف داده‌های مرتبط
            $user->carts()->delete();
            $user->wishlists()->delete();
            $user->reviews()->delete();
            
            $user->delete();
            session()->flash('success', 'کاربر با موفقیت حذف شد.');
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->role, function ($query) {
                $query->where('role', $this->role);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.users.index', compact('users'));
    }
}
