<?php require_once __DIR__ . '/layout_header.php'; ?>
<?php date_default_timezone_set('America/Sao_Paulo')?>
<div class="page-hdr">
    <div>
        <h1>Dashboard</h1>
        <div class="page-hdr-sub">Visão geral da plataforma · <?= date('d/m/Y H:i') ?></div>
    </div>

</div>

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card" style="border-top:3px solid #3b82f6;">
        <div class="kpi-label">Total Usuários</div>
        <div class="kpi-value"><?= $kpis['total_users'] ?></div>
        <div class="kpi-sub" style="color:#22c55e;">▲ <?= $kpis['active_users'] ?> ativos</div>
    </div>
    <div class="kpi-card" style="border-top:3px solid #ef4444;">
        <div class="kpi-label">Bloqueados</div>
        <div class="kpi-value" style="color:#ef4444;"><?= $kpis['blocked_users'] ?></div>
        <div class="kpi-sub">usuários suspensos</div>
    </div>
    <div class="kpi-card" style="border-top:3px solid #f47b20;">
        <div class="kpi-label">Propostas (plataforma)</div>
        <div class="kpi-value"><?= $kpis['total_proposals'] ?></div>
        <div class="kpi-sub" style="color:#22c55e;"><?= $kpis['approved_proposals'] ?> aprovadas</div>
    </div>
    <div class="kpi-card" style="border-top:3px solid #22c55e;">
        <div class="kpi-label">Receita (plataforma)</div>
        <div class="kpi-value" style="font-size:1rem;color:#22c55e;"><?= formatCurrency($kpis['total_revenue']) ?></div>
        <div class="kpi-sub">soma de todos os usuários</div>
    </div>
    <div class="kpi-card" style="border-top:3px solid #a855f7;">
        <div class="kpi-label">Clientes</div>
        <div class="kpi-value"><?= $kpis['total_clients'] ?></div>
        <div class="kpi-sub">no sistema</div>
    </div>
    <div class="kpi-card" style="border-top:3px solid #64748b;">
        <div class="kpi-label">Produtos</div>
        <div class="kpi-value"><?= $kpis['total_products'] ?></div>
        <div class="kpi-sub">cadastrados</div>
    </div>
</div>

<!-- Main Chart — full width -->
<div class="adm-card" style="margin-bottom:20px;">
    <div class="adm-card-header" style="padding:18px 22px 14px;">
        <div>
            <div class="adm-card-title" style="font-size:.95rem;">Receita & Volume de Propostas — Plataforma Inteira</div>
            <div style="font-size:.72rem;color:var(--muted);margin-top:3px;">
                Consolidado de <strong style="color:var(--dim);">todos os usuários</strong> cadastrados nos últimos 6 meses
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
            <div style="text-align:right;">
                <div style="font-size:1.1rem;font-weight:800;color:#3b82f6;" id="admTotalRev">—</div>
                <div style="font-size:.6rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;">receita total</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:1.1rem;font-weight:800;color:#f47b20;" id="admTotalProp">—</div>
                <div style="font-size:.6rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;">propostas</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:1.1rem;font-weight:800;color:#22c55e;" id="admTotalApproved">—</div>
                <div style="font-size:.6rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;">aprovadas</div>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:18px;padding:0 22px 10px;flex-wrap:wrap;">
        <span style="display:flex;align-items:center;gap:6px;font-size:.7rem;font-weight:600;color:var(--muted);">
            <span style="width:13px;height:13px;background:rgba(59,130,246,.8);border-radius:3px;display:inline-block;"></span>Receita aprovada (soma dos usuários)
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:.7rem;font-weight:600;color:var(--muted);">
            <span style="width:20px;height:0;border-top:2.5px solid #f47b20;display:inline-block;"></span>Total de propostas criadas
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:.7rem;font-weight:600;color:var(--muted);">
            <span style="width:20px;height:0;border-top:2px dashed #22c55e;display:inline-block;"></span>Tendência de receita
        </span>
    </div>
    <div style="padding:0 18px 20px;">
        <div style="position:relative;height:280px;">
            <canvas id="chartRevenue"></canvas>
        </div>
    </div>
</div>

