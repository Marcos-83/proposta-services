<?php require_once __DIR__ . '/layout_header.php'; ?>

<div class="adm-page-header">
    <div>
        <h1>Propostas</h1>
        <div class="adm-page-header-sub"><?= count($proposals) ?> propostas encontradas</div>
    </div>
    <a href="<?= APP_URL ?>/admin/export.php?type=proposals" class="btn btn-outline btn-sm">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Exportar CSV
    </a>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar" style="margin-bottom:16px;flex-wrap:wrap;">
    <div class="fg">
        <label>Buscar</label>
        <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($_GET['search']??'') ?>" placeholder="Título, cliente, empresa..." style="width:220px;">
    </div>
    <div class="fg">
        <label>Status</label>
        <select name="status" class="form-control" style="width:130px;">
            <option value="">Todos</option>
            <option value="rascunho" <?= ($_GET['status']??'')==='rascunho'?'selected':'' ?>>Rascunho</option>
            <option value="enviado"  <?= ($_GET['status']??'')==='enviado'?'selected':'' ?>>Enviado</option>
            <option value="aprovado" <?= ($_GET['status']??'')==='aprovado'?'selected':'' ?>>Aprovado</option>
            <option value="reprovado"<?= ($_GET['status']??'')==='reprovado'?'selected':'' ?>>Reprovado</option>
        </select>
    </div>
    <?php if (!empty($_GET['user_id'])): ?>
    <input type="hidden" name="user_id" value="<?= (int)$_GET['user_id'] ?>">
    <?php endif; ?>
    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    <a href="<?= APP_URL ?>/admin/proposals.php" class="btn btn-outline btn-sm">Limpar</a>
</form>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Título</th>
                    <th>Usuário/Empresa</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Assinado</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($proposals as $p): ?>
            <tr>
                <td style="color:var(--text-muted);">#<?= $p['proposal_number'] ?></td>
                <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;"><?= htmlspecialchars($p['title']) ?></td>
                <td>
                    <div style="font-size:.8rem;"><?= htmlspecialchars($p['user_name']) ?></div>
                    <div style="font-size:.7rem;color:var(--text-muted);"><?= htmlspecialchars($p['company_name']) ?></div>
                </td>
                <td><?= htmlspecialchars($p['client_name']) ?></td>
                <td style="font-weight:600;color:var(--success);"><?= formatCurrency($p['total_value']) ?></td>
                <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                <td>
                    <?php if (!empty($p['signed_at'])): ?>
                    <span style="color:var(--success);font-size:.75rem;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= date('d/m/Y', strtotime($p['signed_at'])) ?>
                    </span>
                    <?php else: ?>
                    <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                    <?php endif; ?>
                </td>
                <td style="white-space:nowrap;"><?= date('d/m/Y', strtotime($p['proposal_date'])) ?></td>
                <td>
                    <?php if (!empty($p['public_token'])): ?>
                    <a href="<?= APP_URL ?>/proposta/<?= $p['public_token'] ?>" target="_blank" class="btn btn-outline btn-xs" title="Ver proposta pública">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                    <?php endif; ?>
                    <a href="<?= APP_URL ?>/admin/user_detail.php?id=<?= $p['user_id'] ?>" class="btn btn-outline btn-xs">Usuário</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($proposals)): ?>
            <tr><td colspan="9" style="text-align:center;padding:30px;color:var(--text-muted);">Nenhuma proposta encontrada.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
