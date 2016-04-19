<?php
    $form = new CampaignMonitorForm();
    $form->load( $_GET['form'] );
    $list = CampaignMonitorPluginInstance()->connection->get_list( $form->data['list_id'] )->response;
    $fields = CampaignMonitorPluginInstance()->connection->get_list_fields( $list->ListID )->response;
?>

<!--Stylesheet-->
<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ); ?>../../css/admin/styles.css">

<?php

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


    // JS
    wp_register_script( 'wizard_script', plugin_dir_url( __FILE__ ).'../../js/admin/scripts.js' );
    $variables_array = $allJsFields;
    wp_localize_script( 'wizard_script', 'php_variables', $variables_array );
    wp_enqueue_script( 'wizard_script' );

?>


<form method="post" name="post" id="lists-add">
    <?php wp_nonce_field( 'campaign-monitor-edit-wizard' ); ?>
    
    <input type="hidden" name="list_id" value="<?php echo $form->data['list_id']; ?>">
    <input type="hidden" name="form_id" value="<?php echo $form->id; ?>">
    <div id="wizard-error-message">
        <p><strong><?php echo __('Oops!', 'campaign-monitor'); ?></strong> <?php echo __('Please fill in all the fields below correctly', 'campaign-monitor'); ?>.</p>
    </div>

    <fieldset class="wizard-steps" id="wizard-edit">

        <p class="breadcrumb"><a href="<?php echo ("admin.php?page=campaign-monitor-forms") ?>"><?php echo __('Forms', 'campaign-monitor'); ?></a></p>
        <h2><?php echo __('Edit form', 'campaign-monitor'); ?></h2>
        
        <h3><?php echo __('Customize your form', 'campaign-monitor'); ?></h3>

        <div class="wizard-main-col">
            <table id="wizard-form-fields" class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th colspan="2"><?php echo __('Fields', 'campaign-monitor'); ?></th>	
                    </tr>
                </thead>

                <tbody id="main-fields">
                    <tr>
                        <th class="check-column">			
                            <input id="wizardFieldsEmailIsEnabled" type="checkbox" name="fields[email][enabled]" value="1" checked disabled>
                        </th>
                        <td>
                            <label for="wizardFieldsEmailIsEnabled" class="label-is-enabled"><?php echo __('Email', 'campaign-monitor'); ?></label>
                            <div class="fields-extra-config">
                                <input id="wizardFieldsEmailHasLabel" type="checkbox" name="fields[email][label]" value="1" <?php if ( 1 == $form->get_field_data( 'email', 'label' ) ): ?>checked="checked"<?php endif; ?>> <label for="wizardFieldsEmailHasLabel"><?php echo __('Show Label', 'campaign-monitor'); ?></label>
                                <input id="wizardFieldsEmailIsRequired" type="checkbox" name="fields[email][required]" value="1" checked disabled> <label for="wizardFieldsEmailIsRequired"><?php echo __('Required', 'campaign-monitor'); ?></label>
                            </div>
                            <p class="description"><?php echo __('Placeholder text (optional)', 'campaign-monitor'); ?></p>
                            <input type="text" name="fields[email][placeholder]" id="wizardFieldsEmailPlaceholder" value="<?php echo stripslashes($form->get_field_data( 'email', 'placeholder')); ?>" class="regular-text" placeholder="<?php echo __('Email address', 'campaign-monitor'); ?>">
                            <p class="description description--class"><?php echo __('Additional class (optional)', 'campaign-monitor'); ?></p>
                            <input type="text" name="fields[email][css_classes]" id="wizardFieldsEmailClass" value="<?php echo stripslashes($form->get_field_data( 'email', 'css_classes')); ?>" class="regular-text" placeholder="">

                        </td>	
                    </tr>
                    <tr>
                        <th class="check-column">			
                            <input id="wizardFieldsNameIsEnabled" type="checkbox" name="fields[userInformation][enabled]" value="1" <?php if ( 1 == $form->get_field_data( 'userInformation', 'enabled' ) ): ?>checked="checked"<?php endif; ?>>
                        </th>
                        <td>
                            <label for="wizardFieldsNameIsEnabled" class="label-is-enabled"><?php echo __('Name', 'campaign-monitor'); ?></label>
                            <div class="fields-extra-config">
                                <input id="wizardFieldsNameHasLabel" type="checkbox" name="fields[userInformation][label]" value="1" <?php if ( 1 == $form->get_field_data( 'userInformation', 'label' ) ): ?>checked="checked"<?php endif; ?>> <label for="wizardFieldsNameHasLabel"><?php echo __('Show Label', 'campaign-monitor'); ?></label>
                                <input id="wizardFieldsNameIsRequired" type="checkbox" name="fields[userInformation][required]" value="1" <?php if ( 1 == $form->get_field_data( 'userInformation', 'required' ) ): ?>checked="checked"<?php endif; ?>> <label for="wizardFieldsNameIsRequired"><?php echo __('Required', 'campaign-monitor'); ?></label>
                            </div>
                            <p class="description"><?php echo __('Placeholder text (optional)', 'campaign-monitor'); ?></p>
                            <input type="text" name="fields[userInformation][placeholder]" id="wizardFieldsNamePlaceholder" value="<?php echo stripslashes($form->get_field_data( 'userInformation', 'placeholder')); ?>" class="regular-text" placeholder="Name">
                            <p class="description description--class"><?php echo __('Additional class (optional)', 'campaign-monitor'); ?></p>
                            <input type="text" name="fields[userInformation][css_classes]" id="wizardFieldsNameClass" value="<?php echo stripslashes($form->get_field_data( 'userInformation', 'css_classes')); ?>" class="regular-text" placeholder="">

                        </td>	
                    </tr>
                </tbody>

                <tbody id="list-custom-fields">
                    <?php foreach( $fields as $field ):
                      $key = str_replace( [ "\\'"], [""], $field->Key);
                      $cleanKey = str_replace(array( '[', ']' ), '', $key);
                      $fieldName = str_replace( "\\", "", $form->get_field_data( $cleanKey, 'FieldName' ));
                      if ( empty($fieldName ) ) {
                        $fieldName = $field->FieldName;
                      }
				    ?>
               
                   <tr class="additional-field">
                        <th class="check-column">
                            <input id="fields<?php echo $key;?>[enabled]" type="checkbox" name="fields<?php echo $key;?>[enabled]" value="1" <?php if ( 1 == $form->get_field_data( $cleanKey, 'enabled' ) ): ?>checked="checked"<?php endif; ?>>
                        </th>
                        <td>
                            <label for="fields<?php echo $key;?>[enabled]" class="label-is-enabled"><?php echo $fieldName; ?></label>
                            <ul class="field-options"><li><a href="#" class="rename-field"><?php echo __('Rename', 'campaign-monitor'); ?></a></li><li><a href="#TB_inline?width=300&height=130&inlineId=TB_confirm" class="delete-field thickbox" id="delete<?php echo $key;?>"><?php echo __('Delete', 'campaign-monitor'); ?></a></li></ul>
                            <div class="fields-extra-config">
                                <input id="fields<?php echo $key;?>[label]" type="checkbox" name="fields<?php echo $key;?>[label]" value="1" <?php if ( 1 == $form->get_field_data( $cleanKey, 'label' ) ): ?>checked="checked"<?php endif; ?>> 
                                <label for="fields<?php echo $key;?>[label]"><?php echo __('Show Label', 'campaign-monitor'); ?></label>
                                <input id="fields<?php echo $key;?>[required]" type="checkbox" name="fields<?php echo $key;?>[required]" value="1" <?php if ( 1 == $form->get_field_data( $cleanKey, 'required' ) ): ?>checked="checked"<?php endif; ?>> 
                                <label for="fields<?php echo $key;?>[required]"><?php echo __('Required', 'campaign-monitor'); ?></label>
