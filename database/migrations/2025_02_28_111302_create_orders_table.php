<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained();
            $table->foreignIdFor(Table::class)->constrained();
            $table->string('status');
            $table->foreignIdFor(User::class, 'actioned_by')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
