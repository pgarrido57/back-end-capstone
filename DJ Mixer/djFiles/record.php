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

// Fetching Mix(es)
function fetchMix($col, $result) {
	// Retrieve one recording
	if (isset($_GET['id'])) {
		try {
			$query = array('id' => new MongoID($_GET['id']));
			$fields = array('id' => false);
			$result['result'] = $col->findOne($query, $fields);
		} catch (MongoException $e) {
			$result['error'] = $e->getMessage();
		}
		// Retrieve all recordings
	} else {
		try {
			$query = array();
			$fields = array('packets' => false, 'songs' => fales, 'beatMap' => false, 'info.replayed' => false);
			$cursor = $col->find($query, $fields);
			$cursor ->sort(array('id' => -1));

			if (isset($_GET['limit'])) $cursor->limit($_GET['limit']);
			if (isset($_GET['offset'])) $cursor->skip(intval($_GET['offset']));

			if ($cursor->hasNext()) {
				$ctime = time();
				foreach ($cursor as $record) {
					$ok = true;

					// Fix unstopped mixes
					if ($record['info']['live']) {
						$tt = isset($record['info']['time']) ? intval($record['info']['time']) : 0;
						$dur = isset($record['info']['duration']) ? intval($record['info']['duration'] / 1000) : 0;
						$delay = $ctime - $tt - $dur;

						// No update for 20 sec
						if ($delay > 20) {
							$ok = $dur > 0;
							$query = array('id' => $record['$id']);
							// User didn't stop recordin
							if ($ok) {
								$record['info']['live'] = false;
								$update = array('$set' => array('info.live' => false, 'info.ended' => $tt + $dur));
								$col->update($query, $update);
								// User closed recording before 1st packet
							} else {
								$col->remove($query);
							}
						}
					}
					if ($ok) array_push($result['result'], $record);
					if (sizeof($result['result']) > 230) break;
				}
			}
		} catch (MongoException $e) {
			$result['error'] = $e->getMessage();
		}
	}
}

 ?>
