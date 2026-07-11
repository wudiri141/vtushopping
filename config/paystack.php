<?php

return [
    'public_key' => getenv('PAYSTACK_PUBLIC_KEY') ?: 'pk_live_375512a09283ec4f1fac52f66315d8507d5605db',
    'secret_key' => getenv('PAYSTACK_SECRET_KEY') ?: 'sk_live_b291bfea508529b39abd990e39d5ff4cc2ba6aa7',
];
