<?php
$pageTitle = isset($title) ? $title . ' - VTU Shopping Store' : 'VTU Shopping Store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= asset('css/store.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/fixes.css') ?>">
</head>
<body>
<div class="page-shell">
