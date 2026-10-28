<?php
// app/models/AdminModel.php

class AdminModel {

    public static function allUsers(array $filters = []): array {
        try {
            $sql = "SELECT u.*,
                       COUNT(DISTINCT p.id) AS total_proposals,
                       COUNT(DISTINCT c.id) AS total_clients,
                       COALESCE(SUM(CASE WHEN p.status='aprovado' THEN p.total_value END),0) AS revenue
                FROM users u
                LEFT JOIN proposals p ON p.user_id = u.id
                LEFT JOIN clients c ON c.user_id = u.id
                WHERE u.role = 'user'";
            $params = [];
            if (!empty($filters['status'])) { $sql .= " AND u.status=?"; $params[] = $filters['status']; }
            if (!empty($filters['plan']))   { $sql .= " AND u.plan=?";   $params[] = $filters['plan']; }
            if (!empty($filters['search'])) {
                $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.company_name LIKE ?)";
                $like = '%'.$filters['search'].'%';
                $params = array_merge($params, [$like,$like,$like]);
            }
            $sql .= " GROUP BY u.id ORDER BY u.created_at DESC";
            $stmt = db()->prepare($sql); $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(Exception $e) { return []; }
    }

    public static function findUser(int $id): ?array {
        try {
            $stmt = db()->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch(Exception $e) { return null; }
    }

    public static function blockUser(int $id, string $reason = ''): void {
        try {
            db()->prepare("UPDATE users SET status='bloqueado', blocked_at=NOW(), blocked_reason=? WHERE id=?")
                ->execute([$reason, $id]);
        } catch(Exception $e) {}
    }

    public static function unblockUser(int $id): void {
        try {
            db()->prepare("UPDATE users SET status='ativo', blocked_at=NULL, blocked_reason=NULL WHERE id=?")
                ->execute([$id]);
        } catch(Exception $e) {}
    }

    public static function updateUserPlan(int $id, string $plan): void {
        try {
            db()->prepare("UPDATE users SET plan=? WHERE id=?")->execute([$plan, $id]);
        } catch(Exception $e) {}
    }

    public static function updateUser(int $id, array $data): void {
        try {
            db()->prepare("UPDATE users SET name=?, company_name=?, email=?, whatsapp=?, updated_at=NOW() WHERE id=?")
                ->execute([$data['name'], $data['company_name'], $data['email'], $data['whatsapp'] ?? null, $id]);
        } catch(Exception $e) {}
    }

    public static function resetPassword(int $id, string $password): void {
        try {
            db()->prepare("UPDATE users SET password=?, updated_at=NOW() WHERE id=?")
                ->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
        } catch(Exception $e) {}
    }

    public static function deleteUser(int $id): void {
        try {
            db()->prepare("DELETE FROM users WHERE id=? AND role='user'")->execute([$id]);
        } catch(Exception $e) {}
    }

    public static function globalKpis(): array {
        $db = db();
        $q = function(string $sql) use ($db) {
            try { return $db->query($sql)->fetchColumn(); } catch(Exception $e) { return 0; }
        };
        return [
            'total_users'        => (int)$q("SELECT COUNT(*) FROM users WHERE role='user'"),
            'active_users'       => (int)$q("SELECT COUNT(*) FROM users WHERE role='user' AND status='ativo'"),
            'blocked_users'      => (int)$q("SELECT COUNT(*) FROM users WHERE role='user' AND status='bloqueado'"),
            'total_proposals'    => (int)$q("SELECT COUNT(*) FROM proposals"),
            'approved_proposals' => (int)$q("SELECT COUNT(*) FROM proposals WHERE status='aprovado'"),
            'total_revenue'      => (float)$q("SELECT COALESCE(SUM(total_value),0) FROM proposals WHERE status='aprovado'"),
            'total_clients'      => (int)$q("SELECT COUNT(*) FROM clients"),
            'total_products'     => (int)$q("SELECT COUNT(*) FROM products"),
        ];
    }

    public static function revenueByMonth(): array {
        try {
            return db()->query("
                SELECT DATE_FORMAT(proposal_date,'%Y-%m') AS month,
                       DATE_FORMAT(proposal_date,'%b/%y') AS label,
                       COUNT(*) AS total_proposals,
                       COUNT(CASE WHEN status='aprovado' THEN 1 END) AS approved,
                       COALESCE(SUM(CASE WHEN status='aprovado' THEN total_value END),0) AS revenue
                FROM proposals
                WHERE proposal_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY month, label ORDER BY month ASC
            ")->fetchAll();
        } catch(Exception $e) { return []; }
    }

    public static function newUsersByMonth(): array {
        try {
            return db()->query("
                SELECT DATE_FORMAT(created_at,'%b/%y') AS label, COUNT(*) AS total
                FROM users WHERE role='user'
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at,'%Y-%m'), label
                ORDER BY DATE_FORMAT(created_at,'%Y-%m') ASC
            ")->fetchAll();
        } catch(Exception $e) { return []; }
    }

    public static function userProposals(int $userId): array {
        try {
            $stmt = db()->prepare("
                SELECT p.*, c.name AS client_name
                FROM proposals p JOIN clients c ON c.id = p.client_id
                WHERE p.user_id=? ORDER BY p.created_at DESC LIMIT 20
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch(Exception $e) { return []; }
    }

    public static function getPlans(): array {
        try {
            return db()->query("SELECT * FROM plans ORDER BY sort_order ASC, id ASC")->fetchAll();
        } catch(Exception $e) {
            return [
                ['slug'=>'free',       'name'=>'Gratuito',   'price'=>0,      'max_proposals'=>5,    'max_clients'=>10,   'max_products'=>10,   'is_active'=>1, 'sort_order'=>1],
                ['slug'=>'starter',    'name'=>'Starter',    'price'=>49.90,  'max_proposals'=>50,   'max_clients'=>100,  'max_products'=>50,   'is_active'=>1, 'sort_order'=>2],
                ['slug'=>'pro',        'name'=>'Pro',        'price'=>99.90,  'max_proposals'=>500,  'max_clients'=>500,  'max_products'=>200,  'is_active'=>1, 'sort_order'=>3],
                ['slug'=>'enterprise', 'name'=>'Enterprise', 'price'=>199.90, 'max_proposals'=>9999, 'max_clients'=>9999, 'max_products'=>9999, 'is_active'=>1, 'sort_order'=>4],
            ];
        }
    }

    public static function savePlan(string $slug, array $data): void {
        try {
            $exists = db()->prepare("SELECT COUNT(*) FROM plans WHERE slug=?");
            $exists->execute([$slug]);
            if ($exists->fetchColumn() > 0) {
                db()->prepare("UPDATE plans SET name=?,price=?,max_proposals=?,max_clients=?,max_products=?,is_active=? WHERE slug=?")
                    ->execute([$data['name'],$data['price'],$data['max_proposals'],$data['max_clients'],$data['max_products'],$data['is_active'],$slug]);
            } else {
                db()->prepare("INSERT INTO plans(slug,name,price,max_proposals,max_clients,max_products,is_active) VALUES(?,?,?,?,?,?,?)")
                    ->execute([$slug,$data['name'],$data['price'],$data['max_proposals'],$data['max_clients'],$data['max_products'],$data['is_active']]);
            }
        } catch(Exception $e) {}
    }

    public static function planUserCount(): array {
        try {
            $rows = db()->query("SELECT plan, COUNT(*) as total FROM users WHERE role='user' GROUP BY plan")->fetchAll();
            $map = [];
            foreach ($rows as $r) $map[$r['plan']] = (int)$r['total'];
            return $map;
        } catch(Exception $e) { return []; }
    }
}
