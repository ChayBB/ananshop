<?php

return [
    'checkout' => [
        'cart' => [
            'integrity' => [
                'qty-missing' => 'สินค้าอย่างน้อยหนึ่งรายการควรมีจำนวนมากกว่า 1',
            ],

            'invalid-file-extension' => 'พบนามสกุลไฟล์ที่ไม่ถูกต้อง',
            'inventory-warning' => 'จำนวนที่ร้องขอไม่พร้อมใช้งาน กรุณาลองใหม่อีกครั้งในภายหลัง',
            'missing-links' => 'ลิงก์สำหรับดาวน์โหลดหายไปสำหรับสินค้านี้',
            'missing-options' => 'ตัวเลือกหายไปสำหรับสินค้านี้',
            'selected-products-simple' => 'สินค้าที่เลือกจะต้องเป็นประเภทสินค้าธรรมดา',
        ],
    ],

    'datagrid' => [
        'copy-of-slug' => 'copy-of-:value',
        'copy-of' => 'สำเนาของ :value',
        'variant-already-exist-message' => 'ตัวแปรที่มีตัวเลือกคุณลักษณะเดียวกันมีอยู่แล้ว',
    ],

    'response' => [
        'product-can-not-be-copied' => 'ไม่สามารถคัดลอกสินค้าประเภท :type ได้',
    ],

    'sort-by' => [
        'options' => [
            'cheapest-first' => 'ถูกที่สุดก่อน',
            'expensive-first' => 'แพงที่สุดก่อน',
            'from-a-z' => 'จาก A-Z',
            'from-z-a' => 'จาก Z-A',
            'latest-first' => 'ใหม่ล่าสุดก่อน',
            'oldest-first' => 'เก่าที่สุดก่อน',
        ],
    ],

    'type' => [
        'abstract' => [
            'offers' => 'ซื้อ :qty ชิ้น ในราคา :price ต่อชิ้น และประหยัด :discount',
        ],

        'bundle' => 'ชุดรวม',
        'booking' => 'การจอง',
        'configurable' => 'ปรับแต่งได้',
        'downloadable' => 'ดาวน์โหลดได้',
        'grouped' => 'กลุ่ม',
        'simple' => 'ธรรมดา',
        'virtual' => 'เสมือน',
    ],
];
