<?php

return [
    'public_key' => getenv('PAYSTACK_PUBLIC_KEY') ?: '',
    'secret_key' => getenv('PAYSTACK_SECRET_KEY') ?: '',
];
