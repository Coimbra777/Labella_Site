<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewOrderNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public $tries = 3;

    /** @var int */
    public $timeout = 90;

    public function __construct(
        public int $orderId
    ) {}

    public function handle(OrderNotificationService $notificationService): void
    {
        $order = Order::with('items.product')->find($this->orderId);

        if (!$order) {
            return;
        }

        $notificationService->notifyNewOrder($order);
    }
}
