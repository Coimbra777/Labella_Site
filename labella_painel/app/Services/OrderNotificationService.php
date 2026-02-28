<?php

namespace App\Services;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    public function notifyNewOrder(Order $order): void
    {
        $settings = SiteSetting::getSettings();
        $admin = $settings['admin'] ?? [];

        $adminEmail = $admin['email'] ?? null;
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new NewOrderNotification($order));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $whatsapp = $admin['whatsapp'] ?? null;
        $callmebotKey = $admin['callmebot_apikey'] ?? null;
        if ($whatsapp && $callmebotKey) {
            $this->sendWhatsAppNotification($order, $whatsapp, $callmebotKey);
        }
    }

    private function sendWhatsAppNotification(Order $order, string $phone, string $apikey): void
    {
        $message = "🛒 *Novo pedido #{$order->order_number}*\n\n";
        $message .= "Cliente: {$order->customer_name}\n";
        $message .= "Total: R$ " . number_format($order->total, 2, ',', '.') . "\n\n";
        $message .= "Acesse o painel para gerenciar.";

        $url = 'https://api.callmebot.com/whatsapp.php?' . http_build_query([
            'phone' => $phone,
            'text' => $message,
            'apikey' => $apikey,
        ]);

        try {
            Http::timeout(5)->get($url);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
