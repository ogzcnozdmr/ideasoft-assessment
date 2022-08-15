<?php

namespace App\Observers;

use App\Jobs\Customer\RevenueUpdate;
use App\Models\Order;
use App\Models\Product;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function created(Order $order)
    {
        // sipariş oluştuğunda ilgili siparişe ait ürünlerin stoklarının düşülmesi ve fiyatların hesaplanması.
        $totalPrice = 0;
        $items = [];
        foreach ($order->items as $item) {
            $productModel = Product::whereId($item['id'])->first();
            $productModel->update([
                'stock' => (int)$productModel->stock - $item['quantity']
            ]);

            $unitPrice = $productModel->price * $item['quantity'];
            $items[] = [
                'productId' => $item['id'],
                'categoryId' => $productModel->category,
                'quantity' => $item['quantity'],
                'unitPrice' => $productModel->price,
                'total' => $unitPrice
            ];
            $totalPrice += $unitPrice;
        }
        $order->items = $items;
        $order->total = $totalPrice;
        $order->save();

        dispatch(new RevenueUpdate($order->customerInfo()->first(), $totalPrice)); // müşteri harcanan bakiyeyi güncelle
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function deleted(Order $order)
    {
        // sipariş oluştuğunda ilgili siparişe ait ürünlerin stoklarının geri arttırılması
        $totalPrice = 0;
        foreach ($order->items as $item) {
            $productModel = Product::whereId($item['productId'])->first();
            $productModel->update([
                'stock' => (int)$productModel->stock + $item['quantity']
            ]);
            $totalPrice += $item['total'];
        }
        dispatch(new RevenueUpdate($order->customerInfo()->first(), $totalPrice, false)); // müşteri harcanan bakiyeyi güncelle
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
