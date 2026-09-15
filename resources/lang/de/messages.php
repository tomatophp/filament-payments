<?php

return [
    'datetime_format' => 'd.m.Y h:i:s',
    "title" => "Zahlungen",
    "payments" => [
        "title" => "Zahlungen",
        "columns" => [
            "id" => "ID",
            "transaction_id" => "Transaktions-ID",
            "method_name" => "Zahlungsmethode",
            "amount" => "Betrag",
            "conversion" => "Wechselkurs",
            "status" => "Status",
            "processing" => "In Bearbeitung",
            "completed" => "Abgeschlossen",
            "cancelled" => "Storniert",
            "initiated" => "Gestartet",
            "details" => "Details",
            "username" => "Benutzername",
            "date" => "Datum",
            "transaction_number" => "Transaktionsnummer",
            "method_code" => "Code der Zahlungsmethode",
            "charge" => "Gebühr",
            "rate" => "Wechselkurs",
            "after_rate_conversion" => "Gesamtsumme nach Umrechnung",
            "name" => "Name",
            "email" => "E-Mail",
            "mobile" => "Mobilnummer",
            "address_one" => "Adresse (Zeile 1)",
            "address_two" => "Adresse (Zeile 2)",
            "city" => "Stadt",
            "sub_city" => "Stadtteil",
            "area" => "Gebiet",
            "state" => "Bundesland",
            "postcode" => "Postleitzahl",
            "country" => "Land",
            "customer" => "Kundendaten",
            "shipping" => "Lieferadresse",
            "billing" => "Rechnungsadresse",
        ]
    ],
    "payment_gateways" => [
        "title" => "Zahlungsmethoden",
        "back" => "Zurück",
        "edit" => "Zahlungsmethode bearbeiten",
        "sections" => [
            "payment_gateway_data" => [
                "title" => "Zahlungs-Gateway-Daten",
                "columns" => [
                    "image" => "Bild",
                    "name" => "Name",
                    "description" => "Beschreibung",
                    "status" => "Status",
                ]
            ],
            "gateway_parameters_data" => [
                "title" => "Gateway-Parameter",
                "columns" => [
                    "key" => "Schlüssel",
                    "value" => "Wert",
                ]
            ],
            "supported_currencies" => [
                "title" => "Unterstützte Währungen",
                "columns" => [
                    "currency" => "Währung",
                    "symbol" => "Symbol",
                    "rate" => "Wechselkurs",
                    "minimum_amount" => "Mindestbetrag",
                    "maximum_amount" => "Höchstbetrag",
                    "fixed_charge" => "Feste Gebühr",
                    "percent_charge" => "Prozentuale Gebühr",
                ]
            ],
        ],
        "columns" => [
            "image" => "Bild",
            "name" => "Name",
            "description" => "Beschreibung",
            "alias" => "Alias",
            "status" => "Status",
            "crypto" => "Krypto",
            "toggle_status" => "Status wechseln",
        ]
    ],
    "widgets" => [
        "processing_payments" => [
            "title" => "Zahlungen in Bearbeitung",
            "description" => "Gesamtanzahl der Zahlungen, die derzeit bearbeitet werden",
        ],
        "completed_payments" => [
            "title" => "Abgeschlossene Zahlungen",
            "description" => "Gesamtanzahl der erfolgreich abgeschlossenen Zahlungen",
        ],
        "cancelled_payments" => [
            "title" => "Stornierte Zahlungen",
            "description" => "Gesamtanzahl der stornierten Zahlungen",
        ],
        "wallet_balance" => [
            "title" => "Wallet-Guthaben",
            "description" => "Aktueller Saldo Ihres Wallets",
        ],
        "total_deposits" => [
            "title" => "Gesamteinzahlungen",
            "description" => "Gesamtanzahl der eingezahlten Beträge",
        ],
        "total_withdrawals" => [
            "title" => "Gesamtabhebungen",
            "description" => "Gesamtanzahl der abgehobenen Beträge",
        ],
    ],
    "view" => [
        "payment_action" => "Jetzt kostenpflichtig bestellen",
        "error" => "Fehler!",
        'title_pay_page' => 'Bestellung abschließen',
        'choose_payment_method' => 'Zahlungsmethode auswählen',
        'no_gateways_available' => 'Keine Zahlungsmethode verfügbar',
        'amount' => 'Betrag',
        'payment_gateway_fee' => 'Zahlungsgebühr',
        'total' => 'Gesamtbetrag',
        'pay_now' => 'Jetzt kostenpflichtig bestellen',
        'contact_us' => 'Falls beim Zahlungsvorgang Probleme auftreten oder Sie den gebuchten Service nicht erhalten, kontaktieren Sie uns bitte direkt.',
        'signed_in_as' => 'Angemeldet als',
        'managing_billing_for' => 'Abrechnung für',
        'gateway_error' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.',
        'gateway_updated' => [
            'title' => 'Zahlungs-Gateway aktualisiert',
            'body' => 'Das Gateway wurde erfolgreich aktualisiert.',
        ],
        'currency_not_supported' => 'Diese Währung wird nicht unterstützt.',
        'website_does_not_match' => 'Die aufgerufene Website stimmt nicht mit der ursprünglichen Anfrage überein.',
        'invalid_public_key' => 'Ungültiger öffentlicher Schlüssel.',
        'website_is_inactive' => 'Diese Website ist derzeit inaktiv.',
        'payment_created_successfully' => 'Die Zahlung wurde erfolgreich durchgeführt.',
        'team_not_found' => 'Das Team wurde nicht gefunden.',
        'payment_not_found' => 'Die Zahlungsmethode wurde nicht gefunden.',
        'driver_not_exists' => 'Der Zahlungsanbieter ist nicht vorhanden.',
    ]
];
