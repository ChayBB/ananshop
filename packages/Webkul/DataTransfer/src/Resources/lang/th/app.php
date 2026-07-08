<?php

return [
    'importers' => [
        'customers' => [
            'title' => 'ลูกค้า',

            'validation' => [
                'errors' => [
                    'duplicate-email' => 'อีเมล : \'%s\' พบมากกว่าหนึ่งครั้งในไฟล์นำเข้า',
                    'duplicate-phone' => 'โทรศัพท์ : \'%s\' พบมากกว่าหนึ่งครั้งในไฟล์นำเข้า',
                    'email-not-found' => 'ไม่พบอีเมล : \'%s\' ในระบบ',
                    'invalid-customer-group' => 'กลุ่มลูกค้าไม่ถูกต้องหรือไม่ได้รับการสนับสนุน',
                ],
            ],
        ],

        'products' => [
            'title' => 'สินค้า',

            'validation' => [
                'errors' => [
                    'duplicate-url-key' => 'URL key: \'%s\' ถูกสร้างแล้วสำหรับรายการที่มี SKU: \'%s\'',
                    'invalid-attribute-family' => 'ค่าไม่ถูกต้องสำหรับคอลัมน์ family ของคุณลักษณะ (ไม่มี family ของคุณลักษณะนี้หรือไม่?)',
                    'invalid-type' => 'ประเภทสินค้าไม่ถูกต้องหรือไม่ได้รับการสนับสนุน',
                    'sku-not-found' => 'ไม่พบสินค้าที่มี SKU ที่ระบุ',
                    'super-attribute-not-found' => 'ไม่พบซูเปอร์คุณลักษณะที่มีรหัส: \'%s\' หรือไม่ได้เป็นของ family คุณลักษณะ: \'%s\'',
                ],
            ],
        ],

        'tax-rates' => [
            'title' => 'อัตราภาษี',

            'validation' => [
                'errors' => [
                    'duplicate-identifier' => 'ตัวระบุ : \'%s\' พบมากกว่าหนึ่งครั้งในไฟล์นำเข้า',
                    'identifier-not-found' => 'ไม่พบตัวระบุ : \'%s\' ในระบบ',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => 'คอลัมน์หมายเลข "%s" มีหัวข้อว่างเปล่า',
            'column-name-invalid' => 'ชื่อคอลัมน์ไม่ถูกต้อง: "%s"',
            'column-not-found' => 'ไม่พบคอลัมน์ที่ต้องการ: %s',
            'column-numbers' => 'จำนวนคอลัมน์ไม่ตรงกับจำนวนแถวในส่วนหัว',
            'invalid-attribute' => 'ส่วนหัวมีคุณลักษณะที่ไม่ถูกต้อง: "%s"',
            'system' => 'เกิดข้อผิดพลาดของระบบที่ไม่คาดคิด',
            'wrong-quotes' => 'ใช้เครื่องหมายคำพูดแบบโค้งแทนเครื่องหมายคำพูดแบบตรง',
        ],
    ],
];
