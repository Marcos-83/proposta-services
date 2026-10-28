-- Migration: Soft Delete
-- Adiciona coluna deleted_at em clients, products, proposals e proposal_items
-- Execute no banco de dados existente. Seguro para rodar múltiplas vezes.

USE proposta_services;

ALTER TABLE clients
    ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE products
    ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE proposals
    ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE proposal_items
    ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Índices para performance (filtra registros ativos rapidamente)
ALTER TABLE clients       ADD INDEX IF NOT EXISTS idx_deleted_at (deleted_at);
ALTER TABLE products      ADD INDEX IF NOT EXISTS idx_deleted_at (deleted_at);
ALTER TABLE proposals     ADD INDEX IF NOT EXISTS idx_deleted_at (deleted_at);
ALTER TABLE proposal_items ADD INDEX IF NOT EXISTS idx_deleted_at (deleted_at);