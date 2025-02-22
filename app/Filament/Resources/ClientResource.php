<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Filament\Resources\ClientResource\Pages\ViewClient;
use App\Models\Client;
use App\Traits\AdminAccess;
use App\Traits\HasActiveIcon;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    use AdminAccess;
    use HasActiveIcon;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client Information')
                    ->icon('heroicon-m-information-circle')
                    ->description('You can update or edit the details here.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255),
                        TextInput::make('subdomain')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('address')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                    ])->columns(2),

                Section::make('Subscription')
                    ->icon('heroicon-m-banknotes')
                    ->description('You can add number of days for subscription. 14 is the default value')
                    ->schema([
                        TextInput::make('days')
                            ->label('Number of Days')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(360)
                    ])->visibleOn('create'),

                Section::make('Client Logo')
                    ->icon('heroicon-m-photo')
                    ->schema([
                        FileUpload::make('logo')
                            ->disk('logos')
                            ->columnSpanFull(),
                    ]),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
                TextColumn::make('latestStatus.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Status $state): string => $state->color())
                    ->formatStateUsing(fn (Status $state): string => $state->display()),
                TextColumn::make('expires_at')
                    ->date()
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make()
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::hasAdminAccess();
    }
}
