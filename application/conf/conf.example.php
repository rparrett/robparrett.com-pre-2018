<?php
$config = array('application' => array(
	'directory' => APPLICATION_PATH . '/application',
	'baseUri' => '/index.php',
	'dispatcher' => array(
		'catchException' => 1
	),
	'showDetailedErrors' => true,
	
	'withings' => array(
		'oauthToken' => '',
		'oauthTokenSecret' => '',
		'oauthConsumerKey' => '',
		'oauthConsumerSecret' => '',
		'userId' => 1
	),

	'particle' => array(
		'accessToken' => '',
		'deviceId' => ''
	),

	'bunny' => array(
		'timestampFile' => '/tmp/bunny_timestamp'
	),

	'randomname' => array(
		'path' => '/var/www-db/dict/',
		'patterns' => array(
			array('adjective', 'singularnoun'),
			array('singulararticle', 'adjective', 'singularnoun')
		)
	),

	'sqlite3' => array(
		'path' => '/var/www-db/db.sqlite3'
	)
));
