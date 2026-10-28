<?php
// app/views/products/form.php
$isEdit = isset($product) && $product;
$pageTitle = $isEdit ? 'Editar Produto/Serviço' : 'Novo Produto/Serviço';
require_once __DIR__ . '/../layout_header.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? '✏️ Editar' : '📦 Novo' ?> Produto/Serviço</h1>
    <a href="products.php" class="btn btn-outline">← Voltar</a>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" action="products.php">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'store' ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Tipo *</label>
                    <select name="type" class="form-control" required>
                        <option value="servico" <?= ($product['type'] ?? 'servico') === 'servico' ? 'selected' : '' ?>>🔧 Serviço</option>
                        <option value="produto" <?= ($product['type'] ?? '') === 'produto' ? 'selected' : '' ?>>📦 Produto</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Valor *</label>
                    <input type="text" name="price" class="form-control currency-mask"
                        data-value="<?= $product['price'] ?? '0' ?>"
                        value="<?= isset($product['price']) ? number_format((float)$product['price'], 2, ',', '.') : '' ?>"
                        placeholder="0,00" required>
                </div>
                <div class="form-group full">
                    <label>Descrição *</label>
                    <input type="text" name="description" class="form-control" required
                        value="<?= sanitize($product['description'] ?? '') ?>"
                        placeholder="Ex: Consultoria de Marketing Digital">
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:22px;">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '💾 Salvar Alterações' : '+ Cadastrar' ?>
                </button>
                <a href="products.php" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout_footer.php'; ?>
