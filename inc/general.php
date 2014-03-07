<?php
/*
 * Date: 2012/02/23
 * Time: 12:55 PM
 */

class general {
	function __construct() {
		$this->f3 = \base::instance();
	}



	function _javascript() {
		$expires = 60 * 60 * 24 * 14;
		header("Pragma: public");
		header("Cache-Control: maxage=" . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

		$file = (isset($_GET['file'])) ? $_GET['file'] : "";
		//$file = $this->f3->get('PARAMS.filename');
		header("Content-Type: application/javascript");
		$files = array(
			"/ui/_js/plugins/jquery.jqote2.js",
			"/ui/_js/plugins/jquery.ba-bbq.js",
			"/ui/_js/plugins/jquery.cookie.js",
			"/ui/_js/plugins/jquery.getData.js",
			"/ui/_js/plugins/jquery.roundabout.js",
			"/ui/_js/plugins/jquery.roundabout-shapes.js",

			"/ui/fancybox/jquery.fancybox.js",
			"/ui/_js/scripts.js",
		);

		if (isset($_GET['s'])) {
			$extras = explode(",", $_GET['s']);
			foreach ($extras as $file) {
				$files[] = "/ui/_js/" . $file . ".js";
			}
		}


		//test_array($files);

		$t = "";
		foreach ($files as $file) {
			$fileDetails = pathinfo(($file));
			$base = "." . $fileDetails['dirname'] . "/";
			$file = $fileDetails['basename'];

			$t .= file_get_contents($base . $file);

		}

		$this->f3->set("__noPageEnd", true);

		echo $t;
	}
	function pageStart() {
		$GLOBALS['page_execute_timer'] = new timer(true);
		$this->f3->set("__runTemplate",false);
		ob_start();
	}

	function pageEnd() {


		$f3 = \Base::instance();
		if ($f3->get("__testJson")) exit();

		$GLOBALS["render"] = ob_get_contents();
		$pageSize = ob_get_length();
		ob_end_clean();
		$models = $GLOBALS['models'];
		$t = array();
		foreach ($models as $model) {
			$c = array();
			foreach ($model['m'] as $method) {
				$c[] = $method;
			}
			$model['m'] = $c;
			$t[] = $model;
		}

		$models = $t;
		$pageTime = $GLOBALS['page_execute_timer']->stop("Page Execute");

		$GLOBALS["output"]['timer'] = $GLOBALS['timer'];
		$GLOBALS["output"]['models'] = $models;
		$GLOBALS["output"]['page'] = array(
			"page" => $_SERVER['REQUEST_URI'],
			"time" => $pageTime,
			"size" => ($pageSize)
		);
		//test_array($f3->get("__runTemplate"));

		if ($f3->get("__noPageEnd")){
			echo $GLOBALS["render"];

		} else {

			if (($f3->get("AJAX") && ($f3->get("__runTemplate")==false) || $f3->get("__runJSON"))) {
				header("Content-Type: application/json");

				echo json_encode($GLOBALS["output"]);
			} else {

				$timersbottom = '
					<script type="text/javascript">
				       updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
					</script>
				';
				$content = $GLOBALS["render"];
				if (strpos($GLOBALS["render"], "<!--print version-->") || strpos($GLOBALS["render"], "<!--no_timer_list-->")) {
					//	$content = $GLOBALS["render"];
				} else {
					$content = str_replace("<!--timer_list-->", $timersbottom . '<!--timer_list-->', $content);
				}

				$main_inline_css = $GLOBALS['css'];
				$main_inline_js = $GLOBALS['js'];

				//test_array($main_inline_css);

				$p = "";
				foreach ($main_inline_css as $item) {
					$p = $p . $item;
				}
				$main_inline_css = $p;

				$p = "";
				foreach ($main_inline_js as $item) {
					$p = $p . $item;
				}
				$main_inline_js = $p;


				$content = str_replace("<!-- main_inline_css -->", '<style>' . $main_inline_css . '</style>', $content);
				$content = str_replace("<!-- main_inline_js -->", '<script>' . $main_inline_js . '</script>', $content);

				echo $content;
			}
		}


	}


	function upload() {
		$folder = (isset($_GET['folder'])) ? $_GET['folder'] : "";
		//$folder = substr($folder,0,-1);

		$cfg = $this->f3->get("CFG");


		$user = $this->f3->get("user");


		$app = $this->f3->get('PARAMS.app');




		$folder = ($cfg['upload']['folder'] . $app . "/" . $folder);
		$tmpFolder = $cfg['upload']['folder'] . 'tmp/';

		$folder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $folder);
		$tmpFolder = str_replace(array("/","\\"), DIRECTORY_SEPARATOR, $tmpFolder);

/*

		test_array(array(
			           "folder"=>$folder,
			           "tmp_folder"=>$tmpFolder,
			         //  "name"=> $_REQUEST["name"],
			         //  "cfg"  => $cfg,
			         //  "user" => $user,
			           "app"  => $app

		           )
		);

*/
		ini_set('upload_tmp_dir', $tmpFolder);
		ini_set('upload_max_filesize', '20M');
		ini_set('post_max_size', '20M');


		if (!file_exists($tmpFolder)) @mkdir($tmpFolder, 0777, true);
		if (!file_exists($folder)) @mkdir($folder, 0777, true);

		//$targetDir = $cfg['upload']['folder'] . $app . "/temp/";
		//if (!file_exists($targetDir)) @mkdir($targetDir, 0777, true);

		$targetDir = $folder;


		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-Type: application/json");

// Settings
//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";


		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds

// 5 minutes execution time
		@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) $count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Create target dir
		if (!file_exists($targetDir)) @mkdir($targetDir);

// Remove old temp files
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}

			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"])) $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"])) $contentType = $_SERVER["CONTENT_TYPE"];

// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096)) fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096)) fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
		}

/*
		$exif = @exif_read_data($targetDir . DIRECTORY_SEPARATOR . $fileName,0,true);
// Return JSON-RPC response

		$result = array(
			"jsonrpc"=>"2.0",
			"result"=>"null",
			"id"=>"id"
		);

		if ($exif){
			$result['exif']['meta'] = $exif['IFD0'];
		}
		test_array($result);*/
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

	}


}
