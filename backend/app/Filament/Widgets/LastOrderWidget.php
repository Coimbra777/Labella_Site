<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LastOrderWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Última solicitação';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->withoutTrashed()->latest()->limit(1)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Número')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total ref.')
                    ->money('BRL'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::statusOptions()[$state] ?? $state),
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->paginated(false)
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record]));
    }
}
