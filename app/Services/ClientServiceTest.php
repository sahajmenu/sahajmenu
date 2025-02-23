<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function createDirectoryForClientMenuImages(): void
    {
        Storage::fake('menus');

        $client = Client::factory()->createQuietly();

        resolve(ClientService::class)->createDirectoryForClientMenuImages($client);

        $this->assertDirectoryExists(
            Storage::disk('public')->path('menus/'. $client->id)
        );
    }
}
