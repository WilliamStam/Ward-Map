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
		
		$ward = new models\wards();
		$ward = $ward->get_ward_id($ID);

		$return = $ward;
		$return['councillors']=models\councillors::getAll("wd_councillors.ward_ID='".$ward['ID']."'","name ASC");
		$return['results']=models\results::getAll("wd_results.ward_ID='".$ward['ID']."'","votes DESC");


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
