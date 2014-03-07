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
		$tmpl = new \template("_.tmpl", "ui/", true);
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "map",
			"meta"    => array(
				"title"=> "DC34 - Wards",
			),
			//"help"=> "/apps/nf/help/bookings"
		);
		$tmpl->output();

	}


}
