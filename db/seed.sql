USE eshop;

-- Seed products
INSERT INTO products (product_id, sku_model, name, brand, gender, base_price, description, is_active) VALUES
(100.00, 'SKUM100', 'Tenisky Air Runner', 'AirBrand', 'unisex', 79.99, 'Ľahké tenisky na každodenné nosenie.', 1),
(101.00, 'SKUM101', 'Trail Blazer', 'TrailPro', 'men', 99.99, 'Robustné trailové tenisky.', 1),
(102.00, 'SKUM102', 'City Slip', 'UrbanFeet', 'women', 69.99, 'Pohodlné slip-on tenisky do mesta.', 1);

-- Seed product_variants (variant_id, product_id, sku, color, size_eu, is_active)
INSERT INTO product_variants (variant_id, product_id, sku, color, size_eu, is_active) VALUES
(1000.00, 100.00, 'SKU100-BLK-42', 'black', '42', 1),
(1001.00, 100.00, 'SKU100-WHT-40', 'white', '40', 1),
(1002.00, 101.00, 'SKU101-GRN-44', 'green', '44', 1),
(1003.00, 101.00, 'SKU101-BLU-43', 'blue', '43', 1),
(1004.00, 102.00, 'SKU102-PNK-38', 'pink', '38', 1),
(1005.00, 102.00, 'SKU102-GRY-39', 'grey', '39', 1);

-- Seed inventory (variant_id, stock_qty)
INSERT INTO inventory (variant_id, stock_qty) VALUES
(1000.00, 10),
(1001.00, 5),
(1002.00, 7),
(1003.00, 2),
(1004.00, 20),
(1005.00, 15);

-- End seeds

