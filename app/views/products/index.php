<?php
// app/views/products/index.php
$pageTitle = 'Produtos & Serviços';
require_once __DIR__ . '/../layout_header.php';
?>

<div class="page-header">
    <h1>📦 Produtos & Serviços</h1>
    <a href="products.php?action=create" class="btn btn-primary">+ Novo Item</a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--gray-300);margin-bottom:12px;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                <p>Nenhum produto ou serviço cadastrado.<br>
                <a href="products.php?action=create">Cadastre o primeiro</a></p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table-responsive-cards">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td data-label="Tipo">
                                    <span class="badge <?= $p['type'] === 'produto' ? 'badge-enviado' : 'badge-aprovado' ?>">
                                        <?= $p['type'] === 'produto' ? '📦 Produto' : '🔧 Serviço' ?>
                                    </span>
                                </td>
                                <td data-label="Descrição"><?= sanitize($p['description']) ?></td>
                                <td data-label="Valor"><strong><?= formatCurrency((float)$p['price']) ?></strong></td>
                                <td data-label="Ações">
                                    <div class="actions">
                                        <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Editar</a>
                                        <form method="POST" action="products.php" class="confirm-delete" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Excluir"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></button>
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

<?php require_once __DIR__ . '/../layout_footer.php'; ?>
