<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|exists:users,id')]
    public string $user_id = '';

    #[Rule('nullable|string')]
    public string $shipping_address = '';

    #[Rule('required|in:pending,paid,shipped,completed,canceled')]
    public string $status = 'pending';

    #[Rule('required|in:cash,card,online,bank_transfer')]
    public string $payment_method = 'cash';

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

    public function mount()
    {
        // Initialize with one empty order item
        $this->orderItems = [
            ['product_id' => '', 'quantity' => 1, 'price' => 0]
        ];
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
            
            // Set default address if exists
            $defaultAddress = collect($this->userAddresses)->firstWhere('is_default', true);
            if ($defaultAddress) {
                $this->selected_address_id = $defaultAddress['id'];
                $this->shipping_address = $defaultAddress['address'] . ', ' . $defaultAddress['city'] . ', ' . $defaultAddress['state'];
            }
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
            // Check if product already exists in order items
            $existingIndex = array_search($productId, array_column($this->orderItems, 'product_id'));
            
            if ($existingIndex !== false) {
                // Increase quantity if product already exists
                $this->orderItems[$existingIndex]['quantity']++;
            } else {
                // Add new product
                $this->orderItems[] = [
                    'product_id' => $productId,
                    'quantity' => 1,
                    'price' => $product->price
                ];
            }
            
            $this->selectedProducts[$productId] = $product;
            $this->searchProduct = '';
            $this->searchResults = [];
        }
    }

    public function removeOrderItem($index)
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

        // Validate order items
        if (empty($this->orderItems) || empty(array_filter($this->orderItems, fn($item) => !empty($item['product_id'])))) {
            session()->flash('error', 'حداقل یک محصول باید انتخاب شود.');
            return;
        }

        // Create order
        $order = Order::create([
            'user_id' => $this->user_id,
            'total_amount' => $this->calculateTotal(),
            'status' => $this->status,
            'shipping_address' => $this->shipping_address,
            'payment_method' => $this->payment_method,
        ]);

        // Create order items
        foreach ($this->orderItems as $item) {
            if (!empty($item['product_id'])) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }
        }

        session()->flash('success', 'سفارش جدید با موفقیت ایجاد شد.');
        
        return $this->redirect(route('admin.orders.show', $order), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $users = User::where('role', 'customer')->get();
        return view('livewire.admin.orders.create', compact('users'));
    }
}
