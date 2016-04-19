

<fieldset class="wizard-steps" id="wizardStep1">

    <p class="breadcrumb"><a href="<?php echo ("admin.php?page=campaign-monitor-forms") ?>"><?php echo __('Forms', 'campaign-monitor'); ?></a> ></p>
    <h2><?php echo __('Add new form', 'campaign-monitor'); ?></h2>

    <div class="wizard-main-col">

        <h3><?php echo __('What type of form would you like to create?', 'campaign-monitor'); ?></h3>

        <div class="options-type-of-form">
            <label>
                <input type="radio" name="type" value="slider" />
                <img src="<?php echo plugin_dir_url( __FILE__ ); ?>../../../img/option-slide-out.png" alt="">
            </label>
            <label>
                <input type="radio" name="type" value="lightbox" />
                <img src="<?php echo plugin_dir_url( __FILE__ ); ?>../../../img/option-lightbox.png" alt="">
            </label>
            <label>
                <input type="radio" name="type" value="bar" />
                <img src="<?php echo plugin_dir_url( __FILE__ ); ?>../../../img/option-bar.png" alt="">
            </label>
            <label>
                <input type="radio" name="type" value="button" />
                <img src="<?php echo plugin_dir_url( __FILE__ ); ?>../../../img/option-button.png" alt="">
            </label>
            <label>
                <input type="radio" name="type" value="simple_form" />
                <img src="<?php echo plugin_dir_url( __FILE__ ); ?>../../../img/option-embedded.png" alt="">
            </label>
        </div>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php echo __('Name this form', 'campaign-monitor'); ?></th>
                    <td>
                        <input type="text" name="form_name" id="wizardFormName" value="" class="regular-text">
                    </td>
                </tr>

                <!-- ###  Select an existing list (Default: Visible)  ### -->
                <tr class="form-existing-list">
                    <th scope="row"><?php echo __('Select a list', 'campaign-monitor'); ?></th>
                    <td>
                        <select name="list_id" id="wizardExistingListName" class="regular-text">

                          <?php
                          foreach( CampaignMonitorPluginInstance()->connection->get_clients() as $client ):
                            foreach( CampaignMonitorPluginInstance()->connection->get_client_lists( $client->ClientID ) as $list ): ?>
                            <option value="<?php echo $list->ListID ?>"><?php echo $list->Name ?></option>
                          <?php
                            endforeach;
                          endforeach; ?>

                        </select>
                        <p class="description"><?php echo __('Select the list subscribers will be added to when they complete this form or', 'campaign-monitor'); ?> <a href="#" id="wizardNewList"><?php echo __('create a new list', 'campaign-monitor'); ?></a></p>
                    </td>
                </tr>


            </tbody>
        </table>

        <!-- ###  Create a new list (Default: Hidden)  ### -->
        <div class="form-new-list">
            <h4><?php echo __('Create New List', 'campaign-monitor'); ?></h4>

            <p><?php echo __('Enter the detais to create a new list or', 'campaign-monitor'); ?> <a href="#" id="wizardExistingList"><?php echo __('use existing list', 'campaign-monitor'); ?></a></p>
            <table class="form-table">
                <?php
                $clients = CampaignMonitorPluginInstance()->connection->get_clients();
                if ( count( $clients ) > 1 ): ?>
                    <tr>
                        <th scope="row"><?php echo __('Client', 'campaign-monitor'); ?></th>
                        <td><select name="client" class="regular-text" id="client">
                                <?php foreach( $clients as $client ):?>
                                <option value="<?php echo $client->ClientID;?>"><?php echo $client->Name; ?></option>
                                <?php endforeach; ?>
                            </select></td>
                    </tr>
                <?php
                else:
                    $client_id = $clients[0]->ClientID;
                    ?>

                    <input type="hidden" name="client" value="<?php echo $client_id; ?>">
                <?php endif; ?>
                <tr>
                    <th scope="row"><?php echo __('List Name', 'campaign-monitor'); ?></th>
                    <td>
                        <input type='text' name='title' id="wizardNewListName" value='' class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('List Type', 'campaign-monitor'); ?></th>
                    <td>
                        <select name="ConfirmedOptIn" class="regular-text" id="selectOptIn">
                            <option value="0"><?php echo __('Single opt-in (no confirmation required)', 'campaign-monitor'); ?></option>
                            <option value="1"><?php echo __('Confirmed opt-in (confirmation required)', 'campaign-monitor'); ?></option>
                        </select>
                        <p class="description"><strong><?php echo __('Single opt-in', 'campaign-monitor'); ?></strong> <?php echo __('means new subscribers are added to this list as soon as they complete the subscribe form', 'campaign-monitor'); ?>. <br> <strong><?php echo __('Confirmed opt-in', 'campaign-monitor'); ?></strong> <?php echo __('means a confirmation email will be sent with a link they must click to validate their address', 'campaign-monitor'); ?>.</p>
                    </td>
                </tr>
                <tr class="confirmation-url">
                    <th scope="row"><?php echo __('Confirmation URL (optional)', 'campaign-monitor'); ?></th>
                    <td>
                        <input type="text" name="ConfirmationSuccessPage" id="wizardConfirmationUrl" class="regular-text">
                        <p class="description"><?php echo __('Set the URL that the user will be redirected to after confirming subscription. If empty, will redirect to a default page with a simple confirmation message.', 'campaign-monitor'); ?></p>
                    </td>
                </tr>
            </table>
        </div>


        <div class="wizard-steps-control">
            <input type="button" class="button button-primary" value="<?php echo __('Next', 'campaign-monitor'); ?>" name="submitStep1" id="submitStep1">
        </div>
    </div>

</fieldset>