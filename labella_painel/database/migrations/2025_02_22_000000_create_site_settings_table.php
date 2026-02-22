<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // Inserir registro padrão
        DB::table('site_settings')->insert([
            'settings' => json_encode([
                'contact' => [
                    'instagram' => '@labella',
                    'instagram_url' => 'https://instagram.com/labella',
                    'email' => 'contato@labella.com.br',
                    'phone' => '(11) 99999-9999',
                    'whatsapp' => '5511999999999',
                    'address' => 'São Paulo, SP - Brasil',
                ],
                'social' => [
                    'facebook' => 'https://facebook.com/labella',
                    'instagram' => 'https://instagram.com/labella',
                    'pinterest' => '',
                ],
                'payment_methods' => [
                    ['value' => 'pix', 'label' => 'PIX'],
                    ['value' => 'cartao', 'label' => 'Cartão de crédito'],
                    ['value' => 'boleto', 'label' => 'Boleto'],
                    ['value' => 'transferencia', 'label' => 'Transferência bancária'],
                ],
                'payment_icons' => [
                    ['src' => 'images/icons/icon-pay-01.png', 'alt' => 'Visa'],
                    ['src' => 'images/icons/icon-pay-02.png', 'alt' => 'Mastercard'],
                    ['src' => 'images/icons/icon-pay-03.png', 'alt' => 'Amex'],
                    ['src' => 'images/icons/icon-pay-04.png', 'alt' => 'PayPal'],
                    ['src' => 'images/icons/icon-pay-05.png', 'alt' => 'PIX'],
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
