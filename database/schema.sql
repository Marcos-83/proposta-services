-- Proposta Services - Database Schema
-- MySQL 8.0+

CREATE DATABASE IF NOT EXISTS proposta_services CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE proposta_services;

-- Users (tenants)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    whatsapp VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    whatsapp_template TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clients (per user/tenant)
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

-- Products & Services (per user/tenant)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('produto','servico') NOT NULL DEFAULT 'servico',
    description VARCHAR(255) NOT NULL,
    price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

-- Proposals (per user/tenant)
CREATE TABLE proposals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    client_id INT NOT NULL,
    proposal_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    total_value DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status ENUM('rascunho','enviado','aprovado','reprovado') NOT NULL DEFAULT 'rascunho',
    proposal_date DATE NOT NULL,
    followup_date DATE DEFAULT NULL,
    observations TEXT DEFAULT NULL,
    public_token VARCHAR(64) NOT NULL UNIQUE,
    view_count INT NOT NULL DEFAULT 0,
    viewed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    UNIQUE KEY unique_proposal_number (user_id, proposal_number),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_followup_date (followup_date),
    INDEX idx_public_token (public_token)
);

-- Proposal Items (products/services in a proposal)
CREATE TABLE proposal_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposal_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,3) NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_proposal_id (proposal_id)
);

-- Default WhatsApp template for new users
-- INSERT sample data for dev/testing can be added below
