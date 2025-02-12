<?php

namespace App\Filament\Common\BulkActions;

use App\Actions\SuspendUserAction;
use App\Actions\UnsuspendUserAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class SuspendUnsuspendBulkAction
{
    public function handle(): array
    {
        return [
            $this->suspendBulkAction(),
            $this->unsuspendBulkAction(),
        ];
    }

    private function suspendBulkAction(): BulkAction
    {
        return BulkAction::make('Suspend')
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (Collection $records) => $records->each(function ($record) {
                resolve(SuspendUserAction::class)->handle($record);
            }))
            ->deselectRecordsAfterCompletion();
    }

    private function unsuspendBulkAction(): BulkAction
    {
        return BulkAction::make('Unsuspend')
            ->icon('heroicon-o-check')
            ->color('success')
            ->requiresConfirmation()
            ->action(fn (Collection $records) => $records->each(function ($record) {
                resolve(UnsuspendUserAction::class)->handle($record);
            }))
            ->deselectRecordsAfterCompletion();
    }
}
