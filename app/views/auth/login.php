<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Proposta Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
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

        <h1 class="auth-title">Bem-vindo de volta!</h1>
        <p class="auth-subtitle">Entre com seu e-mail e senha para acessar.</p>

        <form method="POST" action="login.php" style="display:flex;flex-direction:column;gap:16px;">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
                Entrar
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:.82rem;color:var(--gray-500);">
            Não tem conta? <a href="register.php">Criar conta grátis</a>
        </p>
    </div>
</div>
<script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html>
