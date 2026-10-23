<?php

return [
    'enabled' => env('CASH_TRANSFERS_ENABLED', true),
    'fund_types' => [
        'operasional' => 'Dana Operasional',
        'wakaf' => 'Dana Wakaf',
        'zakat' => 'Dana Zakat',
    ],
];
