<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layout_header.php';

$labels      = json_encode(array_column($chartData, 'month_label'));
$values      = json_encode(array_map('floatval', array_column($chartData, 'total')));
$counts      = json_encode(array_map('intval',   array_column($chartData, 'count')));
?>

<div class="page-header">
    <h1>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;color:var(--primary)"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Painel de Controle
    </h1>
    <a href="proposals.php?action=create" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nova Proposta
    </a>
</div>

<!-- KPI Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card-blue">
        <div class="stat-icon-wrap" style="background:rgba(26,107,204,.12);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a6bcc" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-label">Total de Clientes</div>
        <div class="stat-value"><?= $totalClients ?></div>
        <div class="stat-trend"><a href="clients.php" style="font-size:.72rem;color:var(--primary);text-decoration:none;">Ver todos →</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(244,123,32,.12);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f47b20" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="stat-label">Total de Propostas</div>
        <div class="stat-value"><?= $totalProposals ?></div>
        <div class="stat-trend"><a href="proposals.php" style="font-size:.72rem;color:var(--accent);text-decoration:none;">Ver todas →</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(34,197,94,.12);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-label">Propostas Aprovadas</div>
        <div class="stat-value"><?= $totalApproved ?></div>
        <div class="stat-trend"><span style="font-size:.72rem;color:var(--gray-400);"><?= $totalProposals > 0 ? round($totalApproved/$totalProposals*100) : 0 ?>% do total</span></div>
    </div>
    <div class="stat-card stat-card-green">
        <div class="stat-icon-wrap" style="background:rgba(34,197,94,.12);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-label">Valor Aprovado</div>
        <div class="stat-value stat-value-sm"><?= formatCurrency($valueApproved) ?></div>
        <div class="stat-trend"><span style="font-size:.72rem;color:var(--gray-400);">acumulado</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(245,158,11,.12);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-label">Aguardando Resposta</div>
        <div class="stat-value"><?= $totalPending ?></div>
        <div class="stat-trend"><span style="font-size:.72rem;color:var(--warning);">enviadas</span></div>
    </div>
</div>

