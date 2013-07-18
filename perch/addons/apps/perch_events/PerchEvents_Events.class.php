<?php

class PerchEvents_Events extends PerchAPI_Factory
{
    protected $table     = 'events';
	protected $pk        = 'eventID';
	protected $singular_classname = 'PerchEvents_Event';
	
	protected $default_sort_column = 'eventDateTime';
    protected $created_date_column = 'eventDateTime';
	
	public $static_fields   = array('eventTitle', 'eventSlug', 'eventDateTime', 'eventDescRaw', 'eventDescHTML', 'category_names');
    

    /**
     * get the list of events with a date of today or greater to display int he admin area.
     */
    public function all($Paging=false, $future=true)
    {

        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' * 
                FROM '.$this->table;
                
        if ($future) {
            $sql .= ' WHERE eventDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00'));
        }else{
            $sql .= ' WHERE eventDateTime<='.$this->db->pdb(date('Y-m-d 00:00:00'));
        }
                
        $sql .= ' ORDER BY '.$this->default_sort_column;
        
        if (!$future) {
            $sql  .= ' DESC';
        }

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }
        

        $results = $this->db->get_rows($sql);
        
        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }
              
        return $this->return_instances($results);
    }
    
    /*
        Get a single event by its ID
    */
    public function find($eventID) {
		$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'events WHERE eventID = '.$this->db->pdb($eventID);
		
		$row = $this->db->get_row($sql);
		
		if(is_array($row)) {
			$sql = 'SELECT categoryID FROM '.PERCH_DB_PREFIX.'events_to_categories WHERE eventID = '.$this->db->pdb($eventID);
			$result = $this->db->get_rows($sql);
			$a = array();
			if(is_array($result)) {
				foreach($result as $cat_row) {
					$a[] = $cat_row['categoryID'];
				}
			}
			$row['cat_ids'] = $a;
		}
		
		return $this->return_instance($row);
	}
	
