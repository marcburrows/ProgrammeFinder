<?php
include "../classes/search.class.php";
$search = new search;
	
if ($_POST['query']){

	$query = $_POST['query'];
	$programmes = $search->get_results($query, 1, true);
	
	
	

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
	header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
	header("Cache-Control: no-cache, must-revalidate" ); 
	header("Pragma: no-cache" );
	header("Content-type: text/x-json");
	$json['errors'] = 0;
	$json['programmes'] = $programmes;
	$json['msgs'] = "";
	echo json_encode($json);
	exit;
	die;
	
} 
?>