<?php

namespace Webkul\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesReferenceDataSeeder extends Seeder
{
    /**
     * Sales codes present in the source list.
     *
     * @var string[]
     */
    const SALES_CODES = ['002', '003', '004', '005', '006', '008'];

    /**
     * Territories as [code, name, sales code].
     *
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    const TERRITORIES = [
        ['01', 'เชียงใหม่ 1', '008'],
        ['02', 'เชียงใหม่-ฝาง', '008'],
        ['03', 'เชียงใหม่-หางดง', '008'],
        ['04', 'เชียงใหม่-รอบเมือง', '008'],
        ['05', 'เชียงราย-เมือง', '003'],
        ['06', 'เชียงราย-พะเยา', '005'],
        ['07', 'ลำพูน', '005'],
        ['08', 'ลำปาง', '005'],
        ['09', 'แพร่-น่าน', '004'],
        ['10', 'แพร่-พิษณุโลก', '005'],
        ['11', 'อุบล-พิบูล', '002'],
        ['12', 'อุบล-อำนาจ', '004'],
        ['13', 'อุบล-ศรีสะเกษ', '003'],
        ['14', 'โคราช-บุรีรัมย์', '003'],
        ['15', 'อุดร-แหม่ม 1', '006'],
        ['16', 'อุดร-น้ำโสม', '002'],
        ['17', 'อุดร-บึงกาฬ', '006'],
        ['18', 'อุดร-เซกา', '004'],
        ['19', 'อุดร-เลย', '006'],
        ['20', 'ชัยภูมิ', '002'],
        ['21', 'นครพนม', '003'],
        ['22', 'สกลนคร 1', '002'],
        ['23', 'สกลนคร 2', '003'],
        ['24', 'มุกดาหาร', '004'],
        ['25', 'ร้อยเอ็ด-รจนา', '006'],
        ['26', 'ประทาย-ยโสธร', '005'],
        ['27', 'ขอนแก่น-ชุมแพ', '006'],
        ['28', 'กาฬสินธุ์', '006'],
        ['29', 'แม่สอด 1', '003'],
        ['30', 'แม่สอด 2', '003'],
        ['31', 'เพชรบูรณ์', '005'],
        ['32', 'กทม.', '006'],
        ['33', 'BigC-DC', '004'],
        ['34', 'ตี๋น้อย-ผัดไท', '003'],
        ['35', 'กำแพง-สุโขทัย', '006'],
        ['36', 'ฟาร์มเฟรซ-พัทยา', '002'],
        ['37', 'ฟาร์มเฟรซ-สระบุรี+โคราช', '005'],
        ['38', 'ตี๋น้อยปราจีน+บ่อบัว+นครนายก', '002'],
        ['39', 'กลุ่มบริษัทและห้าง', '004'],
        ['40', 'หน้าร้านเชียงใหม่', '008'],
    ];

    /**
     * Seed the sales codes and territories.
     *
     * Rows are matched on their code, so running this again refreshes names
     * and mappings without creating duplicates.
     */
    public function run(): void
    {
        $now = now();

        foreach (self::SALES_CODES as $code) {
            DB::table('sales_codes')->updateOrInsert(
                ['code' => $code],
                ['status' => 1, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        $codeIds = DB::table('sales_codes')->pluck('id', 'code');

        foreach (self::TERRITORIES as [$code, $name, $salesCode]) {
            DB::table('sales_territories')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $name,
                    'sales_code_id' => $codeIds[$salesCode] ?? null,
                    'status' => 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
