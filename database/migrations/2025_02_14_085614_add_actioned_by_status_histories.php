<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('status_histories', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'actioned_by')->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('status_histories', function (Blueprint $table) {
            $table->dropColumn('actioned_by');
        });
    }
};
