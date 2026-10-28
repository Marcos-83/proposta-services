<?php require_once __DIR__ . '/layout_header.php';
$userCount = AdminModel::planUserCount();
?>

<div class="page-hdr">
    <div>
        <h1>Planos SaaS</h1>
        <div class="page-hdr-sub">Gerencie os planos e preços da plataforma</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
<?php foreach ($plans as $plan):
    $slug     = $plan['slug'];
    $ucount   = $userCount[$slug] ?? 0;
    $colors   = ['free'=>'#94a3b8','starter'=>'#3b82f6','pro'=>'#a855f7','enterprise'=>'#f47b20'];
    $color    = $colors[$slug] ?? '#3b82f6';
?>
<div class="adm-card" style="border-top:3px solid <?= $color ?>;<?= !$plan['is_active'] ? 'opacity:.6;' : '' ?>">
    <div class="adm-card-header">
        <div>
            <div class="adm-card-title" style="color:<?= $color ?>;font-size:1rem;"><?= htmlspecialchars($plan['name']) ?></div>
            <div style="font-size:.72rem;color:var(--muted);margin-top:2px;"><?= $ucount ?> usuário<?= $ucount!==1?'s':'' ?> neste plano</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:1.4rem;font-weight:800;color:var(--text);">
                <?= $plan['price'] > 0 ? 'R$'.number_format((float)$plan['price'],2,',','.') : 'Grátis' ?>
            </div>
            <div style="font-size:.65rem;color:var(--muted);"><?= $plan['price'] > 0 ? '/mês' : '' ?></div>
        </div>
    </div>
    <div class="adm-card-body">
        <!-- Current limits display -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:16px;">
            <?php foreach ([
                ['Propostas', $plan['max_proposals']],
                ['Clientes',  $plan['max_clients']],
                ['Produtos',  $plan['max_products']],
            ] as [$label, $val]): ?>
            <div style="background:var(--surface2);border-radius:7px;padding:8px;text-align:center;">
                <div style="font-size:1.1rem;font-weight:800;color:var(--text);"><?= $val >= 9999 ? '∞' : $val ?></div>
                <div style="font-size:.65rem;color:var(--muted);"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Edit form -->
        <form method="POST" action="<?= APP_URL ?>/admin/plans.php">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="slug" value="<?= $slug ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="form-group">
                    <label>Nome do plano</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($plan['name']) ?>">
                </div>
                <div class="form-group">
                    <label>Preço (R$/mês)</label>
                    <input type="text" name="price" class="form-control"
                           value="<?= number_format((float)$plan['price'],2,',','.') ?>"
                           placeholder="0,00">
                </div>
                <div class="form-group">
                    <label>Máx. Propostas</label>
                    <input type="number" name="max_proposals" class="form-control" value="<?= $plan['max_proposals'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Máx. Clientes</label>
                    <input type="number" name="max_clients" class="form-control" value="<?= $plan['max_clients'] ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Máx. Produtos</label>
                    <input type="number" name="max_products" class="form-control" value="<?= $plan['max_products'] ?>" min="0">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:0;">
                        <input type="checkbox" name="is_active" value="1" <?= $plan['is_active'] ? 'checked' : '' ?>
                               style="width:16px;height:16px;accent-color:<?= $color ?>;">
                        <span>Plano ativo</span>
                    </label>
                </div>
            </div>

            <button type="submit" name="action" value="save_plan"
                    class="btn btn-sm" style="width:100%;background:<?= $color ?>;color:#fff;margin-top:4px;">
                💾 Salvar alterações
            </button>
        </form>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Migration tip -->
<div class="adm-card" style="margin-top:20px;border-color:rgba(245,158,11,.2);">
    <div class="adm-card-header" style="border-color:rgba(245,158,11,.2);">
        <div class="adm-card-title" style="color:#f59e0b;">⚠️ Para habilitar edição de planos</div>
    </div>
    <div class="adm-card-body" style="font-size:.82rem;color:var(--muted);line-height:1.8;">
        Execute a migration <code style="background:var(--surface2);padding:2px 6px;border-radius:4px;">database/migration_admin.sql</code>
        para criar a tabela <code style="background:var(--surface2);padding:2px 6px;border-radius:4px;">plans</code>.
        Sem ela, as edições não são persistidas mas os valores atuais são exibidos.
    </div>
</div>

<script>
// Auto-format price fields
document.querySelectorAll('input[name="price"]').forEach(input => {
    input.addEventListener('blur', function() {
        const raw = this.value.replace(/[^\d,]/g,'').replace(',','.');
        const num = parseFloat(raw);
        if (!isNaN(num)) this.value = num.toFixed(2).replace('.',',');
    });
});
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
