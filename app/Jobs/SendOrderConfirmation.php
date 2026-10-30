<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public $backoff = [10, 60];

    /**
     * The order to confirm.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order->load(['customer', 'items.product']);

        Mail::to($order->customer->email)->send(new OrderConfirmationMail($order));

        Log::info('Order confirmation sent.', [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
        ]);
    }

    /**
     * Handle a job failure after all retries are exhausted.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        Log::error('Order confirmation failed.', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
