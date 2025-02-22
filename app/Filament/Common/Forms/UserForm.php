<?php

declare(strict_types=1);

namespace App\Filament\Common\Forms;

use App\Enums\Role;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class UserForm
{
    public static function make(Form $form, array $fields = []): array
    {
        $schema = [
            TextInput::make('name')
                ->required()
                ->string()
                ->maxLength(255),
            TextInput::make('email')
                ->required()
                ->email()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->password()
                ->required()
                ->string()
                ->maxLength(255)
                ->revealable()
                ->suffixAction(
                    Action::make('generateRandomPassword')
                        ->icon('heroicon-m-key')
                        ->action(function (Set $set, $state): void {
                            $set('password', Str::random(10));
                        })
                )
                ->visibleOn('create'),
        ];

        if (in_array('role', $fields)) {
            $schema[] = Select::make('role')
                ->options(
                    Role::getClientRoleOptions(auth()->user()->role),
                )
                ->searchable()
                ->required()
                ->in(Role::getClientRoleOptions(auth()->user()->role)->keys())
                ->live();
        }
        return $schema;
    }
}
