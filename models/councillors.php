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
			SELECT wd_councillors.*, wd_parties.name as party
			FROM wd_councillors LEFT JOIN wd_parties ON wd_councillors.party_ID = wd_parties.ID
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
			SELECT DISTINCT wd_councillors.*, wd_parties.name as party

			FROM wd_councillors LEFT JOIN wd_parties ON wd_councillors.party_ID = wd_parties.ID
			$where
			$orderby
			$limit;
		",''
		);

		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}





}
