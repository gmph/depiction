<?php
ini_set("error_reporting", E_ALL ^ E_NOTICE);
require("rb.php");
R::setup("sqlite:db.db", 'gmph', 'ok');

$userid = strtolower($_POST['id']);

$userdata = R::findOne('user',' atname = ? ', array( $userid ));

if(!$userdata->id){ 
	echo json_encode(array( "error" => true )); 
} 
else { 
	$userdata->message = stripslashes($userdata->message);
	$data = json_encode($userdata->export());
	echo $data; 
};
