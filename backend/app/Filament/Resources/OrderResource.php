<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Commande';

    protected static ?string $pluralModelLabel = 'Commandes';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Détails de la commande')->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\Select::make('user_id')
                        ->label('Client')
                        ->relationship('user', 'name')
                        ->disabled(),

                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),

                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Date de création')
                        ->disabled(),
                ])->columns(2),

                Forms\Components\Section::make('Informations de livraison')->schema([
                    Forms\Components\TextInput::make('shipping_full_name')
                        ->label('Full Name')
                        ->disabled(),
                    Forms\Components\TextInput::make('shipping_address')
                        ->label('Address')
                        ->disabled(),
                ])->columns(2),
            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Détails du paiement')->schema([
                    Forms\Components\TextInput::make('total')
                        ->numeric()
                        ->prefix('XOF')
                        ->disabled(),

                    Forms\Components\Select::make('payment_method')
                        ->label('Moyen de paiement')
                        ->options([
                            'online' => 'Online',
                            'cash_on_delivery' => 'Cash on Delivery',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('payment_status')
                        ->label('Statut du paiement')
                        ->options([
                            'pending' => 'En attente',
                            'paid' => 'Payé',
                            'failed' => 'Échoué',
                        ])
                        ->required(),
                ]),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exporter les commandes')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (array $data, Collection $records) {
                        $service = new \App\Services\OrderExportService();
                        $filename = $service->exportOrders($records);
                        return response()->download(storage_path('app/public/exports/' . $filename));
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Exporter les commandes')
                    ->modalSubheading('Voulez-vous exporter les commandes sélectionnées ?')
                    ->modalButton('Oui, exporter')
                    ->color('success')
                    ->visible(fn () => auth()->user()->can('export_orders')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Numéro de commande')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money('XOF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Action::make('confirm_payment')
                    ->label('Confirmer le paiement')
                    ->action(function (Order $record) {
                        $record->payment_status = 'paid';
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer le paiement')
                    ->modalSubheading('Voulez-vous vraiment marquer cette commande comme payée ?')
                    ->modalButton('Oui, confirmer')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Order $record): bool => $record->payment_method === 'cash_on_delivery' && $record->payment_status === 'pending'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

