<?php
	if (!isset($_GET['for'])) {$_GET['for'] = '';}
	require('config.php');
	
	for ($s=1; $s<=$cfg['sensors']; $s++) {
		if ($cfg['sensor_enable'][$s] == 1) {
			$hs[$cfg['sensor_sort'][$s]] = array('id'=>$s, 'name'=>$cfg['sensor_name'][$s]);
		}
	}
	
	if ($_GET['for'] == 'graph') {
		foreach ($data['record'][$_GET['date']] as $dkey_time => $dval_time) {
			$cdata['insert_time'] = $_GET['date'].' '.$dkey_time;
			foreach ($hs as $dkey => $dval) {
				$cdata[$dval['name']] = $dval_time[$dval['id']];
			}
			$rdata[] = $cdata;
			unset($cdata);
		}
	} else {
		foreach ($data['record'] as $dkey_date => $dval_date) {
			foreach ($dval_date as $dkey_time => $dval_time) {
				$cdata = array($dkey_date.' '.$dkey_time);
				foreach ($hs as $dkey => $dval) {
					array_push($cdata, $dval_time[$dval['id']]);
				}
				$rdata['data'][] = $cdata;
				unset($cdata);
			}
		}
	}
	
	header("content-type:application/json");
	echo json_encode($rdata);
?>