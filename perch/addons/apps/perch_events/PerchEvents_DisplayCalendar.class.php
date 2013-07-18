<?php
/**
* Basic Calendar data and display
* Based on GPL Calendar class by Oscar Merida http://www.oscarm.org/
* Refactored and further developed 2010 by Drew McLellan http://edgeofmyseat.com/
*/
class PerchEvents_DisplayCalendar {
    
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
    private $event_day_class    = 'has-event';
    
    private $calendar_template  = 'events/calendar/calendar.html';
    private $blank_day_template = 'events/calendar/blank-day.html';
    private $event_day_template = 'events/calendar/event-day.html';
    
    private $EventTemplate;
    private $BlankTemplate;
    
    /**
    * Constructor
    *
    * @param integer, year
    * @param integer, month
    * @return object
    * @public
    */
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

    public function get_start_time()
    {
        return $this->start_time;   
    }

    public function get_end_time()
    {    
        return $this->end_time;    
    }

    public function get_year()
    {
        return $this->year;   
    }

    public function get_full_month_name()
    {
        return $this->month_name_full;   
    }

    public function get_short_month_name()
    {
        return $this->month_name_short;   
    }

    public function set_year($year)
    {    
        $this->year = $year;   
    }

    public function set_month($month)
    {
        $this->month = $month;   
    }

    public function set_diary($aDiary) 
    {
    	if(is_array($aDiary)) {
    		$this->diary = $aDiary;
    	}
    }


    /**
    * Any valid strftime format for display weekday names
    *
    * %a - abbreviated, %A - full, %u as number with 1==Monday
    */
    public function set_day_name_format($f, $charlimit=0)
    {
        $this->day_name_format = $f;   
        $this->day_name_limit = $charlimit;
    }


    /**
    * Returns markup for displaying the calendar.
    *
    * @return
    * @public
    */
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
        $data['header'] = $this->display_day_names();
        $data['body']   = $this->display_day_cells();
        $data['next_month'] = $this->next_month();
        $data['prev_month'] = $this->previous_month();
        $data['selected_month'] = $this->year.'-'.$this->month.'-01';
        $data['current_month'] = date('Y-m-01');
        
        $s = $Template->render($data);
        
        return $s;
    }


    /**
    * Displays the row of day names.
    *
    * @return string
    * @private
    */
    public function display_day_names()
    {
        $names = array('2010-08-16','2010-08-17','2010-08-18','2010-08-19','2010-08-20','2010-08-21','2010-08-22');
        
        $s = '<tr>';
        
        for($i=0; $i<7; $i++) {
            $dayname = strftime($this->day_name_format, strtotime($names[$i]));
            if ($this->day_name_limit > 0) {
                $dayname = substr($dayname, 0, $this->day_name_limit);
            }
            
            $s .= '<th';
            
            if ($i >=5) {
                $s .= ' class="'.$this->HTML->encode($this->weekend_class).'"';
            }
            
            $s .= '>'.$this->HTML->encode($dayname)."</th>";
        }
        
        $s .= '</tr>';
        
        return $s;
    }


    /**
    * Displays all day cells for the month
    *
    * @return string
    * @private
    */
    public function display_day_cells()
    {
        $i = 0; // cell counter
        
        $s = '<tr>';
        
    	$dow = 1;
        // first display empty cells based on what weekday the month starts in
        for($c=0; $c<$this->start_offset; $c++) {
            $i++;
            if($dow > 5) {
            	$s .= '<td class="'.$this->HTML->encode($this->weekend_class).' '.$this->HTML->encode($this->notinmonth_class).'"></td>';
            }else {
            	$s .= '<td class="'.$this->HTML->encode($this->notinmonth_class).'"></td>';
            }
            $dow++;
        } // end offset cells
	
        // write out the rest of the days, at each sunday, start a new row.
        for($d=1; $d<=$this->end_day; $d++) {
            $i++;
            $s .= $this->display_day_cell($d, $dow);        

    		$dow++;
            if ($i%7 == 0) {
                $s .= '</tr>';
            }
        
            if ($d<$this->end_day && $i%7 == 0) {
            	$dow = 1;
            	$s .= '<tr>';
            }
        }
    
        // fill in the final row
        $left = 7 - ($i%7);
    
        if ($left < 7)  {
            for ($c=0; $c<$left; $c++) { 
              if($dow > 5) {
            		$s .= '<td class="'.$this->HTML->encode($this->weekend_class).' '.$this->HTML->encode($this->notinmonth_class).'"></td>';
    	        }else {
	               	$s .= '<td class="'.$this->HTML->encode($this->notinmonth_class).'"></td>';
    	        }
              $dow++;
            }
            $s .= "\n\t</tr>";        
        }    

        return $s;        
    }


    
    /**
    * outputs the contents for a given day
    *
    * @param integer, day
    * @abstract
    */
    public function display_day_cell($day, $dow)
    {

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


    	$str =  '<td';
    	$class = '';
        
        if ($thisdate == date('Y-m-d')) {
            $class .= $this->today_class.' ';
        }
    
        if ($thisdate < date('Y-m-d')) {
            $class .= $this->past_class.' ';
        }
        
        if ($dow > 5) {
            $class .= $this->weekend_class.' ';
        }

        if (count($matches)>0) {
            $class .= $this->event_day_class.' ';
        }

        if ($class != '') {
            $str .= ' class="'.trim($this->HTML->encode($class)).'"';
        }
        
        $str .= '>';


        
        if (count($matches)>0) {
            $str .= $this->EventTemplate->render_group($matches);
            $output = true;
        }
        
        if (!$output) {
            $tmp = array();
            $tmp['day'] = $day;
	        $str .= $this->BlankTemplate->render($tmp);
        }
    
        $str.='</td>';
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