<?php
namespace controllers\save;

use \timer as timer;
use \models as models;

class save {

	protected $f3;

	function __construct() {
		$this->f3 = \base::instance();

		$this->f3->set("json", true);
		$this->f3->set("__runJSON", true);
	}

	function __destruct() {


	}


	function candidate() {
		$return = array();



		$return = $_POST;
		$ID = $_REQUEST["candidate_ID"] ? $_REQUEST["candidate_ID"] : "";

		$values = array("ward_ID" => $_REQUEST['ward_ID'] ? $_REQUEST['ward_ID'] : "", "party" => $_REQUEST['candidate_party'] ? $_REQUEST['candidate_party'] : "", "name" => $_REQUEST['candidate_name'] ? $_REQUEST['candidate_name'] : "",);


		$ID = \models\councillors::save($ID, $values);



		return $GLOBALS["output"]['data'] = $return;


	}

	function delete_candidate() {
		$return = array();



		$ID = $_REQUEST["ID"] ? $_REQUEST["ID"] : "";
		
		$ID = \models\councillors::_remove($ID);



		return $GLOBALS["output"]['data'] = $return;


	}

	function upload_candidate() {
		$return = array();

		$cfg = $this->f3->get("cfg");

		//test_array($cfg);

		$ID = $_REQUEST["candidate_ID"] ? $_REQUEST["candidate_ID"] : "";



		$folder = $cfg['upload']['folder'];

		$folder = $folder . "/" . $_REQUEST['ward_ID'] . "/";




		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		/* 
		// Support CORS
		header("Access-Control-Allow-Origin: *");
		// other CORS headers if any...
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			exit; // finish preflight CORS requests here
		}
		*/

// 5 minutes execution time
		@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
		$targetDir = $folder;
//$targetDir = 'uploads';
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
		if (!file_exists($targetDir)) {
			@mkdir($targetDir, 01777, true);
		}

// Get a file name
		if (isset($_REQUEST["name"])) {
			$fileName = $_REQUEST["name"];
		} elseif (!empty($_FILES)) {
			$fileName = $_FILES["file"]["name"];
		} else {
			$fileName = uniqid("file_");
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files	
		if ($cleanupTargetDir) {
			if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}

			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// If temp file is current file proceed to the next
				if ($tmpfilePath == "{$filePath}.part") {
					continue;
				}

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		}


// Open temp file
		if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}

			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}

		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);

// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off 
			rename("{$filePath}.part", $filePath);
		}



		$values = array("ward_ID" => $_REQUEST['ward_ID'] ? $_REQUEST['ward_ID'] : "", "party" => $_REQUEST['candidate_party'] ? $_REQUEST['candidate_party'] : "", "name" => $_REQUEST['candidate_name'] ? $_REQUEST['candidate_name'] : "", "folder" => $_REQUEST['ward_ID'] ? $_REQUEST['ward_ID'] : "", "img" => $fileName,);


		$ID = \models\councillors::save($ID, $values);

// Return Success JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');



	}








}
