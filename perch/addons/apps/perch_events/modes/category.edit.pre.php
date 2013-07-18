<?php
    if (!$CurrentUser->has_priv('perch_events.categories.manage')) {
        exit;
    }

    
    $Categories = new PerchEvents_Categories($API);

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
	
    $message = false;
    
    
    if (isset($_GET['id']) && $_GET['id']!='') {
        $categoryID = (int) $_GET['id'];    
        $Category = $Categories->find($categoryID);
        $details = $Category->to_array();
    }else{
        $message = $HTML->failure_message('Sorry, that category could not be updated.');
    }
    
    
    

    $Form->require_field('categoryTitle', 'Required');

    if ($Form->submitted()) {
		$postvars = array('categoryID','categoryTitle');
		
    	$data = $Form->receive($postvars);
    	
        $Category->update($data);
    	
        if (is_object($Category)) {
            $message = $HTML->success_message('Your category has been successfully edited. Return to %scategory listing%s', '<a href="'.$API->app_path() .'/categories">', '</a>');
        }else{
            $message = $HTML->failure_message('Sorry, that category could not be edited.');
        }
        
        $details = $Category->to_array();
    }

    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your category has been successfully created. Return to %scategory listing%s', '<a href="'.$API->app_path() .'/categories">', '</a>');
    }

?>