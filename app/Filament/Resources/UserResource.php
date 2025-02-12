<?php

namespace App\Filament\Resources;

use App\Actions\SuspendUserAction;
use App\Actions\UnsuspendUserAction;
use App\Enums\Role;
use App\Enums\Status;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use App\Traits\AdminAccess;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserResource extends Resource
{
    use AdminAccess;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->required()
                            ->password()
                            ->revealable()
                            ->visibleOn('create'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('role')
                    ->formatStateUsing(fn (Role $state): string => $state->getLabel()),
                TextColumn::make('client.name')
                    ->default('Vendor'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Status $state): string => $state->color())
                    ->formatStateUsing(fn (Status $state): string => $state->display()),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(Role::getUserRoleOptions(auth()->user()->role)),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('Suspend')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(function (User $record) {
                            resolve(SuspendUserAction::class)->handle($record);
                        }))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('Unsuspend')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(function (User $record) {
                            resolve(UnsuspendUserAction::class)->handle($record);
                        }))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->teamFilterRoles();
    }

    public static function canAccess(): bool
    {
        return self::hasAdminAccess();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isSuperAdmin();
    }
}
