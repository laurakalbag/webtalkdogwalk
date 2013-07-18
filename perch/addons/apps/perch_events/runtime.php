<?php

    PerchSystem::register_search_handler('PerchEvents_SearchHandler');

    require('PerchEvents_Events.class.php');
    require('PerchEvents_Event.class.php');
    require('PerchEvents_Categories.class.php');
    require('PerchEvents_Category.class.php');
    require('PerchEvents_DisplayCalendar.class.php');
    require('PerchEvents_DisplayListing.class.php');
    require('PerchEvents_SearchHandler.class.php');
    
    
    function perch_events_calendar($opts=false, $return=false)
    {
        $year = date('Y');
        $month = date('m');
        
        if (isset($_GET['d']) && $_GET['d']!='') {
            $date = explode('-', $_GET['d']);
            if (isset($date[0])) $year = (int)$date[0];
            if (isset($date[1])) $month = (int)$date[1];
        }
        
        $API  = new PerchAPI(1.0, 'perch_events');
        
        $Events = new PerchEvents_Events($API);
        
        $r = $Events->get_display('calendar', $month, $year, $opts);
        
    	if ($return) return $r;
    	
    	echo $r;
    }
    
    function perch_events_listing($opts=false, $return=false)
    {
        $year = date('Y');
        $month = date('m');
        
        if (isset($_GET['d']) && $_GET['d']!='') {
            $date = explode('-', $_GET['d']);
            if (isset($date[0])) $year = (int)$date[0];
            if (isset($date[1])) $month = (int)$date[1];
        }
        
        $API  = new PerchAPI(1.0, 'perch_events');
        
        $Events = new PerchEvents_Events($API);
        
        $r = $Events->get_display('listing', $month, $year, $opts);
        
    	if ($return) return $r;
    	
    	echo $r;
    }
    
    
    function perch_events_custom($opts=false, $return=false)
    {
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $return  = true; 
            $postpro = false;
        }else{
            $postpro = true;
        }

        $API  = new PerchAPI(1.0, 'perch_events');
        
        $Events = new PerchEvents_Events($API);
        
        $out = $Events->get_custom($opts);

        // Post processing - if there are still <perch:x /> tags
        if ($postpro && !is_array($out) && strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
        
    	if ($return) return $out;
    	
    	echo $out;
    }
    
    /**
     * 
     * Get the content of a specific field
     * @param mixed $id_or_slug the id or slug of the event
     * @param string $field the name of the field you want to return
     * @param bool $return
     */
    function perch_events_event_field($id_or_slug, $field, $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_events');
        $Events = new PerchEvents_Events($API);
        
        $r = false;
        
        if (is_numeric($id_or_slug)) {
            $eventID = intval($id_or_slug);
            $Event = $Events->find($eventID);
        }else{
            $Event = $Events->find_by_slug($id_or_slug);
        }
        
        if (is_object($Event)) {
            $r = $Event->$field();
        }
        
        if ($return) return $r;
        
        $HTML = $API->get('HTML');
        echo $HTML->encode($r);
    }
    

?>