<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountRequest;
use App\Models\Discount;
use App\Models\Order;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function calculate(DiscountRequest $request)
    {
        $order = Order::find($request->orderId);

        if (!$order) {
            return new Response([
                'status' => false,
                'msg' => 'Order not found'
            ], 203);
        }


        $discounts = Discount::orderBy('priority', 'desc')->get();

        $selectedDiscount = [];
        foreach ($discounts as $discount) {
            if (!isset($selectedDiscount[$discount->type . '_' . ($discount->categoryOrProductId ?: 'any')])) {
                $selectedDiscount[$discount->type . '_' . ($discount->categoryOrProductId ?: 'any')] = $discount;
            }
        }

        $discountResponse = [
            'orderId' => $order->id,
            'discounts' => [],
            'totalDiscount' => 0,
            'discountedTotal' => 0
        ];
        $orderSubTotal = $order->total;
        $orderCategoryItems = [];
        foreach ($order->items as $product) {
            $orderCategoryItems[$product['categoryId']][] = $product;
        }
        foreach ($selectedDiscount as $key => $discount) {
            //total değere indirim varsas
            if ($discount->type === 'any') {
                if ($order->total >= $discount->rules['basketTotal']) {
                    if ($discount->rules['type'] === 'percentile') {
                        $discountAmount = ($orderSubTotal / 100) * $discount->rules['rate'];
                    }
                    if ($discount->rules['type'] === 'additive') {
                        $discountAmount = $discount->rules['rate'];
                    }
                    $orderSubTotal -= $discountAmount;

                    $discountResponse['discounts'][] = [
                        'discountReason' => $discount->discountId,
                        'discountAmount' => number_format($discountAmount, '2', '.', null),
                        'subTotal' => number_format($orderSubTotal, '2', '.', null)
                    ];
                }
            }else if ($discount->type === 'category') { //kategorilere indirim varsa
                if (isset($orderCategoryItems[$discount->categoryOrProductId])) {

                    $appliedDiscount = false;

                    switch ($discount->rules['type']) {
                        case 'per_free':
                            foreach ($orderCategoryItems[$discount->categoryOrProductId] as $product) {
                                $count = intval($product['quantity'] / $discount->rules['amount']);
                                if ($count > 0) {
                                    $discountAmount = $product['unitPrice'] * $count;
                                    $orderSubTotal -= $discountAmount;
                                    $appliedDiscount = true;
                                }
                            }
                            break;
                        case 'low_price_percent':
                            $categoryTotalAmount = 0;
                            $cheapestProduct = $orderCategoryItems[$discount->categoryOrProductId][0];

                            foreach ($orderCategoryItems[$discount->categoryOrProductId] as $product) {
                                $categoryTotalAmount += $product['quantity'];
                                if($cheapestProduct['unitPrice'] > $product['unitPrice']) {
                                    $cheapestProduct = $product;
                                }
                            }
                            //kurala uyuyorsa
                            if ($categoryTotalAmount >= $discount->rules['amount']) {
                                $discountAmount = ($cheapestProduct['total'] / 100) * $discount->rules['rate'];
                                $orderSubTotal -= $discountAmount;
                                $appliedDiscount = true;
                            }
                            break;
                    }

                    if ($appliedDiscount) {
                        $discountResponse['discounts'][] = [
                            'discountReason' => $discount->discountId,
                            'discountAmount' => number_format($discountAmount, '2', '.', null),
                            'subTotal' => number_format($orderSubTotal, '2', '.', null)
                        ];
                    }
                }
            }
        }
        $discountResponse['discountedTotal'] = number_format($orderSubTotal, '2', '.', null);
        $discountResponse['totalDiscount'] = number_format(($order->total - $orderSubTotal), '2', '.', null);
        return new Response($discountResponse);
    }
}
