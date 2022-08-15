<?php

namespace App\Jobs\Customer;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RevenueUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Customer Model
     * @var \App\Models\User $customer
     */
    private User $customer;

    /**
     * Order Total Price
     * @var float $totalPrice
     */
    private float $totalPrice;

    /**
     * @var bool $increase
     */
    private bool $increase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $customer, $totalPrice, $increase = true)
    {
        $this->increase = $increase;
        $this->customer = $customer;
        $this->totalPrice = $totalPrice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->increase) {
                $this->customer->update([
                    'revenue' => number_format($this->customer->revenue + $this->totalPrice, 2, '.', '')
                ]);
            } else {
                $this->customer->update([
                    'revenue' => number_format($this->customer->revenue - $this->totalPrice, 2, '.', '')
                ]);
            }
        } catch (\Throwable $exception) {
            dispatch(new self($this->customer, $this->totalPrice))->delay(10); // hata oluşması durumunda 10sn sonra tekrar dene
        }
    }
}
