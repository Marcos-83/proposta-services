<?php
// app/views/proposals/index.php
$pageTitle = 'Propostas';
require_once __DIR__ . '/../layout_header.php';

$statusLabels = [
    'rascunho'  => 'Rascunho',
    'enviado'   => 'Enviado',
    'aprovado'  => 'Aprovado',
    'reprovado' => 'Reprovado',
];
?>

<div class="page-header">
    <h1>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;color:var(--primary)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Propostas
    </h1>
    <a href="proposals.php?action=create" class="btn btn-primary"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Nova Proposta</a>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="proposals.php" class="filter-bar">
        <div class="form-group" style="min-width:180px;">
            <label>Cliente</label>
            <select name="client_id" class="form-control">
                <option value="">Todos os clientes</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($_GET['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= sanitize($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="min-width:140px;">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($statusLabels as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= ($_GET['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="min-width:150px;">
            <label>Follow-up</label>
            <input type="date" name="followup_date" class="form-control"
                value="<?= !empty($_GET['followup_date']) ? (strlen($_GET['followup_date']) === 10 && strpos($_GET['followup_date'], '/') === false ? $_GET['followup_date'] : date('Y-m-d', strtotime(str_replace('/', '-', $_GET['followup_date'])))) : '' ?>">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-primary btn-sm">🔍 Filtrar</button>
            <a href="proposals.php" class="btn btn-outline btn-sm">✕ Limpar</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <?php if (empty($proposals)): ?>
            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <p>Nenhuma proposta encontrada.<br>
                <a href="proposals.php?action=create">Criar primeira proposta</a></p>
            </div>
        <?php else: ?>
            <?php
            // Load user once for WhatsApp template (outside the loop for performance)
            $currentUser = UserModel::findById(auth()['id']);
            $waTemplate  = $currentUser['whatsapp_template'] ?? '';
            ?>
            <div class="table-wrap">
                <table class="table-responsive-cards">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título / Cliente</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Follow-up</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proposals as $p): ?>
                            <tr>
                                <td data-label="#"><strong style="color:var(--primary);">#<?= $p['proposal_number'] ?></strong></td>
                                <td data-label="Proposta">
                                    <div style="font-weight:600;"><?= sanitize($p['title']) ?></div>
                                    <div style="font-size:.75rem;color:var(--gray-500);">👤 <?= sanitize($p['client_name']) ?></div>
                                </td>
                                <td data-label="Valor"><strong><?= formatCurrency((float)$p['total_value']) ?></strong></td>
                                <td data-label="Status"><span class="badge badge-<?= $p['status'] ?>"><?= $statusLabels[$p['status']] ?></span></td>
                                <td data-label="Data"><?= formatDate($p['proposal_date']) ?></td>
                                <td data-label="Follow-up"><?= formatDate($p['followup_date']) ?></td>
                                <td data-label="Ações">
                                    <div class="actions">
                                        <a href="proposals.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" title="Editar"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>

                                        <!-- Change status inline -->
                                        <form method="POST" action="proposals.php" style="display:inline;">
                                            <input type="hidden" name="action" value="updateStatus">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                            <select name="status" class="form-control status-select"
                                                onchange="this.form.submit()">
                                                <?php foreach ($statusLabels as $val => $lbl): ?>
                                                    <option value="<?= $val ?>" <?= $p['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>

                                        <!-- WhatsApp -->
                                        <?php if (!empty($p['client_phone'])): ?>
                                            <?php
                                            $phone = preg_replace('/\D/', '', $p['client_phone']);
                                            $publicLink = APP_URL . '/proposta/' . $p['public_token'];
                                            $msg = str_replace(
                                                ['#PROPOSTA#','#TITULO#','#CLIENTE#','#VALOR#','#LINK#'],
                                                [$p['proposal_number'], $p['title'], $p['client_name'], formatCurrency((float)$p['total_value']), $publicLink],
                                                $waTemplate
                                            );
                                            $waUrl = 'https://wa.me/55' . $phone . '?text=' . rawurlencode($msg);
                                            ?>
                                            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-whatsapp btn-sm btn-icon" title="WhatsApp"><svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                                        <?php endif; ?>

                                        <!-- Link público -->
                                        <a href="<?= APP_URL ?>/proposta/<?= $p['public_token'] ?>" target="_blank" class="btn btn-outline btn-sm btn-icon" title="Link público"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a>

                                        <!-- Delete -->
                                        <form method="POST" action="proposals.php" class="confirm-delete" style="display:inline;">
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
