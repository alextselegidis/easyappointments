<?php

/**
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Add_foreign_keys_cascade_delete_custom_fields extends EA_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up()
    {
        // Drop existing indexes if they exist
        if ($this->db->dbdriver === 'mysqli') {
            // Check and drop foreign keys if they exist
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Drop existing indexes on custom_field_options if they exist
            $result = $this->db->query("SHOW INDEX FROM {$this->db->dbprefix}custom_field_options WHERE Key_name = 'custom_field_options_id_custom_fields'")->result_array();
            if (!empty($result)) {
                $this->db->query("ALTER TABLE {$this->db->dbprefix}custom_field_options DROP INDEX custom_field_options_id_custom_fields");
            }

            // Drop existing indexes on custom_field_values if they exist
            $result = $this->db->query("SHOW INDEX FROM {$this->db->dbprefix}custom_field_values WHERE Key_name = 'custom_field_values_id_custom_fields'")->result_array();
            if (!empty($result)) {
                $this->db->query("ALTER TABLE {$this->db->dbprefix}custom_field_values DROP INDEX custom_field_values_id_custom_fields");
            }

            // Add foreign key with CASCADE DELETE for custom_field_options
            $this->db->query("
                ALTER TABLE {$this->db->dbprefix}custom_field_options
                ADD CONSTRAINT custom_field_options_id_custom_fields
                FOREIGN KEY (id_custom_fields) REFERENCES {$this->db->dbprefix}custom_fields(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ");

            // Add foreign key with CASCADE DELETE for custom_field_values
            $this->db->query("
                ALTER TABLE {$this->db->dbprefix}custom_field_values
                ADD CONSTRAINT custom_field_values_id_custom_fields
                FOREIGN KEY (id_custom_fields) REFERENCES {$this->db->dbprefix}custom_fields(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ");

            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    /**
     * Downgrade method.
     *
     * @throws Exception
     */
    public function down()
    {
        if ($this->db->dbdriver === 'mysqli') {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Drop foreign keys
            $result = $this->db->query("SHOW CREATE TABLE {$this->db->dbprefix}custom_field_options")->row_array();
            if (isset($result['Create Table']) && strpos($result['Create Table'], 'custom_field_options_id_custom_fields') !== false) {
                $this->db->query("ALTER TABLE {$this->db->dbprefix}custom_field_options DROP FOREIGN KEY custom_field_options_id_custom_fields");
            }

            $result = $this->db->query("SHOW CREATE TABLE {$this->db->dbprefix}custom_field_values")->row_array();
            if (isset($result['Create Table']) && strpos($result['Create Table'], 'custom_field_values_id_custom_fields') !== false) {
                $this->db->query("ALTER TABLE {$this->db->dbprefix}custom_field_values DROP FOREIGN KEY custom_field_values_id_custom_fields");
            }

            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
