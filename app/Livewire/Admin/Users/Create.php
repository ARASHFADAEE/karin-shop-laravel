<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('required|email|unique:users,email|max:150')]
    public string $email = '';

    #[Rule('required|string|min:8|confirmed')]
    public string $password = '';

    #[Rule('required|string|min:8')]
    public string $password_confirmation = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('required|in:admin,customer')]
    public string $role = 'customer';

    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone' => $this->phone ?: null,
            'role' => $this->role,
            'email_verified_at' => now(),
        ]);

        session()->flash('success', 'کاربر جدید با موفقیت ایجاد شد.');
        
        return $this->redirect(route('admin.users.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.users.create');
    }
}
