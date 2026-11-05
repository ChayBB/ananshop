<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * LINE is a common contact channel for Thai customers. It is optional and
     * not unique: several customers may share a shop account.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('line_id')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('line_id');
        });
    }
};
