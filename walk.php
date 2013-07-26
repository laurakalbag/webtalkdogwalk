<?php include('perch/runtime.php'); ?>
<?php 
	//we can get the event Title using perch_events_event_field
	$title = perch_events_event_field(perch_get('event'),'eventTitle',true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $title; ?> - Web Talk Dog Walk</title>
    <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>

<body>
	<?php perch_layout('global/header'); ?>
	<div class="main">
		<a class="logo" href="http://webtalkdogwalk.in/brighton"><img src="images/web-talk-dog-walk.png" alt=""/></a>
		<div class="content">
			<aside class="widgets walk">
				<div class="widget next-walk">
					<?php 
						$opts = array(
						    'filter'=>'eventDateTime',
						    'count'=>1,
						    'match'=>'gte',
						    'value'=> date('Y-m-d'),
						    'template'=>'events/listing/event-widget.html'
						);

						perch_events_custom($opts);
					?>
				</div>
				<div class="widget forecast">
					<h3>Forecast</h3>
					<p class="forecast-weather <?php perch_content('Forecast weather slug'); ?>"><?php perch_content('Forecast weather'); ?></p>
					<?php 
						$opts = array(
						    'filter'=>'eventDateTime',
						    'count'=>1,
						    'match'=>'gte',
						    'value'=> date('Y-m-d'),
						    'template'=>'events/date.html'
						);

						perch_events_custom($opts);
					?>
				</div>
				<div class="widget lead-by">
					<h3>Lead by</h3>
					<p><?php perch_content('Lead by'); ?></p>
					<?php perch_content('Lead by photo'); ?>
				</div>
			</aside>
			<?php 		
				perch_events_custom(array(
					'filter'=>'eventSlug',
					'match'=>'eq',
					'value'=>perch_get('event'),
					'template'=>'events/event.html'
				));
			?>	
		</div>
	</div>
	<?php perch_layout('global/footer'); ?>
</body>
</html>