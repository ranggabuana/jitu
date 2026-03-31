<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up orphaned activity logs (user_id yang tidak ada di users table)
        DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->whereNotNull('activity_logs.user_id')
            ->update(['activity_logs.user_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
