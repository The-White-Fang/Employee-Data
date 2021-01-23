<?php 

function loadConfig () {
	$filename = '../../config.json';
	$file = fopen($filename, 'r');
	$config = fread($file, filesize($filename)) or die('unable to open config file');
	fclose($file);

	$config = json_decode($config);

	return $config;
}

$CONFIG = loadConfig();