<?php
    // Prevent running directly:
    if (!defined('PERCH_DB_PREFIX')) exit;
    
    $db = $API->get('DB');
    
    $sql = "SHOW INDEX FROM `".PERCH_DB_PREFIX."events` WHERE Key_name = 'idx_search'";
    $result = $db->get_row($sql);
    if (PerchUtil::count($result)==0) {
        $sql = "ALTER TABLE `".PERCH_DB_PREFIX."events` ADD FULLTEXT idx_search (`eventTitle`, `eventDescRaw`)";
        $db->execute($sql);
    }

    $message = $HTML->warning_message('Install complete. Please delete the file: <code>%s</code>', $API->app_path().'/update.php');  

?>