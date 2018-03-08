<fieldset class="wizard-steps" id="wizardStep2">
        
    <p class="breadcrumb"><a href="<?php echo ("admin.php?page=campaign-monitor-forms") ?>"><?php echo __('Forms', 'campaign-monitor'); ?></a> > <?php echo __('New Form', 'campaign-monitor'); ?></p>
    <h2><?php echo __('Customize your form', 'campaign-monitor'); ?></h2>

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
                            <input id="wizardFieldsEmailHasLabel" type="checkbox" name="fields[email][label]" value="1" checked> <label for="wizardFieldsEmailHasLabel"><?php echo __('Show Label', 'campaign-monitor'); ?></label>
                            <input id="wizardFieldsEmailIsRequired" type="checkbox" name="fields[email][required]" value="1" checked disabled> <label for="wizardFieldsEmailIsRequired"><?php echo __('Required', 'campaign-monitor'); ?></label>
                        </div>
                        <p class="description"><?php echo __('Placeholder text (optional)', 'campaign-monitor'); ?></p>
                        <input type="text" name="fields[email][placeholder]" id="wizardFieldsEmailPlaceholder" value="" class="regular-text" placeholder="Email address">
                        <p class="description description--class"><?php echo __('Additional class (optional)', 'campaign-monitor'); ?></p>
                        <input type="text" name="fields[email][css_classes]" id="wizardFieldsEmailClass" value="" class="regular-text" placeholder="">

                    </td>	
                </tr>
                <tr>
                    <th class="check-column">			
                        <input id="wizardFieldsNameIsEnabled" type="checkbox" name="fields[userInformation][enabled]" value="1" checked>
                    </th>
                    <td>
                        <label for="wizardFieldsNameIsEnabled" class="label-is-enabled"><?php echo __('Name', 'campaign-monitor'); ?></label>
                        <div class="fields-extra-config">
                            <input id="wizardFieldsNameHasLabel" type="checkbox" name="fields[userInformation][label]" value="1" checked> <label for="wizardFieldsNameHasLabel"><?php echo __('Show Label', 'campaign-monitor'); ?></label>
                            <input id="wizardFieldsNameIsRequired" type="checkbox" name="fields[userInformation][required]" value="1" checked> <label for="wizardFieldsNameIsRequired"><?php echo __('Required', 'campaign-monitor'); ?></label>
                        </div>
                        <p class="description"><?php echo __('Placeholder text (optional)', 'campaign-monitor'); ?></p>
                        <input type="text" name="fields[userInformation][placeholder]" id="wizardFieldsNamePlaceholder" value="" class="regular-text" placeholder="Name">
                        <p class="description description--class"><?php echo __('Additional class (optional)', 'campaign-monitor'); ?></p>
                        <input type="text" name="fields[userInformation][css_classes]" id="wizardFieldsNameClass" value="" class="regular-text" placeholder="">

                    </td>	
                </tr>
            </tbody>

            <tbody id="list-custom-fields">
                <!--   FILLED BY JS   -->
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
                            
                            <p class="cancel-field">or <a href="#" id="bt-cancel-field">cancel</a></p>

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
                    <input type='text' name='form_title' id="wizardFormTitle" value='' class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Form Summary', 'campaign-monitor'); ?> <br><?php echo __('(optional)', 'campaign-monitor'); ?></th>
                <td>
                    <textarea name="form_summary" id="wizardFormSummary" cols="45" rows="3"></textarea>
                    <p class="description"><?php echo __('Encourage people to subscribe by explaining the type of content you\'ll be sending', 'campaign-monitor'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Submission Button Text', 'campaign-monitor'); ?></th>
                <td>
                    <input type='text' name='submitText' id="wizardButtonText" value='' class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Success Message Title', 'campaign-monitor'); ?></th>
                <td>
                    <input type='text' name='success_message_title' id="successMsgTitle" value='Thank you!' class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Success Message', 'campaign-monitor'); ?></th>
                <td>
                    <textarea style="width: 100%" title="" name='success_message' id="successMsg">Your subscription has been confirmed. You'll hear from us soon.</textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Form footer', 'campaign-monitor'); ?></th>
                <td>
                    <input id="wizardFieldsHasBadge" type="checkbox" name="hasBadge" value="1" checked> 
                    <label for="wizardFieldsHasBadge">
                        <?php echo __('Show ', 'campaign-monitor'); ?>  
                        <img class="badge-img" src="<?php echo plugins_url( '../../img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>" srcset="<?php echo plugins_url( '../../img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>, <?php echo plugins_url( '../../img/cm-logo-horizontal@2x.png', dirname(__FILE__) ); ?> 2x" alt="<?php echo __('Powered by Campaign Monitor ', 'campaign-monitor'); ?>">
                        
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

        <div class="wizard-steps-control">
            <input type="button" class="button button-primary" value="<?php echo __('Next', 'campaign-monitor'); ?>" name="submitStep2" id="submitStep2">

            <?php echo __('or', 'campaign-monitor'); ?> <a href="#" class="backToStep1"><?php echo __('go back', 'campaign-monitor'); ?></a>
        </div>

    </div>

    

</fieldset>