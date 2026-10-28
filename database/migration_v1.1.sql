-- Migration v1.1 — Segurança e rastreio de visualizações
-- Execute este arquivo caso já tenha o banco instalado com o schema anterior.
-- É seguro rodar múltiplas vezes (usa IF NOT EXISTS / IGNORE).

USE proposta_services;

-- 1. Corrige FK de proposal_items (ON SET NULL → ON DELETE SET NULL)
--    Recria a constraint com o nome correto.
ALTER TABLE proposal_items
    DROP FOREIGN KEY IF EXISTS proposal_items_ibfk_2;

ALTER TABLE proposal_items
    ADD CONSTRAINT fk_pi_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;

-- 2. Adiciona rastreio de visualização nas propostas
ALTER TABLE proposals
    ADD COLUMN IF NOT EXISTS view_count INT NOT NULL DEFAULT 0 AFTER public_token,
    ADD COLUMN IF NOT EXISTS viewed_at  TIMESTAMP NULL DEFAULT NULL AFTER view_count;
