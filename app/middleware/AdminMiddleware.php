<?php

class AdminMiddleware
{
    public function handle(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}
