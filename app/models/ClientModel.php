<?php
// app/models/ClientModel.php

class ClientModel {

    public static function all(int $userId): array {
        $stmt = db()->prepare("SELECT * FROM clients WHERE user_id = ? AND deleted_at IS NULL ORDER BY name ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array {
        $stmt = db()->prepare("SELECT * FROM clients WHERE id = ? AND user_id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $userId, array $data): int {
        $stmt = db()->prepare("INSERT INTO clients (user_id, name, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $data['name'], $data['phone'] ?? null, $data['email'] ?? null]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, int $userId, array $data): void {
        $stmt = db()->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, updated_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$data['name'], $data['phone'] ?? null, $data['email'] ?? null, $id, $userId]);
    }

    /** Soft delete — marca como excluído sem remover do banco */
    public static function delete(int $id, int $userId): void {
        $stmt = db()->prepare("UPDATE clients SET deleted_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $userId]);
    }

    /** Restaura um cliente excluído */
    public static function restore(int $id, int $userId): void {
        $stmt = db()->prepare("UPDATE clients SET deleted_at = NULL WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    /** Lista clientes excluídos (lixeira) */
    public static function trashed(int $userId): array {
        $stmt = db()->prepare("SELECT * FROM clients WHERE user_id = ? AND deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Exclusão permanente (use com cautela) */
    public static function forceDelete(int $id, int $userId): void {
        $stmt = db()->prepare("DELETE FROM clients WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    public static function count(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM clients WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}