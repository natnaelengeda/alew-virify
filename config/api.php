<?php

return [

    'bearer_token' => env('API_BEARER_TOKEN'),

    'rate_limit_per_minute' => (int) env('API_RATE_LIMIT_PER_MINUTE', 60),

];
