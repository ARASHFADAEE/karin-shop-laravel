<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
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
        if ($user && $user->id !== auth()->id()) {
            $user->delete();
            session()->flash('success', 'کاربر با موفقیت حذف شد.');
        }
    }

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

        return view('livewire.admin.users.index', compact('users'))
            ->layout('layouts.admin')
            ->section('title', 'مدیریت کاربران');
    }
}
