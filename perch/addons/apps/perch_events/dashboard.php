<?php
	include('PerchEvents_Events.class.php');
	include('PerchEvents_Event.class.php');
	include('PerchEvents_Categories.class.php');
    include('PerchEvents_Category.class.php');

    $API   = new PerchAPI(1.0, 'perch_events');
    $Lang  = $API->get('Lang');

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

    $Events = new PerchEvents_Events($API);
    $events = $Events->all($Paging);

?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Events'); ?>
		<a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_events/edit/'); ?>" class="add button"><?php echo $Lang->get('Add Event'); ?></a>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($events)) {
				echo '<ul>';
				foreach($events as $Event) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_events/edit/?id='.$Event->id()).'">';
							echo PerchUtil::html($Event->eventTitle());
							echo '<span class="note">'.PerchUtil::html(strftime('%d %B %Y, %l:%M %p', strtotime($Event->eventDateTime()))).'</span>';
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>
</div>