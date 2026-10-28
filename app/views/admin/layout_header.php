<?php
$adminUser = auth();
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
$flash     = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Proposta Services</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#0a0f1e;--sb:#0d1526;--surface:#111827;--surface2:#1a2235;--border:rgba(255,255,255,.07);--primary:#3b82f6;--accent:#f47b20;--success:#22c55e;--warning:#f59e0b;--danger:#ef4444;--text:#e2e8f0;--muted:#64748b;--dim:#94a3b8;--sw:240px;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{font-size:14px;}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;}
a{color:var(--primary);text-decoration:none;}
a:hover{text-decoration:underline;}

.adm-sidebar{width:var(--sw);flex-shrink:0;background:var(--sb);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;inset-block:0;left:0;z-index:100;overflow:hidden;}
.adm-logo{padding:20px 18px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-shrink:0;}
.adm-logo-icon{width:36px;height:36px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.adm-logo-text{font-weight:800;font-size:.95rem;color:#fff;line-height:1.2;}
.adm-logo-sub{font-size:.65rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;}

.adm-panels{flex:1;position:relative;overflow:hidden;}
.adm-panel{position:absolute;inset:0;overflow-y:auto;transition:transform .3s cubic-bezier(.4,0,.2,1),opacity .25s ease;}
.adm-panel-nav{transform:translateX(0);opacity:1;}
.adm-panel-profile{transform:translateX(100%);opacity:0;pointer-events:none;}
.adm-sidebar.profile-open .adm-panel-nav{transform:translateX(-100%);opacity:0;pointer-events:none;}
.adm-sidebar.profile-open .adm-panel-profile{transform:translateX(0);opacity:1;pointer-events:all;}

.adm-nav{padding:12px 10px;}
.adm-nav-label{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);padding:12px 10px 4px;}
.adm-nav a{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:7px;color:var(--dim);font-size:.82rem;font-weight:500;transition:background .15s,color .15s;text-decoration:none;margin-bottom:2px;}
.adm-nav a:hover{background:rgba(255,255,255,.05);color:var(--text);}
.adm-nav a.active{background:rgba(59,130,246,.15);color:var(--primary);font-weight:600;}

.adm-footer{padding:12px 10px;border-top:1px solid var(--border);flex-shrink:0;}
.adm-user-btn{display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:8px;background:rgba(255,255,255,.04);cursor:pointer;border:none;width:100%;text-align:left;transition:background .15s;margin-bottom:6px;font-family:'Inter',sans-serif;}
.adm-user-btn:hover{background:rgba(255,255,255,.09);}
.adm-avatar{width:32px;height:32px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;color:#fff;flex-shrink:0;}
.adm-uname{font-size:.78rem;font-weight:600;color:var(--text);}
.adm-urole{font-size:.64rem;color:var(--muted);}
.adm-chevron{margin-left:auto;color:var(--muted);transition:transform .3s ease;flex-shrink:0;}
.adm-sidebar.profile-open .adm-chevron{transform:rotate(180deg);}
.adm-logout{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:7px;color:var(--muted);font-size:.78rem;transition:all .15s;width:100%;background:none;border:none;cursor:pointer;font-family:'Inter',sans-serif;text-decoration:none;}
.adm-logout:hover{background:rgba(239,68,68,.1);color:#ef4444;text-decoration:none;}

.profile-panel-inner{padding:16px 14px;}
.profile-back-btn{display:flex;align-items:center;gap:6px;background:none;border:none;color:var(--dim);font-size:.78rem;font-weight:600;cursor:pointer;padding:6px 8px;border-radius:6px;transition:all .15s;font-family:'Inter',sans-serif;margin-bottom:16px;width:100%;}
.profile-back-btn:hover{background:rgba(255,255,255,.05);color:var(--text);}
.profile-avatar-big{width:54px;height:54px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem;color:#fff;margin:0 auto 10px;box-shadow:0 0 0 3px rgba(59,130,246,.2),0 4px 12px rgba(59,130,246,.3);}
.profile-name-big{text-align:center;font-size:.9rem;font-weight:700;color:var(--text);margin-bottom:2px;}
.profile-email-small{text-align:center;font-size:.68rem;color:var(--muted);margin-bottom:18px;}
.profile-section-label{font-size:.59rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin:14px 0 8px;}

.pf-group{margin-bottom:10px;}
.pf-group label{font-size:.67rem;font-weight:600;color:var(--muted);display:block;margin-bottom:3px;}
.pf-input{width:100%;padding:7px 10px;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:6px;font-size:.78rem;font-family:'Inter',sans-serif;color:var(--text);transition:border-color .15s,background .15s;}
.pf-input:focus{outline:none;border-color:var(--primary);background:rgba(59,130,246,.04);}
.pf-input-wrap{position:relative;}
.pf-input-wrap .pf-input{padding-right:34px;}
.pf-eye{position:absolute;right:9px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:0;display:flex;transition:color .15s;line-height:1;}
.pf-eye:hover{color:var(--dim);}
.pf-btn{width:100%;padding:8px;border-radius:7px;font-size:.78rem;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;border:none;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px;margin-top:8px;}
.pf-btn-primary{background:var(--primary);color:#fff;}
.pf-btn-primary:hover{background:#2563eb;}
.pf-divider{border:none;border-top:1px solid var(--border);margin:16px 0;}
.pw-strength{display:flex;gap:3px;margin:5px 0 2px;}
.pw-strength span{height:3px;flex:1;background:rgba(255,255,255,.08);border-radius:2px;transition:background .3s;}
.pw-label{font-size:.63rem;min-height:14px;}

.adm-main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh;}
.adm-topbar{height:54px;background:var(--sb);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;position:sticky;top:0;z-index:50;}
.adm-topbar-title{font-size:.85rem;font-weight:600;color:var(--dim);}
.adm-badge{background:rgba(59,130,246,.15);color:var(--primary);font-size:.68rem;font-weight:700;padding:3px 10px;border-radius:20px;}
.adm-body{padding:24px;flex:1;}

.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:24px;}
.kpi-card{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px;transition:border-color .2s;}
.kpi-card:hover{border-color:var(--primary);}
.kpi-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:6px;}
.kpi-value{font-size:1.6rem;font-weight:800;color:var(--text);line-height:1;}
.kpi-sub{font-size:.7rem;color:var(--muted);margin-top:4px;}
.adm-card{background:var(--surface);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:20px;}
.adm-card-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:8px;}
.adm-card-title{font-size:.88rem;font-weight:700;color:var(--text);}
.adm-card-body{padding:18px;}
.adm-grid2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
.adm-table-wrap{overflow-x:auto;}
.adm-table{width:100%;border-collapse:collapse;font-size:.8rem;}
.adm-table thead th{padding:9px 14px;text-align:left;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);background:rgba(255,255,255,.03);border-bottom:1px solid var(--border);white-space:nowrap;}
.adm-table tbody td{padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.04);color:var(--dim);vertical-align:middle;}
.adm-table tbody tr:hover{background:rgba(255,255,255,.025);}
.adm-table tbody tr:last-child td{border-bottom:none;}
.badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:.67rem;font-weight:700;}
.badge-ativo{background:rgba(34,197,94,.12);color:#22c55e;}
.badge-bloqueado{background:rgba(239,68,68,.12);color:#ef4444;}
.badge-free{background:rgba(100,116,139,.15);color:#94a3b8;}
.badge-starter{background:rgba(59,130,246,.15);color:#3b82f6;}
.badge-pro{background:rgba(168,85,247,.15);color:#a855f7;}
.badge-enterprise{background:rgba(244,123,32,.15);color:#f47b20;}
.badge-aprovado{background:rgba(34,197,94,.12);color:#22c55e;}
.badge-enviado{background:rgba(59,130,246,.15);color:#3b82f6;}
.badge-rascunho{background:rgba(100,116,139,.15);color:#94a3b8;}
.badge-reprovado{background:rgba(239,68,68,.12);color:#ef4444;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;font-size:.78rem;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;border:1px solid transparent;transition:all .15s;text-decoration:none;}
.btn:hover{text-decoration:none;}
.btn-primary{background:var(--primary);color:#fff;}.btn-primary:hover{background:#2563eb;}
.btn-danger{background:rgba(239,68,68,.1);color:#ef4444;border-color:rgba(239,68,68,.2);}.btn-danger:hover{background:#ef4444;color:#fff;}
.btn-success{background:rgba(34,197,94,.1);color:#22c55e;border-color:rgba(34,197,94,.2);}.btn-success:hover{background:#22c55e;color:#fff;}
.btn-outline{background:transparent;color:var(--dim);border-color:var(--border);}.btn-outline:hover{background:rgba(255,255,255,.05);color:var(--text);}
.btn-sm{padding:4px 10px;font-size:.72rem;}.btn-xs{padding:3px 8px;font-size:.68rem;border-radius:5px;}
.form-group{margin-bottom:14px;}
.form-group label{font-size:.72rem;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;}
.form-control{width:100%;padding:8px 12px;background:var(--surface2);border:1px solid var(--border);border-radius:7px;font-size:.82rem;font-family:'Inter',sans-serif;color:var(--text);transition:border-color .15s;}
.form-control:focus{outline:none;border-color:var(--primary);}
.form-control option{background:var(--surface2);}
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.page-hdr h1{font-size:1.2rem;font-weight:800;color:var(--text);}
.page-hdr-sub{font-size:.75rem;color:var(--muted);margin-top:2px;}
.adm-flash{padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:.82rem;font-weight:500;display:flex;align-items:center;gap:8px;}
.adm-flash-success{background:rgba(34,197,94,.1);color:#22c55e;border:1px solid rgba(34,197,94,.2);}
.adm-flash-error{background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.2);}
.filter-bar{display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;}
.filter-bar .fg{display:flex;flex-direction:column;gap:4px;}
/* ═══════════════════════════════════════════
   ADMIN RESPONSIVE
═══════════════════════════════════════════ */

/* 1280px — compact sidebar */
@media (max-width: 1280px) {
    :root { --sw: 220px; }
    .adm-body { padding: 18px; }
    .kpi-grid { grid-template-columns: repeat(3, 1fr); }
}

/* 1024px — tighter layout */
@media (max-width: 1024px) {
    :root { --sw: 200px; }
    .adm-grid2 { grid-template-columns: 1fr; }
    .kpi-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .adm-body { padding: 16px; }
}

/* 900px — collapse second grid column */
@media (max-width: 900px) {
    .adm-grid2 { grid-template-columns: 1fr; }
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}

/* 768px — off-canvas sidebar (mobile breakpoint) */
@media (max-width: 768px) {
    :root { --sw: 260px; }

    /* Sidebar hidden by default */
    .adm-sidebar {
        transform: translateX(-100%);
        transition: transform .28s cubic-bezier(.4,0,.2,1);
        box-shadow: none;
    }
    .adm-sidebar.open {
        transform: translateX(0);
        box-shadow: 4px 0 32px rgba(0,0,0,.45);
    }
    /* Profile panel still works when sidebar is open */
    .adm-sidebar.open.profile-open .adm-panel-nav  { transform: translateX(-100%); opacity: 0; pointer-events: none; }
    .adm-sidebar.open.profile-open .adm-panel-profile { transform: translateX(0); opacity: 1; pointer-events: all; }

    /* Main takes full width */
    .adm-main { margin-left: 0; }

    /* Topbar gets hamburger */
    .adm-topbar { padding: 0 16px; justify-content: space-between; }
    .adm-topbar::before {
        content: '';
        display: none;
    }

    /* Mobile overlay */
    .adm-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,.55); z-index: 99;
    }
    .adm-overlay.open { display: block; }

    /* Hamburger button */
    .adm-hamburger {
        display: flex !important;
        align-items: center; justify-content: center;
        background: none; border: none; cursor: pointer;
        color: var(--dim); padding: 7px; border-radius: 7px;
        transition: background .15s;
    }
    .adm-hamburger:hover { background: rgba(255,255,255,.07); }

    /* Body padding */
    .adm-body { padding: 14px; }

    /* KPIs 2 col */
    .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 16px; }
    .kpi-card { padding: 13px; }
    .kpi-value { font-size: 1.35rem; }

    /* Card adjustments */
    .adm-card-header { flex-wrap: wrap; gap: 8px; }
    .adm-card-body { padding: 14px; }

    /* Table scrollable */
    .adm-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    /* Plan cards */
    .adm-plans-grid { grid-template-columns: 1fr !important; }

    /* Page header */
    .page-hdr { flex-direction: column; align-items: flex-start; gap: 10px; margin-bottom: 16px; }
    .page-hdr h1 { font-size: 1.05rem; }

    /* Filter bar */
    .filter-bar { flex-direction: column; gap: 8px; }
    .filter-bar .fg { width: 100%; }
    .filter-bar input,
    .filter-bar select { width: 100%; }
}

/* 480px — small phones */
@media (max-width: 480px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .kpi-card { padding: 11px; }
    .kpi-value { font-size: 1.1rem; }
    .kpi-label { font-size: .6rem; }
    .adm-body { padding: 10px; }
    .adm-card-header { padding: 11px 13px; }
    .adm-card-body { padding: 11px; }
    .btn { font-size: .75rem; padding: 5px 10px; }
    .btn-xs { padding: 3px 7px; font-size: .65rem; }
    .adm-table { font-size: .74rem; }
    .adm-table thead th { padding: 7px 10px; }
    .adm-table tbody td { padding: 8px 10px; }
    .badge { font-size: .62rem; padding: 2px 7px; }
}

/* 360px — very small */
@media (max-width: 360px) {
    .kpi-grid { grid-template-columns: 1fr 1fr; gap: 6px; }
    .adm-body { padding: 8px; }
    .adm-badge { display: none; }
}
</style>
</head>
<body>

<aside class="adm-sidebar" id="admSidebar">
    <div class="adm-logo">
        <div class="adm-logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <div class="adm-logo-text">Proposta Services</div>
            <div class="adm-logo-sub">Painel Admin</div>
        </div>
    </div>

    <div class="adm-panels">

        <!-- NAV PANEL -->
        <div class="adm-panel adm-panel-nav">
            <nav class="adm-nav">
                <a href="<?= APP_URL ?>/admin/index.php" <?= $adminPage==='index'?'class="active"':'' ?>>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <div class="adm-nav-label">Gestão</div>
                <a href="<?= APP_URL ?>/admin/users.php" <?= in_array($adminPage,['users','user_detail'])?'class="active"':'' ?>>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Usuários
                </a>
                <a href="<?= APP_URL ?>/admin/plans.php" <?= $adminPage==='plans'?'class="active"':'' ?>>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Planos & Preços
                </a>
            </nav>
        </div>

        <!-- PROFILE PANEL -->
        <div class="adm-panel adm-panel-profile">
            <div class="profile-panel-inner">
                <button class="profile-back-btn" onclick="closeProfile()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Voltar ao menu
                </button>

                <div class="profile-avatar-big"><?= mb_strtoupper(mb_substr($adminUser['name']??'A',0,1)) ?></div>
                <div class="profile-name-big"><?= htmlspecialchars($adminUser['name']??'Admin') ?></div>
                <div class="profile-email-small"><?= htmlspecialchars($adminUser['email']??'') ?></div>

                <div class="profile-section-label">Dados Pessoais</div>
                <form method="POST" action="<?= APP_URL ?>/admin/index.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="settings_action" value="update_profile">
                    <div class="pf-group">
                        <label>Nome</label>
                        <input type="text" name="name" class="pf-input" value="<?= htmlspecialchars($adminUser['name']??'') ?>" required>
                    </div>
                    <div class="pf-group">
                        <label>E-mail</label>
                        <input type="email" name="email" class="pf-input" value="<?= htmlspecialchars($adminUser['email']??'') ?>" required>
                    </div>
                    <button type="submit" class="pf-btn pf-btn-primary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13"/><polyline points="7 3 7 8 15 8"/></svg>
                        Salvar Dados
                    </button>
                </form>

                <hr class="pf-divider">

                <div class="profile-section-label">Alterar Senha</div>
                <form method="POST" action="<?= APP_URL ?>/admin/index.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="settings_action" value="update_password">
                    <div class="pf-group">
                        <label>Senha atual</label>
                        <div class="pf-input-wrap">
                            <input type="password" name="current_password" id="pfPw0" class="pf-input" placeholder="••••••••" required>
                            <button type="button" class="pf-eye" onclick="pfToggle('pfPw0')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                        </div>
                    </div>
                    <div class="pf-group">
                        <label>Nova senha</label>
                        <div class="pf-input-wrap">
                            <input type="password" name="new_password" id="pfPw1" class="pf-input" placeholder="Mín. 6 caracteres" required oninput="pfStrength(this.value)">
                            <button type="button" class="pf-eye" onclick="pfToggle('pfPw1')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                        </div>
                        <div class="pw-strength"><span id="ps1"></span><span id="ps2"></span><span id="ps3"></span><span id="ps4"></span></div>
                        <div class="pw-label" id="pwLabel"></div>
                    </div>
                    <div class="pf-group">
                        <label>Confirmar senha</label>
                        <div class="pf-input-wrap">
                            <input type="password" name="confirm_password" id="pfPw2" class="pf-input" placeholder="Repita a senha" required>
                            <button type="button" class="pf-eye" onclick="pfToggle('pfPw2')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                        </div>
                    </div>
                    <button type="submit" class="pf-btn pf-btn-primary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Alterar Senha
                    </button>
                </form>

                <hr class="pf-divider">

                <a href="<?= APP_URL ?>/logout.php" class="adm-logout" style="color:#ef4444;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sair da conta
                </a>
            </div>
        </div>
    </div>

    <div class="adm-footer">
        <button class="adm-user-btn" onclick="toggleProfile()">
            <div class="adm-avatar"><?= mb_strtoupper(mb_substr($adminUser['name']??'A',0,1)) ?></div>
            <div style="flex:1;min-width:0;">
                <div class="adm-uname" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($adminUser['name']??'Admin') ?></div>
                <div class="adm-urole">Administrador · clique para editar</div>
            </div>
            <svg class="adm-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
        </button>
        <a href="<?= APP_URL ?>/logout.php" class="adm-logout">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sair
        </a>
    </div>
</aside>

<div class="adm-overlay" id="admOverlay" onclick="admCloseSidebar()"></div>

<main class="adm-main">
<div class="adm-topbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <button class="adm-hamburger" id="admHamburger" onclick="admToggleSidebar()" style="display:none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <span class="adm-topbar-title"><?= htmlspecialchars($pageTitle ?? '') ?></span>
    </div>
    <span class="adm-badge">ADMIN</span>
</div>
<div class="adm-body">
<?php if ($flash): ?>
<div class="adm-flash adm-flash-<?= $flash['type']==='success'?'success':'error' ?>">
    <?= $flash['type']==='success'?'✅':'❌' ?> <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<script>
const _sidebar = document.getElementById('admSidebar');
const _overlay = document.getElementById('admOverlay');
const _hamburger = document.getElementById('admHamburger');

function admToggleSidebar() { _sidebar.classList.toggle('open'); _overlay.classList.toggle('open'); }
function admCloseSidebar()  { _sidebar.classList.remove('open'); _overlay.classList.remove('open'); }
function toggleProfile()    { _sidebar.classList.toggle('profile-open'); }
function closeProfile()     { _sidebar.classList.remove('profile-open'); }
function pfToggle(id)       { const e=document.getElementById(id); e.type=e.type==='password'?'text':'password'; }

// Show hamburger only on mobile
function checkBreakpoint() {
    const isMobile = window.innerWidth <= 768;
    _hamburger.style.display = isMobile ? 'flex' : 'none';
    if (!isMobile) { admCloseSidebar(); }
}
checkBreakpoint();
window.addEventListener('resize', checkBreakpoint);
function pfStrength(v){
    let s=0;
    if(v.length>=6)s++; if(v.length>=10)s++;
    if(/[A-Z]/.test(v)&&/[a-z]/.test(v))s++;
    if(/\d/.test(v)&&/[^A-Za-z0-9]/.test(v))s++;
    const c=['#ef4444','#f59e0b','#3b82f6','#22c55e'];
    const l=['Muito fraca','Fraca','Boa','Forte'];
    [1,2,3,4].forEach(i=>{ document.getElementById('ps'+i).style.background=i<=s?c[s-1]:'rgba(255,255,255,.08)'; });
    const lbl=document.getElementById('pwLabel');
    lbl.textContent=s>0?l[s-1]:''; lbl.style.color=s>0?c[s-1]:'';
}
<?php
// Re-open profile panel ONLY after profile/password save
$_settingsAction = $_SESSION['_last_settings_action'] ?? '';
unset($_SESSION['_last_settings_action']);
if ($flash && in_array($_settingsAction, ['update_profile','update_password'])):
?>
_sidebar.classList.add('profile-open');
<?php endif; ?>
</script>
