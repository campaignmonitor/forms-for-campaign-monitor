<?php 

    $form = new CampaignMonitorForm();
    $allForms = $form->get_all();
    $thisFormId = $form->get_next_id();
    //$form->load( $_GET['form'] );


    // Gets all fields from all the lists and store on $allListsFields
    require 'partials/get_all_fields.php';

    // Gets all output forms options, like Slider, Lightbox, Button, etc.
    require 'partials/get_all_elements.php';



    
    // Localized strings to be used in js

    $allJsFields['str_rename'] = __('Rename', 'campaign-monitor');
    $allJsFields['str_delete'] = __('Delete', 'campaign-monitor');
    $allJsFields['str_show_label'] = __('Show Label', 'campaign-monitor');
    $allJsFields['str_required'] = __('Required', 'campaign-monitor');
    $allJsFields['str_done'] = __('Done', 'campaign-monitor');
    $allJsFields['str_placeholder_text'] = __('Placeholder text (optional)', 'campaign-monitor');
    $allJsFields['str_class_text'] = __('Additional class (optional)', 'campaign-monitor');
    $allJsFields['str_field_options'] = __('Field Options (One per line)', 'campaign-monitor');
    $allJsFields['str_confirm_delete'] = __('Are you sure you want to delete this custom field, including all its data?', 'campaign-monitor');
    $allJsFields['str_cancel'] = __('Cancel', 'campaign-monitor');


    // Site URL
    $allJsFields['preview_url'] = get_home_url()."/preview-cm-form-page/";


    //Script with $allListsFields array
    wp_register_script( 'wizard_script', plugin_dir_url( __FILE__ ).'../../js/admin/scripts.js' );
    $variables_array = $allJsFields;
    wp_localize_script( 'wizard_script', 'php_variables', $variables_array );
    wp_enqueue_script( 'wizard_script' );

?>

<!--Stylesheet-->
<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ); ?>../../css/admin/styles.css">


<div class="wrap">
	<form method="post" name="post" id="lists-add">
		<?php wp_nonce_field( 'campaign-monitor-add-wizard' ); ?>
		<input type="hidden" name="preview_type" id="preview_type">
        <!--	Enabled as default	-->
		<input type="hidden" name="enabled" value="1">
	
	    <div id="wizard-error-message">
            <p><strong><?php echo __('Oops!', 'campaign-monitor'); ?></strong> <?php echo __('Please fill in all the fields below correctly', 'campaign-monitor'); ?>.</p>
        </div>

	    <?php
            // STEP 1
            require 'partials/wizard_step_1.php';
        ?>
        
        <?php
            // STEP 2
            require 'partials/wizard_step_2.php';
        ?>
        
        <?php
            // STEP 3
            require 'partials/wizard_step_3.php';
        ?>

	</form>

</div>

<!-- Thickbox to confirm deletion -->
<?php add_thickbox(); ?>
<div id="TB_confirm" style="display:none;"></div>