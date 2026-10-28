-- ============================================================
-- MIGRATION v3.0 — Executar após migration_v2.sql
-- ============================================================
USE proposta_services;

-- Adiciona colunas em users (logo e termos)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS logo_path VARCHAR(255) DEFAULT NULL AFTER blocked_reason,
    ADD COLUMN IF NOT EXISTS proposal_terms TEXT DEFAULT NULL AFTER logo_path;
