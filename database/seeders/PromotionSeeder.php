<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $promotions = [
            [
                'title' => 'Flash Sale - 50% Off Electronics',
                'description' => 'Limited time offer on all electronics! Grab your favorite gadgets at unbeatable prices.',
                'type' => 'banner',
                'discount_text' => '50% OFF',
                'discount_code' => 'FLASH50',
                'background_color' => '#FF6B6B',
                'text_color' => '#FFFFFF',
                'button_text' => 'Shop Now',
                'button_link' => '/products',
                'button_color' => '#4ECDC4',
                'display_pages' => json_encode(['homepage', 'products']),
                'target_audience' => 'all',
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(7),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Welcome New Customers!',
                'description' => 'Get 20% off your first order with us. Start shopping and save big!',
                'type' => 'popup',
                'discount_text' => '20% OFF First Order',
                'discount_code' => 'WELCOME20',
                'background_color' => '#4ECDC4',
                'text_color' => '#FFFFFF',
                'button_text' => 'Start Shopping',
                'button_link' => '/products',
                'button_color' => '#FF6B6B',
                'display_pages' => json_encode(['homepage']),
                'target_audience' => 'new_users',
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(30),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Free Shipping on Orders Over $100',
                'description' => 'No minimum order required for premium members. Standard shipping applies.',
                'type' => 'banner',
                'discount_text' => 'FREE SHIPPING',
                'background_color' => '#45B7D1',
                'text_color' => '#FFFFFF',
                'button_text' => 'Learn More',
                'button_link' => '/shipping-info',
                'button_color' => '#FFA07A',
                'display_pages' => json_encode(['all']),
                'target_audience' => 'all',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(15),
                'is_active' => true,
                'sort_order' => 3,
            ]
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
