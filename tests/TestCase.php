<?php

class TestCase extends CIPHPUnitTestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->resetInstance();
        $this->CI->load->dbutil();
        if ($this->CI->dbutil->database_exists($this->CI->db->database . '_test')) {
            return;
        }
        $this->CI->db->simple_query('CREATE DATABASE ' . $this->CI->db->database . '_test');
        $this->CI->db->simple_query('USE ' . $this->CI->db->database . '_test');
        $this->request(null, 'console/install');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        $CI =& get_instance();
        $CI->db->simple_query('DROP DATABASE ' . $CI->db->database . '_test');
    }
}
