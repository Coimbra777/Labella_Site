<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['settings'];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Retorna o registro único de configurações (singleton).
     */
    public static function getSettings(): array
    {
        $record = self::first();

        return $record?->settings ?? self::defaultSettings();
    }

    /**
     * Valores padrão quando não há registro.
     */
    public static function defaultSettings(): array
    {
        return [
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
        ];
    }
}
