<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Client;
use App\Models\Table;

class ImportBulkTableAction
{
    public function handle(int $total, Client $client): void
    {
        $max = Table::where('client_id', $client->id)->max('number');

        if ($max) {
            $start = $max + 1;
            $end = $max + $total;
        } else {
            $start = 1;
            $end = $total;
        }

        for ($i = $start; $i <= $end; $i++) {
            Table::create([
                'number' => $i,
                'client_id' => $client->id,
            ]);
        }
    }
}
