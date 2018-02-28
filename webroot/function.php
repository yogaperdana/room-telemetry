<?php
require('config.php');

function content_alert($sel_date, $sel_time=null) {
	$out = '
		<div class="alert alert-danger" role="alert">
			<span class="glyphicon glyphicon-alert" aria-hidden="true"></span> 
			<strong>Tidak ada catatan data pada '.$sel_date.' '.$sel_time.'</strong>
		</div>
	';
	return $out;
}

function content_lastdata(array $data) {
	$out = '';
	if (isset($data['record'][$data['last'][1]][$data['last'][2]])) {
		$out .= '
			<div class="row">
				<div class="col-xs-12">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 
						<strong>Perhatian!</strong> Data tercatat terakhir pada 
						'.$data['last'][1].' '.$data['last'][2].'
					</div>
				</div>
			</div>
		';
	}
	return $out;
}

function content_status(array $cfg, array $data, $sel_date, $sel_time) {
	$out = '';
	if (isset($data['record'][$sel_date][$sel_time])) {
		for ($s=1; $s<=$cfg['sensors']; $s++) {
			if ($cfg['sensor_enable'][$s] == 1) {
				$cd = $data['record'][$sel_date][$sel_time][$s];
				if ($cd <= $cfg['sensor_cutoff'][1]) {
					$rs[$s] = 'bg-primary';
					$ss[$s] = 'ON';
				} else {
					$rs[$s] = 'bg-danger';
					$ss[$s] = 'OFF';
				}
				$re[$cfg['sensor_sort'][$s]] = '
					<div class="col-xs-6 col-sm-3">
						<div class="rect '.$rs[$s].' s-'.$ss[$s].'">
							<span class="rect-text-sm">'.$cfg['sensor_name'][$s].'</span>
							<h2>'.$ss[$s].' <small>'.$cd.'</small></h2>
						</div>
					</div>
				';
			}
		}
		$out .= '<div class="row">';
		ksort($re);
		foreach ($re as $skey => $sval) {
			$out .= $sval;
		}
		$out .= '</div>';
	} else {
		$out .= content_alert($sel_date, $sel_time);
	}
	return $out;
}

function content_graph(array $data, $sel_date) {
	$out = '';
	if (isset($data['record'][$sel_date])) {
		$out .= '
			<svg id="graph-container" class="graph-container"></svg>
			<script src="graph.js"></script>
		';
	} else {
		$out .= content_alert($sel_date);
	}
	return $out;
}

function content_history(array $cfg, array $data) {
	$out = '';
	for ($s=1; $s<=$cfg['sensors']; $s++) {
		if ($cfg['sensor_enable'][$s] == 1) {
			$hs[$cfg['sensor_sort'][$s]] = $cfg['sensor_name'][$s];
		}
	}
	$out .= '
		<table class="table table-condensed table-hover table-bordered" 
			id="history" cellspacing="0" width="100%">
			<thead>
				<tr class="active">
					<th style="">Waktu</th>
	';
	ksort($hs);
	foreach ($hs as $hkey => $hval) {
		$out .= '<th class="no-sort" nowrap>'.$hval.'</th>';
	}
	$out .= '
				</tr>
			</thead>
		</table>
	';
	return $out;
}

function content_setting(array $cfg, array $data) {
	$out = '';
	$out .= '
		<table class="table table-bordered table-condensed table-page-setting">
			<thead>
				<tr class="active">
					<th width="8%">#</th>
					<th width="8%">Aktif</th>
					<th>Nama</th>
					<th width="12%">Urutan</th>
				</tr>
			</thead>
			<tbody>
	';
	for ($s=1; $s<=$cfg['sensors']; $s++) {
		if ($cfg['sensor_enable'][$s] == 1) {
			$sel = ' checked';
		} else {
			$sel = '';
		}
		$out .= '
				<tr>
					<td>'.$s.'</td>
					<td>
						<input type="hidden" name="sensor_enable['.$s.']" value="0">
						<input type="checkbox" name="sensor_enable['.$s.']" 
							value="1"'.$sel.' autocomplete="off">
					</td>
					<td>
						<input type="text" class="form-control input-sm" 
							placeholder="Sensor '.$s.'" autocomplete="off" required 
							name="sensor_name['.$s.']" value="'.$cfg['sensor_name'][$s].'">
					</td>
					<td>
						<input type="number" class="form-control input-sm" 
							size="1" min="1" max="8" autocomplete="off" required 
							name="sensor_sort['.$s.']" value="'.$cfg['sensor_sort'][$s].'">
					</td>
				</tr>
		';
	}
	$out .= '
			</tbody>
		</table>
		<div class="input-group">
			<span class="input-group-addon" id="setting-cutoff">Nilai cut-off sensor</span>
			<input type="number" class="form-control" size="4" min="0" max="1023" required 
				aria-describedby="setting-cutoff" autocomplete="off" placeholder="0-1023" 
				name="sensor_cutoff" value="'.$cfg['sensor_cutoff'][1].'">
		</div>
	';
	return $out;
}
?>