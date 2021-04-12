<?php

class TestCase extends CIPHPUnitTestCase
{
    public function resetInstance($use_my_controller = false)
    {
        parent::resetInstance($use_my_controller);
        $this->CI->load->dbutil();
        if ($this->CI->dbutil->database_exists($_ENV['DB_NAME'])) {
            $this->switchDatabase();
            return;
        }
        $this->CI->db->simple_query('CREATE DATABASE ' . $_ENV['DB_NAME']);
        $this->switchDatabase();
        $this->request(null, 'console/install');
    }

    private function switchDatabase()
    {
        require APPPATH.'config/database.php';
        $db['default']['database'] = $_ENV['DB_NAME'];
        $this->CI->load->database($db['default'], TRUE);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->resetInstance();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        $CI =& get_instance();
        $CI->db->simple_query('DROP DATABASE ' . $_ENV['DB_NAME']);
    }
}
