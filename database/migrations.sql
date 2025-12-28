-- Schema and seed for CRM multi-tenant core
CREATE TABLE IF NOT EXISTS companies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    is_super_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Legacy compatibility: allow NULL company_id for super admins and align foreign key
SET @users_company_nullable := (
    SELECT CASE WHEN IS_NULLABLE = 'NO' THEN 1 ELSE 0 END
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'company_id'
    LIMIT 1
);
SET @alter_users_company_nullable_sql := IF(
    @users_company_nullable = 1,
    'ALTER TABLE users MODIFY company_id INT UNSIGNED NULL',
    'SELECT 1'
);
PREPARE stmt_users_nullable FROM @alter_users_company_nullable_sql;
EXECUTE stmt_users_nullable;
DEALLOCATE PREPARE stmt_users_nullable;

SET @users_fk_name := (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'company_id' AND REFERENCED_TABLE_NAME = 'companies'
    LIMIT 1
);
SET @users_fk_delete_rule := (
    SELECT DELETE_RULE
    FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = @users_fk_name
    LIMIT 1
);
SET @drop_users_fk_sql := IF(
    @users_fk_name IS NOT NULL AND @users_fk_delete_rule <> 'SET NULL',
    CONCAT('ALTER TABLE users DROP FOREIGN KEY ', @users_fk_name),
    'SELECT 1'
);
PREPARE stmt_users_drop_fk FROM @drop_users_fk_sql;
EXECUTE stmt_users_drop_fk;
DEALLOCATE PREPARE stmt_users_drop_fk;

