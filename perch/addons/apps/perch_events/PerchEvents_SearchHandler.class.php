<?php

class PerchEvents_SearchHandler implements PerchAPI_SearchHandler
{
    private static $tmp_url_vars = false;
    
    public static function get_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_events');
        $db = $API->get('DB');
        
        $sql = 'SELECT \''.__CLASS__.'\' AS source, MATCH(eventTitle, eventDescRaw) AGAINST('.$db->pdb($key).') AS score, eventTitle, eventSlug, eventDateTime, eventDescHTML, eventID, "", "", ""
	            FROM '.PERCH_DB_PREFIX.'events 
	            WHERE eventDateTime>'.$db->pdb(date('Y-m-d H:i:s')).'
	                AND MATCH(eventTitle, eventDescRaw) AGAINST('.$db->pdb($key).')';
	    
	    return $sql;
    }
    
    public static function get_backup_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_event');
        $db = $API->get('DB');
        
        $sql = 'SELECT \''.__CLASS__.'\' AS source, eventDateTime AS score, eventTitle, eventSlug, eventDateTime, eventDescHTML, eventID, "", "", ""
	            FROM '.PERCH_DB_PREFIX.'events 
	            WHERE eventDateTime>'.$db->pdb(date('Y-m-d H:i:s')).'
	                AND ( 
	                    concat("  ", eventTitle, "  ") REGEXP '.$db->pdb('[[:<:]]'.$key.'[[:>:]]').' 
                    OR  concat("  ", eventDescRaw, "  ") REGEXP '.$db->pdb('[[:<:]]'.$key.'[[:>:]]').'      
	                    ) ';
	    
	    return $sql;
    }
    
    public static function format_result($key, $options, $result)
    {
        $result['eventTitle']    = $result['col1'];
        $result['eventSlug']     = $result['col2'];
        $result['eventDateTime'] = $result['col3'];
        $result['eventDescHTML'] = $result['col4'];
        $result['eventID']       = $result['col5'];
        $result['_id']          = $result['col5'];
        
        $Settings   = PerchSettings::fetch();
        
        $html = PerchUtil::excerpt_char($result['eventDescHTML'], $options['excerpt_chars'], true);
        // keyword highlight
        $html = preg_replace('/('.$key.')/i', '<span class="keyword">$1</span>', $html);
                        
        $match = array();
        
        $match['url']     = $Settings->get('perch_events_detail_url')->settingValue();
        self::$tmp_url_vars = $result;
        $match['url'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array('self', "substitute_url_vars"), $match['url']);
        self::$tmp_url_vars = false;
        
        $match['title']   = $result['eventTitle'] . ' - ' . strftime('%d %b %Y', strtotime($result['col3']));
        $match['excerpt'] = $html;
        $match['key']     = $key;
        return $match;
    }
    
    private static function substitute_url_vars($matches)
	{
	    $url_vars = self::$tmp_url_vars;
    	if (isset($url_vars[$matches[1]])){
    		return $url_vars[$matches[1]];
    	}
	}
    
}

?>