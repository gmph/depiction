<?php
ini_set("error_reporting", E_ALL ^ E_NOTICE);
session_start();
if (!$_SESSION['twitter']) { 
	die("Oops, make sure you've logged in with your Twitter account before trying to post a new update."); 
}
require("rb.php");
R::setup("sqlite:db.db", 'gmph', 'ok');

$message = $_POST['m'];
$colour = $_POST['c'];

$user = R::findOne("user", "name = ?", array($_SESSION['twitter']['user_id']));

if(!$user->id){ 
	$user = R::dispense("user"); 
}

$user->name = $_SESSION['twitter']['user_id'];
$user->atname = strtolower($_SESSION['twitter']['screen_name']);
$user->message = $message;
$user->colour = $colour;
$user->updated_at = date('d-m-Y-H-i');

R::store($user);

?>
