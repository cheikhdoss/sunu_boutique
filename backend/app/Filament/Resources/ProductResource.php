<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $modelLabel = 'Produit';

    protected static ?string $pluralModelLabel = 'Produits';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Détails du produit')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->unique(Product::class, 'slug', ignoreRecord: true),

                    Forms\Components\MarkdownEditor::make('description')
                        ->label('Description')
                        ->columnSpan('full')
                        ->fileAttachmentsDirectory('products'),
                ])->columns(2),

                Forms\Components\Section::make('Images')->schema([
                    Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable(),
                ]),
            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Prix & Stock')->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Prix')
                        ->numeric()
                        ->prefix('XOF')
                        ->required(),

                    Forms\Components\TextInput::make('sku')
                        ->label('SKU (Unité de gestion de stock)')
                        ->unique(Product::class, 'sku', ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantité')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])->columns(1),

                Forms\Components\Section::make('Associations')->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Catégorie')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->required(),
                ]),

                Forms\Components\Section::make('Statut')->schema([
                    Forms\Components\Toggle::make('is_visible')
                        ->label('Visible')
                        ->helperText('Ce produit sera visible sur tous les canaux de vente.')
                        ->default(true),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('En vedette')
                        ->helperText('Ce produit sera mis en avant sur la page d\'accueil.'),

                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options([
                            'physical' => 'Physique',
                            'digital' => 'Numérique',
                        ])->default('physical'),
                ]),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')->label('Image'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->sortable()
                    ->money('XOF'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantité')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibilité')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visibilité')
                    ->boolean()
                    ->trueLabel('Produits visibles')
                    ->falseLabel('Produits cachés')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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

