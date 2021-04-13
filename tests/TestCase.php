<?php

class TestCase extends CIPHPUnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        parent::resetInstance();
        if ($this->CI->db->table_exists($this->CI->db->dbprefix('settings'))) {
            return;
        }
        $this->request(null, 'console/install');
    }

    public static function setUpBeforeClass(): void
    {
        $CI =& get_instance();
        $CI->db->trans_start();
    }

    public static function tearDownAfterClass(): void
    {
        $CI =& get_instance();
        $CI->db->trans_rollback();
    }
}
