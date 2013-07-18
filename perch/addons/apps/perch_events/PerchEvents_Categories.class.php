<?php

class PerchEvents_Categories extends PerchAPI_Factory
{
    protected $table     = 'events_categories';
	protected $pk        = 'categoryID';
	protected $singular_classname = 'PerchEvents_Category';
	
	protected $default_sort_column = 'categoryTitle';
	
	
	public function get_for_event($eventID)
	{
	    $sql = 'SELECT c.*
	            FROM '.$this->table.' c, '.PERCH_DB_PREFIX.'events_to_categories e2c
	            WHERE c.categoryID=e2c.categoryID
	                AND e2c.eventID='.$this->db->pdb($eventID);
	    $rows   = $this->db->get_rows($sql);
	    
	    return $this->return_instances($rows);
	}
    
}

?>