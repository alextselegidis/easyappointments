<?php
/**
 * Run all the available tests
 */
public function run_all() {
	// All the methods whose names start with "test" are going to be             
	// executed. If you want a method to not be executed remove the 
	// "test" keyword from the beginning.
	$class_methods = get_class_methods('Unit_tests_admins_model');
	foreach ($class_methods as $method_name) {
		if (substr($method_name, 0, 5) === 'test_') {
			call_user_func(array($this, $method_name));
		}
	}
}