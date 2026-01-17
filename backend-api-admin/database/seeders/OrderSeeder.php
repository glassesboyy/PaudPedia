<?php

namespace Database\Seeders;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'user');
        })->get();

        $courses = Course::all();
        $webinars = Webinar::all();
        $products = Product::all();
        $promoCodes = PromoCode::where('is_active', true)->get();

        if ($users->isEmpty()) {
            return;
        }

        // Create orders for random users
        foreach ($users->random(min(10, $users->count())) as $user) {
            $orderCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $usePromo = fake()->boolean(30);
                $promoCode = ($usePromo && $promoCodes->isNotEmpty()) ? $promoCodes->random() : null;
                
                $isPaid = fake()->boolean(70);
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'promo_code' => $promoCode?->code, // String code, not ID
                    'order_number' => Order::generateOrderNumber(),
                    'total_amount' => 0,
                    'discount_amount' => 0,
                    'final_amount' => 0,
                    'status' => $isPaid ? OrderStatus::PAID : fake()->randomElement([
                        OrderStatus::PENDING,
                        OrderStatus::CANCELLED,
                    ]),
                    'payment_method' => $isPaid ? fake()->randomElement([
                        'bank_transfer',
                        'credit_card',
                        'gopay',
                        'qris',
                    ]) : null,
                    'payment_url' => !$isPaid ? 'https://app.sandbox.midtrans.com/snap/v3/...' : null,
                    'paid_at' => $isPaid ? now()->subDays(fake()->numberBetween(1, 30)) : null,
                    'midtrans_order_id' => 'MDT-' . fake()->numerify('##########'),
                    'midtrans_transaction_id' => $isPaid ? 'TRX-' . fake()->numerify('##########') : null,
                ]);

                // Add items to order
                $itemCount = fake()->numberBetween(1, 3);
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $itemType = fake()->randomElement(['course', 'webinar', 'product']);
                    
                    if ($itemType === 'course' && $courses->isNotEmpty()) {
                        $course = $courses->random();
                        OrderItem::create([
                            'order_id' => $order->id,
                            'item_type' => OrderItemType::COURSE,
                            'item_id' => $course->id,
                            'item_title' => $course->title,
                            'item_price' => $course->price,
                            'quantity' => 1,
                            'subtotal' => $course->price,
                        ]);
                    } elseif ($itemType === 'webinar' && $webinars->isNotEmpty()) {
                        $webinar = $webinars->random();
                        OrderItem::create([
                            'order_id' => $order->id,
                            'item_type' => OrderItemType::WEBINAR,
                            'item_id' => $webinar->id,
                            'item_title' => $webinar->title,
                            'item_price' => $webinar->price,
                            'quantity' => 1,
                            'subtotal' => $webinar->price,
                        ]);
                    } elseif ($itemType === 'product' && $products->isNotEmpty()) {
                        $product = $products->random();
                        $quantity = fake()->numberBetween(1, 3);
                        OrderItem::create([
                            'order_id' => $order->id,
                            'item_type' => OrderItemType::PRODUCT,
                            'item_id' => $product->id,
                            'item_title' => $product->title,
                            'item_price' => $product->price,
                            'quantity' => $quantity,
                            'subtotal' => $product->price * $quantity,
                        ]);
                    }
                }

                // Calculate order total
                $order->calculateTotal();
            }
        }
    }
}