<!--                                <input type="hidden" name="fields<?php echo $key;?>[DataType]" id="fields<?php echo $key;?>[DataType]" value="email">-->
                            </div>
                            <div class="fields-rename-field">
                                <input type="text" name="fields<?php echo $key;?>[FieldName]" id="fields<?php echo $key;?>[FieldName]" value="<?php echo str_replace( "\\", "", $fieldName); ?>" class="regular-text">
                                <input type="button" value="<?php echo __('Done', 'campaign-monitor'); ?>" class="button-secondary bt-change-name">
                            </div>
                            <input type="hidden" name="fields<?php echo $key;?>[DataType]" value="<?php echo $field->DataType; ?>" />
                            <?php if ( in_array( $field->DataType, ['Text', 'Number', 'Date'] ) ): ?>
                            <p class="description"><?php echo __('Placeholder text (optional)', 'campaign-monitor'); ?></p><input type="text" name="fields<?php echo $key;?>[placeholder]" value="<?php echo str_replace( "\\", "", $form->get_field_data( $cleanKey, 'placeholder' )); ?>" class="regular-text">
                            <?php endif; ?>
                            
                            <p class="description description--class"><?php echo __('Additional class (optional)', 'campaign-monitor'); ?></p>
                            <input type="text" name="fields<?php echo $key;?>[css_classes]" value="<?php echo str_replace( "\\", "", $form->get_field_data( $cleanKey, 'css_classes' )); ?>" class="regular-text" placeholder="">
                            
                            
                            <?php if ( in_array( $field->DataType, ['MultiSelectOne', 'MultiSelectMany'] ) ): ?>