SET @add_users_fk_sql := IF(
    (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'fk_users_company') = 0 OR @users_fk_delete_rule <> 'SET NULL',
    'ALTER TABLE users ADD CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt_users_add_fk FROM @add_users_fk_sql;
EXECUTE stmt_users_add_fk;
DEALLOCATE PREPARE stmt_users_add_fk;

-- Seed data
INSERT INTO companies (name, status) VALUES ('Örnek Firma', 'active');

INSERT INTO users (company_id, name, email, password_hash, status, is_super_admin)
VALUES (1, 'Admin Kullanıcı', 'admin@example.com', '$2y$12$shAHXMfWk.w5fQvk4CQ.UOOujQZPFGvITdys.KVamWjfs923wPQzK', 'active', 0);

INSERT INTO users (company_id, name, email, password_hash, status, is_super_admin)
VALUES (NULL, 'Super Admin', 'superadmin@example.com', '$2y$12$Xh43DJXNBMzBK00dsr7mxOwyJxBh5pbC.cQGALpSavrX5CBQNpEj2', 'active', 1);

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_roles_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(150) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS packages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    max_users INT UNSIGNED NOT NULL DEFAULT 0,
    max_products INT UNSIGNED NOT NULL DEFAULT 0,
    max_caris INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS package_features (
    package_id INT UNSIGNED NOT NULL,
    feature_key VARCHAR(100) NOT NULL,
    PRIMARY KEY (package_id, feature_key),
    CONSTRAINT fk_package_features_package FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NOT NULL,
    package_id INT UNSIGNED NOT NULL,
    status ENUM('trial', 'active', 'suspended', 'expired') NOT NULL DEFAULT 'trial',
    started_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    CONSTRAINT fk_subscriptions_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_subscriptions_package FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    status ENUM('active', 'passive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_groups_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_product_groups_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NOT NULL,
    product_group_id INT UNSIGNED NULL,
    code VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category VARCHAR(100) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'TRY',
    unit VARCHAR(50) NULL,
    image_url VARCHAR(255) NULL,
    list_price DECIMAL(15,2) NOT NULL,
    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('active', 'passive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_products_group FOREIGN KEY (product_group_id) REFERENCES product_groups(id) ON DELETE SET NULL,
    UNIQUE KEY uniq_company_code (company_id, code),
    INDEX idx_products_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backward compatibility: ensure required product columns exist without data loss
SET @missing_product_group_id := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'product_group_id'
);
SET @add_product_group_id_sql := IF(
    @missing_product_group_id = 0,
    'ALTER TABLE products ADD COLUMN product_group_id INT UNSIGNED NULL AFTER company_id',
    'SELECT 1'
);
PREPARE stmt_add_product_group_id FROM @add_product_group_id_sql;
EXECUTE stmt_add_product_group_id;
DEALLOCATE PREPARE stmt_add_product_group_id;

SET @missing_product_code := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'code'
);
SET @add_product_code_sql := IF(
    @missing_product_code = 0,
    'ALTER TABLE products ADD COLUMN code VARCHAR(100) NOT NULL DEFAULT '''' AFTER product_group_id',
    'SELECT 1'
);
PREPARE stmt_add_product_code FROM @add_product_code_sql;
EXECUTE stmt_add_product_code;
DEALLOCATE PREPARE stmt_add_product_code;

SET @missing_description := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'description'
);
SET @add_description_sql := IF(
    @missing_description = 0,
    'ALTER TABLE products ADD COLUMN description TEXT NULL AFTER name',
    'SELECT 1'
);
PREPARE stmt_add_description FROM @add_description_sql;
EXECUTE stmt_add_description;
DEALLOCATE PREPARE stmt_add_description;

SET @missing_category := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'category'
);
SET @add_category_sql := IF(
    @missing_category = 0,
    'ALTER TABLE products ADD COLUMN category VARCHAR(100) NULL AFTER description',
    'SELECT 1'
);
PREPARE stmt_add_category FROM @add_category_sql;
EXECUTE stmt_add_category;
DEALLOCATE PREPARE stmt_add_category;

SET @missing_currency := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'currency'
);
SET @add_currency_sql := IF(
    @missing_currency = 0,
    'ALTER TABLE products ADD COLUMN currency VARCHAR(10) NOT NULL DEFAULT ''TRY'' AFTER category',
    'SELECT 1'
);
PREPARE stmt_add_currency FROM @add_currency_sql;
EXECUTE stmt_add_currency;
DEALLOCATE PREPARE stmt_add_currency;

SET @missing_unit := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'unit'
);
SET @add_unit_sql := IF(
    @missing_unit = 0,
    'ALTER TABLE products ADD COLUMN unit VARCHAR(50) NULL AFTER currency',
    'SELECT 1'
);
PREPARE stmt_add_unit FROM @add_unit_sql;
EXECUTE stmt_add_unit;
DEALLOCATE PREPARE stmt_add_unit;

SET @missing_image_url := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'image_url'
);
SET @add_image_url_sql := IF(
    @missing_image_url = 0,
    'ALTER TABLE products ADD COLUMN image_url VARCHAR(255) NULL AFTER unit',
    'SELECT 1'
);
PREPARE stmt_add_image_url FROM @add_image_url_sql;
EXECUTE stmt_add_image_url;
DEALLOCATE PREPARE stmt_add_image_url;

SET @missing_list_price := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'list_price'
);
SET @add_list_price_sql := IF(
    @missing_list_price = 0,
    'ALTER TABLE products ADD COLUMN list_price DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER description',
    'SELECT 1'
);
PREPARE stmt_add_list_price FROM @add_list_price_sql;
EXECUTE stmt_add_list_price;
DEALLOCATE PREPARE stmt_add_list_price;

SET @missing_stock_quantity := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'stock_quantity'
);
SET @add_stock_quantity_sql := IF(
    @missing_stock_quantity = 0,
    'ALTER TABLE products ADD COLUMN stock_quantity INT UNSIGNED NOT NULL DEFAULT 0 AFTER list_price',
    'SELECT 1'
);
PREPARE stmt_add_stock_quantity FROM @add_stock_quantity_sql;
EXECUTE stmt_add_stock_quantity;
DEALLOCATE PREPARE stmt_add_stock_quantity;

SET @missing_status := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'status'
);
SET @add_status_sql := IF(
    @missing_status = 0,
    'ALTER TABLE products ADD COLUMN status ENUM(''active'', ''passive'') NOT NULL DEFAULT ''active'' AFTER stock_quantity',
    'SELECT 1'
);
PREPARE stmt_add_status FROM @add_status_sql;
EXECUTE stmt_add_status;
DEALLOCATE PREPARE stmt_add_status;

SET @has_company_code_unique := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND INDEX_NAME = 'uniq_company_code'
);
SET @add_company_code_unique_sql := IF(
    @has_company_code_unique = 0,
    'ALTER TABLE products ADD CONSTRAINT uniq_company_code UNIQUE (company_id, code)',
    'SELECT 1'
);
PREPARE stmt_add_company_code_unique FROM @add_company_code_unique_sql;
EXECUTE stmt_add_company_code_unique;
DEALLOCATE PREPARE stmt_add_company_code_unique;

-- Ensure foreign key is present for product_group_id
SET @has_product_group_fk := (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND CONSTRAINT_NAME = 'fk_products_group'
);
SET @add_product_group_fk_sql := IF(
    @has_product_group_fk = 0,
    'ALTER TABLE products ADD CONSTRAINT fk_products_group FOREIGN KEY (product_group_id) REFERENCES product_groups(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt_fk FROM @add_product_group_fk_sql;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;

CREATE TABLE IF NOT EXISTS caris (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    cari_type ENUM('customer', 'supplier', 'both') NOT NULL DEFAULT 'customer',
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    status ENUM('active', 'passive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_caris_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_caris_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backward compatibility for caris table
SET @has_cari_type := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'cari_type'
);

SET @has_legacy_type := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'type'
);

SET @rename_type_sql := IF(
    @has_cari_type = 0 AND @has_legacy_type = 1,
    'ALTER TABLE caris CHANGE COLUMN `type` cari_type ENUM(''customer'', ''supplier'', ''both'') NOT NULL DEFAULT ''customer'' AFTER name',
    'SELECT 1'
);
PREPARE stmt_rename_cari_type FROM @rename_type_sql;
EXECUTE stmt_rename_cari_type;
DEALLOCATE PREPARE stmt_rename_cari_type;

SET @add_cari_type_sql := IF(
    @has_cari_type = 0 AND @has_legacy_type = 0,
    'ALTER TABLE caris ADD COLUMN cari_type ENUM(''customer'', ''supplier'', ''both'') NOT NULL DEFAULT ''customer'' AFTER name',
    'SELECT 1'
);
PREPARE stmt_add_cari_type FROM @add_cari_type_sql;
EXECUTE stmt_add_cari_type;
DEALLOCATE PREPARE stmt_add_cari_type;

SET @ensure_company_sql := (
    SELECT IF(
        (SELECT IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'company_id') = 'YES',
        'ALTER TABLE caris MODIFY company_id INT UNSIGNED NOT NULL',
        'SELECT 1'
    )
);
PREPARE stmt_company_not_null FROM @ensure_company_sql;
EXECUTE stmt_company_not_null;
DEALLOCATE PREPARE stmt_company_not_null;

SET @add_name_sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'name') = 0,
        'ALTER TABLE caris ADD COLUMN name VARCHAR(255) NOT NULL AFTER company_id',
        'SELECT 1'
    )
);
PREPARE stmt_add_name FROM @add_name_sql;
EXECUTE stmt_add_name;
DEALLOCATE PREPARE stmt_add_name;

SET @add_phone_sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'phone') = 0,
        'ALTER TABLE caris ADD COLUMN phone VARCHAR(50) NULL AFTER cari_type',
        'SELECT 1'
    )
);
PREPARE stmt_add_phone FROM @add_phone_sql;
EXECUTE stmt_add_phone;
DEALLOCATE PREPARE stmt_add_phone;

SET @add_email_sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'email') = 0,
        'ALTER TABLE caris ADD COLUMN email VARCHAR(255) NULL AFTER phone',
        'SELECT 1'
    )
);
PREPARE stmt_add_email FROM @add_email_sql;
EXECUTE stmt_add_email;
DEALLOCATE PREPARE stmt_add_email;

SET @update_status_sql := (
    SELECT IF(
        (SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'status') = 'enum',
        'SELECT 1',
        'ALTER TABLE caris MODIFY status ENUM(''active'', ''passive'') NOT NULL DEFAULT ''active'''
    )
);
PREPARE stmt_update_status FROM @update_status_sql;
EXECUTE stmt_update_status;
DEALLOCATE PREPARE stmt_update_status;

SET @drop_tax_office_sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'tax_office') = 1,
        'ALTER TABLE caris DROP COLUMN tax_office',
        'SELECT 1'
    )
);
PREPARE stmt_drop_tax_office FROM @drop_tax_office_sql;
EXECUTE stmt_drop_tax_office;
DEALLOCATE PREPARE stmt_drop_tax_office;