<!-- Row 2: users chart + metrics -->
<div class="adm-grid2" style="margin-bottom:20px;">
    <div class="adm-card">
        <div class="adm-card-header">
            <div>
                <div class="adm-card-title">Novos Cadastros</div>
                <div style="font-size:.7rem;color:var(--muted);">Usuários registrados por mês</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:1.1rem;font-weight:800;color:#a855f7;" id="admTotalUsers">—</div>
                <div style="font-size:.6rem;color:var(--muted);">no período</div>
            </div>
        </div>
        <div style="padding:8px 18px 18px;">
            <div style="position:relative;height:200px;">
                <canvas id="chartUsers"></canvas>
            </div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-title">Métricas de Conversão</div></div>
        <div class="adm-card-body">
            <?php
            $total    = $kpis['total_proposals'];
            $approved = $kpis['approved_proposals'];
            $rate     = $total > 0 ? round($approved / $total * 100) : 0;
            $propPerUser = $kpis['total_users'] > 0 ? round($total / $kpis['total_users'], 1) : 0;
            $activeRate  = $kpis['total_users'] > 0 ? round($kpis['active_users'] / $kpis['total_users'] * 100) : 0;
            $revPerApproved = $approved > 0 ? $kpis['total_revenue'] / $approved : 0;
            $metrics = [
                ['Taxa de Aprovação',     $rate.'%',                          '#22c55e', $rate],
                ['Propostas / Usuário',   $propPerUser,                       '#3b82f6', min(100,$propPerUser*10)],
                ['Usuários Ativos',       $activeRate.'%',                    '#f47b20', $activeRate],
                ['Receita / Proposta',    formatCurrency($revPerApproved),    '#a855f7', 60],
            ];
            ?>
            <?php foreach ($metrics as [$label, $val, $color, $pct]): ?>
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-size:.78rem;color:var(--dim);"><?= $label ?></span>
                    <span style="font-size:.88rem;font-weight:700;color:<?= $color ?>;"><?= $val ?></span>
                </div>
                <div style="height:5px;background:var(--surface2);border-radius:10px;overflow:hidden;">
                    <div style="height:100%;width:<?= min(100,max(0,(int)$pct)) ?>%;background:<?= $color ?>;border-radius:10px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent users -->
