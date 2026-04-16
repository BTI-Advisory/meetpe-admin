<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Administrateurs';
    protected static ?string $navigationGroup = 'Utilisateurs';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'administrateur';
    protected static ?string $pluralModelLabel = 'administrateurs';
    protected static ?string $slug = 'administrateurs';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('userRoles', fn (Builder $q) => $q->where('role_id', 3));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            FormSection::make('Informations')->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignorable: fn ($record) => $record)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->required(fn (string $operation) => $operation === 'create')
                    ->minLength(8)
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'Laisser vide pour ne pas modifier.' : null),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Retirer')
                    ->modalHeading('Retirer les droits administrateur ?')
                    ->modalDescription('Le compte utilisateur sera supprimé.')
                    ->successNotificationTitle('Administrateur supprimé'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit'   => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}
