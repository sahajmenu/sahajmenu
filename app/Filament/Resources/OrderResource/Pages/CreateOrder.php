<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    use HasWizard;

    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user->clientAccess()) {
            $data['client_id'] = $user->client->id;
        }
        $data['actioned_by'] = $user->id;

        return $data;
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Order Details')
                ->schema(OrderResource::getOrderDetailsFormSchema())->columns(),

            Step::make('Order Items')
                ->schema(OrderResource::getOrderItemsFormSchema()),
        ];
    }
}
