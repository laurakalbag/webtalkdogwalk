<?php include('perch/runtime.php'); ?>
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
			<h2 class="page-title"><?php perch_content('Upcoming Walks page title'); ?></h2>
			<div class="upcoming-walks walks">
			<?php 
				$opts = array(
				    'filter'=>'eventDateTime',
				    'match'=>'gte',
				    'value'=> date('Y-m-d'),
				    'template'=>'events/listing/event-day.html'
				);

				perch_events_custom($opts);
			?>
			</div>
		</div>
	</div>
	<?php perch_layout('global/footer'); ?>
</body>
</html>