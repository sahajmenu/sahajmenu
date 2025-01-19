<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Table;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TableServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function bulk_table_if_maxis_null(): void
    {
        $client = Client::factory()->createQuietly();
        resolve(TableService::class)->importBulkTable(5, $client);
        $this->assertEquals(5, $client->tables->count());
    }

    /**
     * @test
     */
    public function bulk_table_if_max_is_not_null(): void
    {
        $client = Client::factory()->createQuietly();
        for ($i = 1; $i <= 3; $i++) {
            Table::factory()->create([
                'client_id' => $client->id,
                'number' => $i,
            ]);
        }

        resolve(TableService::class)->importBulkTable(5, $client);
        $this->assertEquals(8, $client->tables->count());
    }
}
