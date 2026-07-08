<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add this line to delete the table if it already exists:
        Schema::dropIfExists('agent_conversations');

        Schema::create('agent_conversations', function (Blueprint $table) {
            $table->string('id')->primary();
            // ... the rest of your columns ...
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropColumn('meter_event_name');
        });
    }
};
