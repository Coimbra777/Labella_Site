<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPedidos = Order::query()->withoutTrashed()->count();
        $faturamento = Order::query()
            ->withoutTrashed()
            ->where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');
        $pedidosMes = Order::query()
            ->withoutTrashed()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return [
            Stat::make('Total de Pedidos', $totalPedidos)
                ->description('Todos os pedidos')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Faturamento Total', 'R$ ' . number_format($faturamento, 2, ',', '.'))
                ->description('Somente pedidos pagos')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Pedidos este mês', $pedidosMes)
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
