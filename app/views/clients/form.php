<?php
// app/views/clients/form.php
$isEdit = isset($client) && $client;
$pageTitle = $isEdit ? 'Editar Cliente' : 'Novo Cliente';
require_once __DIR__ . '/../layout_header.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? '✏️ Editar Cliente' : '👥 Novo Cliente' ?></h1>
    <a href="clients.php" class="btn btn-outline">← Voltar</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="clients.php">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'store' ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $client['id'] ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group full">
                    <label>Nome *</label>
                    <input type="text" name="name" class="form-control" required
                        value="<?= sanitize($client['name'] ?? '') ?>" placeholder="Nome do cliente">
                </div>
                <div class="form-group">
                    <label>Telefone / WhatsApp</label>
                    <input type="text" name="phone" class="form-control phone-mask"
                        value="<?= sanitize($client['phone'] ?? '') ?>" placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= sanitize($client['email'] ?? '') ?>" placeholder="cliente@email.com">
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:22px;">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '💾 Salvar Alterações' : '+ Cadastrar Cliente' ?>
                </button>
                <a href="clients.php" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout_footer.php'; ?>
