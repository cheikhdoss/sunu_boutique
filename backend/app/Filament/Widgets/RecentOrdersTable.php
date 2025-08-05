<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrdersTable extends BaseWidget
{
    protected static ?string $heading = 'Commandes Récentes';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Commande')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Client')
                    ->getStateUsing(function (Order $record): string {
                        return $record->user ? $record->user->name : ($record->customer_name ?? 'N/A');
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Email')
                    ->getStateUsing(function (Order $record): string {
                        return $record->user ? $record->user->email : ($record->customer_email ?? 'N/A');
                    })
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending', 'en_attente' => 'warning',
                        'processing', 'en_cours' => 'info',
                        'shipped', 'expediee' => 'primary',
                        'delivered', 'livree' => 'success',
                        'cancelled', 'annulee' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (string $state): string {
                        return match($state) {
                            'pending', 'en_attente' => 'En attente',
                            'processing', 'en_cours' => 'En cours',
                            'shipped', 'expediee' => 'Expédiée',
                            'delivered', 'livree' => 'Livrée',
                            'cancelled', 'annulee' => 'Annulée',
                            default => ucfirst($state),
                        };
                    }),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Paiement')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending', 'en_attente' => 'warning',
                        'paid', 'paye' => 'success',
                        'failed', 'echoue' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (string $state): string {
                        return match($state) {
                            'pending', 'en_attente' => 'En attente',
                            'paid', 'paye' => 'Payé',
                            'failed', 'echoue' => 'Échoué',
                            default => ucfirst($state),
                        };
                    }),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function (?string $state): string {
                        return match($state) {
                            'paydunya' => 'PayDunya',
                            'cash_on_delivery' => 'Paiement à la livraison',
                            'bank_transfer' => 'Virement bancaire',
                            default => $state ?? 'Non défini',
                        };
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()
            ->with(['user'])
            ->latest()
            ->limit(50);
    }
}