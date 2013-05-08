<?php
// This hook is necessary in order to run php unit tests
// against the codeigniter framework.
class DisplayHook {
	public function captureOutput() {
		$this->CI =& get_instance();
		$output = $this->CI->output->get_output();
		if (ENVIRONMENT != 'testing') {
			echo $output;
		}
	}
}