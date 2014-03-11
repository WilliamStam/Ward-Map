<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class home {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		$cfg = $this->f3->get("cfg");
		$tmpl = new \template("_.tmpl", "ui/", true);
		$tmpl->page = array(
			"section"=> "",
			"sub_section"=> "",
			"template"=> "map",
			"meta"    => array(
				"title"=> $cfg['default_title'],
			),
			//"help"=> "/apps/nf/help/bookings"
		);
		$tmpl->output();

	}


}
