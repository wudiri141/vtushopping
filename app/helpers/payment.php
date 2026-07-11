<?php

function format_money(int|float $amount): string
{
    return '₦' . number_format((float) $amount, 2);
}
