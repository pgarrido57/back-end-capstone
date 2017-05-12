<?php

$mongo_host = '';
$mongo_port = '27017';
$mongo_auth_db = 'admin';

$mongo_username = 'admin';
$mongo_password = '123456';

$mongo_database = 'nssDj';
$mongo_collection = 'recording';

// Set this to true for development, false for production
$debug = False;
// Any mix with duration inferior to the value will be removed
$minMixDuration = 3000;

// Init
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$result = array('error' => null, 'result' => array());

if ($debug) {
	ini_set('display_errors', 'on');
	ini_set('error_reporting', E_ALL);
}

// Login to the mongodb database
$mongo_server = 'mongodb://' . $mongo_username . ':' . $mongo_password . '@' . $mongo_host . ':' . $mongo_port . '/' . $mongo_auth_db;

try {
	$mongo = new MongoClient($mongo_server);
	$db = $mongo->{"$mongo_database"};
	$col = $db->{"$mongo_collection"};
} catch (MongoException $e) {
	header('HTTP/1.1 400 Bad Request');
	$result['error'] = $e->getMessage();
	exit(json_encode($result));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') fetchMix($col, $result);
else if ($_SERVER['REQUEST_METHOD'] == 'POST') saveMix($col, $result, $minMixDuration);



 ?>
