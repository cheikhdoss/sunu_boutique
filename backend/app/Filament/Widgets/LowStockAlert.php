<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockAlert extends BaseWidget
{
    protected static ?string $heading = '⚠️ Alertes Stock Faible';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->size(40),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Produit')
                    ->searchable()
                    ->limit(30)
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state > 0 && $state <= 5 => 'warning',
                        $state > 5 && $state <= 10 => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(function (int $state): string {
                        if ($state <= 0) {
                            return 'Rupture';
                        }
                        return $state . ' unités';
                    }),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF'),
            ])
            ->defaultSort('quantity', 'asc')
            ->paginated([5, 10, 25])
            ->poll('60s')
            ->emptyStateHeading('Aucun produit en stock faible')
            ->emptyStateDescription('Tous vos produits ont un stock suffisant.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function getTableQuery(): Builder
    {
        return Product::query()
            ->where('is_visible', true)
            ->where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc');
    }

    public static function canView(): bool
    {
        // Afficher seulement s'il y a des produits en stock faible
        return Product::where('is_visible', true)
            ->where('quantity', '<=', 10)
            ->exists();
    }
}