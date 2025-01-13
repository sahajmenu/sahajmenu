<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Traits\AdminAccess;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    use AdminAccess;

    protected static string $resource = ClientResource::class;

    public function getBreadcrumbs(): array
    {
        if ($this->hasAdminAccess()) {
            return [
                ClientResource::getUrl() => 'Clients',
                'Edit',
            ];
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
