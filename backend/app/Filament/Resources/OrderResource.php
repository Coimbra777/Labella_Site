<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\OrderStatusService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $modelLabel = 'Pedido';

    protected static ?string $pluralModelLabel = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Cliente')
                    ->schema([
                        Forms\Components\Placeholder::make('source')
                            ->label('Origem')
                            ->content(fn (?Order $record): string => $record?->user_id ? 'Pedido associado a usuário interno' : 'Solicitação recebida pelo site')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('order_number')
                            ->label('Número do Pedido')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Endereço de Entrega')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Endereço')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('shipping_city')
                            ->label('Cidade')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_state')
                            ->label('Estado')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_zip')
                            ->label('CEP')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('shipping_country')
                            ->label('País')
                            ->default('BR')
                            ->maxLength(2),
                    ])->columns(4),

                Forms\Components\Section::make('Valores')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal estimado')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Frete')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $subtotal = (float) $get('subtotal');
                                $shipping = (float) $get('shipping_cost');
                                $discount = (float) $get('discount');
                                $set('total', $subtotal + $shipping - $discount);
                            }),

                        Forms\Components\TextInput::make('discount')
                            ->label('Desconto')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $subtotal = (float) $get('subtotal');
                                $shipping = (float) $get('shipping_cost');
                                $discount = (float) $get('discount');
                                $set('total', $subtotal + $shipping - $discount);
                            }),

                        Forms\Components\TextInput::make('total')
                            ->label('Total de referência')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(4),

                Forms\Components\Section::make('Itens solicitados')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('Produto')
                                    ->disabled(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantidade')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Preço unitário')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->disabled(),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->disabled(),
                                Forms\Components\TextInput::make('size')
                                    ->label('Tamanho')
                                    ->disabled(),
                                Forms\Components\TextInput::make('color')
                                    ->label('Cor')
                                    ->disabled(),
                            ])
                            ->columnSpanFull()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->collapsed(false)
                            ->hiddenOn('create'),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status do Pedido')
                            ->helperText('Use "Em atendimento" quando a solicitação for confirmada e o estoque precisar ser reservado.')
                            ->options(fn (?Order $record): array => app(OrderStatusService::class)->statusOptionsFor($record))
                            ->required()
                            ->default('pending'),

                        Forms\Components\Select::make('payment_status')
                            ->label('Status do Pagamento')
                            ->options(Order::paymentStatusOptions())
                            ->required()
                            ->default('pending'),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Método de Pagamento')
                            ->helperText('Preencha somente quando o formato de pagamento for definido manualmente.')
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total ref.')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => Order::statusColor($state))
                    ->formatStateUsing(fn (string $state): string => Order::statusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pagamento')
                    ->badge()
                    ->color(fn (string $state): string => Order::paymentStatusColor($state))
                    ->formatStateUsing(fn (string $state): string => Order::paymentStatusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Order::statusOptions()),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status do Pagamento')
                    ->options(Order::paymentStatusOptions()),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Order $record): bool => $record->canBeDeleted()),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
