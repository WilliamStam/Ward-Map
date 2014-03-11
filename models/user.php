<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;



use \timer as timer;

class user {
	public $ID;
	private $dbStructure;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	public function get($ID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$result = $f3->get("DB")->exec("
				SELECT wd_users.*
				FROM wd_users
				WHERE wd_users.ID = '$ID'
			");
		if (count($result)) {
			$result = $result[0];
		} else {
			$result = $this->dbStructure();
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $result;
	}

	


	public static function login($username, $password) {
		$f3 = \Base::instance();
		$timer = new timer();

		$ID = "";


		setcookie("username", $username, time() + 31536000, "/");


		

		$password_hash = md5("woof" . $password);


		$result = $f3->get("DB")->exec("
			SELECT ID, username FROM wd_users WHERE username ='$username' AND password = '$password_hash'
		");


		if (count($result)) {
			$result = $result[0];
			$ID = $result['ID'];
			$f3->get("DB")->exec("UPDATE wd_users SET lastlogin = now() WHERE ID = '$ID';");
			
			
			$_SESSION['uID'] = $ID;
			if (isset($_COOKIE['username'])) {
				$_COOKIE['username'] = $result['username'];
			} else {
				setcookie("username", $result['username'], time() + 31536000, "/");
			}
		}

		$return = $ID;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	

	public static function save($ID, $values) {
		$f3  = \Base::instance();
		$timer = new timer();
		$user = $f3->get("user");

		if (isset($values['password']) && $values['password']) {
			$values['password'] = md5("woof" . $values['password']);
		}


		$a = new \DB\SQL\Mapper($f3->get("DB"),"wd_users");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			if (isset($a->$key)){
				$a->$key = $value;
			}


		}
		//test_array($user);
		$a->save();

		if (!$a->ID) {
			$ID = $a->_id;
		} else {
			$ID = $a->ID;
		}


		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return $ID;
	}

	


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN wd_users;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		

		return $result;
	}
}
