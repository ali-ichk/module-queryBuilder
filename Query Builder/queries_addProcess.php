<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$search=NULL ;
if (isset($_GET["search"])) {
	$search=$_GET["search"] ;
}
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/queries_add.php&sidebar=false&search=$search" ;

if (isActionAccessible($guid, $connection2, "/modules/Query Builder/queries_add.php")==FALSE) {
	//Fail 0
	$URL=$URL . "&addReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	//Validate Inputs
	$type=$_POST["type"] ;
	$name=$_POST["name"] ;
	$category=$_POST["category"] ;
	$active=$_POST["active"] ;
	$description=$_POST["description"] ;
	$query=$_POST["query"] ;
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
	
	if ($type=="" OR $name=="" OR $category=="" OR $active=="" OR $query=="") {
		//Fail 3
		$URL=$URL . "&addReturn=fail3" ;
		header("Location: {$URL}");
	}
	else {
		//Write to database
		try {
			$data=array("type"=>$type, "name"=>$name, "category"=>$category, "active"=>$active, "description"=>$description, "query"=>$query, "gibbonPersonID"=>$gibbonPersonID); 
			$sql="INSERT INTO queryBuilderQuery SET type=:type, name=:name, category=:category, active=:active, description=:description, query=:query, gibbonPersonID=:gibbonPersonID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			//Fail 2
			$URL=$URL . "&addReturn=fail2" ;
			header("Location: {$URL}");
			exit() ;
		}
		
		//Success 0
		$URL=$URL . "&addReturn=success0" ;
		header("Location: {$URL}");
	}
}
?>