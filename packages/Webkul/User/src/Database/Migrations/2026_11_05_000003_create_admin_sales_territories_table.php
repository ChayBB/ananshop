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
     * A salesperson covers several territories, so the single
     * admins.sales_territory_id column becomes a pivot table. Any value
     * already set is carried over before the column is dropped.
     */
    public function up(): void
    {
        Schema::create('admin_sales_territories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->unsigned();
            $table->integer('sales_territory_id')->unsigned();

            $table->unique(['admin_id', 'sales_territory_id'], 'admin_sales_territory_unique');

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('sales_territory_id')->references('id')->on('sales_territories')->onDelete('cascade');
        });

        if (Schema::hasColumn('admins', 'sales_territory_id')) {
            $existing = DB::table('admins')
                ->whereNotNull('sales_territory_id')
                ->get(['id', 'sales_territory_id']);

            foreach ($existing as $admin) {
                DB::table('admin_sales_territories')->insert([
                    'admin_id' => $admin->id,
                    'sales_territory_id' => $admin->sales_territory_id,
                ]);
            }

            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['sales_territory_id']);
                $table->dropColumn('sales_territory_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->integer('sales_territory_id')->unsigned()->nullable()->after('sales_code_id');

            $table->foreign('sales_territory_id')->references('id')->on('sales_territories')->onDelete('set null');
        });

        $pivots = DB::table('admin_sales_territories')->get();

        foreach ($pivots->groupBy('admin_id') as $adminId => $rows) {
            DB::table('admins')
                ->where('id', $adminId)
                ->update(['sales_territory_id' => $rows->first()->sales_territory_id]);
        }

        Schema::dropIfExists('admin_sales_territories');
    }
};
