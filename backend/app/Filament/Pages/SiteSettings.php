<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

/**
 * @property Form $form
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configurações do Site';

    protected static ?string $title = 'Configurações do Site';

    protected static ?string $navigationGroup = 'Configurações';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::getSettings();
        $this->form->fill([
            'contact' => $settings['contact'] ?? [],
            'social' => $settings['social'] ?? [],
            'admin' => $settings['admin'] ?? [],
            'cities' => $settings['cities'] ?? [],
            'payment_methods' => $settings['payment_methods'] ?? [],
            'payment_icons' => $settings['payment_icons'] ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configurações')
                    ->tabs([
                        Tabs\Tab::make('Contato')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Informações de Contato')
                                    ->description('Dados exibidos no site e no rodapé')
                                    ->schema([
                                        TextInput::make('contact.instagram')
                                            ->label('Instagram (usuário)')
                                            ->placeholder('@labella')
                                            ->maxLength(100),
                                        TextInput::make('contact.instagram_url')
                                            ->label('URL do Instagram')
                                            ->url()
                                            ->placeholder('https://instagram.com/labella')
                                            ->maxLength(255),
                                        TextInput::make('contact.email')
                                            ->label('E-mail')
                                            ->email()
                                            ->placeholder('contato@labella.com.br')
                                            ->maxLength(255),
                                        TextInput::make('contact.phone')
                                            ->label('Telefone (exibição)')
                                            ->placeholder('(11) 99999-9999')
                                            ->maxLength(50),
                                        TextInput::make('contact.whatsapp')
                                            ->label('WhatsApp (número para link)')
                                            ->helperText('Formato: 55 + DDD + número, sem espaços. Ex: 5511999999999')
                                            ->placeholder('5511999999999')
                                            ->maxLength(20),
                                        TextInput::make('contact.address')
                                            ->label('Endereço')
                                            ->placeholder('São Paulo, SP - Brasil')
                                            ->maxLength(255),
                                    ])->columns(2),
                            ]),
                        Tabs\Tab::make('Admin')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Section::make('Dados do Admin')
                                    ->description('E-mail e WhatsApp para receber notificações de novos pedidos')
                                    ->schema([
                                        TextInput::make('admin.email')
                                            ->label('E-mail do Admin')
                                            ->email()
                                            ->placeholder('admin@labella.com.br')
                                            ->helperText('Receberá notificação por e-mail quando houver novo pedido')
                                            ->maxLength(255),
                                        TextInput::make('admin.whatsapp')
                                            ->label('WhatsApp do Admin')
                                            ->placeholder('5511999999999')
                                            ->helperText('Formato: 55 + DDD + número, sem espaços. Ex: 5511999999999')
                                            ->maxLength(20),
                                        TextInput::make('admin.callmebot_apikey')
                                            ->label('CallMeBot API Key (opcional)')
                                            ->placeholder('123456')
                                            ->helperText('Para receber notificação no WhatsApp. Obtenha em callmebot.com')
                                            ->password()
                                            ->maxLength(100),
                                    ])->columns(2),
                            ]),
                        Tabs\Tab::make('Cidades')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Cidades de Entrega')
                                    ->description('Cidades disponíveis no checkout (select)')
                                    ->schema([
                                        Repeater::make('cities')
                                            ->label('Cidades')
                                            ->schema([
                                                TextInput::make('value')
                                                    ->label('Valor (ID)')
                                                    ->required()
                                                    ->placeholder('sao-luis'),
                                                TextInput::make('label')
                                                    ->label('Rótulo')
                                                    ->required()
                                                    ->placeholder('São Luís'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Adicionar cidade'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Redes Sociais')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('Links das Redes Sociais')
                                    ->description('Deixe vazio para ocultar o ícone no site')
                                    ->schema([
                                        TextInput::make('social.facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->maxLength(255),
                                        TextInput::make('social.instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->maxLength(255),
                                        TextInput::make('social.pinterest')
                                            ->label('Pinterest')
                                            ->url()
                                            ->maxLength(255),
                                        TextInput::make('social.whatsapp')
                                            ->label('WhatsApp')
                                            ->url()
                                            ->placeholder('https://wa.me/5511999999999')
                                            ->helperText('Link completo. Ex: https://wa.me/5511999999999')
                                            ->maxLength(255),
                                    ])->columns(2),
                            ]),
                        Tabs\Tab::make('Formas de Pagamento')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Formas de Pagamento Aceitas')
                                    ->description('Opções exibidas no checkout')
                                    ->schema([
                                        Repeater::make('payment_methods')
                                            ->label('Métodos')
                                            ->schema([
                                                TextInput::make('value')
                                                    ->label('Nome do Pagamento')
                                                    ->required()
                                                    ->placeholder('pix'),
                                                TextInput::make('label')
                                                    ->label('Rótulo')
                                                    ->required()
                                                    ->placeholder('PIX'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Adicionar método'),
                                    ]),
                                Section::make('Ícones de Pagamento no Rodapé')
                                    ->description('Ícones exibidos no footer do site')
                                    ->schema([
                                        Repeater::make('payment_icons')
                                            ->label('Ícones')
                                            ->schema([
                                                TextInput::make('src')
                                                    ->label('Caminho da imagem')
                                                    ->required()
                                                    ->placeholder('images/icons/icon-pay-01.png'),
                                                TextInput::make('alt')
                                                    ->label('Texto alternativo')
                                                    ->required()
                                                    ->placeholder('Visa'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Adicionar ícone'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = SiteSetting::first();
        if (! $record) {
            $record = new SiteSetting;
        }

        $record->settings = [
            'contact' => $data['contact'] ?? [],
            'social' => $data['social'] ?? [],
            'admin' => $data['admin'] ?? [],
            'cities' => $data['cities'] ?? [],
            'payment_methods' => $data['payment_methods'] ?? [],
            'payment_icons' => $data['payment_icons'] ?? [],
        ];
        $record->save();

        Notification::make()
            ->title('Configurações salvas com sucesso!')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar configurações')
                ->submit('save'),
        ];
    }
}
