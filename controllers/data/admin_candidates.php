<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\data;

use \timer as timer;
use \models as models;


class admin_candidates extends data {
	function __construct() {
		parent::__construct();


	}

	function details(){
		$return = array();

		$ID = $this->f3->get("PARAMS['ID']");

		$details = new models\councillors();
		$details = $details->get($ID);

		$return = $details;

		$parties = $this->f3->get("DB")->exec("
			SELECT DISTINCT party
			FROM wd_councillors;
		",''
		);
		$p = array();
		foreach ($parties as $item){
			
			$part = str_replace("'","&quot;",$item['party']);
			$p[] = "\"".$part."\"";
		}
		
		$p = implode(",",$p);
		
		

		$return['list']['party_typeahead'] ="[".$p."]";
		
		

		return $GLOBALS["output"]['data'] = $return;


	}

}
