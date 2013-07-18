<?php
   
    # Side panel
    echo $HTML->side_panel_start();
        echo $HTML->para('This page lists your events. To view past events, or to filter by category, use the options on the filter bar.');
    
    echo $HTML->side_panel_end();
    
        
    # Main panel
    echo $HTML->main_panel_start();

    include('_subnav.php');


        echo '<a class="add button" href="'.$HTML->encode($API->app_path().'/edit/').'">'.$Lang->get('Add Event').'</a>';


        echo $HTML->heading1('Listing Events');
    
    if (isset($message)) echo $message;
    


        /* ----------------------------------------- SMART BAR ----------------------------------------- */
        
        ?>


        <ul class="smartbar">
            <li class="<?php echo ($filter=='future'?'selected':''); ?>"><a href="<?php echo $HTML->encode($API->app_path().'?by=future'); ?>"><?php echo $Lang->get('Future'); ?></a></li>
            <li class="<?php echo ($filter=='past'?'selected':''); ?>"><a href="<?php echo $HTML->encode($API->app_path().'?by=past'); ?>"><?php echo $Lang->get('Past'); ?></a></li>
            
            <?php

                if ($filter == 'past') {
                    $Alert->set('filter', PerchLang::get('You are viewing all past events.'). ' <a href="'.$API->app_path().'" class="action">'.PerchLang::get('Clear Filter').'</a>');
                }

 
                if (PerchUtil::count($categories)) {
                    $items = array();
                    foreach($categories as $Category) {
                        $items[] = array(
                                'arg'=>'category',
                                'val'=>$Category->categorySlug(),
                                'label'=>$Category->categoryTitle(),
                                'path'=>$API->app_path()
                            );
                    }
                    echo PerchUtil::smartbar_filter('cf', 'By Category', 'Filtered by ‘%s’', $items, 'folder', $Alert, "You are viewing events in ‘%s’", $API->app_path());
                }
               
                
            
            ?>
        </ul>

        <?php
            if (!PerchUtil::count($events)) {
                //$Alert->set('notice', $Lang->get('There are no events that match the current filter.'));
            }

        ?>

         <?php echo $Alert->output(); ?>


        <?php

        /* ----------------------------------------- /SMART BAR ----------------------------------------- */










    
    if (PerchUtil::count($events)) {
?>
    <table>
        <thead>
            <tr>
                <th><?php echo $Lang->get('Event'); ?></th>
                <th><?php echo $Lang->get('Date'); ?></th>
                <th class="action"></th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($events as $Event) {
?>
            <tr>
                <td class="primary"><a href="<?php echo $HTML->encode($API->app_path()); ?>/edit/?id=<?php echo $HTML->encode(urlencode($Event->id())); ?>" class="edit"><?php echo $HTML->encode($Event->eventTitle()); ?></a></td>
                <td><?php echo $HTML->encode(strftime('%d %B %Y, %l:%M %p', strtotime($Event->eventDateTime()))); ?></td>
                <td><a href="<?php echo $HTML->encode($API->app_path()); ?>/delete/?id=<?php echo $HTML->encode(urlencode($Event->id())); ?>" class="delete inline-delete"><?php echo $Lang->get('Delete'); ?></a></td>
            </tr>

<?php   
    }
?>
        </tbody>
    </table>
<?php
        if ($Paging->enabled()) {
            echo $HTML->paging($Paging);
        }


    } // if pages
    
    echo $HTML->main_panel_end();
?>