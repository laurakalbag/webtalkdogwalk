<?php

class PerchEvents_Category  extends PerchAPI_Base
{
    protected $table  = 'events_categories';
    protected $pk     = 'categoryID';
    
    
    public function delete()
    {
        $this->db->delete(PERCH_DB_PREFIX.'events_to_categories', 'categoryID', $this->id());
        parent::delete();
    }
}

?>