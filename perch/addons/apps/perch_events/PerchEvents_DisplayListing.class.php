<?php

class PerchEvents_DisplayListing {
    
    private $api;

    private $year;
    private $month;    
    private $month_name_full;
    private $month_name_short;
    private $start_day;
    private $end_day;  
	private $start_time;
	private $end_time;
	private $start_offset;
    
    private $day_name_format;
    private $day_name_limit;
    
    private $notinmonth_class   = 'notinmonth';
    private $weekend_class      = 'weekend';
    private $today_class        = 'today';
    private $past_class         = 'past';
    
    private $calendar_template  = 'events/listing/calendar.html';
    private $blank_day_template = 'events/listing/blank-day.html';
    private $event_day_template = 'events/listing/event-day.html';
    
    private $EventTemplate;
    private $BlankTemplate;
    

    public function __construct($api, $yr=false, $mo=false)
    {
        $this->api         = $api;
        $this->HTML        = $api->get('HTML');
        
        if ($yr===false) $yr = date('Y');
        if ($mo===false) $mo = date('m');
        
        $this->year        = $yr;
        $this->month       = (int) $mo;
        $this->diary       = array();
     
        $this->start_time   = strtotime("$yr-$mo-01 00:00");
    
        $this->end_day      = date('t', $this->start_time); 
    
        $this->end_time     = strtotime("$yr-$mo-".$this->end_day." 23:59");
     
        $this->start_day    = date('D', $this->start_time);
        $this->start_offset = date('w', $this->start_time) - 1;

    
        if ($this->start_offset < 0) {        
            $this->start_offset = 6;
        }
    
        $this->month_name_full = strftime('%B', $this->start_time);
        $this->month_name_short= strftime('%b', $this->start_time);
    
        $this->day_name_format = '%a';
        $this->day_name_limit = 0;
    
    }

    public function set_diary($aDiary) 
    {
    	if(is_array($aDiary)) {
    		$this->diary = $aDiary;
    	}
    }

    public function display($templates=false)
    {
        $Template = $this->api->get('Template');
        
        if (is_array($templates)) {
            if (isset($templates['calendar'])) $this->calendar_template = $templates['calendar'];
            if (isset($templates['blank-day'])) $this->blank_day_template = $templates['blank-day'];
            if (isset($templates['event-day'])) $this->event_day_template = $templates['event-day'];
        }

        $Template->set($this->calendar_template, 'events');
        
        $this->EventTemplate = $this->api->get('Template');
        $this->EventTemplate->set($this->event_day_template, 'events');
        
        $this->BlankTemplate = $this->api->get('Template');
        $this->BlankTemplate->set($this->blank_day_template, 'events');
        
        $data = array();
        //$data['header'] = $this->display_day_names();
        $data['body']   = $this->display_day_items();
        
        
        $data['next_month'] = $this->next_month();
        $data['prev_month'] = $this->previous_month();
        $data['selected_month'] = $this->year.'-'.$this->month.'-01';
        $data['current_month'] = date('Y-m-01');
        
        $s = $Template->render($data);
        
        return $s;
    }
    
    private function display_day_items()
    {
        $str = '';
            
        for($day=1; $day<=$this->end_day; $day++) {
            
            $thisdate = mktime(0, 0, 0, $this->month, $day,  $this->year);
            $thisdate = date("Y-m-d",$thisdate);

            $output = false;
            $matches = array();
            
            foreach($this->diary as $Event) {
                if ($Event->date()==$thisdate) {
                    $Event->squirrel('day', $day);
        	        $matches[] = $Event;
        	    }
            }
            
            if (count($matches)>0) {
                $str .= $this->EventTemplate->render_group($matches);
                $output = true;
            }

            if (!$output) {
                $tmp = array();
                $tmp['day'] = $day;
    	        $str .= $this->BlankTemplate->render($tmp);
            }
        
                        
        }
        
        return $str;
    }


   
 
    private function next_month()
    {
        return date('Y-m-d', strtotime($this->year.'-'.$this->month.'-01 +1 MONTH'));
    }
    
    private function previous_month()
    {
        return date('Y-m-d', strtotime($this->year.'-'.$this->month.'-01 -1 MONTH'));
    }
    
}

?>