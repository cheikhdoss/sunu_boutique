<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopProductsTable extends BaseWidget
{
    protected static ?string $heading = 'Produits les Plus Vendus';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->size(50),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Quantité Vendue')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenus Générés')
                    ->money('XOF')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stock Restant')
                    ->numeric()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->defaultSort('total_sold', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        // Utiliser une sous-requête pour éviter les problèmes PostgreSQL avec HAVING et les alias
        $subQuery = DB::table('order_items')
            ->select([
                'product_id',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            ])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', '=', 'paid')
            ->groupBy('product_id')
            ->havingRaw('SUM(order_items.quantity) > 0');

        return Product::query()
            ->select([
                'products.*',
                'sales_data.total_sold',
                'sales_data.total_revenue'
            ])
            ->joinSub($subQuery, 'sales_data', function ($join) {
                $join->on('products.id', '=', 'sales_data.product_id');
            })
            ->with(['category'])
            ->orderBy('sales_data.total_sold', 'desc');
    }
}