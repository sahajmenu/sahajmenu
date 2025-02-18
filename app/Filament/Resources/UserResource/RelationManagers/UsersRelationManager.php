<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\Role;
use App\Enums\Status;
use App\Filament\Common\Actions\SuspendUnsuspendAction;
use App\Models\User;
use App\Services\StatusHistoryService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $icon = 'heroicon-m-users';

    public function form(Form $form): Form
    {
        return $form
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
                    ->suffixAction(
                        Action::make('generateRandomPassword')
                            ->icon('heroicon-m-key')
                            ->action(function (Set $set, $state): void {
                                $set('password', Str::random(10));
                            })
                    )
                    ->visibleOn('create'),
                Select::make('role')
                    ->options(
                        Role::getClientRoleOptions(auth()->user()->role),
                    )
                    ->searchable()
                    ->required()
                    ->in(Role::getClientRoleOptions(auth()->user()->role)->keys())
                    ->live()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('role')
                    ->formatStateUsing(fn (Role $state): string => $state->getLabel()),
                TextColumn::make('latestStatus.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Status $state): string => $state->color())
                    ->formatStateUsing(fn (Status $state): string => $state->display()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->after(function (User $user, StatusHistoryService $statusHistoryService): void {
                    $statusHistoryService->create(record: $user);
                })
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make()
                    ->infolist([
                        Section::make('Status History')
                        ->schema([
                            RepeatableEntry::make('status')
                                ->label('')
                                ->getStateUsing(function ($record) {
                                    return $record->status->sortByDesc('id');
                                })
                                ->schema([
                                    TextEntry::make('status')
                                        ->badge()
                                        ->color(fn (Status $state): string => $state->color())
                                        ->formatStateUsing(fn (Status $state): string => $state->display()),
                                    TextEntry::make('created_at')
                                        ->label('Created Date')
                                        ->since()
                                        ->dateTimeTooltip(),
                                    TextEntry::make('reason'),
                                    TextEntry::make('user.name')
                                        ->label('Actioned By')
                                        ->default('System')
                                ])->columns(2)->contained(true)->grid(2)
                        ])
                    ]),
                    ...resolve(SuspendUnsuspendAction::class)->handle(),
                ]),
            ])->modifyQueryUsing(fn (Builder $query) => $query->filterByUserRole());
    }
}
