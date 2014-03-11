<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class login {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		
		$heading = "Login";
		
		if (count($_POST)){
			
			$username = isset($_POST['username'])?$_POST['username']:"";
			$password = isset($_POST['password'])?$_POST['password']:"";
			
			
			$ID = \models\user::login($username,$password);
			if ($ID){
				$this->f3->reroute("/");
			} else {
				$heading = "Login Failed";
			}
			
		} else {
			$username = isset($_COOKIE['username'])?$_COOKIE['username']:"";
		}
		
		
		$cfg = $this->f3->get("cfg");
		$tmpl = new \template("_.tmpl", "ui/", true);
		$tmpl->page = array(
			"section"=> "",
			"sub_section"=> "",
			"template"=> "login",
			"meta"    => array(
				"title"=> $cfg['default_title'],
			),
			//"help"=> "/apps/nf/help/bookings"
		);
		$tmpl->heading = $heading;
		$tmpl->username = $username;
		$tmpl->output();

	}


}
