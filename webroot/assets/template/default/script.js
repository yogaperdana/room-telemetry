$(function(){
	$('#ingroup-status').datetimepicker({
		format: 'DD-MM-YYYY HH:mm:ss',
		showTodayButton: true,
		showClear: true,
		showClose: true,
		sideBySide: true,
		toolbarPlacement: 'bottom'
	});
	$('#ingroup-graph').datetimepicker({
		format: 'DD-MM-YYYY',
		showTodayButton: true,
		showClear: true,
		showClose: true
	});
	$('#ingroup-history').datetimepicker({
		format: 'DD-MM-YYYY',
		showTodayButton: true,
		showClear: true,
		showClose: true
	});
	$('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function() {
	$('body').scrollspy({
		target: '.sidebar',
		offset: 70
	});
	
	var clone_status = $("#content-status").clone();
	$('#reset-status').click(function(){
		$("#time-status").val(last_data_full);
		$("#content-status").replaceWith(clone_status.clone());
	});

	$('#filter-status').click(function(){
		var sel_date = $('#time-status').val();
		if (sel_date != '') {
			if (sel_date != last_data_full) {
				$.ajax({
					url: 'status.php',
					method: 'GET',
					data: {sel_date:sel_date},
					success: function(data) {
						$('#content-status').html(data);
					}
				});
			}
		} else {
			alert("Mohon isi tanggal dan waktu");
		}
	});
	
	var loadingtext = '<div class="alert alert-info">' +
		'<span class="glyphicon glyphicon-hourglass"></span> ' +
		'Mohon tunggu sebentar...</div>';
	
	function get_graph(sel_date) {
		$.ajax({
			url: 'graph.php',
			method: 'GET',
			data: {sel_date:sel_date},
			beforeSend: function() {
				$('#content-graph').html(loadingtext);
			},
			success: function(data) {
				$('#content-graph').html(data);
			}
		});
	};
	
	$('#reset-graph').click(function(){
		$("#date-graph").val(last_data_date);
		get_graph(last_data_date);
	});
	
	$('#filter-graph').click(function(){
		var sel_date = $('#date-graph').val();
		if (sel_date != '') {
			get_graph(sel_date);
		} else {
			alert("Mohon isi tanggal");
		}
	});
	
	$('#reset-history').click(function(){
		$("#date-history").val(last_data_date);
		$('#history').DataTable().columns(0).search(last_data_date).draw();
	});
	
	$('#filter-history').on('keyup click', function() {
		$('#history').DataTable().columns(0).search(
			$('#date-history').val()
		).draw();
	});
	
	$('#history').dataTable({
		"columnDefs": [{
			"targets": 'no-sort',
			"orderable": false,
		}],
        "processing": true,
        "ajax": "data.json.php",
		"aoSearchCols": [
			{"sSearch": last_data_date}
		],
		"sDom": 'r<"table-responsive"t><"row"<"col-xs-12"<"well well-sm"i>>><"row"<"col-xs-4"l><"col-xs-8"p>>',
		"oLanguage": {
			"sLoadingRecords": "Mohon tunggu sebentar...",
			"sZeroRecords": "Tidak ada data",
			"sInfo": "Menampilkan _START_-_END_ dari _TOTAL_ data",
			"sInfoEmpty": "Tidak ada data yang ditampilkan",
			"sInfoFiltered": "(disaring dari total _MAX_ data)",
			"sLengthMenu": '<div class="input-group">' +
				'<span class="input-group-addon">Tampilkan:</span>' +
				'<select class="form-control" id="history-length">' +
				'<option value="10">10</option>' +
				'<option value="25">25</option>' +
				'<option value="50">50</option>' +
				'<option value="100">100</option>' +
				'</select>' +
				'</div>',
			"oPaginate": {
				"sPrevious": "<span class=\"glyphicon glyphicon-chevron-left\"></span>",
				"sNext": "<span class=\"glyphicon glyphicon-chevron-right\"></span>"
			}
		}
	});

});
