<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // بررسی اینکه آیا ادمین از قبل وجود دارد یا نه
        $adminExists = User::where('role', 'admin')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'مدیر سیستم',
                'email' => 'admin@karinshop.com',
                'password' => Hash::make('admin123456'),
                'phone' => '09123456789',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('ادمین پیش‌فرض با موفقیت ایجاد شد.');
            $this->command->info('ایمیل: admin@karinshop.com');
            $this->command->info('رمز عبور: admin123456');
            $this->command->warn('لطفاً پس از ورود اول، رمز عبور را تغییر دهید!');
        } else {
            $this->command->info('ادمین از قبل در سیستم وجود دارد.');
        }
    }
}
