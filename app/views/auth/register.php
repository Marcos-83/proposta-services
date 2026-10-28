<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — Proposta Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="max-width:500px;">
        <div class="auth-logo">
            <div class="logo-icon">📋</div>
            <div class="logo-text">
                Proposta Services
                <span>Gestão de Orçamentos</span>
            </div>
        </div>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
                <?= $flash['type'] === 'success' ? '✅' : '❌' ?>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <h1 class="auth-title">Criar sua conta</h1>
        <p class="auth-subtitle">Comece a gerenciar suas propostas hoje.</p>

        <form method="POST" action="register.php" style="display:flex;flex-direction:column;gap:14px;">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-grid" style="gap:14px;">
                <div class="form-group">
                    <label>Nome da Empresa *</label>
                    <input type="text" name="company_name" class="form-control" placeholder="Minha Empresa Ltda" required>
                </div>
                <div class="form-group">
                    <label>Seu Nome *</label>
                    <input type="text" name="name" class="form-control" placeholder="João Silva" required>
                </div>
                <div class="form-group">
                    <label>E-mail *</label>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label>WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control phone-mask" placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <label>Senha * (mín. 6 caracteres)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirmar Senha *</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:6px;">
                Criar Conta
            </button>
        </form>

        <p style="text-align:center;margin-top:18px;font-size:.82rem;color:var(--gray-500);">
            Já tem conta? <a href="login.php">Entrar</a>
        </p>
    </div>
</div>
<script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html>
