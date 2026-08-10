<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesForPerformance extends Migration
{
    public function up()
    {
        // Add indexes to businesses table for ultra-fast localized queries and searches
        $this->db->query("ALTER TABLE businesses ADD INDEX idx_status_created (status, created_at)");
        $this->db->query("ALTER TABLE businesses ADD INDEX idx_status_featured (status, featured)");
        $this->db->query("ALTER TABLE businesses ADD INDEX idx_category_status (category_id, status)");
        $this->db->query("ALTER TABLE businesses ADD INDEX idx_name_ur (name_ur(50))");
        $this->db->query("ALTER TABLE businesses ADD INDEX idx_name_en (name_en(50))");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE businesses DROP INDEX idx_status_created");
        $this->db->query("ALTER TABLE businesses DROP INDEX idx_status_featured");
        $this->db->query("ALTER TABLE businesses DROP INDEX idx_category_status");
        $this->db->query("ALTER TABLE businesses DROP INDEX idx_name_ur");
        $this->db->query("ALTER TABLE businesses DROP INDEX idx_name_en");
    }
}
