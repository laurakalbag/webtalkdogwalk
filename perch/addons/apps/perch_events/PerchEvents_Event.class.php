<?php

class PerchEvents_Event extends PerchAPI_Base
{
    protected $table  = 'events';
    protected $pk     = 'eventID';

    private $tmp_url_vars = array();


    public function update($data)
    {
        $PerchEvents_Events = new PerchEvents_Events();
        
        if(isset($data['eventDescRaw'])) {
        	$data['eventDescHTML'] = $PerchEvents_Events->text_to_html($data['eventDescRaw']);
        }else{
        	$data['eventDescHTML'] = false;
        }
        
        if (isset($data['eventTitle'])) {
            $data['eventSlug'] = PerchUtil::urlify(date('Y m d', strtotime($data['eventDateTime'])). ' ' . $data['eventTitle']);
        }

        if (isset($data['cat_ids'])) {
            $catIDs = $data['cat_ids'];
            unset($data['cat_ids']);
        }else{
            $catIDs = false;
        }

        // Update the event itself
        parent::update($data);

        // Delete existing categories
        $this->db->delete(PERCH_DB_PREFIX.'events_to_categories', $this->pk, $this->id());

 		// Add new categories
 		if (is_array($catIDs)) {
 			for($i=0; $i<sizeOf($catIDs); $i++) {
 			    $tmp = array();
 			    $tmp['eventID'] = $this->id();
 			    $tmp['categoryID'] = $catIDs[$i];
 			    $this->db->insert(PERCH_DB_PREFIX.'events_to_categories', $tmp);
 			}
 		}
 		return true;
    }
    
    public function delete()
    {
        parent::delete();
        $this->db->delete(PERCH_DB_PREFIX.'events_to_categories', $this->pk, $this->id());
    }
    
    public function date()
    {
        return date('Y-m-d', strtotime($this->eventDateTime()));
    }

    public function to_array($template_ids=false)
    {
        $out = parent::to_array();
        
        $Categories = new PerchEvents_Categories();
        $cats   = $Categories->get_for_event($this->id());
        
        $out['category_slugs'] = '';
        $out['category_names'] = '';
        
        if (PerchUtil::count($cats)) {
            $slugs = array();
            $names = array();
            foreach($cats as $Category) {
                $slugs[] = $Category->categorySlug();
                $names[] = $Category->categoryTitle();
                
                // for template
                $out[$Category->categorySlug()] = true;
            }
            
            $out['category_slugs'] = implode(' ', $slugs);
            $out['category_names'] = implode(', ', $names);
        }

        if (PerchUtil::count($template_ids) && in_array('eventURL', $template_ids)) {
            $Settings = PerchSettings::fetch();
            $url_template = $Settings->get('perch_events_detail_url')->val();
            $this->tmp_url_vars = $out;
            $out['eventURL'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $url_template);
            $this->tmp_url_vars = false;
        }
        
        if (isset($out['eventDynamicFields']) && $out['eventDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['eventDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }
        
        return $out;
    }

    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }

}

?>