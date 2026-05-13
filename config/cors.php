<?php

return [

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://laravel-vue-shop-sehf-89an8d9qm-medaminehajjy-ais-projects.vercel.app',
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'supports_credentials' => true,
];