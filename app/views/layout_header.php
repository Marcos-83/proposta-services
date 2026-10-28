<?php
// app/views/layout_header.php
// Shared top part of the authenticated layout (after sidebar)

$pageTitle = $pageTitle ?? 'Proposta Services';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Proposta Services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/menu.php'; ?>
<div class="main-content">
    <header class="topbar">
        <div class="topbar-left">
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="topbar-title"><?= htmlspecialchars($pageTitle) ?></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="avatar"><?= mb_strtoupper(mb_substr(auth()['name'], 0, 1)) ?></div>
                <span class="d-none-mobile"><?= htmlspecialchars(auth()['name']) ?></span>
            </div>
        </div>
    </header>
    <main class="page-body">
<?php
$flash = getFlash();
if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= $flash['type'] === 'success' ? '✅' : '❌' ?>
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
