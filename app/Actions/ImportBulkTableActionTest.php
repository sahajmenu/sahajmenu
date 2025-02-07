<?php

namespace App\Actions;

use App\Models\Client;
use App\Models\Table;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportBulkTableActionTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function bulkTableIfMaxIsNull(): void
    {
        $client = Client::factory()->createQuietly();
        resolve(ImportBulkTableAction::class)->handle(5, $client);
        $this->assertEquals(5, $client->tables->count());
    }

    #[Test]
    public function bulkTableIfMaxisNotNull(): void
    {
        $client = Client::factory()->createQuietly();
        for ($i = 1; $i <= 3; $i++) {
            Table::factory()->create([
                'client_id' => $client->id,
                'number' => $i,
            ]);
        }

        resolve(ImportBulkTableAction::class)->handle(5, $client);
        $this->assertEquals(8, $client->tables->count());
    }
}
