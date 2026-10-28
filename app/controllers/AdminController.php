<?php
// app/controllers/AdminController.php

class AdminController {

    private static function guard(): void { requireAdmin(); }

    public static function dashboard(): void {
        self::guard();
        $kpis      = AdminModel::globalKpis();
        $revenue   = AdminModel::revenueByMonth();
        $newUsers  = AdminModel::newUsersByMonth();
        $adminUser = UserModel::findById(auth()['id']);
        $pageTitle = 'Dashboard Admin';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public static function saveSettings(): void {
        self::guard();
        verifyCsrf();
        $action = $_POST['settings_action'] ?? '';

        if ($action === 'update_profile') {
            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            if (!$name || !$email) { flash('error','Nome e e-mail são obrigatórios.'); redirect('admin/index.php'); }
            db()->prepare("UPDATE users SET name=?, email=?, updated_at=NOW() WHERE id=?")
                ->execute([$name, $email, auth()['id']]);
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['_last_settings_action'] = 'update_profile';
            flash('success','Dados atualizados com sucesso!');
        }

        if ($action === 'update_password') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $user    = UserModel::findById(auth()['id']);
            if (!password_verify($current, $user['password'])) { flash('error','Senha atual incorreta.'); redirect('admin/index.php'); }
            if (strlen($new) < 6) { flash('error','Nova senha deve ter mínimo 6 caracteres.'); redirect('admin/index.php'); }
            if ($new !== $confirm) { flash('error','As senhas não coincidem.'); redirect('admin/index.php'); }
            UserModel::updatePassword(auth()['id'], $new);
            $_SESSION['_last_settings_action'] = 'update_password';
            flash('success','Senha alterada com sucesso!');
        }

        redirect('admin/index.php#settings');
    }

    public static function users(): void {
        self::guard();
        $filters   = ['status'=>$_GET['status']??'','plan'=>$_GET['plan']??'','search'=>$_GET['search']??''];
        $users     = AdminModel::allUsers($filters);
        $pageTitle = 'Usuários';
        require_once __DIR__ . '/../views/admin/users.php';
    }

    public static function userDetail(int $id): void {
        self::guard();
        $user = AdminModel::findUser($id);
        if (!$user) { flash('error','Usuário não encontrado.'); redirect('admin/users.php'); }
        $proposals = AdminModel::userProposals($id);
        $pageTitle = 'Usuário — '.$user['name'];
        require_once __DIR__ . '/../views/admin/user_detail.php';
    }

    public static function userAction(): void {
        self::guard(); verifyCsrf();
        $action = $_POST['action'] ?? '';
        $userId = (int)($_POST['user_id'] ?? 0);
        switch ($action) {
            case 'block':
                AdminModel::blockUser($userId, trim($_POST['reason']??''));
                flash('success','Usuário bloqueado.');
                break;
            case 'unblock':
                AdminModel::unblockUser($userId);
                flash('success','Usuário desbloqueado.');
                break;
            case 'update_plan':
                AdminModel::updateUserPlan($userId, $_POST['plan']??'free');
                flash('success','Plano atualizado.');
                break;
            case 'update_user':
                AdminModel::updateUser($userId, [
                    'name'=>trim($_POST['name']??''),'company_name'=>trim($_POST['company_name']??''),
                    'email'=>trim($_POST['email']??''),'whatsapp'=>trim($_POST['whatsapp']??''),
                ]);
                flash('success','Dados atualizados.');
                break;
            case 'reset_password':
                $pass = trim($_POST['new_password']??'');
                if (strlen($pass) >= 6) { AdminModel::resetPassword($userId,$pass); flash('success','Senha redefinida.'); }
                else flash('error','Senha deve ter mínimo 6 caracteres.');
                break;
            case 'delete':
                AdminModel::deleteUser($userId);
                flash('success','Usuário excluído.');
                redirect('admin/users.php'); return;
        }
        redirect('admin/user_detail.php?id='.$userId);
    }

    public static function plans(): void {
        self::guard();
        $plans     = AdminModel::getPlans();
        $pageTitle = 'Planos SaaS';
        require_once __DIR__ . '/../views/admin/plans.php';
    }

    public static function savePlan(): void {
        self::guard(); verifyCsrf();
        $slug = $_POST['slug'] ?? '';
        AdminModel::savePlan($slug, [
            'name'          => trim($_POST['name'] ?? ''),
            'price'         => (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0'),
            'max_proposals' => (int)($_POST['max_proposals'] ?? 0),
            'max_clients'   => (int)($_POST['max_clients'] ?? 0),
            'max_products'  => (int)($_POST['max_products'] ?? 0),
            'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        ]);
        flash('success', 'Plano "'.$slug.'" atualizado.');
        redirect('admin/plans.php');
    }
}
