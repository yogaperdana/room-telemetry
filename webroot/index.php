<?php require('function.php'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $cfg['pagetitle']; ?></title>
	<meta name="author" content="Yoga Perdana Putra">
	<link rel="stylesheet" href="assets/3rdparty/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="assets/3rdparty/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="assets/3rdparty/datatables/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="assets/3rdparty/ie10-viewport/ie10-viewport-bug-workaround.css">
	<!--[if lt IE 9]>
		<script src="assets/3rdparty/html5shiv/html5shiv.js"></script>
		<script src="assets/3rdparty/respond/respond.min.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="assets/template/default/style.css">
</head>
<body>
	<nav class="navbar navbar-fixed-top navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" 
					data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="./"><?php echo $cfg['pagetitle']; ?></a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#page-status"><span class="glyphicon glyphicon-certificate"></span> Status</a></li>
					<li><a href="#page-graph"><span class="glyphicon glyphicon-stats"></span> Statistik</a></li>
					<li><a href="#page-history"><span class="glyphicon glyphicon-list-alt"></span> Riwayat</a></li>
					<li><a href="void();" data-toggle="modal" data-target=".page-setting">
						<span class="glyphicon glyphicon-cog"></span> Pengaturan
					</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<?php echo setting_update_alert($post); ?>
		<div class="row section" id="page-status">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-xs-3 col-sm-6 col-md-8">
						<h2 class="no-margin">Status</h2>
					</div>
					<div class="col-xs-9 col-sm-6 col-md-4">
						<div class="pull-right">
							<form class="form-group" id="form-status-time">
								<div class="input-group date" id="ingroup-status">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
									<input class="form-control" type="text" id="time-status" autocomplete="off" 
										value="<?php echo $data['last'][1]." ".$data['last'][2]; ?>" />
									<span class="input-group-btn">
										<button class="btn btn-default" type="button" id="reset-status" 
											data-toggle="tooltip" data-placement="bottom" 
											title="Kembali ke waktu data terakhir">
											<span class="glyphicon glyphicon-repeat"></span>
										</button>
										<button class="btn btn-primary" type="button" id="filter-status">Lihat</button>
									</span>
								</div>
							</form>
						</div>
					</div>
				</div>
				<hr>
				<?php echo content_lastdata($data); ?>
				<div class="row">
					<div class="col-xs-12" id="content-status">
						<?php echo content_status($cfg, $data, $data['last'][1], $data['last'][2]); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row section" id="page-graph">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-xs-3 col-sm-6 col-md-8">
						<h2 class="no-margin">Statistik</h2>
					</div>
					<div class="col-xs-9 col-sm-6 col-md-4">
						<div class="pull-right">
							<form class="form-group" id="form-graph-date">
								<div class="input-group date" id="ingroup-graph">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
									<input class="form-control" type="text" id="date-graph" autocomplete="off" 
										value="<?php echo $data['last'][1]; ?>" />
									<span class="input-group-btn">
										<button class="btn btn-default" type="button" id="reset-graph" 
											data-toggle="tooltip" data-placement="bottom" 
											title="Kembali ke tanggal data terakhir">
											<span class="glyphicon glyphicon-repeat"></span>
										</button>
										<button class="btn btn-primary" type="button" id="filter-graph">Lihat</button>
									</span>
								</div>
							</form>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<script src="assets/3rdparty/d3/d3.min.js"></script>
					<div class="col-xs-12" id="content-graph">
						<?php echo content_graph($data, $data['last'][1]); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row section" id="page-history">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-xs-3 col-sm-6 col-md-8">
						<h2 class="no-margin">Riwayat</h2>
					</div>
					<div class="col-xs-9 col-sm-6 col-md-4">
						<div class="pull-right">
							<form class="form-group" id="form-history-date">
								<div class="input-group date" id="ingroup-history">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
									<input class="form-control" type="text" id="date-history" autocomplete="off" 
										value="<?php echo $data['last'][1]; ?>" />
									<span class="input-group-btn">
										<button class="btn btn-default" type="button" id="reset-history" 
											data-toggle="tooltip" data-placement="bottom" 
											title="Kembali ke tanggal data terakhir">
											<span class="glyphicon glyphicon-repeat"></span>
										</button>
										<button class="btn btn-primary" type="button" id="filter-history">Lihat</button>
									</span>
								</div>
							</form>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-12" id="content-history">
						<?php echo content_history($cfg, $data); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade page-setting" tabindex="-1" role="dialog" aria-labelledby="page-setting">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="" method="post">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="page-setting">Pengaturan</h4>
					</div>
					<div class="modal-body">
						<?php echo content_setting($cfg, $data); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
						<button type="submit" class="btn btn-primary" name="setting-submit">Simpan Perubahan</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="assets/3rdparty/jquery/jquery-2.2.4.min.js"></script>
	<script src="assets/3rdparty/ie10-viewport/ie10-viewport-bug-workaround.js"></script>
	<script src="assets/3rdparty/bootstrap/bootstrap.min.js"></script>
	<script src="assets/3rdparty/moment/moment.min.js"></script>
	<script src="assets/3rdparty/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script src="assets/3rdparty/datatables/jquery.dataTables.min.js"></script>
	<script src="assets/3rdparty/datatables/dataTables.bootstrap.min.js"></script>
	<script>
		var last_data_full = '<?php echo $data['last'][1]." ".$data['last'][2]; ?>';
		var last_data_date = '<?php echo $data['last'][1]; ?>';
	</script>
	<script src="assets/template/default/script.js"></script>
</body>
</html>