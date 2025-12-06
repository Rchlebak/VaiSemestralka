-- Schema provided by user: E-Shop Tenisiek

CREATE DATABASE IF NOT EXISTS eshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eshop;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id DECIMAL(10,2) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(40)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id DECIMAL(10,2) PRIMARY KEY,
    user_id DECIMAL(10,2),
    email VARCHAR(255) NOT NULL,
    status VARCHAR(20),
    total_amount DECIMAL(10,2),
    ship_name VARCHAR(200),
    ship_street VARCHAR(200),
    ship_city VARCHAR(200),
    ship_zip VARCHAR(20),
    ship_country VARCHAR(20),
    CONSTRAINT fk_orders_users FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    payment_id DECIMAL(10,2) PRIMARY KEY,
    order_id DECIMAL(10,2),
    provider VARCHAR(40),
    status VARCHAR(20),
    amount DECIMAL(10,2),
    reference VARCHAR(100),
    CONSTRAINT fk_payments_orders FOREIGN KEY (order_id) REFERENCES orders(order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    product_id DECIMAL(10,2) PRIMARY KEY,
    sku_model VARCHAR(84),
    name VARCHAR(200) NOT NULL,
    brand VARCHAR(200),
    gender VARCHAR(10),
    base_price DECIMAL(10,2),
    description TEXT,
    is_active INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product Variants Table
CREATE TABLE IF NOT EXISTS product_variants (
    variant_id DECIMAL(10,2) PRIMARY KEY,
    product_id DECIMAL(10,2),
    sku VARCHAR(24),
    color VARCHAR(50),
    size_eu VARCHAR(4),
    is_active INT,
    CONSTRAINT fk_product_variants_products FOREIGN KEY (product_id) REFERENCES products(product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id DECIMAL(10,2) PRIMARY KEY,
    order_id DECIMAL(10,2),
    variant_id DECIMAL(10,2),
    qty INT,
    unit_price DECIMAL(10,2),
    line_total DECIMAL(10,2),
    CONSTRAINT fk_order_items_orders FOREIGN KEY (order_id) REFERENCES orders(order_id),
    CONSTRAINT fk_order_items_product_variants FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inventory Table
CREATE TABLE IF NOT EXISTS inventory (
    variant_id DECIMAL(10,2) PRIMARY KEY,
    stock_qty INT,
    CONSTRAINT fk_inventory_product_variants FOREIGN KEY (variant_id) REFERENCES product_variants(variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- End of schema

-- NOTE: Using DECIMAL for primary keys is unusual; consider INT AUTO_INCREMENT for IDs in future revisions.
