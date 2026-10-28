<?php
// app/models/UserModel.php

class UserModel {

    public static function findByEmail(string $email): ?array {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $stmt = db()->prepare("
            INSERT INTO users (company_name, name, email, whatsapp, password, whatsapp_template, role, status, plan)
            VALUES (?, ?, ?, ?, ?, ?, 'user', 'ativo', 'free')
        ");
        $stmt->execute([
            $data['company_name'],
            $data['name'],
            $data['email'],
            $data['whatsapp'] ?? null,
            password_hash($data['password'], PASSWORD_DEFAULT),
            "Olá #CLIENTE#! Segue proposta #TITULO# no valor de #VALOR#: #LINK#",
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void {
        db()->prepare("UPDATE users SET company_name=?, name=?, email=?, whatsapp=?, updated_at=NOW() WHERE id=?")
            ->execute([$data['company_name'], $data['name'], $data['email'], $data['whatsapp'] ?? null, $id]);
    }

    public static function updatePassword(int $id, string $password): void {
        db()->prepare("UPDATE users SET password=?, updated_at=NOW() WHERE id=?")
            ->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public static function updateTemplate(int $id, string $template): void {
        db()->prepare("UPDATE users SET whatsapp_template=?, updated_at=NOW() WHERE id=?")
            ->execute([$template, $id]);
    }
}
