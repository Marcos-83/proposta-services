<?php
// app/models/ProposalItemModel.php

class ProposalItemModel {

    public static function allByProposal(int $proposalId): array {
        $stmt = db()->prepare("
            SELECT pi.*, p.description AS product_description, p.type AS product_type
            FROM proposal_items pi
            LEFT JOIN products p ON p.id = pi.product_id
            WHERE pi.proposal_id = ? AND pi.deleted_at IS NULL
            ORDER BY pi.id ASC
        ");
        $stmt->execute([$proposalId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $stmt = db()->prepare("SELECT * FROM proposal_items WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $proposalId, array $data): int {
        $total = round((float)$data['quantity'] * (float)$data['unit_price'], 2);
        $stmt  = db()->prepare("
            INSERT INTO proposal_items (proposal_id, product_id, description, quantity, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $proposalId,
            $data['product_id'] ?: null,
            $data['description'],
            $data['quantity'],
            $data['unit_price'],
            $total,
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void {
        $total = round((float)$data['quantity'] * (float)$data['unit_price'], 2);
        $stmt  = db()->prepare("
            UPDATE proposal_items
            SET product_id = ?, description = ?, quantity = ?, unit_price = ?, total_price = ?
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([
            $data['product_id'] ?: null,
            $data['description'],
            $data['quantity'],
            $data['unit_price'],
            $total,
            $id,
        ]);
    }

    /** Soft delete de um item */
    public static function delete(int $id): void {
        $stmt = db()->prepare("UPDATE proposal_items SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
    }

    /** Soft delete de todos os itens de uma proposta */
    public static function deleteByProposal(int $proposalId): void {
        $stmt = db()->prepare("UPDATE proposal_items SET deleted_at = NOW() WHERE proposal_id = ? AND deleted_at IS NULL");
        $stmt->execute([$proposalId]);
    }

    /** Restaura um item excluído */
    public static function restore(int $id): void {
        $stmt = db()->prepare("UPDATE proposal_items SET deleted_at = NULL WHERE id = ?");
        $stmt->execute([$id]);
    }

    /** Exclusão permanente (use com cautela) */
    public static function forceDelete(int $id): void {
        $stmt = db()->prepare("DELETE FROM proposal_items WHERE id = ?");
        $stmt->execute([$id]);
    }
}