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
        $items = $order->items
            ->map(fn ($item) => "- {$item->product_name} x{$item->quantity}")
            ->implode("\n");

        $message = "🛒 *Nova solicitacao #{$order->order_number}*\n\n";
        $message .= "Cliente: {$order->customer_name}\n";
        $message .= "Telefone: {$order->customer_phone}\n";
        if ($order->customer_email) {
            $message .= "E-mail: {$order->customer_email}\n";
        }
        $message .= "Cidade: {$order->shipping_city}\n\n";
        $message .= "Itens:\n{$items}\n\n";
        $message .= "Subtotal estimado: R$ " . number_format((float) $order->subtotal, 2, ',', '.') . "\n";
        if ($order->notes) {
            $message .= "Observacoes: {$order->notes}\n";
        }
        $message .= "\n";
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
