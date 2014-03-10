<?php
namespace controllers\data;
use \timer as timer;
use \models as models;

class data {

	protected $f3;
	function __construct() {
		$this->f3 = \base::instance();

		$this->f3->set("json",true);
		$this->f3->set("__runJSON",true);
	}

	function __destruct() {


	}

	function ward(){
		$return = array();

		$ID = $this->f3->get("PARAMS['ID']");
		
		$dataO = new models\data($ID);
		$data = $dataO->getWard();

		$data['councillors'] = models\councillors::getAll("ward_ID='$ID'","name ASC");

		//test_array($data['councillors']); 
		$r = array();
		foreach ($data['results']['parties'] as $key => $row)
		{
			$r[$key] = $row['votes'];
		}

		//test_array($r); 

		array_multisort($r,  SORT_DESC, $data['results']['parties']);


		//$data['results']['parties'] = array_slice($data['results']['parties'], 0, 5);


		$data['desc'] =  "Ward: ".$data['ward_id'];

		//test_array($data); 
		
		$return = $data;
		//$return['councillors']=models\councillors::getAll("wd_councillors.ward_ID='".$ward['ID']."'","name ASC");
	//	$return['results']=models\results::getAll($ID,5,"votes","DESC");


		// http://iec.code4sa.org/votes/by_ward/?ward=93401002&format=json


		return $GLOBALS["output"]['data'] = $return;
		
		
	}
	function councilor(){
		$return = array();

		$ID = $this->f3->get("PARAMS['ID']");
		
		$details = new models\councillors();
		$details = $details->get($ID);

		$return = $details;
	


		return $GLOBALS["output"]['data'] = $return;
		
		
	}

	






}
