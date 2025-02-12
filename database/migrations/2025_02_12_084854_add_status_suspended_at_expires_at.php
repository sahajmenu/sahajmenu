<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Status;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('status')->default(Status::ACTIVE->value);
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('expires_at')->default(now()->addDay(14));
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('suspended_at');
            $table->dropColumn('expires_at');
        });
    }
};
