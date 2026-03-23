CREATE TABLE IF NOT EXISTS wi_compliance_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(190) NOT NULL,
    business_type VARCHAR(100) NOT NULL,
    risk_profile VARCHAR(50) NOT NULL DEFAULT 'medium',
    site_count INT NOT NULL DEFAULT 1,
    uses_delivery TINYINT(1) NOT NULL DEFAULT 0,
    uses_alcohol_service TINYINT(1) NOT NULL DEFAULT 0,
    stores_staff_records TINYINT(1) NOT NULL DEFAULT 1,
    stores_customer_data TINYINT(1) NOT NULL DEFAULT 1,
    retention_profile VARCHAR(50) NOT NULL DEFAULT 'standard',
    updated_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_legal_register (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    jurisdiction VARCHAR(100) NOT NULL,
    source_type VARCHAR(50) NOT NULL,
    reference_code VARCHAR(120) DEFAULT NULL,
    applies_to VARCHAR(120) DEFAULT 'all_sites',
    summary TEXT NULL,
    review_cycle VARCHAR(50) DEFAULT 'annual',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_checklists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    frequency VARCHAR(50) NOT NULL DEFAULT 'daily',
    assigned_role VARCHAR(100) NOT NULL DEFAULT 'manager',
    evidence_required TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT NULL,
    title VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'open',
    completed_by VARCHAR(120) DEFAULT NULL,
    evidence_path VARCHAR(255) DEFAULT NULL,
    notes TEXT NULL,
    completed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    audit_type VARCHAR(100) NOT NULL DEFAULT 'internal',
    score DECIMAL(5,2) DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'planned',
    due_date DATE DEFAULT NULL,
    completed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    version_label VARCHAR(100) DEFAULT NULL,
    review_due DATE DEFAULT NULL,
    retention_rule VARCHAR(120) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_gdpr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    data_category VARCHAR(120) NOT NULL,
    lawful_basis VARCHAR(120) DEFAULT NULL,
    retention_period VARCHAR(120) DEFAULT NULL,
    processor_name VARCHAR(190) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    priority VARCHAR(50) NOT NULL DEFAULT 'medium',
    due_date DATE NOT NULL,
    owner_role VARCHAR(100) NOT NULL DEFAULT 'manager',
    status VARCHAR(50) NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_archive (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_table VARCHAR(120) NOT NULL,
    source_id INT NOT NULL,
    retention_until DATE DEFAULT NULL,
    archived_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    asset_tag VARCHAR(120) DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'due',
    next_service_date DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_training (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    role_name VARCHAR(120) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'due',
    renewal_due DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wi_compliance_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(120) NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_enabled TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
