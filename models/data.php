<?php
/**
 * User: William
 * Date: 2013/03/20 - 11:35 AM
 */
namespace models;
use \timer as timer;

class data extends _ {

	function __construct($ID) {
		parent::__construct();

		$this->ID = $ID;

	}



	function getWard() {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		$wardID = $this->ID;


		//$json = file_get_contents('http://iec.code4sa.org/votes/by_ward/?ward='.$wardID.'&format=json');
		$json = file_get_contents("http://iec-v2.code4sa.org/national/2009/ward/$wardID/");
		$obj = json_decode($json);
		
		//test_array($obj);
		$return["municipality"] = $obj->municipality;
		$return["province"] = $obj->province;
		$return["ward_id"] = $obj->ward_id;
		
		
		$results = $obj->results;
		//$results = json_decode($results);
		
	
	//	test_array($results);

		
		$return["results"]['registered'] = $results->meta->num_registered;
		$return["results"]['voted'] = $results->meta->total_votes;
		$return["results"]['spoilt'] = $results->meta->spoilt_votes;
		$return["results"]['special'] = $results->meta->special_votes;
		$return["results"]['section_24a'] = $results->meta->section_24a_votes;
		//$return["results"]['2009']['meta'] = $result;
		
		
		$result = array();
		foreach ($results->vote_count as $k=>$v){

			$result[] = array(
				"party"=>$k,
				"votes"=>$v,
			);
		}
		$return["results"]['parties'] = $result;
		
		

		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}





}
