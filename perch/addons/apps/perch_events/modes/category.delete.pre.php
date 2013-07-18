<?php
    
    if (!$CurrentUser->has_priv('perch_events.categories.manage')) {
        exit;
    }
    
    $Categories = new PerchEvents_Categories($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
	$Form->require_field('categoryID', 'Required');
	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Category = $Categories->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {
    	$postvars = array('categoryID');
		
    	$data = $Form->receive($postvars);
    	
    	$Category = $Categories->find($data['categoryID']);
    	
    	if (is_object($Category)) {
    	    $Category->delete();
            PerchUtil::redirect($API->app_path() .'/categories/');
        }else{
            $message = $HTML->failure_message('Sorry, that category could not be deleted.');
        }
    }

    
    
    $details = $Category->to_array();



?>