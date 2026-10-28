<?php
// app/views/menu.php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = auth();

function navLink(string $page, string $icon, string $label, string $current): string {
    $active = $current === $page ? ' class="active"' : '';
    return "<a href=\"{$page}.php\"{$active}><span class=\"nav-icon\">{$icon}</span>{$label}</a>";
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="dashboard.php" class="logo-mark">
            <div class="logo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div class="logo-text">
                Proposta Services
                <span>Gestão de Orçamentos</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" <?= $currentPage === 'dashboard' ? 'class="active"' : '' ?>>
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            Dashboard <br>
            <?php date_default_timezone_set('America/Sao_Paulo')?>
            <?= date('d/m/Y H:i') ?>
        </a>

        <div class="nav-group-label">Cadastros</div>
        <a href="clients.php" <?= $currentPage === 'clients' ? 'class="active"' : '' ?>>
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            Clientes
        </a>
        <a href="products.php" <?= $currentPage === 'products' ? 'class="active"' : '' ?>>
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
            Produtos &amp; Serviços
        </a>

        <div class="nav-group-label">Propostas</div>
        <a href="proposals.php" <?= $currentPage === 'proposals' ? 'class="active"' : '' ?>>
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
            Propostas
        </a>

        <div class="nav-group-label">Conta</div>
        <a href="plans.php" <?= $currentPage === 'plans' ? 'class="active"' : '' ?>>
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
            Planos & Preços
        </a>
        <a href="settings.php" <?= $currentPage === 'settings' ? 'class="active"' : '' ?>>
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></span>
            Configurações
        </a>
        <?php if (($user['role'] ?? '') === 'admin'): ?>
        <div class="nav-group-label">Administração</div>
        <a href="<?= APP_URL ?>/admin/" <?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'class="active"' : '' ?> style="color:var(--accent);">
            <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
            Painel Admin
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user-info">
            <div class="sidebar-avatar"><?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?></div>
            <div>
                <div style="color:#fff;font-weight:600;font-size:.8rem;line-height:1.2;"><?= htmlspecialchars($user['name']) ?></div>
                <div style="color:var(--sidebar-text);font-size:.68rem;"><?= htmlspecialchars($user['company_name']) ?></div>
            </div>
        </div>
                <a href="trash.php" <?= $currentPage === 'trash' ? 'class="active"' : '' ?> style="color:var(--danger);opacity:.8;margin-left:10px;">
            <span class="nav-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></span>
            Lixeira
        </a>
        <a href="logout.php" class="sidebar-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sair
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>