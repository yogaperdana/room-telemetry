<?php
$cfg['user'] = 'root';
$cfg['password'] = '';
$cfg['host'] = 'localhost';
$cfg['database'] = 'telemetri';
$cfg['pagetitle'] = 'Pemantau Ruangan';
$cfg['timezone'] = 'Asia/Jakarta';
$cfg['sensors'] = 8;

/* don't change below */
error_reporting(E_ALL);
date_default_timezone_set($cfg['timezone']);

$con = mysqli_connect($cfg['host'], $cfg['user'], $cfg['password'], $cfg['database']);
if (!$con) exit;

include('post.php');

$que = mysqli_query($con, "select * from sensor_config where config_name like '%sensor_%'");
while($row = mysqli_fetch_array($que)) {
	$exp = explode("|", $row['config_value']);
	$cfg[$row['config_name']] = array_combine(range(1, count($exp)), $exp);
	unset($exp);
}
unset($row);
mysqli_free_result($que);

$que = mysqli_query($con, "select * from sensor_data order by `insert_time` asc");
if (mysqli_num_rows($que) > 0) {
	while($row = mysqli_fetch_array($que)) {
		$data['last'][0] = $row['insert_time'];
		for ($s=1; $s<=$cfg['sensors']; $s++) {
			$data['record'][date('d-m-Y', strtotime($row['insert_time']))]
				[date('H:i:s', strtotime($row['insert_time']))][$s] = $row['sensor'.$s];
		}
	}
	unset($row);
} else {
	$data['last'][0] = 0;
}
mysqli_free_result($que);

$data['last'][1] = date('d-m-Y', strtotime($data['last'][0]));
$data['last'][2] = date('H:i:s', strtotime($data['last'][0]));
?>