<?php

return [
    'verify_id_card' => [
        'app_code' => env('ALI_CLOUD_API_VERIFY_ID_CARD_APP_CODE'),
        'log_channel' => env('ALI_CLOUD_API_VERIFY_ID_CARD_LOG_CHANNEL'),
        'fake_type' => env('ALI_CLOUD_API_VERIFY_ID_CARD_FAKE_TYPE', 'none'),
    ],
];
