<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Enums\Status;
use App\Filament\Common\Actions\SuspendUnsuspendAction;
use App\Filament\Common\Forms\UserForm;
use App\Filament\Resources\ClientResource\RelationManagers\StatusHistoryRelationManager;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use App\Traits\AdminAccess;
use App\Traits\HasActiveIcon;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    use AdminAccess;
    use HasActiveIcon;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema(UserForm::make($form))
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
                TextColumn::make('latestStatus.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Status $state): string => $state->color())
                    ->formatStateUsing(fn (Status $state): string => $state->display()),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(Role::getUserRoleOptions(auth()->user()->role)),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    ...SuspendUnsuspendAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StatusHistoryRelationManager::class,
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
        return parent::getEloquentQuery()->filterByUserRole();
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
