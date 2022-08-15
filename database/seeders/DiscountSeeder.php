<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $discounts = [
            [
                'discountId' => '10_PERCENT_OVER_1000',
                'type' => 'any',
                'priority' => 10,
                'rules' => [
                    'type' => 'percentile',
                    'basketTotal' => 1000,
                    'rate' => 10
                ]
            ],
            [
                'discountId' => '10_ADDITIVE_OVER_1000',
                'type' => 'any',
                'priority' => 9,
                'rules' => [
                    'type' => 'additive',
                    'rate' => 10
                ]
            ],
            [
                'discountId' => 'BUY_5_GET_1',
                'type' => 'category',
                'categoryOrProductId' => 2,
                'priority' => 10,
                'rules' => [
                    'type' => 'per_free',
                    'amount' => 6,
                    'count' => 1
                ]
            ],
            [
                'discountId' => 'BUY_2_LOW_PRICE_PERCENT_20',
                'type' => 'category',
                'categoryOrProductId' => 1,
                'priority' => 10,
                'rules' => [
                    'type' => 'low_price_percent',
                    'amount' => 2,
                    'rate' => 20
                ]
            ],
            [
                'discountId' => 'BUY_2_LOW_PRICE_ADDITIVE_20',
                'type' => 'category',
                'categoryOrProductId' => 1,
                'priority' => 9,
                'rules' => [
                    'type' => 'additive',
                    'amount' => 2,
                    'rate' => 20
                ]
            ]
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
