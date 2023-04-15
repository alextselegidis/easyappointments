<?php  if ( ! defined('BASEPATH') ) exit('No direct script access allowed');

// this class is adapted from system/libraries/Log.php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      EllisLab Dev Team
 * @copyright       Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright       Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Logging Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Logging
 * @author      EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/general/errors.html
 */
class EA_Log {

    protected $_threshold   = 1;
    protected $_date_fmt    = 'Y-m-d H:i:s';
    protected $_levels  = array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

    /**
     * Constructor
     */
    public function __construct()
    {
        $config =& get_config();

        if (is_numeric($config['log_threshold']))
        {
            $this->_threshold = $config['log_threshold'];
        
        }

        if ($config['log_date_format'] != '')
        {
            $this->_date_fmt = $config['log_date_format'];
        
        }
    
    }

    // --------------------------------------------------------------------

    /**
     * Write Log to php://stderr
     *
     * Generally this function will be called using the global log_message() function
     *
     * @param   string  the error level
     * @param   string  the error message
     * @param   bool    whether the error is a native PHP error
     * @return  bool
     */
    public function write_log($level = 'error', $msg, $php_error = FALSE)
    {
        $level = strtoupper($level);

        if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold) )
        {
            return FALSE;
        
        }

        file_put_contents('php://stderr', $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n");

        return TRUE;
    
    }


}
// END Log Class

/* End of file EA_Log.php */
/* Location: ./application/core/EA_Log.php */
 ?>