/*
        Get a single event by its Slug
    */
    public function find_by_slug($eventSlug) 
    {
    
		$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'events WHERE eventSlug = '.$this->db->pdb($eventSlug);
		
		$row = $this->db->get_row($sql);
		
		if(is_array($row)) {
			$sql = 'SELECT categoryID FROM '.PERCH_DB_PREFIX.'events_to_categories WHERE eventID = '.$this->db->pdb($row['eventID']);
			$result = $this->db->get_rows($sql);
			$a = array();
			if(is_array($result)) {
				foreach($result as $cat_row) {
					$a[] = $cat_row['categoryID'];
				}
			}
			$row['cat_ids'] = $a;
		}
		
		return $this->return_instance($row);
	
    }
    
    
	/**
	* takes the event data and inserts it as a new row in the database.
	*/
    public function create($data)
    {
        if(isset($data['eventDescRaw'])) {
        	$data['eventDescHTML'] = $this->text_to_html($data['eventDescRaw']);
        }else{
        	$data['eventDescHTML'] = false;
        }
        
        if (isset($data['eventTitle'])) {
            $data['eventSlug'] = PerchUtil::urlify(date('Y m d', strtotime($data['eventDateTime'])). ' ' . $data['eventTitle']);
        }
        
        if (isset($data['cat_ids']) && is_array($data['cat_ids'])) {
            $cat_ids = $data['cat_ids'];
        }else{
            $cat_ids = false;
        }
        
        unset($data['cat_ids']);
        
        $eventID = $this->db->insert($this->table, $data);
       
		if ($eventID) {
			if(is_array($cat_ids)) {
				for($i=0; $i<sizeOf($cat_ids); $i++) {
				    $tmp = array();
				    $tmp['eventID'] = $eventID;
				    $tmp['categoryID'] = $cat_ids[$i];
				    $this->db->insert(PERCH_DB_PREFIX.'events_to_categories', $tmp);
				}
			}
			
            return $this->find($eventID);
		}				
        return false;
	}
	
	public function get_for_month($month,$year,$future_only=true,$cats=false) {

    	
    	if (is_array($cats)) {
    	    $sql = 'SELECT DISTINCT e.*
    	            FROM '.$this->table.' e, '.PERCH_DB_PREFIX.'events_to_categories e2c, '.PERCH_DB_PREFIX.'events_categories c
    	            WHERE e.eventID=e2c.eventID AND e2c.categoryID=c.categoryID
    	                AND categorySlug IN ('.$this->implode_for_sql_in($cats).') ';
    	}else{
    	    $sql = 'SELECT DISTINCT e.* FROM '.$this->table.' e WHERE 1=1 ';
    	}
    	        
    	$sql .= ' AND MONTH(eventDateTime)=' . $this->db->pdb($month) . ' AND YEAR(eventDateTime)=' . $this->db->pdb($year);
    	        
    	if ($future_only) {
    	    $sql .= ' AND eventDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00'));
    	}        
    	        
    	$results    = $this->db->get_rows($sql);

        return $this->return_instances($results);
    }
 
    public function get_display($type='calendar', $month, $year, $opts)
    {
        // options
        $categories = false;
        $future_only = true;
        $templates = array();
        
        if (is_array($opts)) {
            
            // categories
            if (isset($opts['category'])) {
                if (is_array($opts['category'])) {
                    $categories = $opts['category'];
                }else{
                    $categories = array($opts['category']);
                }
            }
            
            // past events
            if (isset($opts['past-events']) && $opts['past-events']) {
                $future_only = false;
            }
            
            // templates
            if (isset($opts['calendar-template'])) {
                $templates['calendar'] = $opts['calendar-template'];
            }
            if (isset($opts['blank-day-template'])) {
                $templates['blank-day'] = $opts['blank-day-template'];
            }
            if (isset($opts['event-day-template'])) {
                $templates['event-day'] = $opts['event-day-template'];
            }
            
            // dates
            if (isset($opts['month'])) {
                $month = $opts['month'];
            }
            
            if (isset($opts['year'])) {
                $year = $opts['year'];
            }
        }
        
        $events = $this->get_for_month($month, $year, $future_only, $categories);
        
        switch($type) {
            case 'listing':
                $DisplayListing = new PerchEvents_DisplayListing($this->api, $year, $month);
                $DisplayListing->set_diary($events);

            	$r = $DisplayListing->display($templates);
                break;
                
            default:
                $DisplayCalendar = new PerchEvents_DisplayCalendar($this->api, $year, $month);
                $DisplayCalendar->set_diary($events);

            	$r = $DisplayCalendar->display($templates);
                break;
        }
        
    	
    	return $r;
    }
    
    public function get_custom($opts)
    {
        $events = array();
        $Event = false;
        $single_mode = false;
        $where = array();
        $order = array();
        $limit = '';
        
        // find specific _id
	    if (isset($opts['_id'])) {
	        $single_mode = true;
	        $Event = $this->find($opts['_id']);
	    }else{        
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            
	            
	            $key = $opts['filter'];
	            $raw_value = $opts['value'];
	            $value = $this->db->pdb($opts['value']);
	            
	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
                switch ($match) {
                    case 'eq': 
                    case 'is': 
                    case 'exact': 
                        $where[] = $key.'='.$value;
                        break;
                    case 'neq': 
                    case 'ne': 
                    case 'not': 
                        $where[] = $key.'!='.$value;
                        break;
                    case 'gt':
                        $where[] = $key.'>'.$value;
                        break;
                    case 'gte':
                        $where[] = $key.'>='.$value;
                        break;
                    case 'lt':
                        $where[] = $key.'<'.$value;
                        break;
                    case 'lte':
                        $where[] = $key.'<='.$value;
                        break;
                    case 'contains':
                        $v = str_replace('/', '\/', $raw_value);
                        $where[] = $key." REGEXP '[[:<:]]'.$v.'[[:>:]]'";
                        break;
                    case 'regex':
                    case 'regexp':
                        $v = str_replace('/', '\/', $raw_value);
                        $where[] = $key." REGEXP '".$v."'";
                        break;
                    case 'between':
                    case 'betwixt':
                        $vals  = explode(',', $raw_value);
                        if (PerchUtil::count($vals)==2) {
                            $where[] = $key.'>'.trim($this->db->pdb($vals[0]));
                            $where[] = $key.'<'.trim($this->db->pdb($vals[1]));
                        }
                        break;
                    case 'eqbetween':
                    case 'eqbetwixt':
                        $vals  = explode(',', $raw_value);
                        if (PerchUtil::count($vals)==2) {
                            $where[] = $key.'>='.trim($this->db->pdb($vals[0]));
                            $where[] = $key.'<='.trim($this->db->pdb($vals[1]));
                        }
                        break;
                    case 'in':
                    case 'within':
                        $vals  = explode(',', $raw_value);
                        $tmp = array();
                        if (PerchUtil::count($vals)) {
                            foreach($vals as $value) {
                                if ($item[$key]==trim($value)) {
                                    $tmp[] = $item;
                                    break;
                                }
                            }
                            $where[] = $key.' IN '.$this->implode_for_sql_in($tmp);
                            
                        }
                        break;
                }
	        }
	    }
    
	    // sort
	    if (isset($opts['sort'])) {
	        $desc = false;
	        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
	            $desc = true;
	        }else{
	            $desc = false;
	        }
	        $order[] = $opts['sort'].' '.($desc ? 'DESC' : 'ASC');
	    }
    
	    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            $order[] = 'RAND()';
        }
    
	    // limit
	    if (isset($opts['count'])) {
	        $count = (int) $opts['count'];
        
	        if (isset($opts['start'])) {
                $start = (((int) $opts['start'])-1). ',';
	        }else{
	            $start = '';
	        }
        
	        $limit = $start.$count;
	    }
	    
	    if ($single_mode){
	        $events = array($Event);
	    }else{
    	    $sql = 'SELECT DISTINCT e.* FROM '.$this->table.' e ';
	    
    	    // categories
    	    if (isset($opts['category'])) {
    	        $cats = $opts['category'];
    	        if (!is_array($cats)) $cats = array($cats);
	    
    	        if (is_array($cats)) {
            	    $sql = 'SELECT DISTINCT e.*
            	            FROM '.$this->table.' e, '.PERCH_DB_PREFIX.'events_to_categories e2c, '.PERCH_DB_PREFIX.'events_categories c ';
            	    $where[] =  'e.eventID=e2c.eventID AND e2c.categoryID=c.categoryID AND categorySlug IN ('.$this->implode_for_sql_in($cats).') ';
            	}
    	    }
	    	            
    	    if (count($where)) {
    	        $sql .= ' WHERE ' . implode(' AND ', $where);
    	    }
	    
    	    if (count($order)) {
    	        $sql .= ' ORDER BY '.implode(', ', $order);
    	    }
	    
    	    if ($limit!='') {
    	        $sql .= ' LIMIT '.$limit;
    	    }
	    
    	    $rows    = $this->db->get_rows($sql);
    	    $events  = $this->return_instances($rows);

        }
	    
	    
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            
            if ($single_mode) return $Event;
            
            $out = array();
            if (PerchUtil::count($events)) {
                foreach($events as $Event) {
                    $out[] = $Event->to_array();
                }
            }
            return $out; 
	    }
    
	    
	    // template
	    if (isset($opts['template'])) {
	        $template = $opts['template'];
	    }else{
	        $template = 'events/event.html';
	    }
	    
	    $Template = $this->api->get("Template");
	    $Template->set($template, 'events');
	    
        $html = $Template->render_group($events, true);
	    

	    return $html;
    }
    
    public function get_by_category_slug($slug, $Paging=false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }


        $sql .= ' e.*
                FROM '.$this->table.' e, '.PERCH_DB_PREFIX.'events_categories c, '.PERCH_DB_PREFIX.'events_to_categories e2c
                WHERE e.eventID=e2c.eventID AND e2c.categoryID=c.categoryID
                    AND c.categorySlug='.$this->db->pdb($slug).'
                ORDER BY '.$this->default_sort_column;

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }
        
        $results = $this->db->get_rows($sql);
        
        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }
        
        return $this->return_instances($results);
    }
 
    private function implode_for_sql_in($rows)
    {
        foreach($rows as &$item) {
            $item = $this->db->pdb($item);
        }
        
        return implode(', ', $rows);
    }
    
    // Stopgap until we get this into the API
    public function text_to_html($str)
    {
        switch(PERCH_APPS_EDITOR_MARKUP_LANGUAGE) {
            case 'textile' :
                $Textile = new Textile;
                $str  =  $Textile->TextileThis($str);
                break;

            case 'markdown' :
                $Markdown = new Markdown_Parser;
                $str = $Markdown->transform($str);
                break;
        }
        
        if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
		    $str = str_replace(' />', '>', $str);
		}
		
		return $str;
    }
    
}

?>