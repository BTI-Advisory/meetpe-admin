<?php

namespace App\Filament\Resources;

use App\Enums\GuideExperienceStatusEnum;
use App\Filament\Resources\GuideExperienceResource\Pages;
use App\Models\GuideExperience;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Responses;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use App\Filament\Infolist\Components\PhotoCarouselEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Notifications\MakeExperienceNonComplete;
use App\Notifications\YourExperienceIsValid;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class GuideExperienceResource extends Resource
{
    protected static ?string $model = GuideExperience::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Expériences';
    protected static ?string $navigationGroup = 'Expériences';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'expérience';
    protected static ?string $pluralModelLabel = 'expériences';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'ville', 'user.name'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Guide'  => $record->user?->name,
            'Ville'  => $record->ville ?? '—',
            'Statut' => $record->status,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Photos ───────────────────────────────────────────────────────
            FormSection::make('Photos')->schema([
                FileUpload::make('photo_principal')
                    ->label('Photo principale (obligatoire)')
                    ->image()
                    ->disk('s3')
                    ->directory('experience_photos')
                    ->visibility('public')
                    ->required()
                    ->imagePreviewHeight('160')
                    ->columnSpanFull(),
                FileUpload::make('photo_image_0')
                    ->label('Photo 2')
                    ->image()
                    ->disk('s3')
                    ->directory('experience_photos')
                    ->visibility('public')
                    ->deletable()
                    ->imagePreviewHeight('120'),
                FileUpload::make('photo_image_1')
                    ->label('Photo 3')
                    ->image()
                    ->disk('s3')
                    ->directory('experience_photos')
                    ->visibility('public')
                    ->deletable()
                    ->imagePreviewHeight('120'),
                FileUpload::make('photo_image_2')
                    ->label('Photo 4')
                    ->image()
                    ->disk('s3')
                    ->directory('experience_photos')
                    ->visibility('public')
                    ->deletable()
                    ->imagePreviewHeight('120'),
                FileUpload::make('photo_image_3')
                    ->label('Photo 5')
                    ->image()
                    ->disk('s3')
                    ->directory('experience_photos')
                    ->visibility('public')
                    ->deletable()
                    ->imagePreviewHeight('120'),
                FileUpload::make('photo_image_4')
                    ->label('Photo 6')
                    ->image()
                    ->disk('s3')
                    ->directory('experience_photos')
                    ->visibility('public')
                    ->deletable()
                    ->imagePreviewHeight('120'),
            ])->columns(3),

            // ── Informations principales ──────────────────────────────────────
            FormSection::make('Informations')->schema([
                TextInput::make('title')
                    ->label('Titre (FR)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('duree')
                    ->label('Durée')
                    ->placeholder('ex : 2h, 1 journée')
                    ->maxLength(50),
                Textarea::make('description')
                    ->label('Description (FR)')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('prix_par_voyageur')
                    ->label('Prix / voyageur')
                    ->numeric()
                    ->suffix('cts'),
                TextInput::make('nombre_des_voyageur')
                    ->label('Nb. voyageurs max')
                    ->numeric(),
            ])->columns(2),

            // ── Groupe privé ─────────────────────────────────────────────────
            FormSection::make('Groupe privé')->schema([
                Toggle::make('support_group_prive')
                    ->label('Réservation de groupe privé disponible')
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),
                TextInput::make('price_group_prive')
                    ->label('Prix par groupe')
                    ->numeric()
                    ->suffix('cts')
                    ->visible(fn (Get $get) => (bool) $get('support_group_prive')),
                TextInput::make('max_group_size')
                    ->label('Nb. max de personnes par groupe')
                    ->numeric()
                    ->visible(fn (Get $get) => (bool) $get('support_group_prive')),
            ])->columns(2),

            // ── Adresse ──────────────────────────────────────────────────────
            FormSection::make('Adresse')->schema([
                TextInput::make('address_search')
                    ->label('Rechercher une adresse')
                    ->placeholder('Tapez une adresse pour auto-complétion...')
                    ->dehydrated(false)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'x-data' => "addressAutocomplete({rue:'addresse', ville:'ville', codePostal:'code_postale', pays:'country', lat:'lat', lng:'lang'})",
                    ]),
                TextInput::make('addresse')->label('Adresse'),
                TextInput::make('ville')->label('Ville'),
                TextInput::make('code_postale')->label('Code postal'),
                TextInput::make('country')->label('Pays'),
                Hidden::make('lat'),
                Hidden::make('lang'),
            ])->columns(2),

            // ── Catégories & Langues ─────────────────────────────────────────
            FormSection::make('Catégories & Langues')->schema([
                Select::make('categorie')
                    ->label('Catégories')
                    ->multiple()
                    ->searchable()
                    ->options(function () {
                        $question = Question::where('question_key', 'voyageur_experiences')->first();
                        if (! $question) return [];
                        return QuestionChoice::where('question_id', $question->id)
                            ->pluck('choice_txt', 'id')
                            ->toArray();
                    })
                    ->placeholder('Sélectionner des catégories'),

                Select::make('languages')
                    ->label('Langues')
                    ->multiple()
                    ->searchable()
                    ->options(function () {
                        $question = Question::where('question_key', 'languages_fr')->first();
                        if (! $question) return [];
                        return QuestionChoice::where('question_id', $question->id)
                            ->pluck('choice_txt', 'id')
                            ->toArray();
                    })
                    ->placeholder('Sélectionner des langues'),
            ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Expérience')->schema([
                Grid::make(2)->schema([
                    PhotoCarouselEntry::make('photos_carousel')
                        ->label('Photos')
                        ->columnSpanFull(),
                    TextEntry::make('title')->label('Titre'),
                    TextEntry::make('status')->label('Statut')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            GuideExperienceStatusEnum::ONLINE->value      => 'success',
                            GuideExperienceStatusEnum::VERFICATION->value => 'warning',
                            GuideExperienceStatusEnum::REFUSED->value,
                            GuideExperienceStatusEnum::TO_BE_COMPLETED->value => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make('categorie')
                        ->label('Catégories')
                        ->state(fn ($record) => $record->categorie
                            ? Responses::getChoicesOf(array_filter(explode(',', $record->categorie)))->pluck('choix')->join(', ')
                            : '—'
                        )
                        ->placeholder('—'),
                    TextEntry::make('languages')
                        ->label('Langues')
                        ->state(fn ($record) => $record->languages
                            ? Responses::getChoicesOf(array_filter(explode(',', $record->languages)))->pluck('choix')->join(', ')
                            : '—'
                        )
                        ->placeholder('—'),
                    TextEntry::make('prix_par_voyageur')->label('Prix / voyageur')->money('EUR'),
                    TextEntry::make('description')->label('Description')->columnSpanFull(),
                    TextEntry::make('ville')->label('Ville'),
                    TextEntry::make('country')->label('Pays'),
                    TextEntry::make('addresse')->label('Adresse'),
                    TextEntry::make('code_postale')->label('Code postal'),
                    TextEntry::make('duree')->label('Durée'),
                    TextEntry::make('nombre_des_voyageur')->label('Nb. voyageurs max'),
                ]),
            ]),

            Section::make('Guide')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('user.name')->label('Nom'),
                    TextEntry::make('user.email')->label('Email')->copyable(),
                    TextEntry::make('user.phone_number')->label('Téléphone'),
                ]),
            ]),

            Section::make('Options')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('support_group_prive')->label('Groupe privé')
                        ->state(fn ($record) => $record->support_group_prive ? 'Oui' : 'Non'),
                    TextEntry::make('price_group_prive')->label('Prix groupe privé')->money('EUR')
                        ->visible(fn ($record) => (bool) $record->support_group_prive),
                    TextEntry::make('max_group_size')->label('Groupe max')
                        ->visible(fn ($record) => (bool) $record->support_group_prive),
                    TextEntry::make('discount_kids_between_2_and_12')->label('Réduction enfants')
                        ->state(fn ($record) => $record->discount_kids_between_2_and_12 ? 'Oui (−30%)' : 'Non'),
                ]),
            ]),

            Section::make('Note de refus / correction')
                ->schema([
                    TextEntry::make('raison')->label('Raison')->placeholder('—')->columnSpanFull(),
                ])
                ->visible(fn ($record) => !empty($record->raison)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                GuideExperience::with(['photoprincipal', 'user'])
                    ->whereNotIn('status', [GuideExperienceStatusEnum::DELETED->value])
            )
            ->columns([
                ImageColumn::make('photoprincipal.photo_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('img/logo-ct-dark.png'))
                    ->width(48)
                    ->height(48),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('user.name')
                    ->label('Guide')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('categorie')
                    ->label('Catégories')
                    ->state(fn ($record) => $record->categorie
                        ? Responses::getChoicesOf(array_filter(explode(',', $record->categorie)))->pluck('choix')->join(', ')
                        : '—'
                    )
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('languages')
                    ->label('Langues')
                    ->state(fn ($record) => $record->languages
                        ? Responses::getChoicesOf(array_filter(explode(',', $record->languages)))->pluck('choix')->join(', ')
                        : '—'
                    )
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('prix_par_voyageur')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        GuideExperienceStatusEnum::ONLINE->value          => 'success',
                        GuideExperienceStatusEnum::VERFICATION->value     => 'warning',
                        GuideExperienceStatusEnum::TO_BE_COMPLETED->value => 'danger',
                        GuideExperienceStatusEnum::REFUSED->value         => 'danger',
                        GuideExperienceStatusEnum::DOCUMENT->value        => 'info',
                        GuideExperienceStatusEnum::OFFLINE->value         => 'gray',
                        GuideExperienceStatusEnum::ARCHIVED->value        => 'gray',
                        default                                           => 'gray',
                    }),

                TextColumn::make('raison')
                    ->label('Raison')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(GuideExperienceStatusEnum::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => ucfirst($e->value)])
                        ->toArray()
                    ),

                SelectFilter::make('categorie')
                    ->label('Catégorie')
                    ->searchable()
                    ->options(function () {
                        $question = Question::where('question_key', 'voyageur_experiences')->first();
                        if (! $question) return [];
                        return QuestionChoice::where('question_id', $question->id)
                            ->pluck('choice_txt', 'id')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $responseIds = Responses::where('choice_id', (int) $data['value'])->pluck('id');
                        if ($responseIds->isEmpty()) return;
                        $query->where(function ($q) use ($responseIds) {
                            foreach ($responseIds as $rId) {
                                $q->orWhereRaw('FIND_IN_SET(?, categorie)', [$rId]);
                            }
                        });
                    }),

                Filter::make('ville')
                    ->label('Ville')
                    ->form([
                        TextInput::make('ville')->label('Ville'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['ville'])) {
                            $query->where('ville', 'like', '%' . $data['ville'] . '%');
                        }
                    }),

                Filter::make('prix')
                    ->label('Prix')
                    ->form([
                        TextInput::make('prix_min')->label('Prix min (€)')->numeric(),
                        TextInput::make('prix_max')->label('Prix max (€)')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query
                            ->when($data['prix_min'] ?? null, fn ($q) => $q->where('prix_par_voyageur', '>=', (int)$data['prix_min'] * 100))
                            ->when($data['prix_max'] ?? null, fn ($q) => $q->where('prix_par_voyageur', '<=', (int)$data['prix_max'] * 100));
                    }),
            ])
            ->actions([
                ViewAction::make()->label('Détail'),
                EditAction::make()->label('Modifier'),
                ActionGroup::make([
                    Action::make('accepter')
                        ->label('Mettre en ligne')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Mettre cette expérience en ligne ?')
                        ->visible(fn ($record) => $record->status !== GuideExperienceStatusEnum::ONLINE->value
                            && $record->status !== GuideExperienceStatusEnum::DELETED->value)
                        ->action(function ($record) {
                            $record->status = GuideExperienceStatusEnum::ONLINE->value;
                            $record->save();
                            $user = User::find($record->user_id);
                            if ($user) {
                                App::setLocale($user->device_language ?? 'fr');
                                $user->notify(new YourExperienceIsValid(
                                    $user->fcm_token,
                                    $record->getTitleForLocale($user->device_language ?? 'fr')
                                ));
                            }
                            Notification::make()->title('Expérience mise en ligne')->success()->send();
                        }),

                    Action::make('refuser')
                        ->label('Refuser')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Refuser cette expérience ?')
                        ->visible(fn ($record) => $record->status !== GuideExperienceStatusEnum::REFUSED->value
                            && $record->status !== GuideExperienceStatusEnum::DELETED->value)
                        ->action(function ($record) {
                            $record->status = GuideExperienceStatusEnum::REFUSED->value;
                            $record->save();
                            Notification::make()->title('Expérience refusée')->danger()->send();
                        }),

                    Action::make('a_completer')
                        ->label('À compléter')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->color('warning')
                        ->form([
                            Textarea::make('raison')
                                ->label('Raison à communiquer au guide')
                                ->required()
                                ->rows(4),
                        ])
                        ->visible(fn ($record) => $record->status !== GuideExperienceStatusEnum::DELETED->value)
                        ->action(function ($record, array $data) {
                            $record->raison = $data['raison'];
                            $record->status = GuideExperienceStatusEnum::TO_BE_COMPLETED->value;
                            $record->save();
                            $user = User::find($record->user_id);
                            if ($user) {
                                App::setLocale($user->device_language ?? 'fr');
                                $user->notify(new MakeExperienceNonComplete(
                                    $data['raison'],
                                    $record->getTitleForLocale($user->device_language ?? 'fr'),
                                    $user->fcm_token
                                ));
                            }
                            Notification::make()->title('Notification envoyée au guide')->warning()->send();
                        }),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ExperiencesExport(''),
                            'experiences.xlsx'
                        );
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuideExperiences::route('/'),
            'view'  => Pages\ViewGuideExperience::route('/{record}'),
            'edit'  => Pages\EditGuideExperience::route('/{record}/edit'),
        ];
    }
}
