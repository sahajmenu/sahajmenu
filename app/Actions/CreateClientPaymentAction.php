<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Plan;
use App\Models\Client;
use App\Models\ClientPayment;
use Illuminate\Support\Facades\Auth;

readonly class CreateClientPaymentAction
{
    public function __construct(private CreateStatusHistory $statusHistoryService) {}

    public function handle(Client $client, array $data): void
    {
        $client->update([
            'plan' => Plan::PAID,
            'expires_at' => now()->addMonths((int) $data['month']),
        ]);

        ClientPayment::create([
            'client_id' => $client->id,
            'type' => $data['type'],
            'note' => $data['note'],
            'amount' => $data['amount'],
            'statement' => $data['statement'],
            'actioned_by' => Auth::user()->id,
        ]);

        $this->statusHistoryService->handle(record: $client, reason: 'Payment Completed');
    }
}
