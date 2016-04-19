<?php

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
        '<div class="insert-pages">
            <p class="description">'.__('Insert the route of the page (one per line), i.e. "/example-page/"', 'campaign-monitor').'</p>
            <textarea name="pages_list" cols="30" rows="10" class="options-field"></textarea>
        </div>';
    
}else{
    
    $pagesInsert = '<div class="insert-pages">';
    
    foreach ( $pages as $page ) {
        $slug = str_replace( home_url(), "", get_permalink($page->ID) );
        
        $pagesInsert .= '<input type="checkbox" name="pages[' . $page->post_name . ']" id="pages[' . $page->post_name . ']" class="page-checkbox" value="' . $slug . '"> 
        <label for="pages[' . $page->post_name . ']">'.$page->post_title.'</label>';
    }
    $pagesInsert .= '<textarea name="pages_list" cols="30" rows="10" class="options-field hidden-options"></textarea>';
    
    $pagesInsert .= '</div>';
    
}



$allJsFields['lightbox'] =
    '<table id="form-type-lightbox" class="form-table form-type">
        <tr>
            <th scope="row">'.__('Lightbox Location', 'campaign-monitor').'</th>
            <td>
                <label><input type="radio" name="isGlobal" value="1" checked> '.__('Show on every page', 'campaign-monitor').'</label>

                <label><input type="radio" name="isGlobal" value="0"> '.__('Only show on specific pages', 'campaign-monitor').'</label>
                '.$pagesInsert.'
            </td>
        </tr>
        <tr>
            <th scope="row">'.__('Lightbox Delay', 'campaign-monitor').'</th>
            <td>
                <label><input type="radio" name="lightbox_delay" value="immediately" checked> '.__('Show Immediately', 'campaign-monitor').'</label>

                <label><input type="radio" name="lightbox_delay" value="interval"> '.__('Show after a specific amount of time', 'campaign-monitor').'</label>

                <label class="default-locked auxiliar-data label-show-after-time">'.__('Time delay (seconds)', 'campaign-monitor').' <input type="text" name="lightbox_delay_seconds" id="wizardLightboxDelayTime" value="" class="regular-text" disabled> </label>

                <label><input type="radio" name="lightbox_delay" value="scroll"> '.__('Show after a user has scrolled a specific amount', 'campaign-monitor').'</label>

                <label class="default-locked auxiliar-data label-show-after-scroll">'.__('Scroll delay (px or %)', 'campaign-monitor').' <input type="text" name="lightbox_delay_height" id="wizardLightboxDelayScroll" value="" class="regular-text" disabled> </label>

            </td>
        </tr>
    </table>';

$allJsFields['slider'] =
    '<table id="form-type-slide-out" class="form-table form-type">
        <tr>
            <th scope="row">'.__('Slide-Out Location', 'campaign-monitor').'</th>
            <td>
                <label><input type="radio" name="isGlobal" value="1" checked> '.__('Show on every page', 'campaign-monitor').'</label>

                <label><input type="radio" name="isGlobal" value="0"> '.__('Only show on specific pages', 'campaign-monitor').'</label>
                '.$pagesInsert.'
            </td>
        </tr>
        <tr>
            <th scope="row">'.__('Slide-Out Position', 'campaign-monitor').'</th>
            <td>
                <label><input type="radio" name="slider_position" value="top" checked> '.__('Top', 'campaign-monitor').'</label>
                <label><input type="radio" name="slider_position" value="bottom"> '.__('Bottom', 'campaign-monitor').'</label>
                <label><input type="radio" name="slider_position" value="left"> '.__('Left', 'campaign-monitor').'</label>
                <label><input type="radio" name="slider_position" value="right"> '.__('Right', 'campaign-monitor').'</label>
            </td>
        </tr>
    </table>';

$allJsFields['bar'] =
    '<table id="form-type-bar" class="form-table form-type">
        <tr>
            <th scope="row">'.__('Bar Location', 'campaign-monitor').'</th>
            <td>
                <label><input type="radio" name="isGlobal" value="1" checked> '.__('Show on every page', 'campaign-monitor').'</label>

                <label><input type="radio" name="isGlobal" value="0"> '.__('Only show on specific pages', 'campaign-monitor').'</label>
                '.$pagesInsert.'
            </td>
        </tr>
        <tr>
            <th scope="row">'.__('Bar Position', 'campaign-monitor').'</th>
            <td>
                <label><input type="radio" name="bar_position" value="top" checked> '.__('Top', 'campaign-monitor').'</label>
                <label><input type="radio" name="bar_position" value="bottom"> '.__('Bottom', 'campaign-monitor').'</label>
            </td>
        </tr>
    </table>';

$allJsFields['button'] =
    '<table id="form-type-button" class="form-table form-type">
        <tr>
            <th scope="row">'.__('Button Text', 'campaign-monitor').'</th>
            <td>
                <input type="text" name="button_text" id="wizardButtonOnlyText" value="Show form" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">'.__('Button Shortcode', 'campaign-monitor').'</th>
            <td>
                <input type="text" name="button_shortcode" id="wizardButtonShortcode" value="[cm_button id='.$thisFormId.']" class="regular-text" disabled>
                <p class="description">'.__('Copy and paste the shortcode above to the pages or posts where you\'d like your button to appear', 'campaign-monitor').'</p>

                <input type="hidden" name="isGlobal" value="0">
            </td>
        </tr>
    </table>';

$allJsFields['simple_form'] =
    '<table id="form-type-embedded" class="form-table form-type">
        <tr>
            <th scope="row">'.__('Embedded Code', 'campaign-monitor').'</th>
            <td>
                <input type="text" name="embedded_code" id="wizardFormEmbed" value="[cm_simple_form id='.$thisFormId.']" class="regular-text" disabled>
                <p class="description">'.__('Copy and paste the shortcode above to the pages or posts where you\'d like your form to appear', 'campaign-monitor').'</p>
                <input type="hidden" name="isGlobal" value="2">
            </td>
        </tr>
    </table>';