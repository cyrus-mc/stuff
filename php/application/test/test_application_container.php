<?php

error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/* include the required files */
require_once 'application_container.php';
require_once 'memory/cache.php';

/* simple test suite for Application Container */
try {
	$app_container = new application_container('sample_php_application');
	
	/* check if environment existed before */
	if ($app_container->get_init_flag()) {
		print "Environment already existed, not populating with objects\n";
		if ($app_container->destroy())
			print "Environment destroyed\n";
		else
			print "Failed to destroy environment\n";
	} else {
		/* create some objects */
		$app_object_1 = array('key1' => 'value1', 'key2' => 'value2');
		$app_object_2 = new cache(10);
		$app_object_3 = 'simple string';

		/* add some data to the cache */
		$app_object_2->add('cache_line_1', 'some_random_data');

		/* now add these to the application environment */
		$app_container->add_object('object_1', $app_object_1);
		$app_container->add_object('object_2', $app_object_2);
		$app_container->add_object('object_3', $app_object_3);	

		print "Added three objects to the environment\n";
		$app_container->print_app_environment();
	}
} catch (Exception $e) {
	print "caught exception $e\n";
}
?>