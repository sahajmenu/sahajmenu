<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Actions\DownloadStatementAction;
use App\Models\ClientPayment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('amount'),
                TextColumn::make('note'),
                TextColumn::make('user.name')
                    ->label('Actioned By'),
                TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip()

            ])
            ->actions([
                Action::make('Download')
                    ->action(fn (ClientPayment $record, DownloadStatementAction $download) => $download->handle($record))
                    ->hidden(fn (ClientPayment $record) => !$record->statement)
            ])
            ->filters([
                //
            ])->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('created_at'));
    }
}
