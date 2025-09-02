<?php

namespace App\Livewire\Admin\Coupons;

use App\Models\Coupon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|string|max:50|unique:coupons,code')]
    public string $code = '';

    #[Rule('nullable|string|max:255')]
    public string $description = '';

    #[Rule('required|in:percentage,fixed')]
    public string $type = 'percentage';

    #[Rule('required|numeric|min:0')]
    public string $value = '';

    #[Rule('nullable|numeric|min:0')]
    public string $minimum_amount = '';

    #[Rule('nullable|integer|min:1')]
    public string $usage_limit = '';

    #[Rule('nullable|date|after:today')]
    public string $expires_at = '';

    #[Rule('boolean')]
    public bool $is_active = true;

    public function generateCode()
    {
        $this->code = strtoupper(substr(md5(time()), 0, 10));
    }

    public function save()
    {
        $this->validate();

        // Additional validation for percentage type
        if ($this->type === 'percentage' && $this->value > 100) {
            $this->addError('value', 'درصد تخفیف نمی‌تواند بیشتر از 100 باشد.');
            return;
        }

        Coupon::create([
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount ?: null,
            'usage_limit' => $this->usage_limit ?: null,
            'expires_at' => $this->expires_at ?: null,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'کوپن جدید با موفقیت ایجاد شد.');
        
        return $this->redirect(route('admin.coupons.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.coupons.create');
    }
}