<!-- Chart + Follow-ups -->
<div class="grid-2" style="align-items:start;">

    <!-- Professional Chart Card -->
    <div class="card" style="overflow:visible;">
        <div class="card-header" style="border-bottom:1px solid var(--gray-100);padding-bottom:14px;">
            <div>
                <div class="card-title">Desempenho — Últimos 6 meses</div>
                <div style="font-size:.72rem;color:var(--gray-400);margin-top:2px;">Receita aprovada e volume de propostas</div>
            </div>
            <div style="display:flex;gap:20px;align-items:center;">
                <div style="text-align:right;">
                    <div style="font-size:1.15rem;font-weight:800;color:var(--gray-800);" id="chartTotalVal">—</div>
                    <div style="font-size:.63rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;">receita total</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:1.15rem;font-weight:800;color:var(--accent);" id="chartTotalCount">—</div>
                    <div style="font-size:.63rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;">propostas</div>
                </div>
            </div>
        </div>
        <!-- Legend -->
        <div style="display:flex;gap:18px;padding:10px 20px 0;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:6px;font-size:.71rem;font-weight:600;color:var(--gray-500);">
                <span style="width:14px;height:14px;background:rgba(26,107,204,.8);border-radius:3px;display:inline-block;"></span>Receita aprovada
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.71rem;font-weight:600;color:var(--gray-500);">
                <span style="width:22px;height:0;border-top:2.5px solid #f47b20;display:inline-block;border-radius:2px;"></span>Nº propostas
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.71rem;font-weight:600;color:var(--gray-500);">
                <span style="width:22px;height:0;border-top:2px dashed #22c55e;display:inline-block;"></span>Tendência
            </div>
        </div>
        <div class="card-body" style="padding:12px 20px 18px;">
            <div style="position:relative;height:250px;">
                <canvas id="chartProposals"></canvas>
            </div>
        </div>
    </div>

    <!-- Follow-up today -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Follow-ups de Hoje</div>
                <div style="font-size:.72rem;color:var(--gray-400);margin-top:2px;"><?= date('d/m/Y') ?></div>
            </div>
            <span class="followup-count-badge"><?= count($followupsToday) ?></span>
        </div>
        <div class="card-body" style="padding:8px 20px 16px;">
            <?php if (empty($followupsToday)): ?>
                <div class="empty-state" style="padding:28px 0;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="color:var(--gray-200);margin-bottom:8px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p style="font-size:.83rem;">Sem follow-ups agendados para hoje!</p>
                </div>
            <?php else: ?>
                <?php foreach ($followupsToday as $fp): ?>
                <div class="followup-item">
                    <div class="followup-dot badge-<?= $fp['status'] ?>"></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:.83rem;color:var(--gray-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            #<?= $fp['proposal_number'] ?> — <?= htmlspecialchars($fp['title']) ?>
                        </div>
                        <div style="font-size:.74rem;color:var(--gray-500);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <span><?= htmlspecialchars($fp['client_name']) ?></span>
                            <span class="badge badge-<?= $fp['status'] ?>"><?= ucfirst($fp['status']) ?></span>
                            <span style="font-weight:600;color:var(--gray-700);"><?= formatCurrency((float)$fp['total_value']) ?></span>
                        </div>
                    </div>
                    <?php if (!empty($fp['client_phone'])): ?>
                        <?php $phone = preg_replace('/\D/', '', $fp['client_phone']); ?>
                        <a href="https://wa.me/55<?= $phone ?>" target="_blank" class="btn btn-whatsapp btn-sm btn-icon" title="WhatsApp">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function() {
    let labels = <?= $labels ?>;
    let values = <?= $values ?>;
    let counts = <?= $counts ?? '[]' ?>;

    // Pad to 6 months
    const now = new Date();
    const needed = [];
    for (let i = 5; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const lbl = d.toLocaleDateString('pt-BR', {month:'short', year:'2-digit'});
        needed.push(lbl.charAt(0).toUpperCase() + lbl.slice(1));
    }
    while (labels.length < 6) { labels.unshift(needed[5 - labels.length] || ''); values.unshift(0); counts.unshift(0); }
    if (labels.length > 6) { labels = labels.slice(-6); values = values.slice(-6); counts = counts.slice(-6); }

    // KPI totals
    const totalRev   = values.reduce((a,b) => a+b, 0);
    const totalCount = counts.reduce((a,b) => a+b, 0);
    document.getElementById('chartTotalVal').textContent   = 'R$ ' + totalRev.toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    document.getElementById('chartTotalCount').textContent = totalCount;

    // Trend line (linear regression)
    function trendLine(data) {
        const n = data.length;
        if (n < 2) return data.map(() => 0);
        let sx=0,sy=0,sxy=0,sx2=0;
        data.forEach((y,x) => { sx+=x; sy+=y; sxy+=x*y; sx2+=x*x; });
        const slope = (n*sxy - sx*sy) / (n*sx2 - sx*sx);
        const intercept = (sy - slope*sx) / n;
        return data.map((_,x) => Math.max(0, slope*x + intercept));
    }
    const trend = trendLine(values);

    // Bar colors — current month highlighted
    const barBg     = values.map((_, i) => i === values.length-1 ? 'rgba(26,107,204,0.92)' : 'rgba(26,107,204,0.55)');
    const barHover  = values.map((_, i) => i === values.length-1 ? 'rgba(26,107,204,1)'    : 'rgba(26,107,204,0.80)');

    const ctx = document.getElementById('chartProposals').getContext('2d');

    // Gradient fill under trend
    const trendGrad = ctx.createLinearGradient(0, 0, 0, 250);
    trendGrad.addColorStop(0, 'rgba(34,197,94,0.08)');
    trendGrad.addColorStop(1, 'rgba(34,197,94,0)');

    new Chart(ctx, {
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Receita Aprovada',
                    data: values,
                    backgroundColor: barBg,
                    hoverBackgroundColor: barHover,
                    borderRadius: 7,
                    borderSkipped: false,
                    barPercentage: 0.58,
                    categoryPercentage: 0.72,
                    order: 3,
                    yAxisID: 'yRev',
                },
                {
                    type: 'line',
                    label: 'Nº Propostas',
                    data: counts,
                    borderColor: '#f47b20',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#f47b20',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#f47b20',
                    fill: false,
                    tension: 0.38,
                    order: 1,
                    yAxisID: 'yCount',
                },
                {
                    type: 'line',
                    label: 'Tendência',
                    data: trend,
                    borderColor: '#22c55e',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    fill: true,
                    backgroundColor: trendGrad,
                    tension: 0.4,
                    order: 2,
                    yAxisID: 'yRev',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1000, easing: 'easeOutQuart' },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#fff',
                    padding: 14,
                    cornerRadius: 10,
                    titleFont: { size: 11, weight: '500' },
                    bodyFont: { size: 12, weight: '600' },
                    displayColors: true,
                    boxWidth: 10, boxHeight: 10,
                    callbacks: {
                        title: items => '  ' + items[0].label,
                        label: item => {
                            const v = item.raw;
                            const fmt = n => 'R$ ' + n.toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g,'.');
                            if (item.datasetIndex === 0) return '  Receita: ' + fmt(v);
                            if (item.datasetIndex === 1) return '  Propostas: ' + v;
                            return '  Tendência: ' + fmt(v);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8', padding: 6 }
                },
                yRev: {
                    type: 'linear', position: 'left', beginAtZero: true,
                    grid: { color: 'rgba(226,232,240,0.7)', drawTicks: false },
                    border: { display: false, dash: [4,4] },
                    ticks: {
                        font: { size: 10 }, color: '#94a3b8', padding: 8, maxTicksLimit: 5,
                        callback: v => { if(v>=1000000) return 'R$'+(v/1000000).toFixed(1)+'M'; if(v>=1000) return 'R$'+(v/1000).toFixed(0)+'k'; return v>0?'R$'+v.toFixed(0):'0'; }
                    }
                },
                yCount: {
                    type: 'linear', position: 'right', beginAtZero: true,
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 10 }, color: '#f47b20', padding: 8, maxTicksLimit: 5,
                        stepSize: 1, callback: v => Number.isInteger(v) ? v : null
                    }
                }
            },
            layout: { padding: { top: 10, left: 4, right: 8, bottom: 4 } }
        }
    });
})();
</script>

<?php require_once __DIR__ . '/../layout_footer.php'; ?>
