<?php
// app/controllers/PdfController.php
// Gera PDF em PHP puro usando TCPDF (via Composer) ou fallback HTML→PDF nativo.
// Estratégia: tenta TCPDF; se não tiver, gera HTML com CSS print e força download.

class PdfController {

    public static function downloadByUser(int $id): void {
        requireAuth();
        $userId   = auth()['id'];
        $proposal = ProposalModel::find($id, $userId);
        if (!$proposal) { http_response_code(404); echo 'Proposta não encontrada.'; exit; }
        $items = ProposalItemModel::allByProposal($id);
        $user  = UserModel::findById($userId);
        self::build($proposal, $items, $user);
    }

    public static function downloadByToken(string $token): void {
        $proposal = ProposalModel::findByToken($token);
        if (!$proposal) { http_response_code(404); echo 'Proposta não encontrada.'; exit; }
        $items = ProposalItemModel::allByProposal($proposal['id']);
        $stmt  = db()->prepare("SELECT logo_path, proposal_terms, company_name FROM users WHERE id=? LIMIT 1");
        $stmt->execute([$proposal['user_id']]);
        $user  = $stmt->fetch() ?: [];
        self::build($proposal, $items, $user);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Build: gera o PDF como HTML imprimível e força o download
    // Compatível com qualquer servidor PHP sem extensões extras
    // ─────────────────────────────────────────────────────────────────────
    private static function build(array $proposal, array $items, array $user = []): void {

        // Logo em base64 se existir
        $logoBase64 = '';
        if (!empty($user['logo_path'])) {
            $logoFile = realpath(__DIR__ . '/../../public/' . ltrim($user['logo_path'], '/'));
            if ($logoFile && file_exists($logoFile)) {
                $mime = mime_content_type($logoFile);
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
            }
        }

        // Assinatura base64 (se houver)
        $sigBase64 = '';
        if (!empty($proposal['signature_data']) && str_starts_with($proposal['signature_data'], 'data:image')) {
            $sigBase64 = $proposal['signature_data'];
        }

        $terms      = $user['proposal_terms'] ?? '';
        $companyName = htmlspecialchars($proposal['company_name'] ?? $user['company_name'] ?? '');
        $title       = htmlspecialchars($proposal['title']);
        $clientName  = htmlspecialchars($proposal['client_name']);
        $number      = $proposal['proposal_number'];
        $date        = date('d/m/Y', strtotime($proposal['proposal_date']));
        $total       = 'R$ ' . number_format((float)$proposal['total_value'], 2, ',', '.');
        $status      = ucfirst($proposal['status']);
        $obs         = !empty($proposal['observations']) ? nl2br(htmlspecialchars($proposal['observations'])) : '';

        // Status color
        $statusColors = [
            'aprovado'  => ['bg' => '#dcfce7', 'color' => '#15803d', 'border' => '#22c55e'],
            'reprovado' => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#ef4444'],
            'enviado'   => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'border' => '#3b82f6'],
            'rascunho'  => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#94a3b8'],
        ];
        $sc = $statusColors[$proposal['status']] ?? $statusColors['rascunho'];

        // Build items rows
        $itemsHtml = '';
        foreach ($items as $i => $item) {
            $bg  = $i % 2 === 0 ? '#ffffff' : '#f8fafc';
            $qty = number_format((float)$item['quantity'], 2, ',', '.');
            $up  = 'R$ ' . number_format((float)$item['unit_price'], 2, ',', '.');
            $tp  = 'R$ ' . number_format((float)$item['total_price'], 2, ',', '.');
            $desc = htmlspecialchars($item['description']);
            $itemsHtml .= "
            <tr style='background:{$bg};'>
                <td style='padding:9px 12px;border-bottom:1px solid #e2e8f0;font-size:12px;color:#334155;'>{$desc}</td>
                <td style='padding:9px 12px;border-bottom:1px solid #e2e8f0;font-size:12px;color:#334155;text-align:center;'>{$qty}</td>
                <td style='padding:9px 12px;border-bottom:1px solid #e2e8f0;font-size:12px;color:#334155;text-align:right;'>{$up}</td>
                <td style='padding:9px 12px;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#1a6bcc;text-align:right;'>{$tp}</td>
            </tr>";
        }

        // Signed block
        $signedHtml = '';
        if (!empty($proposal['signed_at'])) {
            $sigDate = date('d/m/Y \à\s H:i', strtotime($proposal['signed_at']));
            $sigName    = htmlspecialchars($proposal['signed_name'] ?? '');
            $sigIp      = !empty($proposal['signed_ip']) ? ' · IP: ' . $proposal['signed_ip'] : '';
            $sigNameStr = $sigName ? ' por ' . $sigName : '';
            $imgTag     = $sigBase64 ? "<br><img src='{$sigBase64}' style='max-width:280px;max-height:80px;margin-top:8px;border:1px solid #e2e8f0;border-radius:4px;'>" : '';
            $signedHtml = "
            <div style='margin-top:20px;padding:14px 18px;background:#f0fdf4;border:1.5px solid #22c55e;border-radius:8px;'>
                <div style='font-size:12px;font-weight:700;color:#15803d;margin-bottom:4px;'>&#x2705; Assinado digitalmente{$sigNameStr}</div>
                <div style='font-size:11px;color:#166634;'>{$sigDate}{$sigIp}</div>
                {$imgTag}
            </div>";
        }

        // Terms block
        $termsHtml = '';
        if ($terms) {
            $termsHtml = "
            <div style='margin-top:24px;padding:14px 18px;background:#f8fafc;border-left:3px solid #1a6bcc;border-radius:0 6px 6px 0;page-break-inside:avoid;'>
                <div style='font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;'>Termos e Condições</div>
                <p style='font-size:11px;color:#475569;line-height:1.7;margin:0;white-space:pre-line;'>" . htmlspecialchars($terms) . "</p>
            </div>";
        }

        // Observations block
        $obsHtml = '';
        if ($obs) {
            $obsHtml = "
            <div style='margin-top:18px;padding:12px 16px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:0 6px 6px 0;'>
                <div style='font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;'>Observações</div>
                <p style='font-size:11px;color:#78350f;line-height:1.7;margin:0;'>{$obs}</p>
            </div>";
        }

        // Logo HTML
        $logoHtml = $logoBase64
            ? "<img src='{$logoBase64}' style='max-height:48px;max-width:160px;object-fit:contain;'>"
            : "<div style='width:42px;height:42px;background:#f47b20;border-radius:8px;display:flex;align-items:center;justify-content:center;'>
                 <svg width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2.2'><path d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'/><polyline points='14 2 14 8 20 8'/><line x1='16' y1='13' x2='8' y2='13'/><line x1='16' y1='17' x2='8' y2='17'/></svg>
               </div>";

        $generatedAt = date('d/m/Y \à\s H:i');
        $followup    = !empty($proposal['followup_date'])
            ? "<div style='font-size:11px;color:#64748b;'><span style='font-weight:600;'>Follow-up:</span> " . date('d/m/Y', strtotime($proposal['followup_date'])) . "</div>"
            : '';

        // ── Full HTML document ────────────────────────────────────────────
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Proposta #{$number} — {$companyName}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @page { size: A4; margin: 0; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #fff;
        color: #1e293b;
        font-size: 13px;
        line-height: 1.5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .page { width: 210mm; min-height: 297mm; padding: 0; margin: 0 auto; background: #fff; }

    /* Header */
    .header {
        background: linear-gradient(135deg, #0f1f3d 0%, #1a3668 60%, #1a6bcc 100%);
        padding: 28px 36px 24px;
        display: flex; align-items: flex-start; justify-content: space-between; gap: 20px;
    }
    .header-left { display: flex; align-items: center; gap: 14px; }
    .company-name { color: #fff; font-size: 16px; font-weight: 700; }
    .company-sub  { color: rgba(255,255,255,.6); font-size: 10px; margin-top: 2px; }
    .header-right { text-align: right; }
    .prop-label { font-size: 10px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .08em; }
    .prop-number { font-size: 22px; font-weight: 800; color: #fff; }
    .prop-date   { font-size: 11px; color: rgba(255,255,255,.65); margin-top: 3px; }

    /* Status bar */
    .status-bar {
        background: {$sc['bg']}; border-bottom: 2px solid {$sc['border']};
        padding: 10px 36px; display: flex; align-items: center; gap: 10px;
    }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; background: {$sc['border']}; flex-shrink: 0; }
    .status-text { font-size: 12px; font-weight: 700; color: {$sc['color']}; }

    /* Body */
    .body { padding: 28px 36px; }

    /* Meta grid */
    .meta-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px; }
    .meta-item label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .07em; display: block; margin-bottom: 3px; }
    .meta-item strong { font-size: 13px; color: #1e293b; font-weight: 600; }
    .meta-item.total strong { font-size: 16px; color: #1a6bcc; font-weight: 800; }

    /* Section title */
    .section-title { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #0f1f3d; color: #fff; padding: 9px 12px;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; text-align: left;
    }
    thead th:nth-child(2) { text-align: center; }
    thead th:nth-child(3), thead th:nth-child(4) { text-align: right; }
    tfoot td { padding: 11px 12px; font-size: 13px; background: #f8fafc; border-top: 2px solid #e2e8f0; }
    .total-label { text-align: right; font-weight: 700; color: #334155; }
    .total-value { text-align: right; font-weight: 800; font-size: 15px; color: #1a6bcc; }

    /* Footer */
    .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .footer-brand { font-size: 10px; color: #94a3b8; }
    .footer-page  { font-size: 10px; color: #cbd5e1; }

    @media print {
        body { margin: 0; }
        .page { width: 100%; }
        .no-print { display: none !important; }
    }
</style>
</head>
<body>
<div class="page">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            {$logoHtml}
            <div>
                <div class="company-name">{$companyName}</div>
                <div class="company-sub">Proposta Comercial</div>
            </div>
        </div>
        <div class="header-right">
            <div class="prop-label">Proposta</div>
            <div class="prop-number">#{$number}</div>
            <div class="prop-date">{$date}</div>
        </div>
    </div>

    <!-- Status bar -->
    <div class="status-bar">
        <div class="status-dot"></div>
        <div class="status-text">{$status}</div>
    </div>

    <!-- Body -->
    <div class="body">

        <!-- Title -->
        <h1 style="font-size:18px;font-weight:800;color:#0f1f3d;margin-bottom:20px;line-height:1.2;">{$title}</h1>

        <!-- Meta -->
        <div class="meta-grid">
            <div class="meta-item"><label>Cliente</label><strong>{$clientName}</strong></div>
            <div class="meta-item"><label>Data da Proposta</label><strong>{$date}</strong></div>
            <div class="meta-item total"><label>Valor Total</label><strong>{$total}</strong></div>
        </div>
        {$followup}

        <!-- Items -->
        <div class="section-title" style="margin-top:20px;">Itens da Proposta</div>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th style="text-align:center;width:70px;">Qtd</th>
                    <th style="text-align:right;width:110px;">Valor Unit.</th>
                    <th style="text-align:right;width:110px;">Total</th>
                </tr>
            </thead>
            <tbody>
                {$itemsHtml}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total-label">Valor Total:</td>
                    <td class="total-value">{$total}</td>
                </tr>
            </tfoot>
        </table>

        {$obsHtml}
        {$termsHtml}
        {$signedHtml}

        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">Gerado por <strong>Proposta Services</strong> · {$generatedAt}</div>
            <div class="footer-page">Proposta #{$number}</div>
        </div>
    </div>
</div>
</body>
</html>
HTML;

        // ── Tenta usar Dompdf se disponível via Composer ──────────────────
        $dompdf_autoload = realpath(__DIR__ . '/../../vendor/autoload.php');
        if ($dompdf_autoload && file_exists($dompdf_autoload)) {
            require_once $dompdf_autoload;
            if (class_exists('Dompdf\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $fname = 'proposta_' . $proposal['proposal_number'] . '_' . preg_replace('/[^a-z0-9]/i', '_', $proposal['title']) . '.pdf';
                $dompdf->stream($fname, ['Attachment' => true]);
                exit;
            }
        }

        // ── Fallback: envia HTML com headers para impressão/salvamento ────
        // O browser abrirá o HTML e o usuário pode Ctrl+P → Salvar como PDF
        $fname = 'proposta_' . $proposal['proposal_number'] . '.html';
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="' . $fname . '"');
        echo $html;
        exit;
    }
}