<?php include('perch/runtime.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php perch_pages_title(); ?> - Web Talk Dog Walk</title>
    <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
	<?php perch_layout('global/header'); ?>
	<div class="main">
		<a class="logo" href="index.php"><img src="images/web-talk-dog-walk.png" alt=""/></a>
		<div class="content">
			<h2><?php perch_content('Intro'); ?></h2>
			<aside class="next-walk widgets">
				<div class="widget">
					<h3>Next walk</h3>
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
				<div class="widget">
					<h3>Forecast</h3>
					<p class="forecast-weather <?php perch_content('Forecast weather slug'); ?>"><?php perch_content('Forecast weather'); ?></p>
					<p>for 24th July</p>
				</div>
				<div class="widget">
					<h3>Lead by</h3>
					<p><?php perch_content('Lead by'); ?></p>
					<?php perch_content('Lead by photo'); ?>
				</div>
			</aside>
			<?php perch_content('Main About content'); ?>
		</div>
	</div>
	<?php perch_layout('global/footer'); ?>
</body>
</html>