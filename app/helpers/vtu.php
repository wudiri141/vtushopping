<?php

function vtu_reference(string $prefix = 'VTU'): string
{
    return $prefix . '-' . strtoupper(bin2hex(random_bytes(4)));
}
