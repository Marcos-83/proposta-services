<?php
// app/controllers/SettingsController.php

class SettingsController {

    public static function index(): void {
        requireUser();
        $user = UserModel::findById(auth()['id']);
        require_once __DIR__ . '/../views/settings/index.php';
    }

    public static function updateProfile(): void {
        requireUser();
        verifyCsrf();
        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'name'         => trim($_POST['name'] ?? ''),
            'whatsapp'     => trim($_POST['whatsapp'] ?? ''),
        ];
        if (!$data['company_name'] || !$data['name']) {
            flash('error', 'Nome da empresa e nome são obrigatórios.');
            redirect('settings.php');
        }
        UserModel::update(auth()['id'], $data);
        // Update session
        $_SESSION['user']['company_name'] = $data['company_name'];
        $_SESSION['user']['name']         = $data['name'];
        $_SESSION['user']['whatsapp']     = $data['whatsapp'];
        flash('success', 'Dados atualizados com sucesso!');
        redirect('settings.php');
    }

    public static function updatePassword(): void {
        requireUser();
        verifyCsrf();
        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $user = UserModel::findById(auth()['id']);
        if (!password_verify($current, $user['password'])) {
            flash('error', 'Senha atual incorreta.');
            redirect('settings.php#senha');
        }
        if (strlen($new) < 6) {
            flash('error', 'A nova senha deve ter no mínimo 6 caracteres.');
            redirect('settings.php#senha');
        }
        if ($new !== $confirm) {
            flash('error', 'A confirmação não confere com a nova senha.');
            redirect('settings.php#senha');
        }
        UserModel::updatePassword(auth()['id'], $new);
        flash('success', 'Senha alterada com sucesso!');
        redirect('settings.php');
    }

    public static function updateTemplate(): void {
        requireUser();
        verifyCsrf();
        $template = trim($_POST['whatsapp_template'] ?? '');
        UserModel::updateTemplate(auth()['id'], $template);
        flash('success', 'Mensagem padrão do WhatsApp atualizada!');
        redirect('settings.php#whatsapp');
    }

    public static function uploadLogo(): void {
        requireUser();
        verifyCsrf();
        $userId = auth()['id'];

        if (empty($_FILES['logo']['tmp_name'])) {
            flash('error', 'Selecione uma imagem.');
            redirect('settings.php#logo');
        }
        $file = $_FILES['logo'];
        $allowed = ['image/png','image/jpeg','image/gif','image/webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) {
            flash('error', 'Formato não aceito. Use PNG, JPG ou GIF.');
            redirect('settings.php#logo');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            flash('error', 'Arquivo muito grande. Máximo 2MB.');
            redirect('settings.php#logo');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/logos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext     = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'png';
        $fname   = 'logo_'.$userId.'_'.time().'.'.$ext;
        $dest    = $uploadDir . $fname;

        // Remove old logo if exists
        $old = UserModel::findById($userId)['logo_path'] ?? '';
        if ($old && file_exists(__DIR__ . '/../../public/' . $old)) {
            @unlink(__DIR__ . '/../../public/' . $old);
        }

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            flash('error', 'Erro ao salvar imagem.');
            redirect('settings.php#logo');
        }

        $logoPath = 'uploads/logos/' . $fname;
        db()->prepare("UPDATE users SET logo_path=? WHERE id=?")->execute([$logoPath, $userId]);
        $_SESSION['user']['logo_path'] = $logoPath;
        flash('success', 'Logo atualizada com sucesso!');
        redirect('settings.php#logo');
    }

    public static function removeLogo(): void {
        requireUser();
        verifyCsrf();
        $userId = auth()['id'];
        $old = UserModel::findById($userId)['logo_path'] ?? '';
        if ($old && file_exists(__DIR__ . '/../../public/' . $old)) {
            @unlink(__DIR__ . '/../../public/' . $old);
        }
        db()->prepare("UPDATE users SET logo_path=NULL WHERE id=?")->execute([$userId]);
        $_SESSION['user']['logo_path'] = null;
        flash('success', 'Logo removida.');
        redirect('settings.php#logo');
    }

    public static function updateTerms(): void {
        requireUser();
        verifyCsrf();
        $terms = trim($_POST['proposal_terms'] ?? '');
        db()->prepare("UPDATE users SET proposal_terms=? WHERE id=?")->execute([$terms, auth()['id']]);
        $_SESSION['user']['proposal_terms'] = $terms;
        flash('success', 'Termos & Condições atualizados!');
        redirect('settings.php#termos');
    }
}
