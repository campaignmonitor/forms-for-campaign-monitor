<?php

// Creates array with all the slugs of the pages that the element will appear
$pages_list = array();
if ( isset( $form->data['show_in'] ) ) {
    $pages_list = $form->data['show_in'];
}
$pages_list = array_map( 'trim', $pages_list );

$show_pages_class = "";
// Makes the list of pages visible as default
if ( 0 == $form->global ){
    $show_pages_class = "insert-pages--visible";
}

// Gets all the pages
$args = array(
	'orderby' => 'title',
    'order' => 'ASC',
	'hierarchical' => 1,
	'offset' => 0,
    'posts_per_page' => 26,
	'post_type' => array('post', 'page'),
); 
$pages = get_posts($args); 

// Defines the output of the list. Checkboxes for under 25 and textarea for 25 or more
if (count($pages) > 25){
    
    $pagesInsert = 
        '<div class="insert-pages '.$show_pages_class.'">
            <p class="description">'.__('Insert the route of the page (one per line), i.e. "/example-page/"', 'campaign-monitor').'</p>
            <textarea name="pages_list" cols="30" rows="10" class="options-field">';
     
foreach( $pages_list as $option):
$pagesInsert .= stripslashes(CampaignMonitorPluginInstance()->clean_option($option)) . "\n";
endforeach;
    
    $pagesInsert .='</textarea>
        </div>';
    
}else{
    
    $pagesInsert = '<div class="insert-pages '.$show_pages_class.'">';
    
    foreach ( $pages as $page ) {
        $slug = str_replace( home_url(), "", get_permalink($page->ID) );
        $pagesInsert .= '<input type="checkbox" name="pages[' . $page->post_name . ']" id="pages[' . $page->post_name . ']" class="page-checkbox" value="' . $slug . '" ';
        if ( in_array( $slug, $pages_list ) ) {$pagesInsert .= 'checked="checked" ';}
        $pagesInsert .= '> <label for="pages[' . $page->post_name . ']">'.$page->post_title.'</label>';
    }
    $pagesInsert .= '<textarea name="pages_list" cols="30" rows="10" class="options-field hidden-options">';
    
foreach( $pages_list as $option):
$pagesInsert .= stripslashes(CampaignMonitorPluginInstance()->clean_option($option)) . "\n";
endforeach;    
    
    $pagesInsert .= '</textarea>';
    
    $pagesInsert .= '</div>';
    
}
         
?>      

               
<h3><?php echo __('Location Settings', 'campaign-monitor'); ?></h3>
                
