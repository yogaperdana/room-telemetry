<?php
	require('function.php');
	$sel_date = stripslashes($_GET['sel_date']);
	echo content_graph($data, $sel_date);
?>