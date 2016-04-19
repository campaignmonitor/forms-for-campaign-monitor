<div class="wrap">
	<h2><?php echo __('Campaign Monitor Settings', 'campaign-monitor'); ?></h2>
	<form method="post" id="form-logout">
		<?php wp_nonce_field( 'campaign-monitor-logout' ); ?>
		<h2><?php echo __('Are you sure you want to logout and remove Campaign Monitor Keys?', 'campaign-monitor'); ?></h2>
		<p><?php echo __('Campaign Monitor plugin will not be functional until you add keys and log in again', 'campaign-monitor'); ?>.</p>
		<p><?php echo __('All your forms will be disabled', 'campaign-monitor'); ?>.</p>
		<a class="button" onclick="document.getElementById('form-logout').submit();;"><?php echo __('Yes, I understand', 'campaign-monitor'); ?></a> <a href="<?php echo admin_url('admin.php?page=campaign-monitor-options')?>" class="button"><?php echo __('No, it was a mistake', 'campaign-monitor'); ?></a>
	</form>
</div>
