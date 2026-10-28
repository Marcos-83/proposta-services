<?php
$pageTitle = 'Planos & Preços';
require_once __DIR__ . '/../layout_header.php';
$currentPlan = auth()['plan'] ?? 'free';
?>

<div class="page-header">
    <h1>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;color:var(--primary)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        Planos & Preços
    </h1>
</div>

<!-- Current plan banner -->
<div class="card" style="margin-bottom:24px;background:linear-gradient(135deg,rgba(26,107,204,.08) 0%,rgba(244,123,32,.06) 100%);border-color:rgba(26,107,204,.2);">
    <div class="card-body" style="display:flex;align-items:center;gap:14px;padding:16px 20px;flex-wrap:wrap;">
        <div>
            <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--gray-500);margin-bottom:2px;">Seu plano atual</div>
            <div style="font-size:1.1rem;font-weight:800;color:var(--primary);"><?= ucfirst($currentPlan) ?></div>
        </div>
        <div style="flex:1;min-width:200px;font-size:.82rem;color:var(--gray-600);">
            Para fazer upgrade ou downgrade, entre em contato com o suporte ou aguarde a integração de pagamentos.
        </div>
        <a href="mailto:suporte@propostaservices.com" class="btn btn-primary btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Solicitar Upgrade
        </a>
    </div>
</div>

<!-- Plans grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px;margin-bottom:32px;">
<?php foreach ($plans as $plan):
    $isCurrent = $plan['slug'] === $currentPlan;
    $features  = json_decode($plan['features'] ?? '[]', true) ?: [];
?>
<div class="card" style="position:relative;<?= $isCurrent ? 'border-color:var(--primary);box-shadow:0 0 0 2px rgba(26,107,204,.15);' : '' ?>">
    <?php if ($isCurrent): ?>
    <div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:var(--primary);color:#fff;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:3px 12px;border-radius:0 0 8px 8px;">Plano Atual</div>
    <?php endif; ?>
    <?php if ($plan['slug'] === 'pro'): ?>
    <div style="position:absolute;top:-1px;right:14px;background:var(--accent);color:#fff;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:3px 10px;border-radius:0 0 8px 8px;">Popular</div>
    <?php endif; ?>
    <div class="card-body" style="padding:22px 20px;">
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--gray-400);margin-bottom:4px;"><?= htmlspecialchars($plan['name']) ?></div>
        <div style="font-size:1.9rem;font-weight:900;color:var(--gray-800);line-height:1.1;">
            <?= $plan['price'] > 0 ? 'R$&nbsp;'.number_format($plan['price'],2,',','.') : '<span style="color:var(--success)">Grátis</span>' ?>
        </div>
        <?php if ($plan['price'] > 0): ?>
        <div style="font-size:.72rem;color:var(--gray-400);margin-bottom:16px;">por mês</div>
        <?php else: ?>
        <div style="font-size:.72rem;color:var(--gray-400);margin-bottom:16px;">para sempre</div>
        <?php endif; ?>

        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;">
            <?php foreach ($features as $feat): ?>
            <div style="display:flex;align-items:flex-start;gap:8px;font-size:.82rem;color:var(--gray-600);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="3" style="flex-shrink:0;margin-top:1px;"><polyline points="20 6 9 17 4 12"/></svg>
                <?= htmlspecialchars($feat) ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Limits -->
        <div style="background:var(--gray-50);border-radius:8px;padding:10px 12px;font-size:.76rem;display:flex;flex-direction:column;gap:5px;">
            <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Propostas</span><strong><?= $plan['max_proposals'] >= 9999 ? 'Ilimitado' : $plan['max_proposals'] ?></strong></div>
            <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Clientes</span><strong><?= $plan['max_clients'] >= 9999 ? 'Ilimitado' : $plan['max_clients'] ?></strong></div>
            <div style="display:flex;justify-content:space-between;"><span style="color:var(--gray-500);">Produtos</span><strong><?= $plan['max_products'] >= 9999 ? 'Ilimitado' : $plan['max_products'] ?></strong></div>
        </div>

        <div style="margin-top:14px;">
        <?php if ($isCurrent): ?>
            <div class="btn btn-outline btn-sm" style="width:100%;text-align:center;pointer-events:none;opacity:.5;">Plano Atual</div>
        <?php else: ?>
            <a href="mailto:suporte@propostaservices.com?subject=Upgrade para <?= urlencode($plan['name']) ?>" class="btn <?= $plan['slug']==='pro'?'btn-primary':'btn-outline' ?> btn-sm" style="width:100%;text-align:center;">
                <?= $plan['price'] > 0 ? 'Assinar '.$plan['name'] : 'Usar Grátis' ?>
            </a>
        <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- FAQ -->
<div class="card">
    <div class="card-header"><div class="card-title">Dúvidas Frequentes</div></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
            <div>
                <div style="font-weight:700;font-size:.88rem;margin-bottom:5px;color:var(--gray-800);">Como funciona o upgrade?</div>
                <p style="font-size:.82rem;color:var(--gray-500);">Entre em contato com o suporte. O upgrade é feito manualmente pelo administrador da plataforma com ativação imediata.</p>
            </div>
            <div>
                <div style="font-weight:700;font-size:.88rem;margin-bottom:5px;color:var(--gray-800);">Posso cancelar a qualquer momento?</div>
                <p style="font-size:.82rem;color:var(--gray-500);">Sim. Você pode cancelar sua assinatura a qualquer momento. O acesso ao plano pago se mantém até o fim do período pago.</p>
            </div>
            <div>
                <div style="font-weight:700;font-size:.88rem;margin-bottom:5px;color:var(--gray-800);">Meus dados são seguros?</div>
                <p style="font-size:.82rem;color:var(--gray-500);">Sim. Todos os dados são armazenados com segurança e isolados por usuário. Backups automáticos diários.</p>
            </div>
            <div>
                <div style="font-weight:700;font-size:.88rem;margin-bottom:5px;color:var(--gray-800);">Qual forma de pagamento é aceita?</div>
                <p style="font-size:.82rem;color:var(--gray-500);">No momento, o pagamento é processado manualmente via PIX, boleto ou transferência. Em breve, cartão de crédito.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout_footer.php'; ?>
