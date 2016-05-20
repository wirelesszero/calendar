<!DOCTYPE html>
<html>
<head>
	<title>Calendar</title>
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <!-- Optional theme -->
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
  	<!-- Style theme -->
  	<link rel="stylesheet" href="/css/style.css">

</head>
<body>
	<div id="calendar-body" class="container">
	<?php
		require_once '../app/views/calendar.php';
	?>
	</div>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function(){
  			$('[data-toggle="popover"]').popover({
  				container: 'body',
        		html : true,
        		content: function() {
        			var jsonEvents = $(this).data('events');
        			var htmlEvents = '<table class="table"><tbody><thead><th>Event Title</th><th>Event Start Date</th><th>Event Finish Date</th></thead>';
        			$.each(jsonEvents, function (index, value) {
        				var firstDate = new Date (value.finish_date);
        				var nowDate = new Date ();
        				if (firstDate < nowDate) {
        					var row_class = 'gray';
        					var row_span = '<span class="glyphicon glyphicon-time" aria-hidden="true"></span>';
        				} else {
        					var row_class = '';
        					var row_span = '';
        				}
        				htmlEvents = htmlEvents + '<tr class="'+row_class+'"><td nowrap>'+value.title+' '+row_span+'</td><td nowrap>'+value.start_date+'</td><td nowrap>'+value.finish_date+'</td></tr>';
					});
					htmlEvents = htmlEvents + '</tbody></table>';
          			return htmlEvents;
    			}
    		});
		});
	</script>
</body>
</html>