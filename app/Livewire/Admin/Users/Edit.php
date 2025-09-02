<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public User $user;

    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('required|email|max:150')]
    public string $email = '';

    #[Rule('nullable|string|min:8')]
    public string $password = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('required|in:admin,customer')]
    public string $role = 'customer';

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
    }

    public function save()
    {
        // اعتبارسنجی با در نظر گیری کاربر فعلی برای ایمیل
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,customer',
        ]);

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role' => $this->role,
        ];

        // اگر رمز عبور وارد شده باشد، آن را به‌روزرسانی کن
        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $this->user->update($updateData);

        session()->flash('success', 'کاربر با موفقیت به‌روزرسانی شد.');
        
        return $this->redirect(route('admin.users.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.users.edit');
    }
}