SET @drop_tax_number_sql := (
    SELECT IF(
        (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'caris' AND COLUMN_NAME = 'tax_number') = 1,
        'ALTER TABLE caris DROP COLUMN tax_number',
        'SELECT 1'
    )
);
PREPARE stmt_drop_tax_number FROM @drop_tax_number_sql;
EXECUTE stmt_drop_tax_number;
DEALLOCATE PREPARE stmt_drop_tax_number;

DROP TABLE IF EXISTS contacts;

-- Seed roles
INSERT INTO roles (company_id, name) VALUES
(NULL, 'Admin'),
(NULL, 'Sales'),
(NULL, 'Purchase'),
(NULL, 'Warehouse'),
(NULL, 'Accounting');

-- Seed permissions
INSERT INTO permissions (`key`, description) VALUES
('product.view', 'Ürünleri görüntüleme'),
('product.create', 'Ürün oluşturma'),
('product.edit', 'Ürün düzenleme'),
('product.deactivate', 'Ürün pasife alma'),
('sale.view', 'Satışları görüntüleme'),
('sale.create', 'Satış oluşturma'),
('cari.view', 'Carileri görüntüleme'),
('cari.create', 'Cari oluşturma'),
('cari.edit', 'Cari düzenleme')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Seed packages
INSERT INTO packages (name, max_users, max_products, max_caris) VALUES
('Starter', 5, 100, 50),
('Pro', 25, 1000, 500),
('Premium', 100, 5000, 2000);

