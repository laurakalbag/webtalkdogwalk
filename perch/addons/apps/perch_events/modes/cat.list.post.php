<?php
 
    # Side panel
    echo $HTML->side_panel_start();
        echo $HTML->para('This page lists your event categories.');
    echo $HTML->side_panel_end();
    
    
    # Main panel
    echo $HTML->main_panel_start();
    include('_subnav.php');


     echo '<a class="add button" href="'.$HTML->encode($API->app_path().'/categories/new/').'">'.$Lang->get('Add Category').'</a>';

    echo $HTML->heading1('Listing Categories');
    
    if (PerchUtil::count($categories)) {
?>
    <table>
        <thead>
            <tr>
                <th><?php echo $Lang->get('Category'); ?></th>
                <th><?php echo $Lang->get('Slug'); ?></th>
                <th class="action"></th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($categories as $Category) {
?>
            <tr>
                <td class="primary"><a href="<?php echo $HTML->encode($API->app_path()); ?>/categories/edit/?id=<?php echo $HTML->encode(urlencode($Category->id())); ?>"><?php echo $HTML->encode($Category->categoryTitle())?></a></td>
                <td><?php echo $HTML->encode($Category->categorySlug())?></td>  
                <td><a href="<?php echo $HTML->encode($API->app_path()); ?>/categories/delete/?id=<?php echo $HTML->encode(urlencode($Category->id())); ?>" class="delete"><?php echo $Lang->get('Delete'); ?></a></td>
                
            </tr>

<?php   
    }
?>
        </tbody>
    </table>


    
<?php    
    } // if pages
    
     
    echo $HTML->main_panel_end();


?>