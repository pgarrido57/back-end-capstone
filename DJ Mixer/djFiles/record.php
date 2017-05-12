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

// Saving mix

function saveMix($col, $result, $minMixDuration) {
	if (!isset($_POST['data']) || !is_string($_POST['data'])) {
		header('HTTP/1.1 400 Bad Request');
		$result['error'] = 'bad input format';
		exit(json_encode($result));
	}

	$data = array('packets' => array(), 'beatMap' => array(), 'songs' => array());
	$datas = json_decode($_POST['data'], true);

	// Unpacking the mixDataQueue into $data
	foreach ($datas as $requestNum => $request) {
		if(!isset($lastRequestNum)) $lastRequestNum = isset($_SESSION[$request['id']]) ? $_SESSION[$request['id']] : -1;
		if ($requestNum > $lastRequestNum) {
			$data['id'] = $request['id'];
			$data['requestNum'] = $requestNum;

			if (isset($request['info'])) $data['info'] = $request['info'];
			if (isset($request['packets'])) {
				if (is_string($request['packets'])) $request['packets'] = array($request['packets']);
				$data['packets'] = array_merge($data['packets'], $request['packets']);
			}
			if (isset($request['beatMap'])) $data['beatMap'] = array_merge($data['beatMap'], $request['beatMap']);
			if (isset($request['songs'])) $data['songs'] = array_merge($data['songs'], $request['songs']);
		}
	}

	$content = array('$set' => array());
	$isNewMix = !isset($data['id']) || $data['id'] == 'null';
	$hasPackets = isset($data['packets']) && count($data['packets']);

	if ($isNewMix) {
		$name = substr($_POST['name'], 0, 40);
		$name = ucwords(trim(strtolower($name)));
		$content['$set']['info.duration'] = 0;
		$content['$set']['info.live'] = true;
		$content['$set']['info.name'] = $name;
		$content['$set']['info.time'] = time();
	}
	if ($hasPackets) {
		$content['$set']['info.duration'] = $data['info']['duration'];
		$content['$set']['info.live'] = $data['info']['live'];
		if (isset($data['info'])) $content['$set']['info.error'] = $data['info']['error'];
		if (!$data['info']['live']) $content['$set']['info.ended'] = time();

		$content['$pushAll'] = array(
			'packets' => $data['packets'],
			'beatMap' => $data['beatMap'],
			'songs.player1' => $data['songs']['player1'],
			'songs.player2' => $data['songs']['player2'],
			'songs.started' => $data['songs']['started'],
			'songs.sampler' => $data['songs']['sampler']
		);
	}

	// Upsert in mongo
	$query = array('id' => new MongoId($data['id']));
	$option = array('safe' => true, 'upsert' => true);

	try {
		$upsert = $col->update($query, $content, $options);
		$result['result'] = $upsert['ok'];
		$result['error'] = $upsert['err'];
		if ($isNewMix) $result['id'] = $data['id'] = (string) $upsert['upserted'];
		if ($uspert['ok']) $_SESSION[$data['id']] = $data['requestNum'];
	} catch (MongoException $e) {
		$result['error'] = $e->getMessage();
	}

	if ($hasPackets && !$data['info']['live'] && $data['info']['duration'] < $minMixDuration) {
		$col->remove(array('id' =>  new MongoID($data['id'])));
	}
}

echo json_encode($result);
?>
