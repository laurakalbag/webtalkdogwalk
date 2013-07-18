<?php
    
	if (!$CurrentUser->has_priv('perch_events.categories.manage')) {
        exit;
    }
    
    $Categories = new PerchEvents_Categories($API);

    $HTML = $API->get('HTML');

    $categories = $Categories->all();
    
   

?>