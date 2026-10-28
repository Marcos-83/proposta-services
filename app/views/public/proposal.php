<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta #<?= $proposal['proposal_number'] ?> — <?= sanitize($proposal['company_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
    <style>
    .pub-container { max-width: 820px; margin: 0 auto; padding: 24px 16px 60px; }
    .pub-header { background: var(--sidebar-bg); border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; display: flex; align-items: center; gap: 14px; }
    .pub-logo { width: 44px; height: 44px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pub-company { color: #fff; font-weight: 700; font-size: 1rem; }
    .pub-sub { color: var(--sidebar-text); font-size: .72rem; }
    .pub-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; margin-bottom: 20px; }
    .pub-card-head { background: linear-gradient(135deg, var(--primary) 0%, #1557b0 100%); padding: 24px 28px; }
    .pub-number { font-size: .72rem; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
    .pub-title { font-size: 1.4rem; font-weight: 700; color: #fff; margin-bottom: 4px; }
    .pub-card-body { padding: 24px 28px; }
    .pub-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .pub-meta-item label { font-size: .65rem; font-weight: 700; color: var(--gray-400); text-transform: uppercase; letter-spacing: .07em; display: block; margin-bottom: 4px; }
    .pub-meta-item strong { font-size: .92rem; color: var(--gray-800); font-weight: 600; }
    .pub-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 2px solid var(--gray-100); align-items: center; }
    .pub-actions-label { flex: 1; min-width: 180px; font-size: .85rem; color: var(--gray-500); font-weight: 500; }
    /* Signature pad */
    .sig-section { background: var(--gray-50); border: 1.5px dashed var(--gray-300); border-radius: 10px; padding: 20px; margin-top: 20px; }
    .sig-title { font-size: .85rem; font-weight: 700; color: var(--gray-700); margin-bottom: 4px; display: flex; align-items: center; gap: 7px; }
    .sig-subtitle { font-size: .75rem; color: var(--gray-400); margin-bottom: 14px; }
    #signatureCanvas { border: 2px solid var(--gray-300); border-radius: 8px; background: #fff; cursor: crosshair; width: 100%; max-width: 600px; display: block; touch-action: none; }
    .sig-signed { background: #f0fdf4; border: 1.5px solid #22c55e; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 12px; }
    .sig-date { font-size: .8rem; color: #166534; }
    /* Status banners */
    .status-banner { border-radius: 10px; padding: 14px 20px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: .9rem; }
    .status-banner.aprovado  { background: #f0fdf4; border: 1.5px solid #22c55e; color: #166534; }
    .status-banner.reprovado { background: #fef2f2; border: 1.5px solid #ef4444; color: #991b1b; }
    @media (max-width: 600px) { .pub-card-body { padding: 16px; } .pub-card-head { padding: 18px 16px; } .pub-actions { flex-direction: column; } .pub-actions button, .pub-actions a { width: 100%; justify-content: center; } }
    </style>
</head>
<body style="background:var(--gray-50);">
<div class="pub-container">

    <!-- Header -->
    <div class="pub-header">
        <div class="pub-logo">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div>
            <div class="pub-company"><?= sanitize($proposal['company_name']) ?></div>
            <div class="pub-sub">Proposta Comercial</div>
        </div>
        <div style="margin-left:auto;">
            <a href="<?= APP_URL ?>/pdf/download_public.php?token=<?= urlencode($proposal['public_token']) ?>"
               class="btn btn-outline btn-sm" style="border-color:rgba(255,255,255,.3);color:#FF0000;"
               title="Baixar PDF">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Baixar PDF
            </a>
        </div>
    </div>

    <?php $status = $proposal['status']; ?>

    <!-- Status banners -->
    <?php if ($status === 'aprovado'): ?>
    <div class="status-banner aprovado">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
        Proposta APROVADA<?= !empty($proposal['action_at']) ? ' em ' . date('d/m/Y', strtotime($proposal['action_at'])) : '' ?>
    </div>
    <?php elseif ($status === 'reprovado'): ?>
    <div class="status-banner reprovado">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        Proposta REPROVADA<?= !empty($proposal['action_at']) ? ' em ' . date('d/m/Y', strtotime($proposal['action_at'])) : '' ?>
    </div>
    <?php endif; ?>

    <!-- Main card -->
    <div class="pub-card">
        <div class="pub-card-head">
            <div class="pub-number">Proposta Comercial · Nº <?= $proposal['proposal_number'] ?></div>
            <div class="pub-title"><?= sanitize($proposal['title']) ?></div>
            <span class="badge badge-<?= $status ?>" style="margin-top:6px;"><?= ucfirst($status) ?></span>
        </div>

        <div class="pub-card-body">
            <div class="pub-meta">
                <div class="pub-meta-item"><label>Cliente</label><strong><?= sanitize($proposal['client_name']) ?></strong></div>
                <div class="pub-meta-item"><label>Data</label><strong><?= formatDate($proposal['proposal_date']) ?></strong></div>
                <div class="pub-meta-item"><label>Valor Total</label><strong style="color:var(--primary);font-size:1.05rem;"><?= formatCurrency((float)$proposal['total_value']) ?></strong></div>
                <?php if (!empty($proposal['followup_date'])): ?>
                <div class="pub-meta-item"><label>Follow-up</label><strong><?= formatDate($proposal['followup_date']) ?></strong></div>
                <?php endif; ?>
            </div>

            <!-- Items table -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Descrição</th><th style="text-align:center;">Qtd</th><th style="text-align:right;">Valor Unit.</th><th style="text-align:right;">Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= sanitize($item['description']) ?></td>
                            <td style="text-align:center;"><?= number_format((float)$item['quantity'],2,',','.') ?></td>
                            <td style="text-align:right;"><?= formatCurrency((float)$item['unit_price']) ?></td>
                            <td style="text-align:right;"><strong><?= formatCurrency((float)$item['total_price']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:700;padding:13px 14px;">Valor Total:</td>
                            <td style="font-weight:800;color:var(--primary);font-size:1.05rem;padding:13px 14px;text-align:right;"><?= formatCurrency((float)$proposal['total_value']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if (!empty($proposal['observations'])): ?>
            <div style="margin-top:20px;padding:14px 18px;background:var(--gray-50);border-radius:8px;border-left:3px solid var(--primary);">
                <div style="font-size:.68rem;font-weight:700;color:var(--gray-500);text-transform:uppercase;margin-bottom:6px;">Observações</div>
                <p style="font-size:.87rem;color:var(--gray-700);white-space:pre-line;"><?= sanitize($proposal['observations']) ?></p>
            </div>
            <?php endif; ?>

            <!-- Already signed -->
            <?php if (!empty($proposal['signed_at'])): ?>
            <div class="sig-signed" style="margin-top:20px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <div>
                    <div style="font-weight:700;color:#166534;font-size:.88rem;">Assinado digitalmente<?= !empty($proposal['signed_name']) ? ' por ' . sanitize($proposal['signed_name']) : '' ?></div>
                    <div class="sig-date">Em <?= date('d/m/Y \à\s H:i', strtotime($proposal['signed_at'])) ?><?= !empty($proposal['signed_ip']) ? ' · IP: '.$proposal['signed_ip'] : '' ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action buttons and signature -->
            <?php if (!in_array($status, ['aprovado','reprovado'])): ?>
            <div class="pub-actions">
                <div class="pub-actions-label">Qual é a sua decisão sobre esta proposta?</div>
                <button type="button" id="btnReject" class="btn btn-danger btn-public-action"
                        onclick="submitAction('reject')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Reprovar Proposta
                </button>
                <button type="button" id="btnApprove" class="btn btn-success btn-public-action"
                        onclick="showSignature()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Aprovar Proposta
                </button>
            </div>

            <!-- Signature pad (shown on approve click) -->
            <div id="signatureSection" class="sig-section" style="display:none;">
                <div class="sig-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    Assinar Digitalmente
                </div>
                <div class="sig-subtitle">Use o mouse ou dedo para assinar no campo abaixo</div>

                <div style="margin-bottom:10px;">
                    <label style="font-size:.75rem;font-weight:600;color:var(--gray-600);">Seu nome completo *</label>
                    <input type="text" id="signerName" class="form-control" placeholder="Digite seu nome completo" style="max-width:400px;margin-top:4px;">
                </div>

                <canvas id="signatureCanvas" width="600" height="160"></canvas>

                <div style="display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="clearSignature()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        Limpar
                    </button>
                    <button type="button" id="btnConfirmApprove" class="btn btn-success" onclick="confirmApprove()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Confirmar Aprovação
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('signatureSection').style.display='none'">Cancelar</button>
                    <span id="sigError" style="font-size:.78rem;color:var(--danger);display:none;"></span>
                </div>
            </div>

            <!-- Hidden forms -->
            <form id="formApprove" method="POST" action="<?= APP_URL ?>/proposta/<?= $proposal['public_token'] ?>" style="display:none;">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="sig_data" id="sig_data">
                <input type="hidden" name="sig_name" id="sig_name">
            </form>
            <form id="formReject" method="POST" action="<?= APP_URL ?>/proposta/<?= $proposal['public_token'] ?>" style="display:none;">
                <input type="hidden" name="action" value="reject">
            </form>
            <?php endif; ?>
        </div>
    </div>

    <p style="text-align:center;font-size:.72rem;color:var(--gray-400);">
        Gerado por <strong>Proposta Services</strong> · <?= sanitize($proposal['company_name']) ?>
    </p>
</div>

<script>
// ── Signature pad ──────────────────────────────────────────────────────────
const canvas  = document.getElementById('signatureCanvas');
const ctx     = canvas ? canvas.getContext('2d') : null;
let drawing   = false;
let hasStrokes= false;

function setupCanvas() {
    if (!canvas) return;
    const rect = canvas.getBoundingClientRect();
    canvas.width  = canvas.offsetWidth;
    canvas.height = 160;
    ctx.strokeStyle = '#1a1a2e';
    ctx.lineWidth   = 2.2;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';
}

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    if (e.touches) return {
        x: (e.touches[0].clientX - rect.left) * scaleX,
        y: (e.touches[0].clientY - rect.top)  * scaleX,
    };
    return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleX };
}

if (canvas) {
    setupCanvas();
    canvas.addEventListener('mousedown',  e => { drawing=true; ctx.beginPath(); const p=getPos(e); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  e => { if(!drawing) return; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); hasStrokes=true; });
    canvas.addEventListener('mouseup',    () => { drawing=false; });
    canvas.addEventListener('mouseleave', () => { drawing=false; });
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing=true; ctx.beginPath(); const p=getPos(e); ctx.moveTo(p.x,p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  e => { e.preventDefault(); if(!drawing) return; const p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); hasStrokes=true; }, {passive:false});
    canvas.addEventListener('touchend',   () => { drawing=false; });
    window.addEventListener('resize', setupCanvas);
}

function clearSignature() {
    if (!ctx) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasStrokes = false;
}

function showSignature() {
    document.getElementById('signatureSection').style.display = 'block';
    document.getElementById('signatureSection').scrollIntoView({behavior:'smooth',block:'center'});
}

function submitAction(action) {
    if (!confirm(action==='reject' ? 'Confirma a REPROVAÇÃO desta proposta?' : 'Confirma a APROVAÇÃO?')) return;
    document.getElementById('form'+action.charAt(0).toUpperCase()+action.slice(1)).submit();
}

function confirmApprove() {
    const name = document.getElementById('signerName').value.trim();
    const errEl = document.getElementById('sigError');
    errEl.style.display = 'none';

    if (!name) { errEl.textContent='Informe seu nome completo.'; errEl.style.display='inline'; return; }
    if (!hasStrokes) { errEl.textContent='Faça sua assinatura no campo acima.'; errEl.style.display='inline'; return; }

    document.getElementById('sig_data').value = canvas.toDataURL('image/png');
    document.getElementById('sig_name').value = name;
    document.getElementById('formApprove').submit();
}
</script>
</body>
</html>
