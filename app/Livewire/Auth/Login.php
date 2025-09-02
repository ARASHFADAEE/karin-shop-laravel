<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        // Rate limiting
        $key = 'login.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "تعداد تلاش‌های ورود بیش از حد مجاز. لطفاً {$seconds} ثانیه صبر کنید."
            ]);
        }

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
            'role' => 'admin' // فقط ادمین‌ها می‌توانند وارد شوند
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            RateLimiter::clear($key);
            request()->session()->regenerate();
            
            return $this->redirect(route('admin.dashboard'), navigate: true);
        }

        RateLimiter::hit($key, 60); // 60 seconds penalty
        
        throw ValidationException::withMessages([
            'email' => 'اطلاعات ورود نادرست است یا شما دسترسی ادمین ندارید.'
        ]);
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
