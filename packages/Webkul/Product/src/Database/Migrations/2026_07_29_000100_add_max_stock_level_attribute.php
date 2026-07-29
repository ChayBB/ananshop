<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $attributeId = DB::table('attributes')->insertGetId([
            'code'               => 'max_stock_level',
            'admin_name'         => 'Max Stock Level',
            'type'               => 'text',
            'validation'         => 'numeric',
            'position'           => 2,
            'is_required'        => 0,
            'is_unique'          => 0,
            'is_filterable'      => 0,
            'is_comparable'      => 0,
            'is_configurable'    => 0,
            'is_user_defined'    => 0,
            'is_visible_on_front' => 0,
            'value_per_locale'   => 0,
            'value_per_channel'  => 0,
            'enable_wysiwyg'     => 0,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        $inventoriesGroupId = DB::table('attribute_groups')->where('code', 'inventories')->value('id');

        if ($inventoriesGroupId) {
            DB::table('attribute_group_mappings')->insert([
                'attribute_id'       => $attributeId,
                'attribute_group_id' => $inventoriesGroupId,
                'position'           => 2,
            ]);
        }

        DB::table('attribute_translations')->insert([
            'attribute_id' => $attributeId,
            'locale'       => 'th',
            'name'         => 'ระดับสต๊อกสูงสุด',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $attributeId = DB::table('attributes')->where('code', 'max_stock_level')->value('id');

        if ($attributeId) {
            DB::table('attribute_group_mappings')->where('attribute_id', $attributeId)->delete();

            DB::table('attribute_translations')->where('attribute_id', $attributeId)->delete();

            DB::table('product_attribute_values')->where('attribute_id', $attributeId)->delete();

            DB::table('attributes')->where('id', $attributeId)->delete();
        }
    }
};
