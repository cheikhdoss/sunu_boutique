-- Create permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Create model_has_permissions table
CREATE TABLE IF NOT EXISTS model_has_permissions (
    permission_id BIGINT NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    CONSTRAINT fk_model_has_permissions_permissions
        FOREIGN KEY (permission_id)
        REFERENCES permissions (id)
        ON DELETE CASCADE
);

-- Create model_has_roles table
CREATE TABLE IF NOT EXISTS model_has_roles (
    role_id BIGINT NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    CONSTRAINT fk_model_has_roles_roles
        FOREIGN KEY (role_id)
        REFERENCES roles (id)
        ON DELETE CASCADE
);

-- Create role_has_permissions table
CREATE TABLE IF NOT EXISTS role_has_permissions (
    permission_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    CONSTRAINT fk_role_has_permissions_permissions
        FOREIGN KEY (permission_id)
        REFERENCES permissions (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_role_has_permissions_roles
        FOREIGN KEY (role_id)
        REFERENCES roles (id)
        ON DELETE CASCADE
);

-- Insert export_orders permission
INSERT INTO permissions (name, guard_name, created_at, updated_at)
VALUES ('export_orders', 'web', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON CONFLICT (name, guard_name) DO NOTHING;

-- Insert admin role
INSERT INTO roles (name, guard_name, created_at, updated_at)
VALUES ('admin', 'web', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON CONFLICT (name, guard_name) DO NOTHING;

-- Link permission to role
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE p.name = 'export_orders'
AND r.name = 'admin'
AND NOT EXISTS (
    SELECT 1 FROM role_has_permissions
    WHERE permission_id = p.id AND role_id = r.id
);