<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\Role;
use App\Enums\Status;
use App\Filament\Common\Actions\SuspendUnsuspendAction;
use App\Filament\Common\Forms\UserForm;
use App\Models\User;
use App\Services\StatusHistoryService;
use Filament\Forms\Form;
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

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $icon = 'heroicon-m-users';

    public function form(Form $form): Form
    {
        return $form
            ->schema(UserForm::make($form, ['role']));
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
                    ->slideOver()
                    ->infolist([
                        Section::make('Status History')
                        ->schema([
                            RepeatableEntry::make('status')
                                ->label('')
                                ->getStateUsing(fn ($record) => $record->status->sortByDesc('id'))
                                ->schema([
                                    TextEntry::make('status')
                                        ->badge()
                                        ->color(fn (Status $state): string => $state->color())
                                        ->formatStateUsing(fn (Status $state): string => $state->display()),
                                    TextEntry::make('created_at')
                                        ->label('Created Date')
                                        ->since()
                                        ->dateTimeTooltip(),
                                    TextEntry::make('reason')
                                        ->limit(50)
                                        ->tooltip(function (TextEntry $entry): ?string {
                                            $state = $entry->getState();
                                            if (strlen($state) <= $entry->getCharacterLimit()) {
                                                return null;
                                            }
                                            return $state;
                                        }),
                                    TextEntry::make('user.name')
                                        ->label('Actioned By')
                                        ->default('System')
                                ])->columns(2)->contained(true)->grid(2)
                        ])
                    ]),
                    ...SuspendUnsuspendAction::make()
                ]),
            ])->modifyQueryUsing(fn (Builder $query) => $query->filterByUserRole());
    }
}
