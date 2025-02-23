<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('client_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained();
            $table->string('type');
            $table->string('statement')->nullable();
            $table->string('note')->nullable();
            $table->integer('amount');
            $table->foreignIdFor(User::class, 'actioned_by')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_payments');
    }
};
