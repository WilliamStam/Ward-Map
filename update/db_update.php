<?php
/*
 * Date: 2012/07/24
 * Time: 9:47 AM
 */


/*
 * CREATE TABLE IF NOT EXISTS `system` (`ID` int(6) NOT NULL AUTO_INCREMENT,`system` varchar(100) DEFAULT NULL,`value` varchar(100) DEFAULT NULL, PRIMARY KEY (`ID`));
 */
$i = 1;
$sql = array();

	
































if (isset($_GET['debug'])){
	 header("Content-Type: application/json");
	 echo json_encode($sql);
	 exit();
}


/*
function test_array($array) {
	header("Content-Type: application/json");
	echo json_encode($array);
	exit();
}

test_array($sql);
*/