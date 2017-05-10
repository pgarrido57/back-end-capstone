<?php
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *')

$dir = "./sampler_directory";

// List all samples
if (is_dir($dir)) {
	// Open sampler directory
	if ($dh = opendir($dir)) {
	$data = array();
			// Push all samples in $data
			while (($file = readdir($dh)) ! == false) {
				$ext = substr( $file, strlen($file)-4);
				if($ext == ".mp3"){
					$file = substr( $file, 0, strlen($file)-4);
					arrayPush($data, $file);
				}
					}

			// Sort $data
			sort($data,SORT_STRING);
			closedir($dh);
	}
}
echo json_encode(array('result'=>$data));
?>
