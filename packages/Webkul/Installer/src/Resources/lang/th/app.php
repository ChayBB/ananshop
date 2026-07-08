<?php

return [
    'seeders' => [
        'attribute' => [
            'attribute-families' => [
                'default' => 'ค่าเริ่มต้น',
            ],

            'attribute-groups' => [
                'description' => 'คำอธิบาย',
                'general' => 'ทั่วไป',
                'inventories' => 'คลังสินค้า',
                'meta-description' => 'คำอธิบายเมตา',
                'price' => 'ราคา',
                'rma' => 'RMA',
                'settings' => 'การตั้งค่า',
                'shipping' => 'การจัดส่ง',
            ],

            'attributes' => [
                'allow-rma' => 'อนุญาต RMA',
                'brand' => 'แบรนด์',
                'color' => 'สี',
                'cost' => 'ต้นทุน',
                'description' => 'คำอธิบาย',
                'featured' => 'แนะนำ',
                'guest-checkout' => 'ชำระเงินในฐานะแขก',
                'height' => 'ส่วนสูง',
                'length' => 'ความยาว',
                'manage-stock' => 'จัดการคลังสินค้า',
                'meta-description' => 'คำอธิบายเมตา',
                'meta-keywords' => 'คำหลักเมตา',
                'meta-title' => 'ชื่อเรื่องเมตา',
                'name' => 'ชื่อ',
                'new' => 'ใหม่',
                'price' => 'ราคา',
                'product-number' => 'หมายเลขสินค้า',
                'rma-rules' => 'กฎ RMA',
                'short-description' => 'คำอธิบายสั้น',
                'size' => 'ขนาด',
                'sku' => 'SKU',
                'special-price' => 'ราคาพิเศษ',
                'special-price-from' => 'ราคาพิเศษตั้งแต่',
                'special-price-to' => 'ราคาพิเศษถึง',
                'status' => 'สถานะ',
                'tax-category' => 'หมวดหมู่ภาษี',
                'url-key' => 'URL Key',
                'visible-individually' => 'แสดงแยก',
                'weight' => 'น้ำหนัก',
                'width' => 'ความกว้าง',
            ],

            'attribute-options' => [
                'black' => 'ดำ',
                'green' => 'เขียว',
                'l' => 'L',
                'm' => 'M',
                'red' => 'แดง',
                's' => 'S',
                'white' => 'ขาว',
                'xl' => 'XL',
                'yellow' => 'เหลือง',
            ],
        ],

        'category' => [
            'categories' => [
                'description' => 'คำอธิบายหมวดหมู่ราก',
                'name' => 'ราก',
            ],
        ],

        'cms' => [
            'pages' => [
                'about-us' => [
                    'content' => 'เนื้อหาหน้าเกี่ยวกับเรา',
                    'title' => 'เกี่ยวกับเรา',
                ],

                'contact-us' => [
                    'content' => 'เนื้อหาหน้าติดต่อเรา',
                    'title' => 'ติดต่อเรา',
                ],

                'customer-service' => [
                    'content' => 'เนื้อหาหน้าบริการลูกค้า',
                    'title' => 'บริการลูกค้า',
                ],

                'payment-policy' => [
                    'content' => 'เนื้อหานโยบายการชำระเงิน',
                    'title' => 'นโยบายการชำระเงิน',
                ],

                'privacy-policy' => [
                    'content' => 'เนื้อหานโยบายความเป็นส่วนตัว',
                    'title' => 'นโยบายความเป็นส่วนตัว',
                ],

                'refund-policy' => [
                    'content' => 'เนื้อหานโยบายการคืนเงิน',
                    'title' => 'นโยบายการคืนเงิน',
                ],

                'return-policy' => [
                    'content' => 'เนื้อหานโยบายการคืนสินค้า',
                    'title' => 'นโยบายการคืนสินค้า',
                ],

                'shipping-policy' => [
                    'content' => 'เนื้อหานโยบายการจัดส่ง',
                    'title' => 'นโยบายการจัดส่ง',
                ],

                'terms-conditions' => [
                    'content' => 'เนื้อหาข้อกำหนดและเงื่อนไข',
                    'title' => 'ข้อกำหนดและเงื่อนไข',
                ],

                'terms-of-use' => [
                    'content' => 'เนื้อหาข้อกำหนดการใช้งาน',
                    'title' => 'ข้อกำหนดการใช้งาน',
                ],

                'whats-new' => [
                    'content' => 'เนื้อหาหน้ามีอะไรใหม่',
                    'title' => 'มีอะไรใหม่',
                ],
            ],
        ],

        'core' => [
            'channels' => [
                'meta-description' => 'คำอธิบายเมตาของร้านตัวอย่าง',
                'meta-keywords' => 'คำหลักเมตาของร้านตัวอย่าง',
                'meta-title' => 'ร้านตัวอย่าง',
                'name' => 'ค่าเริ่มต้น',
            ],

            'currencies' => [
                'AED' => 'ไดเรแฮมสหรัฐอาหรับเอมิเรตส์',
                'ARS' => 'เปโซอาร์เจนตินา',
                'AUD' => 'ดอลลาร์ออสเตรเลีย',
                'BDT' => 'ตากาบังกลาเทศ',
                'BHD' => 'ดินาห์บาห์เรน',
                'BRL' => 'เรียลบราซิล',
                'CAD' => 'ดอลลาร์แคนาดา',
                'CHF' => 'ฟรังก์สวิส',
                'CLP' => 'เปโซชิลี',
                'CNY' => 'หยวนจีน',
                'COP' => 'เปโซโคลอมเบีย',
                'CZK' => 'โครูนาเช็ก',
                'DKK' => 'โครนเดนมาร์ก',
                'DZD' => 'ไดเรแฮมแอลจีเรีย',
                'EGP' => 'ปอนด์อียิปต์',
                'EUR' => 'ยูโร',
                'FJD' => 'ดอลลาร์ฟิจิ',
                'GBP' => 'ปอนด์สเตอร์ลิงอังกฤษ',
                'HKD' => 'ดอลลาร์ฮ่องกง',
                'HUF' => 'โฟรินต์ฮังการี',
                'IDR' => 'รูปีอินโดนีเซีย',
                'ILS' => 'เชเกลอิสราเอลใหม่',
                'INR' => 'รูปีอินเดีย',
                'JOD' => 'ดินาห์จอร์แดน',
                'JPY' => 'เยนญี่ปุ่น',
                'KRW' => 'วอนเกาหลีใต้',
                'KWD' => 'ดินาห์คูเวต',
                'KZT' => 'เทงเจคาซัคสถาน',
                'LBP' => 'ปอนด์เลบานอน',
                'LKR' => 'รูปีศรีลังกา',
                'LYD' => 'ดินาห์ลิเบีย',
                'MAD' => 'ไดเรแฮมโมร็อกโก',
                'MUR' => 'รูปีมอริเชียส',
                'MXN' => 'เปโซเม็กซิโก',
                'MYR' => 'ริงกิตมาเลเซีย',
                'NGN' => 'ไนรานิจีเรีย',
                'NOK' => 'โครนนอร์เวย์',
                'NPR' => 'รูปีเนปาล',
                'NZD' => 'ดอลลาร์นิวซีแลนด์',
                'OMR' => 'เรียลโอมาน',
                'PAB' => 'บัลบัวปานามา',
                'PEN' => 'นูเอโวโซลเปรู',
                'PHP' => 'เปโซฟิลิปปินส์',
                'PKR' => 'รูปีปากีสถาน',
                'PLN' => 'ซลอตีโปแลนด์',
                'PYG' => 'กวารานีปารากวัย',
                'QAR' => 'เรียลกาตาร์',
                'RON' => 'เลวโรมาเนีย',
                'RUB' => 'รูเบิลรัสเซีย',
                'SAR' => 'เรียลซาอุดีอาระเบีย',
                'SEK' => 'โครนสวีเดน',
                'SGD' => 'ดอลลาร์สิงคโปร์',
                'THB' => 'บาทไทย',
                'TND' => 'ดินาห์ตูนิเซีย',
                'TRY' => 'ลีราตุรกี',
                'TWD' => 'ดอลลาร์ไต้หวันใหม่',
                'UAH' => 'ฮรีฟเนียยูเครน',
                'USD' => 'ดอลลาร์สหรัฐ',
                'UZS' => 'ซอมอุซเบกิสถาน',
                'VEF' => 'โบลิวาร์เวเนซุเอลา',
                'VND' => 'ดองเวียดนาม',
                'XAF' => 'ฟรังก์ CFA BEAC',
                'XOF' => 'ฟรังก์ CFA BCEAO',
                'ZAR' => 'แรนด์แอฟริกาใต้',
                'ZMW' => 'ควาชาแซมเบีย',
            ],

            'locales' => [
                'ar' => 'อาหรับ',
                'bn' => 'เบงกาลี',
                'ca' => 'คาตาลัน',
                'de' => 'เยอรมัน',
                'en' => 'อังกฤษ',
                'es' => 'สเปน',
                'fa' => 'เปอร์เซีย',
                'fr' => 'ฝรั่งเศส',
                'he' => 'ฮิบรู',
                'hi_IN' => 'ฮินดี',
                'id' => 'อินโดนีเซีย',
                'it' => 'อิตาลี',
                'ja' => 'ญี่ปุ่น',
                'nl' => 'ดัตช์',
                'pl' => 'โปแลนด์',
                'pt_BR' => 'โปรตุเกสบราซิล',
                'ro' => 'โรมาเนีย',
                'ru' => 'รัสเซีย',
                'sin' => 'สิงลา',
                'th' => 'ไทย',
                'tr' => 'ตุรกี',
                'uk' => 'ยูเครน',
                'zh_CN' => 'จีน',
            ],
        ],

        'customer' => [
            'customer-groups' => [
                'general' => 'ทั่วไป',
                'guest' => 'แขก',
                'wholesale' => 'ขายส่ง',
            ],
        ],

        'inventory' => [
            'inventory-sources' => [
                'name' => 'ค่าเริ่มต้น',
            ],
        ],

        'shop' => [
            'theme-customizations' => [
                'bold-collections' => [
                    'content' => [
                        'btn-title' => 'ดูคอลเลกชัน',
                        'description' => 'แนะนำคอลเลกชันใหม่ของเรา! ยกระดับสไตล์ของคุณด้วยดีไซน์ที่กล้าหาญและคำพูดที่มีสีสัน สำรวจลวดลายที่โดดเด่นและสีสันที่กล้าหาญที่重新นิยามตู้เสื้อผ้าของคุณ เตรียมตัวให้พร้อมสำหรับสิ่งที่พิเศษ!',
                        'title' => 'เตรียมตัวสำหรับคอลเลกชัน Bold ใหม่ของเรา!',
                    ],

                    'name' => 'คอลเลกชัน Bold',
                ],

                'bold-collections-2' => [
                    'content' => [
                        'btn-title' => 'ดูคอลเลกชัน',
                        'description' => 'คอลเลกชัน Bold ของเราพร้อม重新นิยามตู้เสื้อผ้าของคุณด้วยดีไซน์ที่ไม่เกรงกลัวและสีสันที่โดดเด่น จากลวดลายที่กล้าหาญไปจนถึงสีที่ทรงพลัง นี่คือโอกาสของคุณที่จะหลุดพ้นจากความธรรมดาและก้าวสู่ความพิเศษ',
                        'title' => 'ปลดปล่อยความกล้าหาญของคุณด้วยคอลเลกชันใหม่ของเรา!',
                    ],

                    'name' => 'คอลเลกชัน Bold',
                ],

                'book-tickets' => [
                    'name' => 'จองตั๋ว',

                    'options' => [
                        'title' => 'จองตั๋ว',
                    ],
                ],

                'categories-collections' => [
                    'name' => 'คอลเลกชันหมวดหมู่',
                ],

                'footer-links' => [
                    'name' => 'ลิงก์ส่วนท้าย',

                    'options' => [
                        'about-us' => 'เกี่ยวกับเรา',
                        'contact-us' => 'ติดต่อเรา',
                        'customer-service' => 'บริการลูกค้า',
                        'payment-policy' => 'นโยบายการชำระเงิน',
                        'privacy-policy' => 'นโยบายความเป็นส่วนตัว',
                        'refund-policy' => 'นโยบายการคืนเงิน',
                        'return-policy' => 'นโยบายการคืนสินค้า',
                        'shipping-policy' => 'นโยบายการจัดส่ง',
                        'terms-conditions' => 'ข้อกำหนดและเงื่อนไข',
                        'terms-of-use' => 'ข้อกำหนดการใช้งาน',
                        'whats-new' => 'มีอะไรใหม่',
                    ],
                ],

                'game-container' => [
                    'content' => [
                        'sub-title-1' => 'คอลเลกชันของเรา',
                        'sub-title-2' => 'คอลเลกชันของเรา',
                        'title' => 'เกมกับสิ่งใหม่ๆ ของเรา!',
                    ],

                    'name' => 'เกมคอนเทนเนอร์',
                ],

                'image-carousel' => [
                    'name' => 'คารูเซลรูปภาพ',

                    'sliders' => [
                        'title' => 'เตรียมตัวสำหรับคอลเลกชันใหม่',
                    ],
                ],

                'kids-collection' => [
                    'name' => 'คอลเลกชันเด็ก',

                    'options' => [
                        'title' => 'คอลเลกชันเด็ก',
                    ],
                ],

                'mens-collection' => [
                    'name' => 'คอลเลกชันผู้ชาย',

                    'options' => [
                        'title' => 'คอลเลกชันผู้ชาย',
                    ],
                ],

                'offer-information' => [
                    'content' => [
                        'title' => 'ลดสูงสุด 40% สำหรับคำสั่งซื้อแรกของคุณ ช้อปเลย',
                    ],

                    'name' => 'ข้อมูลโปรโมชัน',
                ],

                'services-content' => [
                    'description' => [
                        'emi-available-info' => 'มี EMI ไม่มีดอกเบี้ยสำหรับบัตรเครดิตหลักทั้งหมด',
                        'free-shipping-info' => 'เพลิดเพลินกับการจัดส่งฟรีสำหรับทุกคำสั่งซื้อ',
                        'product-replace-info' => 'มีบริการเปลี่ยนสินค้า!',
                        'time-support-info' => 'บริการสนับสนุนตลอด 24/7 ผ่านแชทและอีเมล',
                    ],

                    'name' => 'เนื้อหาบริการ',

                    'title' => [
                        'emi-available' => 'มี EMI',
                        'free-shipping' => 'จัดส่งฟรี',
                        'product-replace' => 'เปลี่ยนสินค้า',
                        'time-support' => 'สนับสนุน 24/7',
                    ],
                ],

                'top-collections' => [
                    'content' => [
                        'sub-title-1' => 'คอลเลกชันของเรา',
                        'sub-title-2' => 'คอลเลกชันของเรา',
                        'sub-title-3' => 'คอลเลกชันของเรา',
                        'sub-title-4' => 'คอลเลกชันของเรา',
                        'sub-title-5' => 'คอลเลกชันของเรา',
                        'sub-title-6' => 'คอลเลกชันของเรา',
                        'title' => 'เกมกับสิ่งใหม่ๆ ของเรา!',
                    ],

                    'name' => 'คอลเลกชันยอดนิยม',
                ],

                'womens-collection' => [
                    'name' => 'คอลเลกชันผู้หญิง',

                    'options' => [
                        'title' => 'คอลเลกชันผู้หญิง',
                    ],
                ],
            ],
        ],

        'user' => [
            'roles' => [
                'description' => 'ผู้ใช้ที่มีบทบาทนี้จะมีสิทธิ์เข้าถึงทั้งหมด',
                'name' => 'ผู้ดูแลระบบ',
            ],

            'users' => [
                'name' => 'ตัวอย่าง',
            ],
        ],

        'sample-categories' => [
            'category-translation' => [
                '2' => [
                    'description' => '<p>ผู้ชาย</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ผู้ชาย',
                    'slug' => 'mens',
                    'url-path' => 'men',
                ],

                '3' => [
                    'description' => '<p>เด็ก</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'เด็ก',
                    'slug' => 'kids',
                    'url-path' => 'kids',
                ],

                '4' => [
                    'description' => '<p>ผู้หญิง</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ผู้หญิง',
                    'slug' => 'womens',
                    'url-path' => 'woman',
                ],

                '5' => [
                    'description' => '<p>ชุดทางการ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ชุดทางการ',
                    'slug' => 'formal-wear-men',
                    'url-path' => 'men/formal-wear-men',
                ],

                '6' => [
                    'description' => '<p>ชุดลำลอง</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ชุดลำลอง',
                    'slug' => 'casual-wear-men',
                    'url-path' => 'men/casual-wear-men',
                ],

                '7' => [
                    'description' => '<p>ชุดออกกำลังกาย</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ชุดออกกำลังกาย',
                    'slug' => 'active-wear',
                    'url-path' => 'men/active-wear',
                ],

                '8' => [
                    'description' => '<p>รองเท้า</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'รองเท้า',
                    'slug' => 'footwear',
                    'url-path' => 'men/footwear',
                ],

                '9' => [
                    'description' => '<p><span>ชุดทางการ</span></p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ชุดทางการ',
                    'slug' => 'formal-wear-female',
                    'url-path' => 'woman/formal-wear-female',
                ],

                '10' => [
                    'description' => '<p>ชุดลำลอง</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ชุดลำลอง',
                    'slug' => 'casual-wear-female',
                    'url-path' => 'woman/casual-wear-female',
                ],

                '11' => [
                    'description' => '<p>ออกกำลังกาย</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'ชุดออกกำลังกาย',
                    'slug' => 'active-wear-female',
                    'url-path' => 'woman/active-wear-female',
                ],

                '12' => [
                    'description' => '<p>รองเท้า</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'รองเท้า',
                    'slug' => 'footwear-female',
                    'url-path' => 'woman/footwear-female',
                ],

                '13' => [
                    'description' => '<p>เสื้อผ้าเด็กผู้หญิง</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => 'เสื้อผ้าเด็กผู้หญิง',
                    'name' => 'เสื้อผ้าเด็กผู้หญิง',
                    'slug' => 'girls-clothing',
                    'url-path' => 'kids/girls-clothing',
                ],

                '14' => [
                    'description' => '<p>เสื้อผ้าเด็กผู้ชาย</p>',
                    'meta-description' => 'แฟชั่นเด็กผู้ชาย',
                    'meta-keywords' => '',
                    'meta-title' => 'เสื้อผ้าเด็กผู้ชาย',
                    'name' => 'เสื้อผ้าเด็กผู้ชาย',
                    'slug' => 'boys-clothing',
                    'url-path' => 'kids/boys-clothing',
                ],

                '15' => [
                    'description' => '<p>รองเท้าเด็กผู้หญิง</p>',
                    'meta-description' => 'คอลเลกชันรองเท้าแฟชั่นสำหรับเด็กผู้หญิง',
                    'meta-keywords' => '',
                    'meta-title' => 'รองเท้าเด็กผู้หญิง',
                    'name' => 'รองเท้าเด็กผู้หญิง',
                    'slug' => 'girls-footwear',
                    'url-path' => 'kids/girls-footwear',
                ],

                '16' => [
                    'description' => '<p>รองเท้าเด็กผู้ชาย</p>',
                    'meta-description' => 'คอลเลกชันรองเท้ามีสไตล์สำหรับเด็กผู้ชาย',
                    'meta-keywords' => '',
                    'meta-title' => 'รองเท้าเด็กผู้ชาย',
                    'name' => 'รองเท้าเด็กผู้ชาย',
                    'slug' => 'boys-footwear',
                    'url-path' => 'kids/boys-footwear',
                ],

                '17' => [
                    'description' => '<p>ฟิตเนส</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'สุขภาพ',
                    'slug' => 'wellness',
                    'url-path' => 'wellness',
                ],

                '18' => [
                    'description' => '<p>utorialโยคะดาวน์โหลดได้</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'utorialโยคะดาวน์โหลดได้',
                    'slug' => 'downloadable-yoga-tutorial',
                    'url-path' => 'wellness/downloadable-yoga-tutorial',
                ],

                '19' => [
                    'description' => '<p>คอลเลกชันหนังสือ</p>',
                    'meta-description' => 'คอลเลกชันหนังสือ',
                    'meta-keywords' => '',
                    'meta-title' => 'คอลเลกชันหนังสือ',
                    'name' => 'หนังสืออิเล็กทรอนิกส์',
                    'slug' => 'e-books',
                    'url-path' => 'wellness/e-books',
                ],

                '20' => [
                    'description' => '<p>บัตรชมภาพยนตร์</p>',
                    'meta-description' => 'ดื่มด่ำกับเวทมนตร์ของภาพยนตร์ 10 เรื่องต่อเดือนโดยไม่มีค่าใช้จ่ายเพิ่มเติม ใช้ได้ทั่วประเทศโดยไม่มีวันหยุด บัตรนี้มอบสิทธิพิเศษและส่วนลด snack bar ทำให้เป็นสิ่งที่ต้องมีสำหรับคนรักภาพยนตร์',
                    'meta-keywords' => '',
                    'meta-title' => 'บัตรชมภาพยนตร์รายเดือน CineXperience',
                    'name' => 'บัตรชมภาพยนตร์',
                    'slug' => 'movie-pass',
                    'url-path' => 'wellness/movie-pass',
                ],

                '21' => [
                    'description' => '<p>จัดการและขายสินค้าที่จองได้อย่างง่ายดายด้วยระบบการจองที่ราบรื่นของเรา ไม่ว่าคุณจะให้บริการนัดหมาย การเช่า งานอีเวนต์ หรือการจอง โซลูชันของเราช่วยให้ประสบการณ์ที่ราบรื่นสำหรับทั้งธุรกิจและลูกค้า ด้วยความพร้อมใช้งานแบบเรียลไทม์ การจัดตารางที่ยืดหยุ่น และการแจ้งเตือนอัตโนมัติ คุณสามารถปรับปรุงกระบวนการจองของคุณได้อย่างง่ายดาย เพิ่มความสะดวกสบายให้ลูกค้าและเพิ่มยอดขายของคุณด้วยโซลูชันสินค้าจองที่มีประสิทธิภาพของเรา!</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การจอง',
                    'slug' => 'bookings',
                    'url-path' => '',
                ],

                '22' => [
                    'description' => '<p>การจองนัดหมายช่วยให้ลูกค้าสามารถกำหนดเวลาสำหรับบริการหรือคำปรึกษากับธุรกิจหรือผู้เชี่ยวชาญ ระบบมักใช้ในอุตสาหกรรมเช่น การดูแลสุขภาพ ความงาม การศึกษา และบริการส่วนบุคคล ช่วยปรับปรุงการจัดตาราง ลดเวลารอ และเพิ่มความพึงพอใจของลูกค้าด้วยการจองที่สะดวกสบายตามเวลา</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การจองนัดหมาย',
                    'slug' => 'appointment-booking',
                    'url-path' => '',
                ],

                '23' => [
                    'description' => '<p>การจองงานอีเวนต์ช่วยให้บุคคลหรือกลุ่มสามารถลงทะเบียนหรือจองที่นั่งสำหรับงานสาธารณะหรือส่วนตัว เช่น คอนเสิร์ต เวิร์คช็อป การประชุม หรืองานปาร์ตี้ โดยทั่วไปมีตัวเลือกสำหรับการเลือกวัน ประเภทที่นั่ง และหมวดหมู่ตั๋ว ช่วยให้ผู้จัดงานจัดการผู้เข้าร่วมได้อย่างมีประสิทธิภาพและรับประกันกระบวนการเข้าที่ราบรื่น</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การจองงานอีเวนต์',
                    'slug' => 'event-booking',
                    'url-path' => '',
                ],

                '24' => [
                    'description' => '<p>การจองห้องชุมชนช่วยให้บุคคล องค์กร หรือกลุ่มสามารถจองพื้นที่ชุมชนสำหรับงานต่างๆ เช่น งานแต่งงาน การประชุม โปรแกรมวัฒนธรรม หรืองานสังสรรค์ ระบบช่วยจัดการความพร้อมใช้งาน จัดตารางการจอง และจัดการโลจิสติกส์ เช่น ความจุ สิ่งอำนวยความสะดวก และระยะเวลาเช่า รับประกันการใช้ห้องสาธารณะหรือส่วนตัวอย่างมีประสิทธิภาพในขณะที่มอบวิธีที่สะดวกสำหรับผู้ใช้ในการจัดงานของพวกเขา</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การจองห้องชุมชน',
                    'slug' => 'community-hall-bookings',
                    'url-path' => '',
                ],

                '25' => [
                    'description' => '<p>การจองโต๊ะช่วยให้ลูกค้าสามารถจองโต๊ะที่ร้านอาหาร คาเฟ่ หรือสถานที่รับประทานอาหารล่วงหน้า ช่วยจัดการความจุ ลดเวลารอ และมอบประสบการณ์การรับประทานอาหารที่ดีขึ้น ระบบนี้มีประโยชน์โดยเฉพาะในชั่วโมงเร่งด่วน งานพิเศษ หรือสำหรับกลุ่มใหญ่ที่มีความต้องการเฉพาะ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การจองโต๊ะ',
                    'slug' => 'table-booking',
                    'url-path' => '',
                ],

                '26' => [
                    'description' => '<p>การจองเช่าช่วยอำนวยความสะดวกในการจองสิ่งของหรืออสังหาริมทรัพย์เพื่อใช้ชั่วคราว เช่น ยานพาหนะ อุปกรณ์ บ้านพักตากอากาศ หรือพื้นที่ประชุม มีฟีเจอร์สำหรับเลือกช่วงเวลาเช่า ตรวจสอบความพร้อมใช้งาน และจัดการการชำระเงิน ระบบนี้รองรับการเช่าทั้งระยะสั้นและระยะยาว เพิ่มความสะดวกสบายสำหรับทั้งผู้ให้บริการและผู้เช่า</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การจองเช่า',
                    'slug' => 'rental-booking',
                    'url-path' => '',
                ],

                '27' => [
                    'description' => '<p>สำรวจเทคโนโลยีอุปกรณ์อิเล็กทรอนิกส์ล่าสุด ออกแบบมาเพื่อให้คุณเชื่อมต่อ ทำงาน และเพลิดเพลิน ไม่ว่าคุณจะอัปเกรดอุปกรณ์หรือมองหาโซลูชันอัจฉริยะ เรามีทุกสิ่งที่คุณต้องการ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'อิเล็กทรอนิกส์',
                    'slug' => 'electronics',
                    'url-path' => '',
                ],

                '28' => [
                    'description' => '<p>ค้นพบสมาร์ทโฟน ที่ชาร์จ เคส และสิ่งจำเป็นอื่นๆ สำหรับการเชื่อมต่อระหว่างเดินทาง</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'โทรศัพท์มือถือและอุปกรณ์เสริม',
                    'slug' => 'mobile-phones-accessories',
                    'url-path' => '',
                ],

                '29' => [
                    'description' => '<p>ค้นหาแล็ปท็อปที่มีประสิทธิภาพและแท็บเล็ตพกพาสำหรับการทำงาน การเรียน และการเล่น</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'แล็ปท็อปและแท็บเล็ต',
                    'slug' => 'laptops-tablets',
                    'url-path' => '',
                ],

                '30' => [
                    'description' => '<p>ช้อปหูฟัง หูฟังแบบอินเอียร์ และลำโพงเพื่อเพลิดเพลินกับเสียงที่คมชัดและประสบการณ์เสียงที่ดื่มด่ำ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'อุปกรณ์เสียง',
                    'slug' => 'audio-devices',
                    'url-path' => '',
                ],

                '31' => [
                    'description' => '<p>ทำให้ชีวิตง่ายขึ้นด้วยไฟส่องสว่างอัจฉริยะ เทอร์โมสตัท ระบบความปลอดภัย และอื่นๆ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'บ้านอัจฉริยะและการทำงานอัตโนมัติ',
                    'slug' => 'smart-home-automation',
                    'url-path' => '',
                ],

                '32' => [
                    'description' => '<p>อัปเกรดพื้นที่อยู่อาศัยของคุณด้วยสิ่งจำเป็นสำหรับบ้านและห้องครัวที่ใช้งานได้จริงและมีสไตล์ จากการทำอาหารไปจนถึงการทำความสะอาด ค้นหาสินค้าที่เพิ่มความสะดวกสบายและประสิทธิภาพ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'เครื่องใช้ในบ้าน',
                    'slug' => 'household',
                    'url-path' => '',
                ],

                '33' => [
                    'description' => '<p>เบราว์ส์เครื่องปั่น เครื่องทอดอากาศ เครื่องชงกาแฟ และอื่นๆ เพื่อทำให้การเตรียมอาหารง่ายขึ้น</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'เครื่องใช้ไฟฟ้าในครัว',
                    'slug' => 'kitchen-appliances',
                    'url-path' => '',
                ],

                '34' => [
                    'description' => '<p>สำรวจชุดเครื่องครัว อุปกรณ์ทำอาหาร จานชาม และอุปกรณ์เสิร์ฟสำหรับความต้องการด้านการทำอาหารของคุณ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'เครื่องครัวและอาหาร',
                    'slug' => 'cookware-dining',
                    'url-path' => '',
                ],

                '35' => [
                    'description' => '<p>เพิ่มความสะดวกสบายและเสน่ห์ด้วยโซฟา โต๊ะ ภาพประดับผนัง และของตกแต่งบ้าน</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'เฟอร์นิเจอร์และของตกแต่ง',
                    'slug' => 'furniture-decor',
                    'url-path' => '',
                ],

                '36' => [
                    'description' => '<p>รักษาพื้นที่ของคุณให้สะอาดหมดจดด้วยเครื่องดูดฝุ่น สเปรย์ทำความสะอาด ไม้กวาด และอุปกรณ์จัดระเบียบ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'อุปกรณ์ทำความสะอาด',
                    'slug' => 'cleaning-supplies',
                    'url-path' => '',
                ],

                '37' => [
                    'description' => '<p>จุดประกายจินตนาการของคุณหรือจัดระเบียบพื้นที่ทำงานของคุณด้วยหนังสือและเครื่องเขียนที่หลากหลาย เหมาะสำหรับผู้อ่าน นักเรียน ผู้เชี่ยวชาญ และศิลปิน</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'หนังสือและเครื่องเขียน',
                    'slug' => 'books-stationery',
                    'url-path' => '',
                ],

                '38' => [
                    'description' => '<p>ดำดิ่งสู่นวนิยายขายดี ชีวประวัติ หนังสือพัฒนาตนเอง และอื่นๆ</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'หนังสือ fiction และ non-fiction',
                    'slug' => 'fiction-non-fiction-books',
                    'url-path' => '',
                ],

                '39' => [
                    'description' => '<p>ค้นหาตำราเรียน วัสดุอ้างอิง และคู่มือการเรียนรู้สำหรับทุกวัย</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'การศึกษาและวิชาการ',
                    'slug' => 'educational-academic',
                    'url-path' => '',
                ],

                '40' => [
                    'description' => '<p>ช้อปปากกา สมุดโน้ต สมุดจัดตาราง และอุปกรณ์สำนักงานที่จำเป็นสำหรับผลผลิต</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'อุปกรณ์สำนักงาน',
                    'slug' => 'office-supplies',
                    'url-path' => '',
                ],

                '41' => [
                    'description' => '<p>สำรวจสีทา แปรง สมุดร่าง และชุดงานฝีมือ DIY สำหรับผู้สร้างสรรค์</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'อุปกรณ์ศิลปะและงานฝีมือ',
                    'slug' => 'art-craft-materials',
                    'url-path' => '',
                ],
            ],
        ],
    ],

    'installer' => [
        'middleware' => [
            'already-installed' => 'แอปพลิเคชันได้รับการติดตั้งแล้ว',
        ],

        'index' => [
            'create-administrator' => [
                'admin' => 'ผู้ดูแลระบบ',
                'bagisto' => 'Bagisto',
                'confirm-password' => 'ยืนยันรหัสผ่าน',
                'email' => 'อีเมล',
                'email-address' => 'admin@example.com',
                'password' => 'รหัสผ่าน',
                'title' => 'สร้างผู้ดูแลระบบ',
            ],

            'environment-configuration' => [
                'algerian-dinar' => 'ไดเรแฮมแอลจีเรีย (DZD)',
                'allowed-currencies' => 'สกุลเงินที่อนุญาต',
                'allowed-locales' => 'ภาษาที่อนุญาต',
                'application-name' => 'ชื่อแอปพลิเคชัน',
                'argentine-peso' => 'เปโซอาร์เจนตินา (ARS)',
                'australian-dollar' => 'ดอลลาร์ออสเตรเลีย (AUD)',
                'bagisto' => 'Bagisto',
                'bangladeshi-taka' => 'ตากาบังกลาเทศ (BDT)',
                'bahraini-dinar' => 'ดินาห์บาห์เรน (BHD)',
                'brazilian-real' => 'เรียลบราซิล (BRL)',
                'british-pound-sterling' => 'ปอนด์สเตอร์ลิงอังกฤษ (GBP)',
                'canadian-dollar' => 'ดอลลาร์แคนาดา (CAD)',
                'cfa-franc-bceao' => 'ฟรังก์ CFA BCEAO (XOF)',
                'cfa-franc-beac' => 'ฟรังก์ CFA BEAC (XAF)',
                'chilean-peso' => 'เปโซชิลี (CLP)',
                'chinese-yuan' => 'หยวนจีน (CNY)',
                'colombian-peso' => 'เปโซโคลอมเบีย (COP)',
                'czech-koruna' => 'โครูนาเช็ก (CZK)',
                'danish-krone' => 'โครนเดนมาร์ก (DKK)',
                'database-connection' => 'การเชื่อมต่อฐานข้อมูล',
                'database-hostname' => 'โฮสต์เนมฐานข้อมูล',
                'database-name' => 'ชื่อฐานข้อมูล',
                'database-password' => 'รหัสผ่านฐานข้อมูล',
                'database-port' => 'พอร์ตฐานข้อมูล',
                'database-prefix' => 'คำนำหน้าฐานข้อมูล',
                'database-prefix-help' => 'คำนำหน้าควรมีความยาว 4 ตัวอักษรและมีได้เฉพาะตัวอักษร ตัวเลข และขีดล่างเท่านั้น',
                'database-username' => 'ชื่อผู้ใช้ฐานข้อมูล',
                'default-currency' => 'สกุลเงินเริ่มต้น',
                'default-locale' => 'ภาษาเริ่มต้น',
                'default-timezone' => 'เขตเวลาเริ่มต้น',
                'default-url' => 'URL เริ่มต้น',
                'default-url-link' => 'https://localhost',
                'egyptian-pound' => 'ปอนด์อียิปต์ (EGP)',
                'euro' => 'ยูโร (EUR)',
                'fijian-dollar' => 'ดอลลาร์ฟิจิ (FJD)',
                'hong-kong-dollar' => 'ดอลลาร์ฮ่องกง (HKD)',
                'hungarian-forint' => 'โฟรินต์ฮังการี (HUF)',
                'indian-rupee' => 'รูปีอินเดีย (INR)',
                'indonesian-rupiah' => 'รูปีอินโดนีเซีย (IDR)',
                'israeli-new-shekel' => 'เชเกลอิสราเอลใหม่ (ILS)',
                'japanese-yen' => 'เยนญี่ปุ่น (JPY)',
                'jordanian-dinar' => 'ดินาห์จอร์แดน (JOD)',
                'kazakhstani-tenge' => 'เทงเจคาซัคสถาน (KZT)',
                'kuwaiti-dinar' => 'ดินาห์คูเวต (KWD)',
                'lebanese-pound' => 'ปอนด์เลบานอน (LBP)',
                'libyan-dinar' => 'ดินาห์ลิเบีย (LYD)',
                'malaysian-ringgit' => 'ริงกิตมาเลเซีย (MYR)',
                'mauritian-rupee' => 'รูปีมอริเชียส (MUR)',
                'mexican-peso' => 'เปโซเม็กซิโก (MXN)',
                'moroccan-dirham' => 'ไดเรแฮมโมร็อกโก (MAD)',
                'mysql' => 'Mysql',
                'nepalese-rupee' => 'รูปีเนปาล (NPR)',
                'new-taiwan-dollar' => 'ดอลลาร์ไต้หวันใหม่ (TWD)',
                'new-zealand-dollar' => 'ดอลลาร์นิวซีแลนด์ (NZD)',
                'nigerian-naira' => 'ไนรานิจีเรีย (NGN)',
                'norwegian-krone' => 'โครนนอร์เวย์ (NOK)',
                'omani-rial' => 'เรียลโอมาน (OMR)',
                'pakistani-rupee' => 'รูปีปากีสถาน (PKR)',
                'panamanian-balboa' => 'บัลบัวปานามา (PAB)',
                'paraguayan-guarani' => 'กวารานีปารากวัย (PYG)',
                'peruvian-nuevo-sol' => 'นูเอโวโซลเปรู (PEN)',
                'pgsql' => 'pgSQL',
                'philippine-peso' => 'เปโซฟิลิปปินส์ (PHP)',
                'polish-zloty' => 'ซลอตีโปแลนด์ (PLN)',
                'qatari-rial' => 'เรียลกาตาร์ (QAR)',
                'romanian-leu' => 'เลวโรมาเนีย (RON)',
                'russian-ruble' => 'รูเบิลรัสเซีย (RUB)',
                'saudi-riyal' => 'เรียลซาอุดีอาระเบีย (SAR)',
                'select-timezone' => 'เลือกเขตเวลา',
                'singapore-dollar' => 'ดอลลาร์สิงคโปร์ (SGD)',
                'south-african-rand' => 'แรนด์แอฟริกาใต้ (ZAR)',
                'south-korean-won' => 'วอนเกาหลีใต้ (KRW)',
                'sqlsrv' => 'SQLSRV',
                'sri-lankan-rupee' => 'รูปีศรีลังกา (LKR)',
                'swedish-krona' => 'โครนสวีเดน (SEK)',
                'swiss-franc' => 'ฟรังก์สวิส (CHF)',
                'thai-baht' => 'บาทไทย (THB)',
                'title' => 'การตั้งค่าร้านค้า',
                'tunisian-dinar' => 'ดินาห์ตูนิเซีย (TND)',
                'turkish-lira' => 'ลีราตุรกี (TRY)',
                'ukrainian-hryvnia' => 'ฮรีฟเนียยูเครน (UAH)',
                'united-arab-emirates-dirham' => 'ไดเรแฮมสหรัฐอาหรับเอมิเรตส์ (AED)',
                'united-states-dollar' => 'ดอลลาร์สหรัฐ (USD)',
                'uzbekistani-som' => 'ซอมอุซเบกิสถาน (UZS)',
                'venezuelan-bolívar' => 'โบลิวาร์เวเนซุเอลา (VEF)',
                'vietnamese-dong' => 'ดองเวียดนาม (VND)',
                'warning-message' => 'โปรดทราบ! การตั้งค่าสำหรับภาษาเริ่มต้นของระบบและสกุลเงินเริ่มต้นจะถูกตั้งค่าถาวรและไม่สามารถเปลี่ยนแปลงได้หลังจากตั้งค่าแล้ว',
                'zambian-kwacha' => 'ควาชาแซมเบีย (ZMW)',
            ],

            'sample-products' => [
                'no' => 'ไม่',
                'note' => 'หมายเหตุ: เวลาในการจัดทำดัชนีขึ้นอยู่กับจำนวนภาษาที่เลือก กระบวนการนี้อาจใช้เวลาสูงสุด 2 นาที หากคุณเพิ่มภาษาเพิ่มเติม ลองเพิ่มเวลาexecution สูงสุดในการตั้งค่าเซิร์ฟเวอร์และ PHP ของคุณ หรือคุณสามารถใช้ตัวติดตั้ง CLI ของเราเพื่อหลีกเลี่ยงหมดเวลาคำขอ',
                'sample-products' => 'สินค้าตัวอย่าง',
                'title' => 'สินค้าตัวอย่าง',
                'yes' => 'ใช่',
            ],

            'installation-processing' => [
                'bagisto' => 'กำลังติดตั้ง Bagisto',
                'bagisto-info' => 'กำลังสร้างตารางฐานข้อมูล อาจใช้เวลาสักครู่',
                'title' => 'การติดตั้ง',
            ],

            'installation-completed' => [
                'admin-panel' => 'แผงผู้ดูแลระบบ',
                'bagisto-forums' => 'ฟอรัม Bagisto',
                'customer-panel' => 'แผงลูกค้า',
                'explore-bagisto-extensions' => 'สำรวจส่วนขยาย Bagisto',
                'title' => 'การติดตั้งเสร็จสมบูรณ์',
                'title-info' => 'Bagisto ได้รับการติดตั้งสำเร็จในระบบของคุณ',
            ],

            'ready-for-installation' => [
                'create-database-tables' => 'สร้างตารางฐานข้อมูล',
                'drop-existing-tables' => 'ลบตารางที่มีอยู่',
                'install' => 'การติดตั้ง',
                'install-info' => 'Bagisto สำหรับการติดตั้ง',
                'install-info-button' => 'คลิกปุ่มด้านล่างเพื่อ',
                'populate-database-tables' => 'กรอกข้อมูลในตารางฐานข้อมูล',
                'start-installation' => 'เริ่มการติดตั้ง',
                'title' => 'พร้อมสำหรับการติดตั้ง',
            ],

            'start' => [
                'language' => 'ภาษาของตัวช่วยติดตั้ง',
                'locale' => 'ภาษา',
                'main' => 'เริ่ม',
                'select-locale' => 'เลือกภาษา',
                'title' => 'การติดตั้ง Bagisto ของคุณ',
                'welcome-title' => 'ยินดีต้อนรับสู่ Bagisto',
            ],

            'server-requirements' => [
                'calendar' => 'ปฏิทิน',
                'ctype' => 'cType',
                'curl' => 'cURL',
                'dom' => 'dom',
                'fileinfo' => 'fileInfo',
                'filter' => 'Filter',
                'gd' => 'GD',
                'hash' => 'Hash',
                'intl' => 'intl',
                'json' => 'JSON',
                'mbstring' => 'mbstring',
                'openssl' => 'openssl',
                'pcre' => 'pcre',
                'pdo' => 'pdo',
                'php' => 'PHP',
                'php-version' => 'เวอร์ชัน :version หรือสูงกว่า',
                'session' => 'session',
                'title' => 'ความต้องการของระบบ',
                'tokenizer' => 'tokenizer',
                'xml' => 'XML',
            ],

            'arabic' => 'อาหรับ',
            'back' => 'ย้อนกลับ',
            'bagisto' => 'Bagisto',
            'bagisto-info' => 'โครงการชุมชนโดย',
            'bagisto-logo' => 'โลโก้ Bagisto',
            'bengali' => 'เบงกาลี',
            'catalan' => 'คาตาลัน',
            'chinese' => 'จีน',
            'continue' => 'ดำเนินการต่อ',
            'dutch' => 'ดัตช์',
            'english' => 'อังกฤษ',
            'french' => 'ฝรั่งเศส',
            'german' => 'เยอรมัน',
            'hebrew' => 'ฮิบรู',
            'hindi' => 'ฮินดี',
            'indonesian' => 'อินโดนีเซีย',
            'installation-description' => 'การติดตั้ง Bagisto โดยทั่วไปเกี่ยวข้องกับหลายขั้นตอน นี่คือภาพรวมทั่วไปของกระบวนการติดตั้งสำหรับ Bagisto',
            'installation-info' => 'เรายินดีที่ได้พบคุณที่นี่!',
            'installation-title' => 'ยินดีต้อนรับสู่การติดตั้ง',
            'italian' => 'อิตาลี',
            'japanese' => 'ญี่ปุ่น',
            'persian' => 'เปอร์เซีย',
            'polish' => 'โปแลนด์',
            'portuguese' => 'โปรตุเกสบราซิล',
            'romanian' => 'โรมาเนีย',
            'russian' => 'รัสเซีย',
            'sinhala' => 'สิงลา',
            'spanish' => 'สเปน',
            'title' => 'ตัวติดตั้ง Bagisto',
            'turkish' => 'ตุรกี',
            'ukrainian' => 'ยูเครน',
            'webkul' => 'Webkul',
        ],
    ],
];
