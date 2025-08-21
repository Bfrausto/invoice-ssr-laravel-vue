<?php
return [
    'currencies' => [
        'MXN' => ['symbol' => '$', 'label' => 'MXN (Peso Mexicano)'],
        'USD' => ['symbol' => '$', 'label' => 'USD (Dólar Americano)'],
    ],
    'exchange_rate_usd_to_mxn' => env('EXCHANGE_RATE_USD_TO_MXN', 20.50),
];
