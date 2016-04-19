<?php $Table = new CampaignMonitorWpTableForms(); ?>
<div class="wrap">
	<h2>
		<?php _e('Forms', 'campaign-monitor');?>
		<a href="<?php echo admin_url("admin.php?page=campaign-monitor-add-wizard"); ?>" class="add-new-h2"><?php _e('Add New', 'campaign-monitor');?></a>
	</h2>
	<?php
	$Table->views();
	$Table->prepare_items();
	$Table->display(); ?>
</div>