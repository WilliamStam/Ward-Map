<?php
/**
 * User: William
 * Date: 2013/03/20 - 11:35 AM
 */
namespace models;
use \timer as timer;

class councillors extends _ {

	function __construct() {
		parent::__construct();


	}



	function get($ID) {

		$where = "wd_councillors.ID = '$ID'";


		$timer = new timer();
		$result = $this->f3->get("DB")->exec("
			SELECT wd_councillors.*
			FROM wd_councillors
			WHERE $where;
		");


		if (count($result)) {
			$return = $result[0];
		} else {

			$return = parent::dbStructure("wd_councillors");
		}
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}


	public static function getAll($where = "", $orderby = "", $limit = "") {
		$timer = new timer();
		$f3 = \Base::instance();

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($limit) {
			$limit = " LIMIT " . $limit;
		}



		$result = $f3->get("DB")->exec("
			SELECT wd_councillors.*
			FROM wd_councillors
			$where
			$orderby
			$limit;
		",''
		);

		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}


	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();


		$old = array();
		$lookupColumns = array();

		$a = new \DB\SQL\Mapper($f3->get("DB"),"wd_councillors");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}
		}
		
		$a->save();

		$ID = $a->ID;


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}
	public static function _remove($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"wd_councillors");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}

}
