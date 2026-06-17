<?php

namespace App\Filament\Resources;

use App\Exports\GuidesExport;
use App\Filament\Resources\GuideResource\Pages;
use App\Filament\Resources\GuideResource\RelationManagers;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
                Placeholder::make('audio_actuel')
                    ->label('Bio audio actuelle')
                    ->content(fn ($record) => $record && $record->about_me_audio
                        ? new \Illuminate\Support\HtmlString(
                            '<a href="' . (str_starts_with($record->about_me_audio, 'http')
                                ? $record->about_me_audio
                                : Storage::disk('s3')->url($record->about_me_audio))
                            . '" target="_blank" class="text-primary-600 underline">Écouter l\'audio</a>'
                        )
                        : 'Aucun audio enregistré'
                    )
                    ->columnSpanFull(),
                Toggle::make('delete_audio')
                    ->label('Supprimer l\'audio')
                    ->default(false)
                    ->visible(fn ($record) => $record && !empty($record->about_me_audio))
                    ->columnSpanFull(),
            ])->columns(2),

            // ── Questions / Réponses ──────────────────────────────────────────
            FormSection::make('Questions / Réponses')->schema(function () {
                return Question::where('contexts', 'like', '%guide%')
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($question) => Select::make('response_q_' . $question->id)
                        ->label($question->question_text)
                        ->multiple()
                        ->searchable()
                        ->options(
                            QuestionChoice::where('question_id', $question->id)
                                ->orderBy('order_index')
                                ->pluck('choice_txt', 'id')
                                ->toArray()
                        )
                    )->toArray();
            })->columns(2),

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

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::whereHas('Guide')->with('Guide')
                    ->addSelect([
                        'users.*',
                        'has_questionnaire' => DB::table('responses')
                            ->selectRaw('1')
                            ->where('responses.entity', 'guide')
                            ->whereColumn('responses.entity_id', DB::raw(
                                '(SELECT guide_id FROM guides WHERE guides.user_id = users.id LIMIT 1)'
                            ))
                            ->limit(1),
                    ])
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

                TextColumn::make('questionnaire_alert')
                    ->label('')
                    ->badge()
                    ->state(fn (User $record) => $record->has_questionnaire ? null : '⚠ Questionnaire incomplet')
                    ->color('warning')
                    ->placeholder(''),

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
                        'sent'    => 'gray',
                        'pending' => 'warning',
                        'success' => 'success',
                        default   => 'gray',
                    })
                    ->placeholder('Non configuré'),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('sans_questionnaire')
                    ->label('Questionnaire non rempli')
                    ->query(fn (Builder $query) => $query->whereRaw(
                        'NOT EXISTS (SELECT 1 FROM responses WHERE responses.entity = ? AND responses.entity_id = (SELECT guide_id FROM guides WHERE guides.user_id = users.id LIMIT 1))',
                        ['guide']
                    ))
                    ->toggle(),

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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ExperiencesRelationManager::class,
            RelationManagers\TrackingsRelationManager::class,
        ];
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
