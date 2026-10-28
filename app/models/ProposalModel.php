<?php
// app/models/ProposalModel.php

class ProposalModel {

    public static function nextNumber(int $userId): int {
        $stmt = db()->prepare("SELECT COALESCE(MAX(proposal_number), 0) + 1 FROM proposals WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function all(int $userId, array $filters = []): array {
        $sql = "
            SELECT p.*, c.name AS client_name, c.phone AS client_phone
            FROM proposals p
            JOIN clients c ON c.id = p.client_id
            WHERE p.user_id = ? AND p.deleted_at IS NULL
        ";
        $params = [$userId];

        if (!empty($filters['client_id'])) {
            $sql .= " AND p.client_id = ?";
            $params[] = $filters['client_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['followup_date'])) {
            $sql .= " AND p.followup_date = ?";
            $params[] = $filters['followup_date'];
        }

        $sql .= " ORDER BY p.proposal_number DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array {
        $stmt = db()->prepare("
            SELECT p.*, c.name AS client_name, c.phone AS client_phone, c.email AS client_email
            FROM proposals p
            JOIN clients c ON c.id = p.client_id
            WHERE p.id = ? AND p.user_id = ? AND p.deleted_at IS NULL LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public static function findByToken(string $token): ?array {
        $stmt = db()->prepare("
            SELECT p.*, c.name AS client_name, c.phone AS client_phone, c.email AS client_email,
                   u.company_name, u.name AS user_name, u.whatsapp AS user_whatsapp
            FROM proposals p
            JOIN clients c ON c.id = p.client_id
            JOIN users u ON u.id = p.user_id
            WHERE p.public_token = ? AND p.deleted_at IS NULL LIMIT 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Registra visualização do link público.
     * Incrementa view_count e salva viewed_at na primeira vez.
     */
    public static function registerView(string $token): void {
        $stmt = db()->prepare("
            UPDATE proposals
            SET view_count = view_count + 1,
                viewed_at  = CASE WHEN viewed_at IS NULL THEN NOW() ELSE viewed_at END
            WHERE public_token = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$token]);
    }

    public static function create(int $userId, array $data): int {
        $token  = generateToken(32);
        $number = self::nextNumber($userId);
        $stmt   = db()->prepare("
            INSERT INTO proposals
                (user_id, client_id, proposal_number, title, total_value, status,
                 proposal_date, followup_date, observations, public_token)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $data['client_id'],
            $number,
            $data['title'],
            $data['total_value'] ?? 0,
            $data['status'] ?? 'rascunho',
            $data['proposal_date'],
            $data['followup_date'] ?: null,
            $data['observations'] ?? null,
            $token,
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, int $userId, array $data): void {
        $stmt = db()->prepare("
            UPDATE proposals
            SET client_id = ?, title = ?, total_value = ?, status = ?,
                proposal_date = ?, followup_date = ?, observations = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([
            $data['client_id'],
            $data['title'],
            $data['total_value'] ?? 0,
            $data['status'],
            $data['proposal_date'],
            $data['followup_date'] ?: null,
            $data['observations'] ?? null,
            $id,
            $userId,
        ]);
    }

    public static function updateStatus(int $id, int $userId, string $status): void {
        $stmt = db()->prepare("UPDATE proposals SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$status, $id, $userId]);
    }

    public static function updateStatusByToken(string $token, string $status): bool {
        if (!in_array($status, ['aprovado', 'reprovado'])) return false;
        $stmt = db()->prepare("UPDATE proposals SET status = ?, updated_at = NOW() WHERE public_token = ? AND deleted_at IS NULL");
        $stmt->execute([$status, $token]);
        return $stmt->rowCount() > 0;
    }

    public static function recalcTotal(int $id): void {
        $stmt = db()->prepare("
            UPDATE proposals SET total_value = (
                SELECT COALESCE(SUM(total_price), 0) FROM proposal_items
                WHERE proposal_id = ? AND deleted_at IS NULL
            ) WHERE id = ?
        ");
        $stmt->execute([$id, $id]);
    }

    /** Soft delete — move a proposta para a lixeira */
    public static function delete(int $id, int $userId): void {
        $stmt = db()->prepare("UPDATE proposals SET deleted_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $userId]);
    }

    /** Restaura uma proposta excluída */
    public static function restore(int $id, int $userId): void {
        $stmt = db()->prepare("UPDATE proposals SET deleted_at = NULL WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    /** Lista propostas excluídas (lixeira) */
    public static function trashed(int $userId): array {
        $stmt = db()->prepare("
            SELECT p.*, c.name AS client_name
            FROM proposals p
            JOIN clients c ON c.id = p.client_id
            WHERE p.user_id = ? AND p.deleted_at IS NOT NULL
            ORDER BY p.deleted_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Exclusão permanente (use com cautela) */
    public static function forceDelete(int $id, int $userId): void {
        $stmt = db()->prepare("DELETE FROM proposals WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    public static function countAll(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM proposals WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function countByStatus(int $userId, string $status): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM proposals WHERE user_id = ? AND status = ? AND deleted_at IS NULL");
        $stmt->execute([$userId, $status]);
        return (int) $stmt->fetchColumn();
    }

    public static function sumByStatus(int $userId, string $status): float {
        $stmt = db()->prepare("SELECT COALESCE(SUM(total_value), 0) FROM proposals WHERE user_id = ? AND status = ? AND deleted_at IS NULL");
        $stmt->execute([$userId, $status]);
        return (float) $stmt->fetchColumn();
    }

    public static function todayFollowups(int $userId): array {
        $today = date('Y-m-d');
        $stmt  = db()->prepare("
            SELECT p.*, c.name AS client_name, c.phone AS client_phone
            FROM proposals p
            JOIN clients c ON c.id = p.client_id
            WHERE p.user_id = ? AND p.followup_date = ? AND p.deleted_at IS NULL
            ORDER BY p.proposal_number DESC
        ");
        $stmt->execute([$userId, $today]);
        return $stmt->fetchAll();
    }

    public static function approvedByMonth(int $userId): array {
        $stmt = db()->prepare("
            SELECT DATE_FORMAT(proposal_date, '%Y-%m') AS month,
                   DATE_FORMAT(proposal_date, '%b/%Y')  AS month_label,
                   COALESCE(SUM(total_value), 0)        AS total
            FROM proposals
            WHERE user_id = ? AND status = 'aprovado' AND deleted_at IS NULL
              AND proposal_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY month, month_label
            ORDER BY month ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}