<p class="description"><?php echo __('Field Options (One per line)', 'campaign-monitor'); ?></p>                                    
<textarea name="fields<?php echo $field->Key ?>[Options]"  rows="10" cols="30" class="options-field required-options">
<?php foreach( $field->FieldOptions as $option):?>
<?php echo stripslashes(CampaignMonitorPluginInstance()->clean_option($option)) . "\n"; ?>
<?php endforeach; ?>

</textarea>
                                    
                                <?php endif; ?>
                        </td>
                    </tr>
               
               
               <?php endforeach; ?>
               
               
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2">

                            <!-- ###  Add new fields to form (Default: Hidden)  ### -->
                            <div class="field-generator">

                               <h3><?php echo __('New Field', 'campaign-monitor'); ?></h3>
                                <label for="wizardNewFieldName"><?php echo __('Field Name (100 character max)', 'campaign-monitor'); ?></label>
                                <input type="text" name='wizardNewFieldName' id="wizardNewFieldName" value='' class="regular-text" maxlength="100">

                                <label for="wizardNewFieldDataType"><?php echo __('Data Type', 'campaign-monitor'); ?></label>
                                <select name="wizardNewFieldDataType" id="wizardNewFieldDataType" class="regular-text">
                                    <option value="Text"><?php echo __('Text', 'campaign-monitor'); ?></option>
                                    <option value="Number"><?php echo __('Number', 'campaign-monitor'); ?></option>
                                    <option value="MultiSelectOne"><?php echo __('Multiple Options (can only select one)', 'campaign-monitor'); ?></option>
                                    <option value="MultiSelectMany"><?php echo __('Multiple Options (can select many)', 'campaign-monitor'); ?></option>
                                    <option value="Date"><?php echo __('Date', 'campaign-monitor'); ?></option>
                                    <option value="Country"><?php echo __('Country', 'campaign-monitor'); ?></option>
                                    <option value="USState"><?php echo __('US State', 'campaign-monitor'); ?></option>
                                </select>

                                <input type="button" value="<?php echo __('Create Field', 'campaign-monitor'); ?>" class="button-secondary" id="bt-generate-field" />
                                
                                <p class="cancel-field" id="bt-cancel-field">or <a href="#">cancel</a></p>

                            </div>

                            <input type="button" value="<?php echo __('Add New Field', 'campaign-monitor'); ?>" id="add-new-field" class="button-secondary" />
                        </td>	
                    </tr>
                </tfoot>

            </table>


            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo __('Form Title', 'campaign-monitor'); ?></th>
                    <td>
                        <input type='text' name='form_title' id="wizardFormTitle" value="<?php echo stripslashes( $form->data['form_title'] );?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Form Summary', 'campaign-monitor'); ?> <br>(<?php echo __('optional', 'campaign-monitor'); ?>)</th>
                    <td>
                        <textarea name="form_summary" id="wizardFormSummary" cols="45" rows="3"><?php echo stripslashes( $form->data['form_summary'] );?></textarea>
                        <p class="description"><?php echo __('Encourage people to subscribe by explaining the type of content you\'ll be sending', 'campaign-monitor'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('Submission Button Text', 'campaign-monitor'); ?></th>
                    <td>
                        <input type='text' name='submitText' id="wizardButtonText" value="<?php echo stripslashes( $form->data['submitText'] );?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php echo __('Form footer', 'campaign-monitor'); ?></th>
                    <td>
                        <input id="wizardFieldsHasBadge" type="checkbox" name="hasBadge" value="1" <?php if ( isset($form->data['hasBadge']) && 1 == $form->data['hasBadge'] ): ?>checked="checked"<?php endif; ?>> 
                        <label for="wizardFieldsHasBadge">
                            <?php echo __('Show ', 'campaign-monitor'); ?>  
                            <img class="badge-img" src="<?php echo plugins_url( '../img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>" alt="<?php echo __('Powered by Campaign Monitor ', 'campaign-monitor'); ?>">
                            <?php echo __('on your form', 'campaign-monitor'); ?>
                        </label>
                        <?php 
                            $current_options = get_option('campaign_monitor_settings');
                            if (isset($current_options['has_badge']) && $current_options['has_badge'] == "no"){
                        ?>
                        <p class="description"><?php echo __('This function is currently disabled. Please turn it on in ', 'campaign-monitor'); ?><a href="admin.php?page=campaign-monitor-options">Settings</a>.</p>
                        <?php }else{ ?>
                        <p class="description"><?php echo __('Permanently disable this badge in the plugin settings', 'campaign-monitor'); ?></p>
                        <?php } ?>
                    </td>
                </tr>
                
            </table>	
            
            <div id="step-3-options">
               
               <?php
                    // Location Settings
                    require 'partials/wizard_edit_location.php';
                ?>
               
            </div>

        </div>

        
        <div class="wizard-aside-col">
          
          <!-- ###  Publish Box  ### -->
          <div class="postbox">
                <h3><span><?php echo __('Publish Changes', 'campaign-monitor'); ?></span></h3>
                <div class="inside">
                   <div class="submitbox" id="submitpost">

                        <div id="minor-publishing">

                            <div id="misc-publishing-actions">

                                <div class="misc-pub-section misc-pub-post-status">
                                   <label for="post_status"><?php echo __('Form Status', 'campaign-monitor'); ?>:</label>
                                    <span id="post-status-display">
                                        <?php if ($form->enabled == "1" ){echo __('Enabled', 'campaign-monitor');}else if ($form->enabled == "0" ){echo __('Disabled', 'campaign-monitor');}else{echo __('Disconnected', 'campaign-monitor');}  ?>
                                    </span>
                                    
                                    <?php if ($form->enabled !== "2" ){ ?>
                                        <a href="#" class="edit-post-status"><?php echo __('Edit', 'campaign-monitor'); ?></a>
                                    

                                    <div id="post-status-select">
                                        <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="publish">
                                        <select name="enabled" id="post_status">
                                            <option <?php if ($form->enabled == "1" ){echo "selected=selected";}  ?> value="1"><?php echo __('Enabled', 'campaign-monitor'); ?></option>
                                            <option <?php if ($form->enabled == "0" ){echo "selected=selected";}  ?> value="0"><?php echo __('Disabled', 'campaign-monitor'); ?></option>
                                        </select>
                                         <a href="#" class="save-post-status button"><?php echo __('OK', 'campaign-monitor'); ?></a>
                                         <a href="#" class="cancel-post-status button-cancel"><?php echo __('Cancel', 'campaign-monitor'); ?></a>
                                    </div>
                                    
                                    <?php }else{ ?>
                                        <p class="alert-disconnected"><?php echo __('This form is linked to an account which has been disconnected.', 'campaign-monitor') ?></p>
                                    <?php } ?>

                                </div><!-- .misc-pub-section -->

                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <?php if ($form->enabled !== "2" ){ ?>
                <div id="major-publishing-actions">
                    <div id="publishing-action">
                        <input type="submit" class="button button-primary" value="<?php echo __('Save', 'campaign-monitor'); ?>" name="publish" id="publish">
                        <span class="spinner"></span>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php } ?>
            </div>
           
           <!-- ###  Preview Box  ### -->
           <div class="postbox">
                <h3><span><?php echo __('Preview Form', 'campaign-monitor'); ?></span></h3>
                <div class="inside">
                    <p><?php echo __('See how your form is looking at any time by viewing a preview in a new window', 'campaign-monitor'); ?>.</p>
                    <input type="button" value="<?php echo __('Preview', 'campaign-monitor'); ?>" class="button-secondary" id="preview-form" />
                </div>
            </div>
            
            
        </div>

    </fieldset>


</form>

<!-- Thickbox to confirm deletion -->
<?php add_thickbox(); ?>
<div id="TB_confirm" style="display:none;"></div>