-- Migration: add image_url column to products if missing
-- This uses ALTER TABLE ... ADD COLUMN IF NOT EXISTS (MySQL 8+ supports this)
ALTER TABLE products ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) DEFAULT NULL;

-- End of migration
