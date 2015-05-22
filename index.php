<?php
define("START_TIME", microtime(true));
define("APPLICATION_PATH", dirname(__FILE__));

// TODO load conf for current environment
require_once(APPLICATION_PATH . "/application/conf/conf.php");

$app  = new Yaf_Application($config);
$app->bootstrap()
    ->run();
