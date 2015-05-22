#!/usr/bin/env php
<?php

define("APPLICATION_PATH", dirname(__FILE__) . '/../..');

// TODO load conf for current environment
require_once(APPLICATION_PATH . "/application/conf/conf.php");

$app  = new Yaf_Application($config);
$app->bootstrap();

$withings = Yaf_Registry::get('dic')->get('withingsModel');
$withings->updateWeightsLocalFromRemote($app->getConfig()->application->withings->userId);
