<?php

namespace App\Filament\Common\Actions;

use App\Actions\SuspendAction;
use App\Actions\UnsuspendAction;
use App\Models\Client;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\Action;

class SuspendUnsuspendAction
{
    public function handle(): array
    {
        return [
            $this->makeAction(
                name: 'suspend',
                icon: 'heroicon-o-lock-closed',
                color: 'danger',
                action : SuspendAction::class
            ),
            $this->makeAction(
                name: 'unsuspend',
                icon: 'heroicon-o-lock-open',
                color: 'success',
                action : UnsuspendAction::class
            ),
        ];
    }

    private function makeAction(string $name, string $icon, string $color, string $action): Action
    {
        return Action::make($name)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->form([
                Textarea::make('reason')
                    ->string()
                    ->nullable()
                    ->maxLength(255)
            ])
            ->action(fn (User|Client $record, array $data) => resolve($action)->handle(
                record: $record,
                reason: $data['reason']
            ));
    }
}
