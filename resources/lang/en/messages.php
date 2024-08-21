<?php

return [
    "title" => "Payments",
    "payments" => [
        "title" => "Payments",
        "columns" => [
            "id" => "id",
            "transaction_id" => "Transaction ID",
            "method_name" => "Method Name",
            "amount" => "Amount",
            "conversion" => "Conversion",
            "status" => "Status",
            "processing" => "Processing",
            "completed" => "Completed",
            "cancelled" => "Cancelled",
            "initiated" => "Initiated",
            "details" => "Details",
            "username" => "Username",
            "date" => "Date",
            "transaction_number" => "Transaction Number",
            "method_name" => "Method",
            "method_code" => "Method Code",
            "charge" => "Charge",
            "rate" => "Rate",
            "after_rate_conversion" => "After Rate Conversion",
            "name" => "Name",
            "email" => "Email",
            "mobile" => "Mobile",
            "address_one" => "Address one",
            "address_two" => "Address two",
            "city" => "City",
            "sub_city" => "Sub city",
            "area" => "Area",
            "state" => "State",
            "postcode" => "Postcode",
            "country" => "Country",
            "customer" => "Customer",
            "shipping" => "Shipping",
            "billing" => "Billing",
        ]
    ],
    "payment_gateways" => [
        "title" => "Payment Gateways",
        "sections" => [
            "payment_gateway_data" => [
                "title" => "Payment Gateway Data",
                "columns" => [
                    "image" => "Image",
                    "name" => "Name",
                    "description" => "Description",
                    "status" => "Status",
                ]
            ],
            "gateway_parameters_data" => [
                "title" => "Gateway Parameters Data",
                "columns" => [
                    "key" => "Key",
                    "value" => "Value",
                ]
            ],
            "supported_currencies" => [
                "title" => "Supported Currencies",
                "columns" => [
                    "currency" => "Currency",
                    "symbol" => "Symbol",
                    "rate" => "Rate",
                    "minimum_amount" => "Minimum Amount",
                    "maximum_amount" => "Maximum Amount",
                    "fixed_charge" => "Fixed Charge",
                    "percent_charge" => "Percent Charge",
                ]
            ],
        ],
        "columns" => [
            "image" => "Image",
            "name" => "Name",
            "description" => "Description",
            "alias" => "Alias",
            "status" => "Status",
            "crypto" => "Crypto",
            "toggle_status" => "Toggle Status",
        ]
    ],
    "widgets" => [
        "processing_payments" => [
            "title" => "Processing Payments",
            "description" => "Total payments in processing",
        ],
        "completed_payments" => [
            "title" => "Completed Payments",
            "description" => "Total payments completed",
        ],
        "cancelled_payments" => [
            "title" => "Cancelled Payments",
            "description" => "Total payments cancelled",
        ],
        "wallet_balance" => [
            "title" => "Wallet Balance",
            "description" => "Current wallet balance",
        ],
        "total_deposits" => [
            "title" => "Total Deposits",
            "description" => "Total amount deposited",
        ],
        "total_withdrawals" => [
            "title" => "Total Withdrawals",
            "description" => "Total amount withdrawn",
        ],
    ],
    "view" => [
        'title_pay_page' => 'Checkout / Payment Gateway',
        'choose_payment_method' => 'Select a payment method',
        'no_gateways_available' => 'No gateways available',
        'amount' => 'Amount',
        'payment_gateway_fee' => 'Payment Gateway Fee',
        'total' => 'Total',
        'pay_now' => 'Pay Now',
        'contact_us' => 'If you encounter any issues with the payment process or do not receive the provided service, contact us directly',
    ]
];
