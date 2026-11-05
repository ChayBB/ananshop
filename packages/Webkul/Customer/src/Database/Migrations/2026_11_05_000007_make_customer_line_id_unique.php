<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One LINE account belongs to one customer. Blank values are normalised to
     * null first, since MySQL allows repeated nulls in a unique index but not
     * repeated empty strings.
     */
    public function up(): void
    {
        DB::table('customers')->where('line_id', '')->update(['line_id' => null]);

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('line_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['line_id']);
        });
    }
};
