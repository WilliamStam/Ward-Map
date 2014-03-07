<?php
/**
 * User: William
 * Date: 2013/06/03 - 3:20 AM
 */

class app {
	function __construct(){
		$this->f3 = require('lib/f3/base.php');
	}
	function __destruct(){

	}
	function set($key, $val, $ttl = 0){
		return $this->f3->set($key, $val, $ttl);
	}
	function get($key){
		return $this->f3->get($key);
	}
	function route($pattern, $handler, $ttl = 0, $kbps = 0){
		return $this->f3->route($pattern, $handler, $ttl, $kbps);

	}

	function run(){
		return $this->f3->run();
	}

}
