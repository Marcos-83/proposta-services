<?php require_once __DIR__ . '/layout_header.php'; ?>

<div class="page-hdr">
    <div>
        <h1><?= htmlspecialchars($user['name']) ?></h1>
        <div class="page-hdr-sub"><?= htmlspecialchars($user['company_name']) ?> · <?= htmlspecialchars($user['email']) ?></div>
    </div>
    <a href="<?= APP_URL ?>/admin/users.php" class="btn btn-outline btn-sm">← Voltar</a>
</div>

<div class="adm-grid2">
    <!-- Left: Actions -->
    <div>
        <!-- Status & Plan -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">Status & Plano</div>
                <div>
                    <span class="badge badge-<?= $user['status']??'ativo' ?>"><?= ucfirst($user['status']??'ativo') ?></span>
                    <span class="badge badge-<?= $user['plan']??'free' ?>" style="margin-left:4px;"><?= ucfirst($user['plan']??'free') ?></span>
                </div>
            </div>
            <div class="adm-card-body">
                <?php if (($user['status']??'ativo') === 'ativo'): ?>
                <form method="POST" action="<?= APP_URL ?>/admin/user_action.php" onsubmit="return confirm('Bloquear este usuário?')">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="block">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div class="form-group">
                        <label>Motivo do bloqueio</label>
                        <input type="text" name="reason" class="form-control" placeholder="Ex: inadimplência, violação de termos...">
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">🚫 Bloquear Usuário</button>
                </form>
                <?php else: ?>
                <p style="font-size:.8rem;color:var(--muted);margin-bottom:10px;">
                    Bloqueado em: <?= formatDate($user['blocked_at']??null) ?><br>
                    Motivo: <?= htmlspecialchars($user['blocked_reason']??'-') ?>
                </p>
                <form method="POST" action="<?= APP_URL ?>/admin/user_action.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="unblock">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn btn-success btn-sm" style="width:100%;">✅ Desbloquear Usuário</button>
                </form>
                <?php endif; ?>

                <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">

                <form method="POST" action="<?= APP_URL ?>/admin/user_action.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="update_plan">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div class="form-group">
                        <label>Alterar Plano</label>
                        <select name="plan" class="form-control">
                            <?php foreach(['free','starter','pro','enterprise'] as $p): ?>
                            <option value="<?= $p ?>" <?= ($user['plan']??'free')===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Atualizar Plano</button>
                </form>
            </div>
        </div>

        <!-- Edit user data -->
        <div class="adm-card">
            <div class="adm-card-header"><div class="adm-card-title">Editar Dados</div></div>
            <div class="adm-card-body">
                <form method="POST" action="<?= APP_URL ?>/admin/user_action.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Empresa</label>
                        <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($user['company_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($user['whatsapp']??'') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Salvar Dados</button>
                </form>
            </div>
        </div>

        <!-- Reset password -->
        <div class="adm-card">
            <div class="adm-card-header"><div class="adm-card-title">Redefinir Senha</div></div>
            <div class="adm-card-body">
                <form method="POST" action="<?= APP_URL ?>/admin/user_action.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type="text" name="new_password" class="form-control" placeholder="Mínimo 6 caracteres">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm" style="width:100%;">Redefinir Senha</button>
                </form>
            </div>
        </div>

        <!-- Delete -->
        <div class="adm-card" style="border-color:rgba(239,68,68,.2);">
            <div class="adm-card-header" style="border-color:rgba(239,68,68,.2);"><div class="adm-card-title" style="color:#ef4444;">Zona de Perigo</div></div>
            <div class="adm-card-body">
                <form method="POST" action="<?= APP_URL ?>/admin/user_action.php" onsubmit="return confirm('ATENÇÃO: Isso vai excluir o usuário e todos os seus dados permanentemente. Confirmar?')">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <p style="font-size:.78rem;color:var(--muted);margin-bottom:12px;">Esta ação é irreversível. Todos os clientes, produtos e propostas deste usuário serão excluídos.</p>
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">🗑️ Excluir Usuário Permanentemente</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Proposals -->
    <div>
        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">Últimas Propostas</div>
                <span style="font-size:.75rem;color:var(--muted);"><?= count($proposals) ?> propostas</span>
            </div>
            <?php if (empty($proposals)): ?>
                <div class="adm-card-body" style="text-align:center;color:var(--muted);font-size:.82rem;padding:30px;">Nenhuma proposta ainda.</div>
            <?php else: ?>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th>Nº</th><th>Título</th><th>Cliente</th><th>Valor</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($proposals as $p): ?>
                    <tr>
                        <td style="color:var(--muted);">#<?= $p['proposal_number'] ?></td>
                        <td style="color:var(--text);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($p['title']) ?></td>
                        <td><?= htmlspecialchars($p['client_name']) ?></td>
                        <td style="color:#22c55e;font-weight:600;"><?= formatCurrency((float)$p['total_value']) ?></td>
                        <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- User info summary -->
        <div class="adm-card">
            <div class="adm-card-header"><div class="adm-card-title">Informações da Conta</div></div>
            <div class="adm-card-body">
                <table style="width:100%;font-size:.82rem;border-collapse:collapse;">
                    <?php $rows = [
                        ['ID',          '#'.$user['id']],
                        ['Cadastro',    formatDate($user['created_at']??null)],
                        ['Atualização', formatDate($user['updated_at']??null)],
                        ['WhatsApp',    $user['whatsapp']??'-'],
                    ]; ?>
                    <?php foreach ($rows as [$k,$v]): ?>
                    <tr>
                        <td style="color:var(--muted);padding:6px 0;width:40%;"><?= $k ?></td>
                        <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($v) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
