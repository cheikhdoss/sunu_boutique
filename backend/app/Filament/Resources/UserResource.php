<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';
    protected static ?string $navigationGroup = 'Gestion des Utilisateurs';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Personnelles')
                    ->description('Informations de base de l\'utilisateur')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom complet')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Jean Dupont'),

                                Forms\Components\TextInput::make('email')
                                    ->label('Adresse email')
                                    ->email()
                                    ->required()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Ex: jean@example.com'),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('Ex: +221 77 123 45 67'),

                                Forms\Components\Select::make('gender')
                                    ->label('Genre')
                                    ->options([
                                        'male' => 'Homme',
                                        'female' => 'Femme',
                                        'other' => 'Autre',
                                    ])
                                    ->placeholder('Sélectionner le genre'),

                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date de naissance')
                                    ->maxDate(now()->subYears(13))
                                    ->displayFormat('d/m/Y'),

                                Forms\Components\Toggle::make('is_admin')
                                    ->label('Administrateur')
                                    ->helperText('Donner les droits d\'administration à cet utilisateur')
                                    ->default(false),
                            ]),
                    ]),

                Section::make('Avatar')
                    ->description('Photo de profil de l\'utilisateur')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Photo de profil')
                            ->image()
                            ->directory('avatars')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Formats acceptés: JPG, PNG. Taille max: 2MB'),
                    ]),

                Section::make('Sécurité')
                    ->description('Paramètres de sécurité du compte')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Mot de passe')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->placeholder('Minimum 8 caractères'),

                                Forms\Components\TextInput::make('password_confirmation')
                                    ->label('Confirmer le mot de passe')
                                    ->password()
                                    ->same('password')
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrated(false),
                            ]),

                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Laisser vide si l\'email n\'est pas vérifié'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn (): string => 'https://ui-avatars.com/api/?name=' . urlencode('User') . '&color=7F9CF5&background=EBF4FF'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone')
                    ->placeholder('Non renseigné'),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email vérifié')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Commandes')
                    ->counts('orders')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_admin')
                    ->label('Type d\'utilisateur')
                    ->options([
                        '1' => 'Administrateurs',
                        '0' => 'Clients',
                    ])
                    ->placeholder('Tous les utilisateurs'),

                Filter::make('email_verified')
                    ->label('Email vérifié')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),

                Filter::make('email_not_verified')
                    ->label('Email non vérifié')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),

                Filter::make('has_orders')
                    ->label('Avec commandes')
                    ->query(fn (Builder $query): Builder => $query->has('orders')),

                Filter::make('no_orders')
                    ->label('Sans commandes')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('orders')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Inscrit depuis'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Inscrit jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Modifier'),
                    
                    Tables\Actions\Action::make('verify_email')
                        ->label('Vérifier email')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => is_null($record->email_verified_at))
                        ->action(function ($record) {
                            $record->update(['email_verified_at' => now()]);
                            Notification::make()
                                ->title('Email vérifié')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('toggle_admin')
                        ->label(fn ($record) => $record->is_admin ? 'Retirer admin' : 'Rendre admin')
                        ->icon(fn ($record) => $record->is_admin ? 'heroicon-o-user' : 'heroicon-o-shield-check')
                        ->color(fn ($record) => $record->is_admin ? 'warning' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_admin' => !$record->is_admin]);
                            Notification::make()
                                ->title($record->is_admin ? 'Utilisateur promu administrateur' : 'Droits admin retirés')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('verify_emails')
                        ->label('Vérifier les emails')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['email_verified_at' => now()]));
                            Notification::make()
                                ->title('Emails vérifiés')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('make_admin')
                        ->label('Rendre administrateurs')
                        ->icon('heroicon-o-shield-check')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['is_admin' => true]));
                            Notification::make()
                                ->title('Utilisateurs promus administrateurs')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}