-- Seed package features
INSERT INTO package_features (package_id, feature_key)
SELECT p.id, pf.feature_key
FROM packages p
JOIN (
    SELECT 'Starter' AS package_name, 'product' AS feature_key UNION ALL
    SELECT 'Starter', 'cari' UNION ALL
    SELECT 'Pro', 'product' UNION ALL
    SELECT 'Pro', 'cari' UNION ALL
    SELECT 'Pro', 'offer' UNION ALL
    SELECT 'Pro', 'sale' UNION ALL
    SELECT 'Premium', 'product' UNION ALL
    SELECT 'Premium', 'cari' UNION ALL
    SELECT 'Premium', 'offer' UNION ALL
    SELECT 'Premium', 'sale' UNION ALL
    SELECT 'Premium', 'purchase' UNION ALL
    SELECT 'Premium', 'stock'
) AS pf ON p.name = pf.package_name
ON DUPLICATE KEY UPDATE feature_key = VALUES(feature_key);

-- Example role-permission mapping
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.name = 'Admin';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.`key` IN ('product.edit', 'product.deactivate')
WHERE r.name = 'Admin'
ON DUPLICATE KEY UPDATE permission_id = permission_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.`key` IN ('cari.view', 'cari.create', 'cari.edit')
WHERE r.name = 'Sales'
ON DUPLICATE KEY UPDATE permission_id = permission_id;

-- Assign default admin role to seeded user
INSERT INTO user_roles (user_id, role_id)
SELECT 1, id FROM roles WHERE name = 'Admin' LIMIT 1;

-- Example subscription for the sample company
INSERT INTO subscriptions (company_id, package_id, status, started_at, ends_at)
VALUES (1, 1, 'trial', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY));
