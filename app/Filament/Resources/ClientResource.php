<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Filament\Resources\TableResource\RelationManagers\TablesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\UsersRelationManager;
use App\Models\Client;
use App\Traits\AdminAccess;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ClientResource extends Resource
{
    use AdminAccess;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client Information')
                    ->description("You can update or edit the details here.")
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->getOwnClient();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::hasAdminAccess();
    }
}
