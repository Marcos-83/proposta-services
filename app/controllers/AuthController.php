<?php
// app/controllers/AuthController.php

class AuthController {

    public static function showLogin(): void {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public static function showRegister(): void {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public static function login(): void {
        verifyCsrf();
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Preencha e-mail e senha.');
            redirect('login.php');
        }

        // ── Rate limiting ────────────────────────────────────────────────
        if (loginIsBlocked($email)) {
            $mins = loginBlockedMinutesLeft($email);
            flash('error', "Muitas tentativas incorretas. Aguarde {$mins} minuto(s) para tentar novamente.");
            redirect('login.php');
        }

        $user = UserModel::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            loginAttemptFail($email);
            flash('error', 'E-mail ou senha incorretos.');
            redirect('login.php');
        }

        // Conta bloqueada pelo admin
        if (($user['status'] ?? 'ativo') === 'bloqueado') {
            $reason = !empty($user['blocked_reason']) ? ' Motivo: ' . $user['blocked_reason'] : '';
            flash('error', 'Acesso bloqueado.' . $reason . ' Entre em contato com o suporte.');
            redirect('login.php');
        }

        // Login OK — limpa tentativas e regenera sessão (session fixation)
        loginAttemptSuccess($email);
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'           => $user['id'],
            'name'         => $user['name'],
            'company_name' => $user['company_name'],
            'email'        => $user['email'],
            'whatsapp'     => $user['whatsapp'],
            'role'         => $user['role']      ?? 'user',
            'status'       => $user['status']    ?? 'ativo',
            'plan'         => $user['plan']      ?? 'free',
            'logo_path'    => $user['logo_path'] ?? null,
        ];

        // Redireciona admin para painel admin, usuário para dashboard
        if (($user['role'] ?? 'user') === 'admin') {
            ob_end_clean();
            header('Location: ' . APP_URL . '/admin/index.php');
            exit;
        }

        ob_end_clean();
        header('Location: ' . APP_URL . '/dashboard.php');
        exit;
    }

    public static function register(): void {
        verifyCsrf();
        $data = [
            'company_name'     => trim($_POST['company_name'] ?? ''),
            'name'             => trim($_POST['name'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'whatsapp'         => trim($_POST['whatsapp'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];

        if (!$data['company_name'] || !$data['name'] || !$data['email'] || !$data['password']) {
            flash('error', 'Preencha todos os campos obrigatórios.'); redirect('register.php');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('error', 'E-mail inválido.'); redirect('register.php');
        }
        if (strlen($data['password']) < 6) {
            flash('error', 'A senha deve ter no mínimo 6 caracteres.'); redirect('register.php');
        }
        if ($data['password'] !== $data['password_confirm']) {
            flash('error', 'As senhas não coincidem.'); redirect('register.php');
        }
        if (UserModel::findByEmail($data['email'])) {
            flash('error', 'Já existe uma conta com este e-mail.'); redirect('register.php');
        }

        UserModel::create($data);
        session_regenerate_id(true);
        flash('success', 'Conta criada com sucesso! Faça login.');
        redirect('login.php');
    }

    public static function logout(): void {
        // Limpa sessão completamente
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
        ob_end_clean();
        header('Location: ' . APP_URL . '/login.php');
        exit;
    }
}
