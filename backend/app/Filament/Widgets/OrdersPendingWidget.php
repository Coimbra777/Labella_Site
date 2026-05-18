<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrdersPendingWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Solicitações pendentes';

    protected static ?string $description = 'Solicitações recebidas pelo site e ainda não tratadas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->withoutTrashed()
                    ->where('status', 'pending')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Número')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Telefone')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Subtotal ref.')
                    ->money('BRL'),
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->paginated(false)
            ->recordUrl(fn (Order $record): string => \App\Filament\Resources\OrderResource::getUrl('edit', ['record' => $record]));
    }
}
