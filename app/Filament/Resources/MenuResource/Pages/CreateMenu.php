<?php

declare(strict_types=1);

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    public function injectClientIdBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user->clientAccess()) {
            $data['client_id'] = $user->client->id;
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->injectClientIdBeforeCreate($data);
    }
}
