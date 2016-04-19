<?php $current_user = wp_get_current_user(); ?>
<div class="wrap">
	<h2><?php echo __('Campaign Monitor for WordPress Settings', 'campaign-monitor'); ?></h2>
	<?php if ( ! CampaignMonitorPluginInstance()->connection->enabled() ): ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'campaign-monitor-options' );
			do_settings_sections( 'campaign-monitor-options' );
			submit_button();
			?>
		</form>
	<?php else: 
    
        if (isset($_GET['settings-updated'])){
			CampaignMonitorPluginInstance()->reconnect_elements();
            echo '<div class="updated"><p>';
            echo __('Congratulations! You have successfully activated the Campaign Monitor plug-in.', 'campaign-monitor'); 
            echo ' <a href="'.admin_url("admin.php?page=campaign-monitor-forms").'">';
            echo __('Create your first form', 'campaign-monitor'); 
            echo '</a>.</p></div>';
            
            //Updates badge
            $current_options = get_option('campaign_monitor_settings');
            $current_options['has_badge'] = "yes";
            update_option('campaign_monitor_settings',$current_options);
        }
    
    ?>
     
         <?php
            if (isset($_POST['check_badge_change'])){
                if(isset($_POST['has_badge'])){$hasBadge = "yes";}else{$hasBadge = "no";}
                $current_options = get_option('campaign_monitor_settings');
                $current_options['has_badge'] = $hasBadge;
                update_option('campaign_monitor_settings',$current_options);
            }

        ?>
      
       
        <form method="post" class="badge-form">
           <input type="hidden" name="check_badge_change" value="1">
            <?php $current_options = get_option('campaign_monitor_settings'); ?>
            <input id="wizardFieldsHasBadge" type="checkbox" name="has_badge" value="1" <?php if(!isset($current_options['has_badge']) || $current_options['has_badge'] == "yes"){echo "checked=checked";} ?>> 
            <label for="wizardFieldsHasBadge">
                <?php echo __('Show ', 'campaign-monitor'); ?>  
                <img class="badge-img" src="<?php echo plugins_url( '../img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>" srcset="<?php echo plugins_url( '../img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>, <?php echo plugins_url( '../img/cm-logo-horizontal@2x.png', dirname(__FILE__) ); ?> 2x" alt="<?php echo __('Powered by Campaign Monitor ', 'campaign-monitor'); ?>">
                <?php echo __('on your form', 'campaign-monitor'); ?>
            </label>
        </form>
        
        
	    <!-- <p><?php echo __('You are connected as', 'campaign-monitor'); ?> <?php echo CampaignMonitorPluginInstance()->connection->get_company_name(); ?></p> -->
		<p><?php echo __('To disconnect Campaign Monitor for WordPress and remove all in-app form customization, click below', 'campaign-monitor'); ?>.</p>
		<a href="<?php echo admin_url("admin.php?page=campaign-monitor-logout"); ?>" class="button"><?php echo __('Log Out', 'campaign-monitor'); ?></a>
	<?php endif; ?>
</div>


<style>
    .badge-form{
        margin:20px 0 5px;
    }
    #wizardFieldsHasBadge{
        margin-bottom:-2px;
    }
    .badge-img{margin: 0 3px -3px 3px;}
</style>
<script>
    jQuery('#wizardFieldsHasBadge').on('change',function(){
        jQuery('.badge-form').submit();
    });
</script>