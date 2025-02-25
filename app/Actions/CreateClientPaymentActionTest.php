<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentType;
use App\Enums\Plan;
use App\Enums\Status;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateClientPaymentActionTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function adminCanRenewClientSubscription(): void
    {
        $admin = User::factory()->asAdmin()->createQuietly();

        $client = Client::factory()->withStatusHistory(Status::EXPIRED)->createQuietly();

        $this->actingAs($admin);

        $data = [
            'month' => 2,
            'type' => PaymentType::CASH,
            'note' => 'Payment Tested',
            'amount' => 5000,
            'statement' => null,
        ];

        $this->assertDatabaseEmpty('client_payments');
        $this->assertEquals(Plan::FREE, $client->plan);

        resolve(CreateClientPaymentAction::class)->handle($client, $data);

        $this->assertEquals(Plan::PAID, $client->plan);
        $this->assertNotEmpty($client->clientPayments);
        $this->assertEquals($data['type'], $client->latestClientPayment->type);
        $this->assertEquals($data['note'], $client->latestClientPayment->note);
        $this->assertEquals($data['amount'], $client->latestClientPayment->amount);
        $this->assertEquals($client->expires_at->toDateString(), now()->addMonths($data['month'])->toDateString());
    }
}
