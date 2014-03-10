<?php
/**
 * User: William
 * Date: 2013/03/20 - 11:35 AM
 */
namespace models;
use \timer as timer;

class results extends _ {

	function __construct() {
		parent::__construct();


	}



	function get($ID) {

		$where = "wd_results.ID = '$ID'";


		$timer = new timer();
		$result = $this->f3->get("DB")->exec("
			SELECT wd_results,*
			FROM wd_results LEFT JOIN wd_parties ON wd_results.party_ID = wd_parties.ID
			WHERE $where;
		");


		if (count($result)) {
			$return = $result[0];
		} else {

			$return = parent::dbStructure("wd_wards");
		}
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}



	public static function getAll($wardID,$limit="",$order_column="votes",$order_direction="DESC") {
		$timer = new timer();
		$f3 = \Base::instance();
		$result = array();

	
		

		//$json = file_get_contents('http://iec.code4sa.org/votes/by_ward/?ward='.$wardID.'&format=json');
		$json = file_get_contents("http://iec-v2.code4sa.org/national/2009/ward/$wardID/");
		$obj = json_decode($json);
		
		test_array($obj); 
		$obj = $obj->results;
		$obj = $obj[0];
		$obj = $obj->votes;
		//test_array($obj); 
		foreach ($obj as $item){

			$result[] = array(
				"party"=>$item->party,
				"votes"=>$item->votes,
			);
		}



		$return = array();
		foreach ($result as $key => $row)
		{
			$return[$key] = $row[$order_column];
		}
		
		if ($order_direction=="ASC"){
			
		}
		
		array_multisort($return,  ($order_direction=="DESC")?SORT_DESC:SORT_ASC, $result);
		
		
		if ($limit){
			$result = array_slice($result, 0, $limit);
		}
		
		//test_array($result); 
		
	

		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}





}
