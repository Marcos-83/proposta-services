-- Migration v1.1 — Correções de segurança e rastreio de visualizações
-- Execute apenas se já tiver o banco instalado com schema anterior.
-- Seguro para rodar múltiplas vezes.

USE proposta_services;

-- 1. Corrige FK de proposal_items (ON SET NULL → ON DELETE SET NULL)
ALTER TABLE proposal_items
    DROP FOREIGN KEY IF EXISTS proposal_items_ibfk_2;

ALTER TABLE proposal_items
    ADD CONSTRAINT fk_pi_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;

-- 2. Rastreio de visualização do link público
ALTER TABLE proposals
    ADD COLUMN IF NOT EXISTS view_count INT NOT NULL DEFAULT 0 AFTER public_token,
    ADD COLUMN IF NOT EXISTS viewed_at  TIMESTAMP NULL DEFAULT NULL AFTER view_count;
