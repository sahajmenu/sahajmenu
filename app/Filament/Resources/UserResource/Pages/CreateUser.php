<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Actions\CreateStatusHistory;
use App\Enums\Role;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = Role::ADMIN;

        return $data;
    }

    protected function afterCreate(): void
    {
        resolve(CreateStatusHistory::class)->handle(
            record: $this->record,
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
