<?php
// app/views/trash/index.php
$pageTitle = 'Lixeira';
require_once __DIR__ . '/../layout_header.php';

$totalTrashed = count($proposals) + count($clients) + count($products);
$tabCounts = [
    'proposals' => count($proposals),
    'clients'   => count($clients),
    'products'  => count($products),
];

// Aba ativa
$tab = $_GET['tab'] ?? 'proposals';
if (!in_array($tab, ['proposals', 'clients', 'products'])) $tab = 'proposals';
?>

<div class="page-header">
    <h1>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;color:var(--danger)"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        Lixeira
        <?php if ($totalTrashed > 0): ?>
            <span style="font-size:.75rem;font-weight:600;background:var(--danger);color:#fff;padding:2px 10px;border-radius:20px;margin-left:8px;vertical-align:middle;"><?= $totalTrashed ?></span>
        <?php endif; ?>
    </h1>
    <div style="font-size:.8rem;color:var(--gray-400);display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Itens excluídos ficam aqui até serem apagados permanentemente
    </div>
</div>

<?php if ($totalTrashed === 0): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty-state" style="padding:60px 20px;">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="color:var(--gray-200);margin-bottom:16px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                <p style="font-size:1rem;font-weight:600;color:var(--gray-400);">Lixeira vazia</p>
                <p style="font-size:.82rem;color:var(--gray-300);margin-top:4px;">Nenhum item foi excluído ainda.</p>
            </div>
        </div>
    </div>

<?php else: ?>

<!-- Tabs -->
<div style="display:flex;gap:4px;margin-bottom:18px;border-bottom:2px solid var(--gray-200);padding-bottom:0;">
    <?php
    $tabs = [
        'proposals' => ['label' => 'Propostas',        'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
        'clients'   => ['label' => 'Clientes',          'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'],
        'products'  => ['label' => 'Produtos/Serviços', 'icon' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
    ];
    foreach ($tabs as $key => $t):
        $active = $tab === $key;
        $count  = $tabCounts[$key];
        $style  = $active
            ? 'border-bottom:2px solid var(--primary);color:var(--primary);margin-bottom:-2px;background:var(--primary-light);'
            : 'color:var(--gray-500);';
    ?>
    <a href="trash.php?tab=<?= $key ?>"
       style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px 8px 0 0;font-size:.83rem;font-weight:600;text-decoration:none;transition:all .15s;<?= $style ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $t['icon'] ?></svg>
        <?= $t['label'] ?>
        <?php if ($count > 0): ?>
            <span style="background:<?= $active ? 'var(--primary)' : 'var(--gray-300)' ?>;color:#fff;border-radius:20px;padding:1px 8px;font-size:.68rem;font-weight:700;"><?= $count ?></span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Tab: Propostas -->
<?php if ($tab === 'proposals'): ?>
<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($proposals)): ?>
            <div class="empty-state" style="padding:40px;">
                <p>Nenhuma proposta na lixeira.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table-responsive-cards">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título / Cliente</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Excluído em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proposals as $p): ?>
                        <tr>
                            <td data-label="#"><strong style="color:var(--gray-400);">#<?= $p['proposal_number'] ?></strong></td>
                            <td data-label="Proposta">
                                <div style="font-weight:600;color:var(--gray-500);"><?= sanitize($p['title']) ?></div>
                                <div style="font-size:.75rem;color:var(--gray-400);">👤 <?= sanitize($p['client_name']) ?></div>
                            </td>
                            <td data-label="Valor"><?= formatCurrency((float)$p['total_value']) ?></td>
                            <td data-label="Status"><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td data-label="Excluído em" style="color:var(--danger);font-size:.78rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= date('d/m/Y H:i', strtotime($p['deleted_at'])) ?>
                            </td>
                            <td data-label="Ações">
                                <div class="actions">
                                    <!-- Restaurar -->
                                    <form method="POST" action="trash.php" style="display:inline;">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="type" value="proposal">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn btn-success btn-sm" title="Restaurar">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.69"/></svg>
                                            Restaurar
                                        </button>
                                    </form>
                                    <!-- Excluir permanentemente -->
                                    <form method="POST" action="trash.php" class="confirm-force-delete" style="display:inline;">
                                        <input type="hidden" name="action" value="forceDelete">
                                        <input type="hidden" name="type" value="proposal">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Excluir permanentemente">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: Clientes -->
<?php elseif ($tab === 'clients'): ?>
<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($clients)): ?>
            <div class="empty-state" style="padding:40px;">
                <p>Nenhum cliente na lixeira.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table-responsive-cards">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th>Excluído em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c): ?>
                        <tr>
                            <td data-label="Nome"><strong style="color:var(--gray-500);"><?= sanitize($c['name']) ?></strong></td>
                            <td data-label="Telefone"><?= sanitize($c['phone'] ?? '-') ?></td>
                            <td data-label="E-mail"><?= sanitize($c['email'] ?? '-') ?></td>
                            <td data-label="Excluído em" style="color:var(--danger);font-size:.78rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= date('d/m/Y H:i', strtotime($c['deleted_at'])) ?>
                            </td>
                            <td data-label="Ações">
                                <div class="actions">
                                    <form method="POST" action="trash.php" style="display:inline;">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="type" value="client">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.69"/></svg>
                                            Restaurar
                                        </button>
                                    </form>
                                    <form method="POST" action="trash.php" class="confirm-force-delete" style="display:inline;">
                                        <input type="hidden" name="action" value="forceDelete">
                                        <input type="hidden" name="type" value="client">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Excluir permanentemente">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: Produtos -->
<?php elseif ($tab === 'products'): ?>
<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($products)): ?>
            <div class="empty-state" style="padding:40px;">
                <p>Nenhum produto na lixeira.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table-responsive-cards">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Preço</th>
                            <th>Excluído em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td data-label="Descrição"><strong style="color:var(--gray-500);"><?= sanitize($p['description']) ?></strong></td>
                            <td data-label="Tipo">
                                <span class="item-type-tag <?= $p['type'] === 'produto' ? 'product-tag' : 'service-tag' ?>">
                                    <?= $p['type'] === 'produto' ? 'Produto' : 'Serviço' ?>
                                </span>
                            </td>
                            <td data-label="Preço"><?= formatCurrency((float)$p['price']) ?></td>
                            <td data-label="Excluído em" style="color:var(--danger);font-size:.78rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= date('d/m/Y H:i', strtotime($p['deleted_at'])) ?>
                            </td>
                            <td data-label="Ações">
                                <div class="actions">
                                    <form method="POST" action="trash.php" style="display:inline;">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="type" value="product">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.69"/></svg>
                                            Restaurar
                                        </button>
                                    </form>
                                    <form method="POST" action="trash.php" class="confirm-force-delete" style="display:inline;">
                                        <input type="hidden" name="action" value="forceDelete">
                                        <input type="hidden" name="type" value="product">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Excluir permanentemente">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; // fim totalTrashed > 0 ?>

<script>
// Confirmação para exclusão permanente
document.querySelectorAll('.confirm-force-delete').forEach(form => {
    form.addEventListener('submit', e => {
        if (!confirm('⚠️ Atenção! Esta ação é irreversível.\n\nDeseja excluir permanentemente este item do banco de dados?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout_footer.php'; ?>