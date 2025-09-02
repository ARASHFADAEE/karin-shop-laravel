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

    public function generateRandomPassword()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        // Ensure at least one of each type
        $password .= chr(rand(97, 122)); // lowercase
        $password .= chr(rand(65, 90));  // uppercase
        $password .= chr(rand(48, 57));  // number
        $password .= '!@#$%^&*'[rand(0, 7)]; // special char
        
        // Fill the rest randomly
        for ($i = 4; $i < 12; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Shuffle the password
        $password = str_shuffle($password);
        
        $this->password = $password;
        $this->password_confirmation = $password;
        
        session()->flash('success', 'پسورد رندوم تولید شد: ' . $password);
    }

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
