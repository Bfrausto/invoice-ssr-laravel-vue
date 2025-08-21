<?php
return [
    'currencies' => [
        'MXN' => ['symbol' => '$', 'label' => 'MXN'],
        'USD' => ['symbol' => '$', 'label' => 'USD'],
    ],
    'exchange_rate_usd_to_mxn' => env('EXCHANGE_RATE_USD_TO_MXN', 20.50),
];
