<?php
    # include the API
    include('../../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_events');
    $Lang = $API->get('Lang');

    if (!$CurrentUser->has_priv('perch_events.categories.manage')) {
        PerchUtil::redirect($API->app_path());
    }

    # include your class files
    include('../../PerchEvents_Categories.class.php');
    include('../../PerchEvents_Category.class.php');

    # Set the page title
    $Perch->page_title = $Lang->get('Manage Event Categories');


    # Do anything you want to do before output is started
    include('../../modes/category.new.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../../modes/category.new.post.php');
    
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>