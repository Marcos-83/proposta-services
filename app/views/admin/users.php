<?php require_once __DIR__ . '/layout_header.php'; ?>

<div class="page-hdr">
    <div>
        <h1>Usuários</h1>
        <div class="page-hdr-sub"><?= count($users) ?> usuários cadastrados</div>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="">
<div class="filter-bar">
    <div class="fg">
        <label>Busca</label>
        <input type="text" name="search" class="form-control" placeholder="Nome, email, empresa..." value="<?= htmlspecialchars($_GET['search']??'') ?>" style="width:220px;">
    </div>
    <div class="fg">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="">Todos</option>
            <option value="ativo" <?= ($_GET['status']??'')==='ativo'?'selected':'' ?>>Ativo</option>
            <option value="bloqueado" <?= ($_GET['status']??'')==='bloqueado'?'selected':'' ?>>Bloqueado</option>
        </select>
    </div>
    <div class="fg">
        <label>Plano</label>
        <select name="plan" class="form-control">
            <option value="">Todos</option>
            <option value="free" <?= ($_GET['plan']??'')==='free'?'selected':'' ?>>Free</option>
            <option value="starter" <?= ($_GET['plan']??'')==='starter'?'selected':'' ?>>Starter</option>
            <option value="pro" <?= ($_GET['plan']??'')==='pro'?'selected':'' ?>>Pro</option>
            <option value="enterprise" <?= ($_GET['plan']??'')==='enterprise'?'selected':'' ?>>Enterprise</option>
        </select>
    </div>
    <div class="fg"><label>&nbsp;</label>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="users.php" class="btn btn-outline">Limpar</a>
    </div>
</div>
</form>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>ID</th><th>Nome</th><th>Empresa</th><th>Email</th>
                    <th>Plano</th><th>Status</th><th>Propostas</th><th>Receita</th><th>Ação</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="9" style="text-align:center;padding:30px;color:var(--muted);">Nenhum usuário encontrado.</td></tr>
            <?php else: ?>
            <?php foreach ($users as $u): ?>
            <tr>
                <td style="color:var(--muted);">#<?= $u['id'] ?></td>
                <td style="color:var(--text);font-weight:600;"><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['company_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge badge-<?= $u['plan']??'free' ?>"><?= ucfirst($u['plan']??'free') ?></span></td>
                <td><span class="badge badge-<?= $u['status']??'ativo' ?>"><?= ucfirst($u['status']??'ativo') ?></span></td>
                <td style="text-align:center;"><?= $u['total_proposals'] ?></td>
                <td style="color:#22c55e;font-weight:600;"><?= formatCurrency((float)$u['revenue']) ?></td>
                <td>
                    <a href="<?= APP_URL ?>/admin/user_detail.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-xs">Gerenciar</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
