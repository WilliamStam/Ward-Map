<?php
/*
require_once('/inc/app.php');
$app = new app();
$app->set("t","hi");
$app->route("GET /",function() use ($app) {
		$t = $app->get("t");
		echo "woof".$t;
	});
$app->run();
exit();*/
date_default_timezone_set('Africa/Johannesburg');
//ini_set('memory_limit', '256M');
if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}

$GLOBALS["models"] = array();
$GLOBALS["css"] = array();
$GLOBALS["js"] = array();
$GLOBALS["output"] = array();
$GLOBALS["render"] = "";

$f3 = require('lib/f3/base.php');
require_once('inc/general.php');

require_once('/inc/class.timer.php');
require_once('/inc/functions.php');
require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();


//require_once('inc/class.msg.php');
require_once('/inc/class.template.php');
//require_once('inc/class.store.php');

$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}

$general = new general();
$general->pageStart();

function pageEnd() {
	$general = new general();
	$general->pageEnd();

}

$version = "1";
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}


$GLOBALS['cfg'] = $cfg;

$f3->set('AUTOLOAD', './|lib/|controllers/|inc/');
$f3->set('PLUGINS', 'lib/f3/|lib/mods/');
$f3->set('CACHE', true);

$f3->set('DEBUG', 2);
$f3->set('ONERROR', 'Error::handler');



$f3->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password']));
$f3->set('cfg', $cfg);

$f3->set('UNLOAD', 'pageEnd');

$f3->set('UI', 'ui/|media/');
$f3->set('MEDIA', './media/');
$f3->set('VERSION', $version);




$f3->route('GET /thumb/councillor/@ID/@width/@height/*', function ($f3, $params) {
		$cfg = $f3->get("cfg");
		$f3->set("json",False);

		
		
		$ID = $params['ID'];
		$details = new models\councillors();
		$details = $details->get($ID);
		
		
		$width = $params['width'];
		$height = $params['height'];
		
		
		
		
		$return = array(
			"ID"=>$ID,
			"width"=>$width,
			"height"=>$height,
			"details"=>$details,
			"cfg"=>$cfg
		);
		//test_array($return);
		if ($details['ID']){
			$folder = $cfg['upload']['folder']."/".$details['folder']."/";
			$folder = $f3->fixslashes($folder);
			$folder = str_replace("//","/",$folder);
			$folder = str_replace("//","/",$folder);
			
			$file = $folder . $details['img'];
			
			if (!file_exists($file))$f3->error(404);
			
			$return["folder"] = $folder;
			$return["full_path"] = $file;

			
			//test_array($return); 
			

			$thumb = new \mods_Image($file);

			$thumb->resize($width, $height, false);
			$thumb->render();


			//test_array($return);

		} else {
			$f3->error(404);
		}
		


		


	}
);



$f3->route('GET /min/js*', 'general->_javascript');
$f3->route('GET /', 'controllers\home->page');
$f3->route('GET /data/ward/@ID', 'controllers\data\data->ward');
$f3->route('GET /data/councillor/@ID', 'controllers\data\data->councilor');



$f3->route('GET /php', function () {
		phpinfo();
		exit();
	}
);


$f3->run();
