<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Blank birth dates used to be saved as the zero date, which then failed
     * the date validation on every later edit and locked those customers out
     * of being saved. The code no longer writes it; this clears the rows that
     * already carry it.
     */
    public function up(): void
    {
        DB::table('customers')
            ->whereRaw("CAST(date_of_birth AS CHAR) = '0000-00-00'")
            ->update(['date_of_birth' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * Deliberately empty: the zero date carried no information, so writing it
     * back would only restore the bug.
     */
    public function down(): void
    {
        //
    }
};
