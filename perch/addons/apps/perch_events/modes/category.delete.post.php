<?php
    # Side panel
    echo $HTML->side_panel_start();
        echo $HTML->para('Take care, there is no undo.');
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start(); 
    include('_subnav.php');
    echo $HTML->heading1('Deleting an Event Category');
    



    echo $Form->form_start();
    
    if ($message) {
        echo $message;
    }else{
        echo $HTML->warning_message('Are you sure you wish to delete the category %s?', $details['categoryTitle']);
        echo $Form->form_start();
        echo $Form->hidden('categoryID', $details['categoryID']);
		echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());


        echo $Form->form_end();
    }
    
    echo $HTML->main_panel_end();

?>