<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Table;
use Illuminate\Support\Facades\Auth;

class TableService
{
    /**
     * It generates the options for table depending on user role for order resource
     */
    public function getTableOptionsForOrder(?int $clientId): array
    {
        $user = Auth::user();
        if ($user->adminAccess()) {
            return $clientId ? Table::where('client_id', $clientId)->pluck('number', 'id')->toArray() : [];
        }

        return Table::getClientOwnTable()->pluck('number', 'id')->toArray();
    }
}
