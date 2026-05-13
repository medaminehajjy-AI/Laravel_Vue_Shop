<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://laravel-vue-shop-sehf-89an8d9qm-medaminehajjy-ais-projects.vercel.app',
        'http://localhost:5173',
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'supports_credentials' => true,
];