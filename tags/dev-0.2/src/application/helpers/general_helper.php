<?php 
/**
 * Get date in RFC3339
 * For example used in XML/Atom
 * 
 * @link http://stackoverflow.com/questions/5671433/php-time-to-google-calendar-dates-time-format
 * 
 * @param integer $timestamp
 * @return string date in RFC3339
 * @author Boris Korobkov
 */
function date3339($timestamp=0) {

    if (!$timestamp) {
        $timestamp = time();
    }
    $date = date('Y-m-d\TH:i:s', $timestamp);

    $matches = array();
    if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
        $date .= $matches[1].$matches[2].':'.$matches[3];
    } else {
        $date .= 'Z';
    }
    return $date;
}

/* End of file general_helper.php */
/* Location: ./application/helpers/general_helper.php */