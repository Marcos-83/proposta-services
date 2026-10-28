<?php require_once __DIR__ . '/layout_header.php'; ?>

<div class="adm-page-header">
    <div>
        <h1>Log de Auditoria</h1>
        <div class="adm-page-header-sub"><?= count($logs) ?> registros encontrados</div>
    </div>
    <a href="<?= APP_URL ?>/admin/export.php?type=audit" class="btn btn-outline btn-sm">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Exportar CSV
    </a>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar" style="margin-bottom:16px;flex-wrap:wrap;">
    <div class="fg">
        <label>Usuário</label>
        <select name="user_id" class="form-control" style="width:200px;">
            <option value="">Todos</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($_GET['user_id']??'')==$u['id']?'selected':'' ?>><?= htmlspecialchars($u['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="fg">
        <label>Ação</label>
        <input type="text" name="action" class="form-control" value="<?= htmlspecialchars($_GET['action']??'') ?>" placeholder="ex: user_blocked" style="width:160px;">
    </div>
    <div class="fg">
        <label>De</label>
        <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from']??'') ?>">
    </div>
    <div class="fg">
        <label>Até</label>
        <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to']??'') ?>">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    <a href="<?= APP_URL ?>/admin/audit_log.php" class="btn btn-outline btn-sm">Limpar</a>
</form>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Admin</th>
                    <th>Ação</th>
                    <th>Entidade</th>
                    <th>Descrição</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td style="white-space:nowrap;font-family:monospace;font-size:.73rem;"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                <td>
                    <?php if (!empty($log['user_name'])): ?>
                    <a href="<?= APP_URL ?>/admin/user_detail.php?id=<?= $log['user_id'] ?>" style="color:var(--text);text-decoration:none;font-size:.8rem;"><?= htmlspecialchars($log['user_name']) ?></a>
                    <?php else: ?><span style="color:var(--text-muted);">—</span><?php endif; ?>
                </td>
                <td style="font-size:.78rem;color:var(--text-muted);"><?= htmlspecialchars($log['admin_name'] ?? '—') ?></td>
                <td>
                    <?php
                    $actionColors = [
                        'user_blocked'=>'danger','user_unblocked'=>'success','user_deleted'=>'danger',
                        'plan_changed'=>'primary','billing_added'=>'success','billing_updated'=>'warning',
                        'user_updated'=>'primary','password_reset'=>'warning','proposal_approved'=>'success',
                        'proposal_rejected'=>'danger',
                    ];
                    $ac = $actionColors[$log['action']] ?? 'default';
                    ?>
                    <code style="font-size:.68rem;background:rgba(<?= $ac==='danger'?'239,68,68':($ac==='success'?'34,197,94':($ac==='warning'?'245,158,11':'59,130,246')) ?>,.12);color:var(--<?= $ac==='default'?'text-muted':$ac ?>);padding:2px 7px;border-radius:4px;"><?= htmlspecialchars($log['action']) ?></code>
                </td>
                <td style="font-size:.75rem;color:var(--text-muted);"><?= htmlspecialchars($log['entity']??'') ?><?= !empty($log['entity_id']) ? ' #'.$log['entity_id'] : '' ?></td>
                <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.8rem;"><?= htmlspecialchars($log['description']??'') ?></td>
                <td style="font-family:monospace;font-size:.7rem;color:var(--text-muted);"><?= htmlspecialchars($log['ip_address']??'') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($logs)): ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">Nenhum registro encontrado para os filtros aplicados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
