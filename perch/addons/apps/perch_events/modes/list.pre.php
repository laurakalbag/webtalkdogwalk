<?php
    
    $HTML = $API->get('HTML');
    
    // Try to update
    //if (file_exists('update.php')) include('update.php');
    
    $Events = new PerchEvents_Events($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);
    
    $Categories = new PerchEvents_Categories($API);
    $categories = $Categories->all();
   
    $events = array();

    $filter = 'future';
    
    if (isset($_GET['by']) && $_GET['by']!='') {
        $filter = $_GET['by'];
    }

    if (isset($_GET['category']) && $_GET['category'] != '') {
        $filter = 'category';
        $category = $_GET['category'];
    }
    
    
    switch ($filter) {
        case 'past':
            $events = $Events->all($Paging, false);
            break;
            
        case 'category':
            $events = $Events->get_by_category_slug($category, $Paging);
            break;

        default:
            $events = $Events->all($Paging);
            
            // Install
            if ($events == false) {
                $Events->attempt_install();
            }
            
            break;
    }

?>