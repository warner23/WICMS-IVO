<?php
declare(strict_types=1);

/**
 * Base Migration Class
 * WICMS Core
 */

abstract class Migration
{
    protected WIdb $db;

    public function __construct()
    {
        $this->db = WIdb::getInstance();
    }

    /**
     * Unique migration version string.
     * Example: 20260313_001_create_update_tables
     */
    abstract public function version(): string;

    /**
     * Human readable migration name.
     */
    abstract public function name(): string;

    /**
     * Run migration.
     */
    abstract public function up(): void;

    /**
     * Optional rollback.
     */
    public function down(): void
    {
        // Optional
    }
}