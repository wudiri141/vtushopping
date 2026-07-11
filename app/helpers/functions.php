<?php

function redirect_to(string $url): void
{
    header('Location: ' . $url);
    exit;
}
