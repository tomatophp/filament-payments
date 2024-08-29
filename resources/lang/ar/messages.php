<?php

return [
    "title" => "المدفوعات",
    "payments" => [
        "title" => "المدفوعات",
        "columns" => [
            "id" => "رقم التعريف",
            "transaction_id" => "معرف المعاملة",
            "method_name" => "اسم الطريقة",
            "amount" => "المبلغ",
            "conversion" => "التحويل",
            "status" => "الحالة",
            "processing" => "قيد المعالجة",
            "completed" => "مكتمل",
            "cancelled" => "ملغي",
            "initiated" => "مبادر به",
            "details" => "التفاصيل",
            "username" => "اسم المستخدم",
            "date" => "التاريخ",
            "transaction_number" => "رقم المعاملة",
            "method_name" => "الطريقة",
            "method_code" => "رمز الطريقة",
            "charge" => "الرسوم",
            "rate" => "السعر",
            "after_rate_conversion" => "بعد تحويل السعر",
            "name" => "الاسم",
            "email" => "البريد الإلكتروني",
            "mobile" => "الهاتف المحمول",
            "address_one" => "العنوان الأول",
            "address_two" => "العنوان الثاني",
            "city" => "المدينة",
            "sub_city" => "المنطقة الفرعية",
            "area" => "المنطقة",
            "state" => "الولاية",
            "postcode" => "الرمز البريدي",
            "country" => "البلد",
            "customer" => "العميل",
            "shipping" => "الشحن",
            "billing" => "الفوترة",
        ]
    ],
    "payment_gateways" => [
        "title" => "بوابات الدفع",
        "edit" => "تعديل بوابات الدفع",
        "back" => "رجوع",
        "sections" => [
            "payment_gateway_data" => [
                "title" => "بيانات بوابة الدفع",
                "columns" => [
                    "image" => "الصورة",
                    "name" => "الاسم",
                    "description" => "الوصف",
                    "status" => "الحالة",
                ]
            ],
            "gateway_parameters_data" => [
                "title" => "بيانات تكوين الدفع",
                "columns" => [
                    "key" => "المفتاح",
                    "value" => "القيمة",
                ]
            ],
            "supported_currencies" => [
                "title" => "العملات المدعومة",
                "columns" => [
                    "currency" => "العملة",
                    "symbol" => "الرمز",
                    "rate" => "السعر",
                    "minimum_amount" => "أدنى مبلغ",
                    "maximum_amount" => "أعلى مبلغ",
                    "fixed_charge" => "الرسوم الثابتة",
                    "percent_charge" => "الرسوم بالنسبه المئويه",
                ]
            ],
        ],
        "columns" => [
            "image" => "الصورة",
            "name" => "الاسم",
            "description" => "الوصف",
            "alias" => "اللقب",
            "status" => "الحالة",
            "crypto" => "العملات الرقمية",
            "toggle_status" => "تبديل الحالة",
        ]
    ],
    "widgets" => [
        "processing_payments" => [
            "title" => "المدفوعات قيد المعالجة",
            "description" => "إجمالي المدفوعات قيد المعالجة",
        ],
        "completed_payments" => [
            "title" => "المدفوعات المكتملة",
            "description" => "إجمالي المدفوعات المكتملة",
        ],
        "cancelled_payments" => [
            "title" => "المدفوعات الملغاة",
            "description" => "إجمالي المدفوعات الملغاة",
        ],
        "wallet_balance" => [
            "title" => "رصيد المحفظة",
            "description" => "رصيد المحفظة الحالي",
        ],
        "total_deposits" => [
            "title" => "إجمالي الإيداعات",
            "description" => "إجمالي المبلغ المودع",
        ],
        "total_withdrawals" => [
            "title" => "إجمالي السحوبات",
            "description" => "إجمالي المبلغ المسحوب",
        ],
    ],
    "view" => [
        "payment_action" => "دفع",
        "error" => "خطأ!",
        'title_pay_page' => 'الدفع / بوابة الدفع',
        'choose_payment_method' => 'اختر طريقة الدفع',
        'no_gateways_available' => 'لا توجد بوابات متاحة',
        'amount' => 'المبلغ',
        'payment_gateway_fee' => 'رسوم بوابة الدفع',
        'total' => 'الإجمالي',
        'pay_now' => 'ادفع الآن',
        'contact_us' => 'إذا واجهت أي مشاكل في عملية الدفع أو لم تستلم الخدمة المقدمة، اتصل بنا مباشرة',
        'signed_in_as' => 'مسجل الدخول كـ',
        'managing_billing_for' => 'إدارة الفواتير لـ',
    ]
];
