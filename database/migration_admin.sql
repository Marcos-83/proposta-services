-- ============================================================
-- MIGRATION: Adiciona suporte a admin e controle de usuários
-- Execute no phpMyAdmin > aba SQL
-- ============================================================

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS role ENUM('admin','user') NOT NULL DEFAULT 'user' AFTER password,
    ADD COLUMN IF NOT EXISTS status ENUM('ativo','bloqueado') NOT NULL DEFAULT 'ativo' AFTER role,
    ADD COLUMN IF NOT EXISTS plan ENUM('free','starter','pro','enterprise') NOT NULL DEFAULT 'free' AFTER status,
    ADD COLUMN IF NOT EXISTS blocked_at TIMESTAMP NULL DEFAULT NULL AFTER plan,
    ADD COLUMN IF NOT EXISTS blocked_reason VARCHAR(255) DEFAULT NULL AFTER blocked_at,
    ADD COLUMN IF NOT EXISTS logo_path VARCHAR(255) DEFAULT NULL AFTER blocked_reason;

-- Criar usuário admin (senha: Admin@2025)
INSERT IGNORE INTO users (company_name, name, email, password, role, status, plan)
VALUES (
    'Administração',
    'Administrador',
    'admin@admin.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'ativo',
    'enterprise'
);

-- IMPORTANTE: A senha acima é "password"
-- Para trocar, crie um arquivo PHP temporário com:
-- <?php echo password_hash('SuaSenha', PASSWORD_DEFAULT);
-- E execute UPDATE users SET password='HASH' WHERE role='admin';

-- Plans table
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    max_proposals INT NOT NULL DEFAULT 5,
    max_clients INT NOT NULL DEFAULT 10,
    max_products INT NOT NULL DEFAULT 10,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO plans (slug, name, price, max_proposals, max_clients, max_products, is_active, sort_order) VALUES
('free',       'Gratuito',   0.00,   5,    10,   10,   1, 1),
('starter',    'Starter',    49.90,  50,   100,  50,   1, 2),
('pro',        'Pro',        99.90,  500,  500,  200,  1, 3),
('enterprise', 'Enterprise', 199.90, 9999, 9999, 9999, 1, 4);
