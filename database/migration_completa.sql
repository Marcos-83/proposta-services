-- ============================================================
-- MIGRATION SEGURA — Adiciona apenas colunas que não existem
-- Execute no phpMyAdmin ou MySQL Workbench
-- ============================================================

-- Colunas em 'users'
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS role ENUM('admin','user') NOT NULL DEFAULT 'user',
    ADD COLUMN IF NOT EXISTS status ENUM('ativo','bloqueado') NOT NULL DEFAULT 'ativo',
    ADD COLUMN IF NOT EXISTS plan ENUM('free','starter','pro','enterprise') NOT NULL DEFAULT 'free',
    ADD COLUMN IF NOT EXISTS plan_expires_at DATE DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS blocked_at TIMESTAMP NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS blocked_reason VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS logo_path VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS proposal_terms TEXT DEFAULT NULL;

-- Colunas em 'proposals' (assinatura digital)
ALTER TABLE proposals
    ADD COLUMN IF NOT EXISTS signature_data LONGTEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS signed_at TIMESTAMP NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS signed_ip VARCHAR(45) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS signed_name VARCHAR(150) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS action_at TIMESTAMP NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS action_ip VARCHAR(45) DEFAULT NULL;

-- Tabela de planos
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    max_proposals INT NOT NULL DEFAULT 5,
    max_clients INT NOT NULL DEFAULT 10,
    max_products INT NOT NULL DEFAULT 10,
    features JSON DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO plans (slug, name, price, max_proposals, max_clients, max_products, features, sort_order) VALUES
('free',       'Gratuito',   0.00,   5,    10,   10,   '["Até 5 propostas","10 clientes","10 produtos","Link público"]', 1),
('starter',    'Starter',    49.90,  50,   100,  50,   '["50 propostas/mês","100 clientes","PDF download","Assinatura digital","Suporte por email"]', 2),
('pro',        'Pro',        99.90,  500,  500,  200,  '["500 propostas/mês","Clientes ilimitados","Relatórios avançados","Suporte prioritário"]', 3),
('enterprise', 'Enterprise', 199.90, 9999, 9999, 9999, '["Propostas ilimitadas","Tudo do Pro","SLA garantido","Onboarding dedicado"]', 4);

-- Tabela de faturamento
CREATE TABLE IF NOT EXISTS billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_slug VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pendente','pago','cancelado') NOT NULL DEFAULT 'pendente',
    payment_method VARCHAR(50) DEFAULT NULL,
    reference VARCHAR(100) DEFAULT NULL,
    period_start DATE DEFAULT NULL,
    period_end DATE DEFAULT NULL,
    paid_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);

-- Tabela de auditoria
CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    admin_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity VARCHAR(50) DEFAULT NULL,
    entity_id INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Usuário admin padrão (senha: Admin@2025)
-- TROQUE A SENHA APÓS O PRIMEIRO ACESSO!
INSERT IGNORE INTO users (company_name, name, email, role, status, plan, password, whatsapp_template)
VALUES (
    'Administração',
    'Administrador',
    'admin@propostaservices.com',
    'admin',
    'ativo',
    'enterprise',
    '$2y$12$LcMM7ejMsqz5pFHkK9CxT.0UVB9y7pzP5RO.eS0rLHT4AQoqRoKJy',
    'Olá #CLIENTE#! Segue proposta #TITULO# no valor de #VALOR#: #LINK#'
);
-- Para gerar novo hash de senha:
-- php -r "echo password_hash('SuaSenha', PASSWORD_DEFAULT);"
