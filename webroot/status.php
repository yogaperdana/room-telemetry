<?php
	require('function.php');
	$sel_date = stripslashes(date('d-m-Y', strtotime($_GET['sel_date'])));
	$sel_time = stripslashes(date('H:i:s', strtotime($_GET['sel_date'])));
	echo content_status($cfg, $data, $sel_date, $sel_time);
?>