
<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://your-vercel-app.vercel.app',
        'http://localhost:5173',
        
    ],

    'allowed_headers' => ['*'],

    'supports_credentials' => false,
];