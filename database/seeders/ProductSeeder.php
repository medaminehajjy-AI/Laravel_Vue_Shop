<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'Premium over-ear headphones with noise cancellation, 30-hour battery life, and crystal-clear sound quality.',
                'price' => 89.99,
                'image' => 'https://picsum.photos/seed/headphones/400/400',
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smart Fitness Watch',
                'description' => 'Track your health with heart rate monitoring, GPS, and 7-day battery life. Water resistant up to 50m.',
                'price' => 149.99,
                'image' => 'https://picsum.photos/seed/smartwatch/400/400',
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Organic Cotton T-Shirt',
                'description' => 'Comfortable and eco-friendly t-shirt made from 100% organic cotton. Available in multiple colors.',
                'price' => 34.99,
                'image' => 'https://picsum.photos/seed/tshirt/400/400',
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'The Art of Programming',
                'description' => 'A comprehensive guide to modern software development practices and clean code principles.',
                'price' => 49.99,
                'image' => 'https://picsum.photos/seed/book1/400/400',
                'category_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minimalist Desk Lamp',
                'description' => 'LED desk lamp with adjustable brightness and color temperature. USB charging port included.',
                'price' => 59.99,
                'image' => 'https://picsum.photos/seed/lamp/400/400',
                'category_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Portable Bluetooth Speaker',
                'description' => 'Waterproof speaker with 360-degree sound and 12-hour playtime. Perfect for outdoor adventures.',
                'price' => 79.99,
                'image' => 'https://picsum.photos/seed/speaker/400/400',
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Extra thick non-slip yoga mat with alignment lines. Eco-friendly materials, 6mm thickness.',
                'price' => 45.99,
                'image' => 'https://picsum.photos/seed/yogamat/400/400',
                'category_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Denim Jacket Classic',
                'description' => 'Timeless denim jacket with a modern fit. Perfect for layering in any season.',
                'price' => 89.99,
                'image' => 'https://picsum.photos/seed/jacket/400/400',
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plant-Based Cookbooks',
                'description' => '100 delicious plant-based recipes for every occasion. Includes nutrition tips and meal plans.',
                'price' => 39.99,
                'image' => 'https://picsum.photos/seed/cookbook/400/400',
                'category_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ceramic Plant Pot Set',
                'description' => 'Set of 3 handmade ceramic pots with bamboo trays. Perfect for succulents and small plants.',
                'price' => 54.99,
                'image' => 'https://picsum.photos/seed/plantpot/400/400',
                'category_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
