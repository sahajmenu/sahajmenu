<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Filament\Resources\TableResource\RelationManagers\TablesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\UsersRelationManager;
use App\Models\Client;
use App\Traits\AdminAccess;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ClientResource extends Resource
{
    use AdminAccess;

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
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->lazy()
                            ->required(),
                        TextInput::make('subdomain')->required()->unique(ignoreRecord: true),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        TextInput::make('address')->string(),
                        TextInput::make('phone')->numeric(),
                    ])->columns(2),

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
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            TablesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::hasAdminAccess();
    }
}
