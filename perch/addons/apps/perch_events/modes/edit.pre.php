<?php
    
    $Events = new PerchEvents_Events($API);
    $message = false;
    $Categories = new PerchEvents_Categories($API);
    $categories = $Categories->all();

    $HTML = $API->get('HTML');

    if (isset($_GET['id']) && $_GET['id']!='') {
        $eventID = (int) $_GET['id'];    
        $Event = $Events->find($eventID);
        $details = $Event->to_array();
    
        $heading1 = 'Editing an Event';
        

    }else{
        $Event = false;
        $eventID = false;
        $details = array();

        $heading1 = 'Adding an Event';
    }

    $heading2 = 'Event details';


    $Template   = $API->get('Template');
    $Template->set('events/event.html', 'events');
    

    $Form = $API->get('Form');

    $Form->require_field('eventTitle', 'Required');
    $Form->require_field('eventDescRaw', 'Required');
    $Form->require_field('eventDateTime_minute', 'Required');

    $Form->set_required_fields_from_template($Template);

    if ($Form->submitted()) {
    	        
        $postvars = array('eventTitle','eventDescRaw','cat_ids');
		
    	$data = $Form->receive($postvars);
    	
    	$data['eventDateTime'] = $Form->get_date('eventDateTime');
    	

        $prev = false;

        if (isset($details['eventDynamicFields'])) {
            $prev = PerchUtil::json_safe_decode($details['eventDynamicFields'], true);
        }
        
        $dynamic_fields = $Form->receive_from_template_fields($Template, $prev);

    	$data['eventDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
    	
    	$result = false;
    	
    	
    	if (is_object($Event)) {
    	    $result = $Event->update($data);
    	}else{
    	    $new_event = $Events->create($data);
    	    if ($new_event) {
    	        $result = true;
    	        PerchUtil::redirect($API->app_path() .'/edit/?id='.$new_event->id().'&created=1');
    	    }else{
    	        $message = $HTML->failure_message('Sorry, that event could not be updated.');
    	    }
    	}
    	
        if ($result) {
            $message = $HTML->success_message('Your event has been successfully updated. Return to %sevent listing%s', '<a href="'.$API->app_path() .'">', '</a>');  
        }else{
            $message = $HTML->failure_message('Sorry, that event could not be updated.');
        }
        
        if (is_object($Event)) {
            $details = $Event->to_array();
        }else{
            $details = array();
        }
        
    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('Your event has been successfully created. Return to %sevent listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }
?>