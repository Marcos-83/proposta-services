<?php
// app/views/settings/index.php
$pageTitle = 'Configurações';
require_once __DIR__ . '/../layout_header.php';
?>

<div class="page-header">
    <h1>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;color:var(--primary)"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
    Configurações
</h1>
</div>

<div style="display:flex;flex-direction:column;gap:22px;max-width:680px;">

    <!-- Profile -->
    <div class="card" id="perfil">
        <div class="card-header">
            <span class="card-title"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Dados da Conta</span>
        </div>
        <div class="card-body">
            <form method="POST" action="settings.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="updateProfile">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Nome da Empresa *</label>
                        <input type="text" name="company_name" class="form-control" required
                            value="<?= sanitize($user['company_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Seu Nome *</label>
                        <input type="text" name="name" class="form-control" required
                            value="<?= sanitize($user['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control phone-mask"
                            value="<?= sanitize($user['whatsapp'] ?? '') ?>" placeholder="(11) 99999-9999">
                    </div>
                    <div class="form-group full">
                        <label>E-mail (não editável)</label>
                        <input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" disabled
                            style="background:var(--gray-100);color:var(--gray-400);">
                    </div>
                </div>
                <div style="margin-top:18px;">
                    <button type="submit" class="btn btn-primary">Salvar Dados</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password -->
    <div class="card" id="senha">
        <div class="card-header">
            <span class="card-title"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Alterar Senha</span>
        </div>
        <div class="card-body">
            <form method="POST" action="settings.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="updatePassword">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Senha Atual *</label>
                        <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label>Nova Senha * (mín. 6 caracteres)</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Senha *</label>
                        <input type="password" name="confirm_password" class="form-control" required placeholder="••••••••">
                    </div>
                </div>
                <div style="margin-top:18px;">
                    <button type="submit" class="btn btn-primary"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Alterar Senha</button>
                </div>
            </form>
        </div>
    </div>

    <!-- WhatsApp template -->
    <div class="card" id="whatsapp">
        <div class="card-header">
            <span class="card-title"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> Mensagem Padrão — WhatsApp</span>
        </div>
        <div class="card-body">
            <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:12px;">
                Configure o texto padrão enviado ao compartilhar propostas. Use as variáveis abaixo:
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
                <?php foreach (['#PROPOSTA#','#TITULO#','#CLIENTE#','#VALOR#','#LINK#'] as $var): ?>
                    <code style="background:var(--primary-light);color:var(--primary);padding:3px 10px;border-radius:5px;font-size:.76rem;cursor:pointer;"
                        onclick="insertVar('<?= $var ?>')"><?= $var ?></code>
                <?php endforeach; ?>
            </div>
            <form method="POST" action="settings.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="updateTemplate">
                <div class="form-group">
                    <textarea name="whatsapp_template" id="waTemplate" class="form-control" rows="6"
                        placeholder="Ex: Olá #CLIENTE#! Segue a proposta #PROPOSTA#..."><?= sanitize($user['whatsapp_template'] ?? '') ?></textarea>
                </div>
                <div style="margin-top:14px;">
                    <button type="submit" class="btn btn-primary">Salvar Mensagem</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logo upload -->
    <div class="card" id="logo">
        <div class="card-header">
            <span class="card-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Logo da Empresa
            </span>
        </div>
        <div class="card-body">
            <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:16px;">
                A logo aparece no PDF da proposta. Formatos aceitos: PNG, JPG. Tamanho máximo: 2MB.
            </p>
            <?php if (!empty($user['logo_path'])): ?>
            <div style="margin-bottom:14px;padding:10px;background:var(--gray-50);border-radius:8px;display:inline-block;">
                <img src="<?= APP_URL . '/' . htmlspecialchars($user['logo_path']) ?>" alt="Logo" style="max-height:60px;max-width:200px;object-fit:contain;">
                <div style="font-size:.72rem;color:var(--gray-400);margin-top:6px;">Logo atual</div>
            </div>
            <?php endif; ?>
            <form method="POST" action="settings.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="uploadLogo">
                <div class="form-group" style="margin-bottom:14px;">
                    <label>Selecionar nova logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/gif">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Enviar Logo</button>
                <?php if (!empty($user['logo_path'])): ?>
                <button type="submit" name="action" value="removeLogo" class="btn btn-outline btn-sm" style="margin-left:8px;" onclick="return confirm('Remover logo?')">Remover Logo</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Terms & conditions -->
    <div class="card" id="termos">
        <div class="card-header">
            <span class="card-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Termos & Condições do PDF
            </span>
        </div>
        <div class="card-body">
            <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:12px;">
                Texto exibido no rodapé do PDF da proposta (validade, formas de pagamento, etc).
            </p>
            <form method="POST" action="settings.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="updateTerms">
                <div class="form-group">
                    <textarea name="proposal_terms" class="form-control" rows="5"
                        placeholder="Ex: Esta proposta tem validade de 15 dias. Pagamento via PIX ou boleto em até 30 dias..."
                    ><?= sanitize($user['proposal_terms'] ?? '') ?></textarea>
                </div>
                <div style="margin-top:14px;">
                    <button type="submit" class="btn btn-primary">Salvar Termos</button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php
$extraScripts = '<script>
function insertVar(v) {
    const ta = document.getElementById("waTemplate");
    const start = ta.selectionStart, end = ta.selectionEnd;
    ta.value = ta.value.substring(0, start) + v + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = start + v.length;
    ta.focus();
}
</script>';
require_once __DIR__ . '/../layout_footer.php';
?>
