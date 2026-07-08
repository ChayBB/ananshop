<?php

return [
    'description' => 'ชำระเงินอย่างปลอดภัยด้วยบัตรเครดิต/เดบิตผ่าน Stripe',
    'title' => 'Stripe',

    'response' => [
        'cart-not-found' => 'ไม่พบตะกร้าสินค้าหรือไม่ถูกต้อง',
        'cart-processed' => 'ตะกร้าสินค้านี้ได้รับการดำเนินการแล้ว',
        'invalid-session' => 'เซสชันการชำระเงินไม่ถูกต้อง',
        'payment-cancelled' => 'การชำระเงินถูกยกเลิก',
        'payment-failed' => 'การชำระเงินล้มเหลว',
        'payment-success' => 'การชำระเงินเสร็จสิ้นเรียบร้อย',
        'provide-credentials' => 'กรุณาระบุข้อมูลรับรอง Stripe ที่ถูกต้อง',
        'session-invalid' => 'เซสชันการชำระเงินหมดอายุหรือไม่ถูกต้อง',
        'session-not-found' => 'ไม่พบเซสชันการชำระเงิน',
        'verification-failed' => 'การตรวจสอบการชำระเงินล้มเหลว',
    ],
];
