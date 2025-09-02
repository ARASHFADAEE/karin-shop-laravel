<?php

namespace App\Livewire\Admin\Coupons;

use App\Models\Coupon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Coupon $coupon;

    #[Rule('required|string|max:50')]
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

    #[Rule('nullable|date')]
    public string $expires_at = '';

    #[Rule('boolean')]
    public bool $is_active = true;

    public function mount(Coupon $coupon)
    {
        $this->coupon = $coupon;
        
        // Load coupon data
        $this->code = $coupon->code;
        $this->description = $coupon->description ?? '';
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->minimum_amount = $coupon->minimum_amount ?? '';
        $this->usage_limit = $coupon->usage_limit ?? '';
        $this->expires_at = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '';
        $this->is_active = $coupon->is_active;
    }

    public function save()
    {
        // Custom validation for unique code (excluding current coupon)
        $this->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->coupon->id,
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Additional validation for percentage type
        if ($this->type === 'percentage' && $this->value > 100) {
            $this->addError('value', 'درصد تخفیف نمی‌تواند بیشتر از 100 باشد.');
            return;
        }

        $this->coupon->update([
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount ?: null,
            'usage_limit' => $this->usage_limit ?: null,
            'expires_at' => $this->expires_at ?: null,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'کوپن با موفقیت به‌روزرسانی شد.');
        
        return $this->redirect(route('admin.coupons.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.coupons.edit');
    }
}
