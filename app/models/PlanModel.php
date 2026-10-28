<?php
// app/models/PlanModel.php

class PlanModel {
    public static function all(): array {
        try {
            $stmt = db()->query("SELECT * FROM plans WHERE is_active=1 ORDER BY sort_order ASC");
            return $stmt->fetchAll();
        } catch(Exception $e) { return []; }
    }
    public static function find(string $slug): ?array {
        try {
            $stmt = db()->prepare("SELECT * FROM plans WHERE slug=? LIMIT 1");
            $stmt->execute([$slug]); return $stmt->fetch() ?: null;
        } catch(Exception $e) { return null; }
    }
    public static function allAdmin(): array {
        try {
            return db()->query("
                SELECT p.*,COUNT(u.id) AS users_count
                FROM plans p LEFT JOIN users u ON u.plan=p.slug AND u.role='user'
                GROUP BY p.id ORDER BY p.sort_order
            ")->fetchAll();
        } catch(Exception $e) { return []; }
    }
    public static function update(string $slug, array $data): void {
        db()->prepare("UPDATE plans SET name=?,price=?,max_proposals=?,max_clients=?,max_products=?,is_active=? WHERE slug=?")
            ->execute([$data['name'],$data['price'],$data['max_proposals'],$data['max_clients'],$data['max_products'],(int)$data['is_active'],$slug]);
    }
    public static function checkLimit(int $userId, string $limitType): bool {
        try {
            $stmt = db()->prepare("SELECT plan FROM users WHERE id=? LIMIT 1");
            $stmt->execute([$userId]); $planSlug = $stmt->fetchColumn() ?: 'free';
            $plan = self::find($planSlug);
            if (!$plan) return true;
            $limit = (int)($plan['max_'.$limitType] ?? 9999);
            if ($limit >= 9999) return true;
            $table = ['proposals'=>'proposals','clients'=>'clients','products'=>'products'][$limitType] ?? null;
            if (!$table) return true;
            $c = db()->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id=?");
            $c->execute([$userId]);
            return (int)$c->fetchColumn() < $limit;
        } catch(Exception $e) { return true; }
    }
}
