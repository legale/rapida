<?php

session_start();

	$query1 = '';
	$query2 = '';
	$r1 = 10;
	$r2 = 10;
	
if(isset($_POST['start']) && isset($_SESSION['admin'])){
	$query1 = $_POST['q1']['sql'];
	$query2 = $_POST['q2']['sql'];
	$r1 = $_POST['q1']['rounds'];
	$r2 = $_POST['q2']['rounds'];
}


?>

<HTML>
	<HEAD>
		<style type="text/css">
			textarea { 
			font-size: 11px;
			font-family: Verdana, Arial, Helvetica, sans-serif;
			color: #000;
			width: 100%;
			height: 150px;
			
			}
		</style>

	</HEAD>
	<BODY>
MYSQL QUERIES SPEED TEST
	<form method="post" enctype="multipart/form-data">
		<div>
			
			<label>query1 rounds</label>
			<input type="text" name="q1[rounds]" value="<?php print $r1 ?>">
			<label>query2 rounds</label>
			<input type="text" name="q2[rounds]" value="<?php print $r2 ?>">
		</div>
		<div>
			<label>query1</label>
			<textarea alt="query1" type="text" name="q1[sql]"/><?php print $query1 ?></textarea>
		</div>
		<div>
			<label>query2</label>
			<textarea alt="query2" type="text" name="q2[sql]"/><?php print $query2 ?></textarea>
		</div>
		<div>
		<input type="hidden" name="start" value="true">
			<button type="submit">SUBMIT</button>
		</div>
	</form>
	</BODY>
</HTML>




<?php


if(isset($_POST['start']) && isset($_SESSION['admin'])){
	$query1 = $_POST['q1']['sql'];
	$query2 = $_POST['q2']['sql'];
	$r1 = $_POST['q1']['rounds'];
	$r2 = $_POST['q2']['rounds'];
	

print "<pre>";
//~ print_r($_POST);


	if($q1 = query_time($query1, $r1)){
		$atime1 = array_sum($q1) / count($q1);
		print "\n $r1 rounds. \t\t average time query1 in ms: $atime1"; 
	}

	if($q2 = query_time($query2, $r2)){
		$atime2 = array_sum($q2) / count($q2);
		print "\n $r2 rounds. \t\t average time query2 in ms: $atime2"; 
	}


//~ print "\n query1 $query1"; 
//~ print "\n query2 $query2"; 

}elseif(isset($_POST['start']) && !isset($_SESSION['admin'])){
	print "\n LOGIN AS ADMIN TO ENTER QUERIES"; 
	print "\n". print_r($_SESSION); 
}


function query_time($query, $qty){
	if(empty($query) || empty((integer)$qty)){
		return false;
	}
	require_once( dirname(__FILE__) . '/api/Simpla.php');
	$simpla = new Simpla();

	$simpla->db->query($query);

	
	$count = 0;
	$array = array ();

	while ( $count < $qty){
		$time = microtime(true);
		$simpla->db->query($query);
		$time = microtime(true)-$time;
		$time_ms = $time * 1000;
		$array[] = $time_ms;
		$count++;
	}
	return $array;
}
?>