<div class="adm-card" style="margin-bottom:24px;">
    <div class="adm-card-header">
        <div class="adm-card-title">Usuários Recentes</div>
        <a href="<?= APP_URL ?>/admin/users.php" class="btn btn-outline btn-sm">Ver todos →</a>
    </div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>Nome</th><th>Empresa</th><th>Email</th><th>Plano</th><th>Status</th><th>Propostas</th><th>Receita</th><th></th></tr></thead>
            <tbody>
            <?php foreach (array_slice(AdminModel::allUsers([]), 0, 6) as $u): ?>
            <tr>
                <td style="color:var(--text);font-weight:600;"><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['company_name']) ?></td>
                <td style="color:var(--muted);"><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge badge-<?= $u['plan']??'free' ?>"><?= ucfirst($u['plan']??'free') ?></span></td>
                <td><span class="badge badge-<?= $u['status']??'ativo' ?>"><?= ucfirst($u['status']??'ativo') ?></span></td>
                <td style="text-align:center;font-weight:600;color:var(--text);"><?= $u['total_proposals'] ?></td>
                <td style="color:#22c55e;font-weight:600;"><?= formatCurrency((float)$u['revenue']) ?></td>
                <td><a href="<?= APP_URL ?>/admin/user_detail.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-xs">Gerenciar</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function() {
    const revRaw = <?= json_encode(array_values($revenue)) ?>;
    const usrRaw = <?= json_encode(array_values($newUsers)) ?>;

    // Pad to 6 months
    const now = new Date();
    const months = [];
    for (let i = 5; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        months.push(d.toLocaleDateString('pt-BR',{month:'short',year:'2-digit'}).replace(/^\w/,c=>c.toUpperCase()));
    }
    function pad(raw, lk, vk) {
        const map = {};
        raw.forEach(r => { map[r[lk]] = parseFloat(r[vk]) || parseInt(r[vk]) || 0; });
        return months.map(m => map[m] || 0);
    }

    const revVals  = pad(revRaw, 'label', 'revenue');
    const propVals = pad(revRaw, 'label', 'total_proposals');
    const apprVals = pad(revRaw, 'label', 'approved');
    const usrVals  = pad(usrRaw, 'label', 'total');

    const fmtCur = v => 'R$ ' + v.toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    document.getElementById('admTotalRev').textContent      = fmtCur(revVals.reduce((a,b)=>a+b,0));
    document.getElementById('admTotalProp').textContent     = propVals.reduce((a,b)=>a+b,0);
    document.getElementById('admTotalApproved').textContent = apprVals.reduce((a,b)=>a+b,0);
    document.getElementById('admTotalUsers').textContent    = usrVals.reduce((a,b)=>a+b,0);

    // Linear regression trend
    function trend(data) {
        const n = data.length;
        let sx=0,sy=0,sxy=0,sx2=0;
        data.forEach((y,x)=>{ sx+=x; sy+=y; sxy+=x*y; sx2+=x*x; });
        const s = (n*sxy - sx*sy) / (n*sx2 - sx*sx || 1);
        const b = (sy - s*sx) / n;
        return data.map((_,x) => Math.max(0, s*x + b));
    }
    const revTrend = trend(revVals);
    const usrTrend = trend(usrVals);

    // Bar highlight: last month darker
    const barBg    = revVals.map((_,i) => i===revVals.length-1 ? 'rgba(59,130,246,0.92)' : 'rgba(59,130,246,0.50)');
    const barHover = revVals.map((_,i) => i===revVals.length-1 ? 'rgba(59,130,246,1)'    : 'rgba(59,130,246,0.78)');

    // ── Revenue + Proposals chart ──────────────────────────────────────────
    const ctx1 = document.getElementById('chartRevenue').getContext('2d');

    const trendGrad = ctx1.createLinearGradient(0, 0, 0, 280);
    trendGrad.addColorStop(0, 'rgba(34,197,94,0.12)');
    trendGrad.addColorStop(1, 'rgba(34,197,94,0)');

    new Chart(ctx1, {
        data: {
            labels: months,
            datasets: [
                {
                    type: 'bar',
                    label: 'Receita',
                    data: revVals,
                    backgroundColor: barBg,
                    hoverBackgroundColor: barHover,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.52,
                    categoryPercentage: 0.68,
                    yAxisID: 'yRev',
                    order: 3,
                },
                {
                    type: 'line',
                    label: 'Propostas',
                    data: propVals,
                    borderColor: '#f47b20',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#f47b20',
                    pointBorderColor: '#0d1526',
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    fill: false,
                    tension: 0.38,
                    yAxisID: 'yProp',
                    order: 1,
                },
                {
                    type: 'line',
                    label: 'Tendência',
                    data: revTrend,
                    borderColor: '#22c55e',
                    borderWidth: 2,
                    borderDash: [7, 4],
                    pointRadius: 0,
                    pointHoverRadius: 0,
                    fill: true,
                    backgroundColor: trendGrad,
                    tension: 0.45,
                    yAxisID: 'yRev',
                    order: 2,
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
                    backgroundColor: '#0f172a',
                    titleColor: '#64748b',
                    bodyColor: '#e2e8f0',
                    padding: 14,
                    cornerRadius: 10,
                    borderColor: 'rgba(255,255,255,.08)',
                    borderWidth: 1,
                    displayColors: true,
                    boxWidth: 10, boxHeight: 10,
                    callbacks: {
                        title: i => '  ' + i[0].label,
                        label: i => {
                            if (i.datasetIndex === 0) return '  Receita: ' + fmtCur(i.raw);
                            if (i.datasetIndex === 1) return '  Propostas: ' + i.raw;
                            return '  Tendência: ' + fmtCur(i.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#475569', font: { size: 11, weight: '600' }, padding: 6 }
                },
                yRev: {
                    type: 'linear', position: 'left', beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,.05)', drawTicks: false },
                    border: { display: false },
                    ticks: {
                        color: '#475569', font: { size: 10 }, padding: 10, maxTicksLimit: 5,
                        callback: v => v>=1000000 ? 'R$'+(v/1000000).toFixed(1)+'M' : v>=1000 ? 'R$'+(v/1000).toFixed(0)+'k' : v>0 ? 'R$'+v.toFixed(0) : '0'
                    }
                },
                yProp: {
                    type: 'linear', position: 'right', beginAtZero: true,
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: '#f47b20', font: { size: 10 }, padding: 10, maxTicksLimit: 5,
                        stepSize: 1, callback: v => Number.isInteger(v) ? v : null
                    }
                }
            },
            layout: { padding: { top: 8, right: 8 } }
        }
    });

    // ── New users area chart ───────────────────────────────────────────────
    const ctx2 = document.getElementById('chartUsers').getContext('2d');

    const usrGrad = ctx2.createLinearGradient(0, 0, 0, 200);
    usrGrad.addColorStop(0, 'rgba(168,85,247,0.28)');
    usrGrad.addColorStop(1, 'rgba(168,85,247,0)');

    const usrTrGrad = ctx2.createLinearGradient(0, 0, 0, 200);
    usrTrGrad.addColorStop(0, 'rgba(34,197,94,0.07)');
    usrTrGrad.addColorStop(1, 'rgba(34,197,94,0)');

    new Chart(ctx2, {
        data: {
            labels: months,
            datasets: [
                {
                    type: 'line',
                    label: 'Novos usuários',
                    data: usrVals,
                    borderColor: '#a855f7',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#0d1526',
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    fill: true,
                    backgroundColor: usrGrad,
                    tension: 0.42,
                    order: 1,
                },
                {
                    type: 'line',
                    label: 'Tendência',
                    data: usrTrend,
                    borderColor: '#22c55e',
                    borderWidth: 1.5,
                    borderDash: [6, 4],
                    pointRadius: 0,
                    fill: true,
                    backgroundColor: usrTrGrad,
                    tension: 0.4,
                    order: 2,
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
                    backgroundColor: '#0f172a',
                    titleColor: '#64748b',
                    bodyColor: '#e2e8f0',
                    padding: 12,
                    cornerRadius: 10,
                    borderColor: 'rgba(255,255,255,.08)',
                    borderWidth: 1,
                    callbacks: {
                        title: i => '  ' + i[0].label,
                        label: i => i.datasetIndex===0 ? '  Novos usuários: '+i.raw : '  Tendência: '+i.raw.toFixed(1)
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#475569', font: { size: 11, weight: '600' }, padding: 6 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,.05)', drawTicks: false },
                    border: { display: false },
                    ticks: {
                        color: '#475569', font: { size: 10 }, padding: 8, maxTicksLimit: 5,
                        stepSize: 1, callback: v => Number.isInteger(v) ? v : null
                    }
                }
            },
            layout: { padding: { top: 8, right: 8 } }
        }
    });
})();
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
