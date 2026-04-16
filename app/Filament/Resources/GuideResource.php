<?php

namespace App\Filament\Resources;

use App\Enums\GuideExperienceStatusEnum;
use App\Exports\GuidesExport;
use App\Filament\Resources\GuideExperienceResource;
use App\Filament\Resources\GuideResource\Pages;
use App\Models\Guide;
use App\Models\User;
use App\Notifications\MailStripeConnectURLForGuide;
use App\Services\StripeService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class GuideResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Guides';
    protected static ?string $navigationGroup = 'Utilisateurs';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'guide';
    protected static ?string $pluralModelLabel = 'guides';
    protected static ?string $slug = 'guides';
    protected static ?string $globalSearchResultTitle = 'Guide';

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Email'     => $record->email,
            'Téléphone' => $record->phone_number,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone_number'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            FormSection::make('Identité')->schema([
                TextInput::make('name')
                    ->label('Prénom / Nom')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignorable: fn ($record) => $record)
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->label('Téléphone')
                    ->required()
                    ->maxLength(30),
                FileUpload::make('profile_path')
                    ->label('Photo de profil')
                    ->image()
                    ->disk('s3')
                    ->directory('guides_profiles')
                    ->visibility('public')
                    ->imagePreviewHeight('80')
                    ->nullable(),
            ])->columns(2),

            FormSection::make('Adresse')->schema([
                TextInput::make('address_search')
                    ->label('Rechercher une adresse')
                    ->placeholder('Tapez une adresse pour auto-complétion...')
                    ->dehydrated(false)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'x-data' => "addressAutocomplete({rue:'rue', ville:'ville', codePostal:'code_postal', pays:null})",
                    ]),
                TextInput::make('rue')
                    ->label('Rue')
                    ->nullable()
                    ->maxLength(255),
                TextInput::make('ville')
                    ->label('Ville')
                    ->nullable()
                    ->maxLength(100),
                TextInput::make('code_postal')
                    ->label('Code postal')
                    ->nullable()
                    ->maxLength(20),
            ])->columns(3),

            FormSection::make('Profil guide')->schema([
                Textarea::make('about_me')
                    ->label('Mot sur le guide (FR)')
                    ->helperText('Traduit automatiquement en anglais à l\'enregistrement.')
                    ->rows(4)
                    ->columnSpanFull()
                    ->nullable(),
            ])->columns(2),

            FormSection::make('Statut du compte')->schema([
                Select::make('guide_type')
                    ->label('Statut')
                    ->options([
                        'local' => 'Particulier (sans SIREN)',
                        'pro'   => 'Professionnel (avec SIREN)',
                    ])
                    ->default('local')
                    ->required()
                    ->live(),
                Toggle::make('is_tva_applicable')
                    ->label('Assujetti à la TVA')
                    ->default(false)
                    ->visible(fn (Get $get) => $get('guide_type') === 'pro'),
                TextInput::make('siren_number')
                    ->label('Numéro SIREN')
                    ->nullable()
                    ->visible(fn (Get $get) => $get('guide_type') === 'pro'),
                TextInput::make('name_of_company')
                    ->label('Nom de la société')
                    ->nullable()
                    ->visible(fn (Get $get) => $get('guide_type') === 'pro'),
                Toggle::make('is_verified_account')
                    ->label('Compte actif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Profil')->schema([
                Grid::make(3)->schema([
                    ImageEntry::make('profile_path')
                        ->label('Photo')
                        ->disk('s3')
                        ->circular()
                        ->height(80)
                        ->columnSpanFull(),
                    TextEntry::make('name')->label('Nom'),
                    TextEntry::make('email')->label('Email')->copyable(),
                    TextEntry::make('phone_number')->label('Téléphone'),
                    TextEntry::make('siren_number')->label('SIREN')->placeholder('—'),
                    TextEntry::make('name_of_company')->label('Société')->placeholder('—'),
                    TextEntry::make('is_tva_applicable')->label('Assujetti TVA')
                        ->state(fn ($record) => $record->is_tva_applicable ? 'Oui' : 'Non'),
                    TextEntry::make('ville')->label('Ville')->placeholder('—'),
                    TextEntry::make('rue')->label('Rue')->placeholder('—'),
                    TextEntry::make('code_postal')->label('Code postal')->placeholder('—'),
                ]),
            ]),

            Section::make('Statut du compte')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('guide_type_label')
                        ->label('Type')
                        ->state(fn ($record) => optional($record->Guide->first())->pro_local === 'pro' ? 'Professionnel' : 'Local')
                        ->badge()
                        ->color(fn ($state) => $state === 'Professionnel' ? 'info' : 'gray'),
                    TextEntry::make('is_verified_account')->label('Compte vérifié')
                        ->state(fn ($record) => $record->is_verified_account ? 'Vérifié' : 'Non vérifié')
                        ->badge()
                        ->color(fn ($state) => $state === 'Vérifié' ? 'success' : 'danger'),
                    TextEntry::make('account_status')
                        ->label('Statut')
                        ->state(fn ($record) => $record->experiences()->where('status', 'en ligne')->exists() ? 'Actif' : 'Inactif')
                        ->badge()
                        ->color(fn ($state) => $state === 'Actif' ? 'success' : 'warning'),
                    TextEntry::make('created_at')->label('Inscrit le')->date('d/m/Y'),
                    TextEntry::make('experiences_count')
                        ->label('Activités')
                        ->state(fn ($record) => $record->experiences()->count()),
                    TextEntry::make('revenue_total')
                        ->label('CA total généré')
                        ->state(fn ($record) => number_format(
                            $record->experiences()
                                ->join('reservations', 'guide_experiences.id', '=', 'reservations.experience_id')
                                ->where('reservations.status', 'Acceptée')
                                ->where('reservations.is_payed', true)
                                ->sum('reservations.total_price') / 100,
                            2, ',', ' '
                        ) . ' €'),
                ]),
            ]),

            Section::make('Stripe')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('stripe_status')
                        ->label('Statut Stripe Connect')
                        ->badge()
                        ->state(fn ($record) => optional($record->Guide->first())->stripe_connect_form_status)
                        ->color(fn (?string $state) => match ($state) {
                            'sent'    => 'success',
                            'pending' => 'warning',
                            default   => 'gray',
                        })
                        ->placeholder('Non configuré'),
                    TextEntry::make('stripe_account_id')
                        ->label('Stripe Account ID')
                        ->state(fn ($record) => optional($record->Guide->first())->stripe_account_id)
                        ->copyable()
                        ->placeholder('—'),
                    TextEntry::make('stripe_connect_form_url')
                        ->label('Lien Stripe Connect')
                        ->state(fn ($record) => optional($record->Guide->first())->stripe_connect_form_url)
                        ->copyable()
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),
            ]),

            Section::make('À propos')->schema([
                TextEntry::make('about_me')->label('Bio')->placeholder('—')->columnSpanFull(),
            ]),

            Section::make('Expériences')->schema([
                RepeatableEntry::make('experiences')
                    ->label('')
                    ->schema([
                        ImageEntry::make('photoprincipal.photo_url')
                            ->label('')
                            ->height(56)
                            ->width(56)
                            ->extraImgAttributes(['class' => 'rounded-lg object-cover'])
                            ->defaultImageUrl(fn () => asset('img/logo-ct-dark.png')),
                        TextEntry::make('title')
                            ->label('Titre')
                            ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                            ->url(fn ($record) => GuideExperienceResource::getUrl('view', ['record' => $record->id]))
                            ->color('primary'),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                GuideExperienceStatusEnum::ONLINE->value      => 'success',
                                GuideExperienceStatusEnum::VERFICATION->value => 'warning',
                                GuideExperienceStatusEnum::REFUSED->value,
                                GuideExperienceStatusEnum::TO_BE_COMPLETED->value => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('ville')
                            ->label('Ville')
                            ->icon('heroicon-m-map-pin')
                            ->placeholder('—'),
                        TextEntry::make('prix_par_voyageur')
                            ->label('Prix / pers.')
                            ->money('EUR')
                            ->placeholder('—'),
                    ])
                    ->columns(5)
                    ->placeholder('Aucune expérience pour ce guide')
                    ->getStateUsing(fn ($record) => $record->experiences()
                        ->whereNotIn('status', [GuideExperienceStatusEnum::DELETED->value])
                        ->with('photoprincipal')
                        ->latest()
                        ->get()
                    ),
            ]),

            Section::make('Documents')->schema([
                Grid::make(3)->schema([
                    ImageEntry::make('piece_d_identite')
                        ->label("Pièce d'identité (recto)")
                        ->disk('s3')
                        ->height(200)
                        ->placeholder('—'),
                    ImageEntry::make('piece_d_identite_verso')
                        ->label("Pièce d'identité (verso)")
                        ->disk('s3')
                        ->height(200)
                        ->placeholder('—'),
                    ImageEntry::make('KBIS_file')
                        ->label('KBIS')
                        ->disk('s3')
                        ->height(200)
                        ->placeholder('—'),
                ]),

                RepeatableEntry::make('otherDocuments')
                    ->label('Autres documents')
                    ->schema([
                        TextEntry::make('document_title')->label('Titre')->placeholder('—'),
                        ImageEntry::make('document_path')
                            ->label('Document')
                            ->disk('s3')
                            ->height(200),
                    ])
                    ->columns(2)
                    ->placeholder('Aucun document'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::whereHas('Guide')->with('Guide')
            )
            ->columns([
                ImageColumn::make('profile_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('img/logo-ct-dark.png'))
                    ->width(48)
                    ->height(48),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('phone_number')
                    ->label('Téléphone')
                    ->sortable(),

                TextColumn::make('guide_type')
                    ->label('Type')
                    ->badge()
                    ->state(fn (User $record) => optional($record->Guide->first())->pro_local === 'pro' ? 'Pro' : 'Local')
                    ->color(fn (?string $state) => $state === 'Pro' ? 'info' : 'gray'),

                TextColumn::make('is_tva_applicable')
                    ->label('TVA')
                    ->badge()
                    ->state(fn (User $record) => $record->is_tva_applicable ? 'Oui' : 'Non')
                    ->color(fn (?string $state) => $state === 'Oui' ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('account_status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn (User $record) => $record->is_verified_account ? 'Actif' : 'Inactif')
                    ->color(fn (?string $state) => $state === 'Actif' ? 'success' : 'danger'),

                TextColumn::make('stripe_status')
                    ->label('Stripe')
                    ->badge()
                    ->state(fn (User $record) => optional($record->Guide->first())->stripe_connect_form_status)
                    ->color(fn (?string $state): string => match ($state) {
                        'sent'    => 'success',
                        'pending' => 'warning',
                        default   => 'gray',
                    })
                    ->placeholder('Non configuré'),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('account_status')
                    ->label('Statut')
                    ->options([
                        'actif'   => 'Actif (vérifié)',
                        'inactif' => 'Inactif (non vérifié)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'actif') {
                            $query->where('is_verified_account', true);
                        } elseif ($data['value'] === 'inactif') {
                            $query->where('is_verified_account', false);
                        }
                    }),

                SelectFilter::make('guide_type')
                    ->label('Type de guide')
                    ->options(['local' => 'Local', 'pro' => 'Professionnel'])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('Guide', fn ($q) => $q->where('pro_local', $data['value']));
                        }
                    }),

                Filter::make('date_inscription')
                    ->label('Date d\'inscription')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Du'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                ViewAction::make()->label('Détail'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => Excel::download(new GuidesExport(), 'guides.xlsx')),

                Action::make('ajouter')
                    ->label('Ajouter un guide')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn () => static::getUrl('create')),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGuides::route('/'),
            'create' => Pages\CreateGuide::route('/create'),
            'view'   => Pages\ViewGuide::route('/{record}'),
            'edit'   => Pages\EditGuide::route('/{record}/edit'),
        ];
    }
}
