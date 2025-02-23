<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ClientPayment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadStatementAction
{
    public function handle(ClientPayment $clientPayment): ?BinaryFileResponse
    {
        if (Storage::disk('public')->exists($clientPayment->statement)) {
            $path = Storage::disk('public')->path($clientPayment->statement);
            return response()->download($path);
        }
        return null;
    }
}
