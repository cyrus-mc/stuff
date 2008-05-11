<?php

require_once 'application_container.php';
require_once '../../memory/trunk/cache.php';

$appc = new application_container('php_application');

$object1 = array('key1' => 'value1', 'key2' => 'value2');
$object2 = new cache(10);
$object3 = 'simple string';
$object2->add('cache1', 'test_cache_line');
//$appc->remove_object('object1');
//$appc->add_object('object1', $object1);
//$appc->add_object('object2', $object2);
//$appc->add_object('object3', $object3);
$appc->destroy();
$appc->print_err();
//$appc->print_app_environment();


?>