<?php

namespace App\Filament\Resources\StatusHistoryResource\RelationManagers;

use App\Enums\Status;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class StatusRelationManager extends RelationManager
{
    protected static string $relationship = 'status';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Status $state): string => $state->color())
                    ->formatStateUsing(fn (Status $state): string => $state->display()),
                TextColumn::make('reason')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('user.name')
                    ->label('Actioned By')
                    ->default('System')
            ])
            ->filters([
                //
            ])->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('created_at'));
    }
}