<?php
switch ( $form->type ) {
    case 'lightbox':
?>

    <table id="form-type-lightbox" class="form-table form-type">
        <tr>
            <th scope="row"><?php echo __('Lightbox Location', 'campaign-monitor'); ?></th>
            <td>
                <label><input type="radio" name="isGlobal" value="1" <?php if ( 1 == $form->global ): ?>checked="checked"<?php endif; ?>> <?php echo __('Show on every page', 'campaign-monitor'); ?></label>

                <label><input type="radio" name="isGlobal" value="0" <?php if ( 0 == $form->global ): ?>checked="checked"<?php endif; ?>> <?php echo __('Only show on specific pages', 'campaign-monitor'); ?></label>
                <?php echo $pagesInsert; ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo __('Lightbox Delay', 'campaign-monitor'); ?></th>
            <td>
                <label><input type="radio" name="lightbox_delay" value="immediately" <?php if ( "immediately" == $form->data['lightbox_delay'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Show Immediately', 'campaign-monitor'); ?></label>

                <label><input type="radio" name="lightbox_delay" value="interval" <?php if ( "interval" == $form->data['lightbox_delay'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Show after a specific amount of time', 'campaign-monitor'); ?></label>

                <label class="<?php if ( "interval" != $form->data['lightbox_delay'] ): ?>defaultLocked<?php endif; ?> auxiliar-data label-show-after-time"><?php echo __('Time delay (seconds)', 'campaign-monitor'); ?> <input type="text" name="lightbox_delay_seconds" id="wizardLightboxDelayTime" value="<?php echo $form->data['lightbox_delay_seconds']; ?>" class="regular-text" <?php if ( "interval" != $form->data['lightbox_delay'] ): ?>disabled<?php endif; ?>> </label>

                <label><input type="radio" name="lightbox_delay" value="scroll" <?php if ( "scroll" == $form->data['lightbox_delay'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Show after a user has scrolled a specific amount', 'campaign-monitor'); ?></label>

                <label class="<?php if ( "scroll" != $form->data['lightbox_delay'] ): ?>defaultLocked<?php endif; ?> auxiliar-data label-show-after-scroll">Scroll delay (px or %) <input type="text" name="lightbox_delay_height" id="wizardLightboxDelayScroll" value="<?php echo $form->data['lightbox_delay_height']; ?>" class="regular-text" <?php if ( "scroll" != $form->data['lightbox_delay'] ): ?>disabled<?php endif; ?>> </label>

            </td>
        </tr>
    </table>

<?php      
    break;

    case 'slider':
?>

    <table id="form-type-slide-out" class="form-table form-type">
        <tr>
            <th scope="row"><?php echo __('Slide-Out Location', 'campaign-monitor'); ?></th>
            <td>
                <label><input type="radio" name="isGlobal" value="1" <?php if ( 1 == $form->global ): ?>checked="checked"<?php endif; ?>> <?php echo __('Show on every page', 'campaign-monitor'); ?></label>

                <label><input type="radio" name="isGlobal" value="0" <?php if ( 0 == $form->global ): ?>checked="checked"<?php endif; ?>> <?php echo __('Only show on specific pages', 'campaign-monitor'); ?></label>
                <?php echo $pagesInsert; ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo __('Slide-Out Position', 'campaign-monitor'); ?></th>
            <td>
                <label><input type="radio" name="slider_position" value="top" <?php if ( "top" == $form->data['slider_position'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Top', 'campaign-monitor'); ?></label>
                <label><input type="radio" name="slider_position" value="bottom" <?php if ( "bottom" == $form->data['slider_position'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Bottom', 'campaign-monitor'); ?></label>
                <label><input type="radio" name="slider_position" value="left" <?php if ( "left" == $form->data['slider_position'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Left', 'campaign-monitor'); ?></label>
                <label><input type="radio" name="slider_position" value="right" <?php if ( "right" == $form->data['slider_position'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Right', 'campaign-monitor'); ?></label>
            </td>
        </tr>
    </table>

<?php
    break;

    case 'bar':
?>

    <table id="form-type-bar" class="form-table form-type">
        <tr>
            <th scope="row"><?php echo __('Bar Location', 'campaign-monitor'); ?></th>
            <td>
                <label><input type="radio" name="isGlobal" value="1" <?php if ( 1 == $form->global ): ?>checked="checked"<?php endif; ?>> <?php echo __('Show on every page', 'campaign-monitor'); ?></label>

                <label><input type="radio" name="isGlobal" value="0" <?php if ( 0 == $form->global ): ?>checked="checked"<?php endif; ?>>  <?php echo __('Only show on specific pages', 'campaign-monitor'); ?></label>
                <?php echo $pagesInsert; ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo __('Bar Position', 'campaign-monitor'); ?></th>
            <td>
                <label><input type="radio" name="bar_position" value="top" <?php if ( "top" == $form->data['bar_position'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Top', 'campaign-monitor'); ?></label>
                <label><input type="radio" name="bar_position" value="bottom" <?php if ( "bottom" == $form->data['bar_position'] ): ?>checked="checked"<?php endif; ?>> <?php echo __('Bottom', 'campaign-monitor'); ?></label>
            </td>
        </tr>
    </table>

<?php        
    break; 

    case 'button':
?>

    <table id="form-type-button" class="form-table form-type">
       <tr>
            <th scope="row"><?php echo __('Button Text', 'campaign-monitor'); ?></th>
            <td>
                <input type="text" name="button_text" id="wizardButtonOnlyText" value="<?php echo CampaignMonitorPluginInstance()->clean_option($form->data['text']); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo __('Button Shortcode', 'campaign-monitor'); ?></th>
            <td>
                <input type="text" name="button_shortcode" id="wizardButtonShortcode" value="[cm_button id=<?php echo $form->id; ?>]" class="regular-text" disabled>
                <p class="description"><?php echo __('Copy and paste the shortcode above to the pages or posts where you\'d like your form to appear', 'campaign-monitor'); ?></p>

                <input type="hidden" name="global" value="1">
            </td>
        </tr>
    </table>

<?php        
    break; 

    case 'simple_form':
?>

    <table id="form-type-embedded" class="form-table form-type">
        <tr>
            <th scope="row"><?php echo __('Embedded Code', 'campaign-monitor'); ?></th>
            <td>
                <input type="text" name="embedded_code" id="wizardFormEmbed" value="[cm_simple_form id=<?php echo $form->id; ?>]" class="regular-text" disabled>

                <input type="hidden" name="global" value="2">
            </td>
        </tr>
    </table>

<?php
    break; 
}                

?>