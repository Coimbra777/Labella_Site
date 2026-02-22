<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $modelLabel = 'Produto';

    protected static ?string $pluralModelLabel = 'Produtos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Descrição Curta')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Preços e Estoque')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Preço')
                            ->required()
                            ->numeric()
                            ->prefix('R$')
                            ->step(0.01)
                            ->minValue(0),

                        Forms\Components\TextInput::make('compare_price')
                            ->label('Preço de Comparação')
                            ->numeric()
                            ->prefix('R$')
                            ->step(0.01)
                            ->minValue(0),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->unique(Product::class, 'sku', ignoreRecord: true),

                        Forms\Components\TextInput::make('barcode')
                            ->label('Código de Barras')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantidade')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(3),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destaque'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Imagens')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->label('Imagens')
                            ->schema([
                                Forms\Components\TextInput::make('image')
                                    ->label('URL da Imagem')
                                    ->url()
                                    ->maxLength(255),
                            ])
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Opções')
                    ->schema([
                        Forms\Components\Repeater::make('sizes')
                            ->label('Tamanhos')
                            ->schema([
                                Forms\Components\TextInput::make('size')
                                    ->label('Tamanho')
                                    ->maxLength(50),
                            ])
                            ->defaultItems(1),

                        Forms\Components\Repeater::make('colors')
                            ->label('Cores')
                            ->schema([
                                Forms\Components\TextInput::make('color')
                                    ->label('Cor')
                                    ->maxLength(50),
                            ])
                            ->defaultItems(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Imagem')
                    ->getStateUsing(function (Product $record) {
                        $images = $record->images;
                        if (is_array($images) && !empty($images)) {
                            return $images[0];
                        }
                        return null;
                    })
                    ->circular()
                    ->defaultImageUrl(url('/images/product-01.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Estoque')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destaque')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destaque'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
