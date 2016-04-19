<?php if ( isset( CampaignMonitorPluginInstance()->connection->last_error ) ) :?>
<div class="wrap">
	<h2><?php echo __('OAuth Error!', 'campaign-monitor'); ?></h2>
	<p> <?php echo __('We have an error communicating with CampaignMonitor. The error is:', 'campaign-monitor'); ?></p>
	<p><strong><?php echo CampaignMonitorPluginInstance()->connection->last_error['error'] ?></strong></p>
	<p><?php echo CampaignMonitorPluginInstance()->connection->last_error['error_description'] ?></p>
	<a href="<?php echo CampaignMonitorPluginInstance()->connection->authorize_url()?>"><?php echo __('Try again?', 'campaign-monitor'); ?></a> <?php echo __('or', 'campaign-monitor'); ?> <a href="http://www.campaignmonitor.com" target="_blank"><?php echo __('Go to CampaignMonitor.com', 'campaign-monitor'); ?></a> <?php echo __('and fix the problem', 'campaign-monitor'); ?>.
</div>
<?php endif;?>