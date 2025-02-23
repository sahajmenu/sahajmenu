<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'clientPayments';

    protected static ?string $icon = 'heroicon-o-credit-card';

    protected static ?string $title = 'Payments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('note'),
                TextColumn::make('user.name')
                    ->label('Actioned By')
            ])
            ->filters([
                //
            ]);
    }
}
