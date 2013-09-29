<?php
ini_set("error_reporting", E_ALL ^ E_NOTICE);
session_start();
session_destroy();

header( 'Location: http://depiction.co' );

?>
