<?php
if (isset($_POST['setting-submit'])) {
	$post = array(
		'sensor_enable' => mysqli_real_escape_string($con, implode("|", $_POST['sensor_enable'])),
		'sensor_name' => mysqli_real_escape_string($con, implode("|", $_POST['sensor_name'])),
		'sensor_sort' => mysqli_real_escape_string($con, implode("|", $_POST['sensor_sort'])),
		'sensor_cutoff' => mysqli_real_escape_string($con, $_POST['sensor_cutoff']),
	);
	$stmt = "update sensor_config set config_value = '".$post['sensor_enable']."' where config_name = 'sensor_enable';".
			"update sensor_config set config_value = '".$post['sensor_name']."' where config_name = 'sensor_name';".
			"update sensor_config set config_value = '".$post['sensor_sort']."' where config_name = 'sensor_sort';".
			"update sensor_config set config_value = '".$post['sensor_cutoff']."' where config_name = 'sensor_cutoff';";
	if (mysqli_multi_query($con, $stmt)) {
		do {
			if ($result = mysqli_store_result($con)) {
				mysqli_free_result($result);
			}
		} while (mysqli_more_results($con) && mysqli_next_result($con));
	}
	if ($querr = mysqli_error($con)) {
		function setting_update_alert($post) {
			$out = '
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
							<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 
							'.$querr.'
						</div>
					</div>
				</div>
			';
			return $out;
		}
	} else {
		function setting_update_alert($post) {
			$out = '
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
							<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
							Pengaturan telah berhasil diperbarui.
						</div>
					</div>
				</div>
			';
			return $out;
		}
	}
} else {
	$post = null;
	function setting_update_alert($post) {
		return null;
	}
}
?>