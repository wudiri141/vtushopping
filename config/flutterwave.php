<?php

return [
    'public_key' => getenv('FLUTTERWAVE_PUBLIC_KEY') ?: '',
    'secret_key' => getenv('FLUTTERWAVE_SECRET_KEY') ?: '',
];
