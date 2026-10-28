<?php
// app/models/ProductModel.php

class ProductModel {

    public static function all(int $userId): array {
        $stmt = db()->prepare("SELECT * FROM products WHERE user_id = ? AND deleted_at IS NULL ORDER BY description ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array {
        $stmt = db()->prepare("SELECT * FROM products WHERE id = ? AND user_id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $userId, array $data): int {
        $stmt = db()->prepare("INSERT INTO products (user_id, type, description, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $data['type'], $data['description'], $data['price']]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, int $userId, array $data): void {
        $stmt = db()->prepare("UPDATE products SET type = ?, description = ?, price = ?, updated_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$data['type'], $data['description'], $data['price'], $id, $userId]);
    }

    /** Soft delete — marca como excluído sem remover do banco */
    public static function delete(int $id, int $userId): void {
        $stmt = db()->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $userId]);
    }

    /** Restaura um produto excluído */
    public static function restore(int $id, int $userId): void {
        $stmt = db()->prepare("UPDATE products SET deleted_at = NULL WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    /** Lista produtos excluídos (lixeira) */
    public static function trashed(int $userId): array {
        $stmt = db()->prepare("SELECT * FROM products WHERE user_id = ? AND deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Exclusão permanente (use com cautela) */
    public static function forceDelete(int $id, int $userId): void {
        $stmt = db()->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    public static function count(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM products WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}