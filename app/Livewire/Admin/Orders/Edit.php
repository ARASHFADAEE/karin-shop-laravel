<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Order $order;

    #[Rule('required|exists:users,id')]
    public string $user_id = '';

    #[Rule('nullable|string')]
    public string $shipping_address = '';

    #[Rule('required|in:pending,processing,shipped,delivered,cancelled')]
    public string $status = 'pending';

    #[Rule('required|in:credit_card,bank_transfer,cash_on_delivery')]
    public string $payment_method = 'cash_on_delivery';

    public array $orderItems = [];
    public array $selectedProducts = [];
    public string $searchProduct = '';
    public array $searchResults = [];
    
    // User search
    public string $searchUser = '';
    public array $userSearchResults = [];
    public string $selectedUserName = '';
    
    // User addresses
    public array $userAddresses = [];
    public string $selected_address_id = '';

    public function mount(Order $order)
    {
        $this->order = $order->load(['user', 'orderItems.product']);
        
        // Load order data
        $this->user_id = $order->user_id;
        $this->selectedUserName = $order->user->name;
        $this->shipping_address = $order->shipping_address;
        $this->status = $order->status;
        $this->payment_method = $order->payment_method;
        
        // Load order items
        $this->orderItems = $order->orderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        })->toArray();
        
        // اگر order items خالی است، یک item خالی اضافه کن
        if (empty($this->orderItems)) {
            $this->orderItems = [[
                'id' => null,
                'product_id' => '',
                'product_name' => '',
                'quantity' => 1,
                'price' => 0,
            ]];
        }
        
        // Load selected products
        foreach ($this->orderItems as $item) {
            $this->selectedProducts[$item['product_id']] = $item['product_name'];
        }
        
        // Load user addresses
        $this->loadUserAddresses();
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) >= 2) {
            $this->searchResults = Product::where('name', 'like', '%' . $this->searchProduct . '%')
                ->orWhere('sku', 'like', '%' . $this->searchProduct . '%')
                ->where('status', 'active')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function updatedSearchUser()
    {
        if (strlen($this->searchUser) >= 2) {
            $this->userSearchResults = User::where('role', 'customer')
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->searchUser . '%')
                          ->orWhere('email', 'like', '%' . $this->searchUser . '%')
                          ->orWhere('phone', 'like', '%' . $this->searchUser . '%');
                })
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->userSearchResults = [];
        }
    }

    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->selectedUserName = $userName;
        $this->searchUser = '';
        $this->userSearchResults = [];
        
        // Load user addresses
        $this->loadUserAddresses();
    }
    
    public function loadUserAddresses()
    {
        if ($this->user_id) {
            $user = User::with('addresses')->find($this->user_id);
            $this->userAddresses = $user ? $user->addresses->toArray() : [];
        } else {
            $this->userAddresses = [];
            $this->selected_address_id = '';
        }
    }
    
    public function selectAddress($addressId)
    {
        $address = collect($this->userAddresses)->firstWhere('id', $addressId);
        if ($address) {
            $this->selected_address_id = $addressId;
            $this->shipping_address = $address['address'] . ', ' . $address['city'] . ', ' . $address['state'];
        }
    }

    public function addProduct($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $existingIndex = array_search($productId, array_column($this->orderItems, 'product_id'));
            
            if ($existingIndex !== false) {
                // Increase quantity if product already exists
                $this->orderItems[$existingIndex]['quantity']++;
            } else {
                // Add new product
                $this->orderItems[] = [
                    'id' => null, // New item
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'quantity' => 1,
                    'price' => $product->price,
                ];
            }
            
            $this->selectedProducts[$productId] = $product->name;
            $this->searchProduct = '';
            $this->searchResults = [];
        }
    }

    public function removeProduct($index)
    {
        if (isset($this->orderItems[$index])) {
            $productId = $this->orderItems[$index]['product_id'];
            unset($this->orderItems[$index], $this->selectedProducts[$productId]);
            $this->orderItems = array_values($this->orderItems); // Re-index array
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if (isset($this->orderItems[$index]) && $quantity > 0) {
            $this->orderItems[$index]['quantity'] = $quantity;
        }
    }

    public function updatePrice($index, $price)
    {
        if (isset($this->orderItems[$index]) && $price >= 0) {
            $this->orderItems[$index]['price'] = $price;
        }
    }

    public function calculateTotal()
    {
        return array_sum(array_map(function ($item) {
            return $item['quantity'] * $item['price'];
        }, $this->orderItems));
    }

    public function save()
    {
        $this->validate();

        // Validate order items only if there are actual products
        $validItems = array_filter($this->orderItems, fn($item) => !empty($item['product_id']));
        if (empty($validItems) && !empty(array_filter($this->orderItems, fn($item) => !empty($item['product_name'])))) {
            session()->flash('error', 'حداقل یک محصول باید انتخاب شود.');
            return;
        }

        // Update order
        $this->order->update([
            'user_id' => $this->user_id,
            'total_amount' => $this->calculateTotal(),
            'status' => $this->status,
            'shipping_address' => $this->shipping_address,
            'payment_method' => $this->payment_method,
        ]);

        // Update order items only if there are valid items
        if (!empty($validItems)) {
            // First, delete existing items
            $this->order->orderItems()->delete();
            
            // Then create new items
            foreach ($this->orderItems as $item) {
                if (!empty($item['product_id'])) {
                    OrderItem::create([
                        'order_id' => $this->order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }
        }

        session()->flash('success', 'سفارش با موفقیت به‌روزرسانی شد.');
        
        return $this->redirect(route('admin.orders.show', $this->order), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.orders.edit');
    }
}
