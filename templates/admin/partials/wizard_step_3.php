<fieldset class="wizard-steps" id="wizardStep3">

   <p class="breadcrumb"><a href="<?php echo ("admin.php?page=campaign-monitor-forms") ?>"><?php echo __('Forms', 'campaign-monitor'); ?></a> > <a href="#" class="backToStep2"><?php echo __('New Form', 'campaign-monitor'); ?></a> > <?php echo __('Customize', 'campaign-monitor'); ?> ></p>
   <h2><?php echo __('Customize your form', 'campaign-monitor'); ?></h2>

  <div class="wizard-main-col">

    <div id="step-3-options">

        <!--   FILLED BY JS   -->

    </div>

    <div class="wizard-steps-control">
        <input type="submit" class="button button-primary" value="<?php echo __('Enable Form', 'campaign-monitor'); ?>" name="publish" id="publish">

        <?php echo __('or', 'campaign-monitor'); ?> <a href="#" class="backToStep2"><?php echo __('go back', 'campaign-monitor'); ?></a>
    </div>

    </div>
    <!-- ###  Preview Box  ### -->
    <div class="wizard-aside-col">
        <div class="postbox">
            <h3><span><?php echo __('Preview Form', 'campaign-monitor'); ?></span></h3>
            <div class="inside">
                <p><?php echo __('See how your form is looking at any time by viewing a preview in a new window.', 'campaign-monitor'); ?></p>
                <input type="button" value="<?php echo __('Preview', 'campaign-monitor'); ?>" class="button-secondary" id="preview-form" />
            </div>
        </div>
    </div>

</fieldset>