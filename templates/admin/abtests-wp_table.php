<?php $Table = new CampaignMonitorWpTableABTests(); ?>
<div class="wrap">
	<h2>
		<?php _e('A/B Tests', 'campaign-monitor');?>
		<a href="<?php echo admin_url("admin.php?page=campaign-monitor-add-abtest"); ?>" class="add-new-h2"><?php _e('Add New', 'campaign-monitor');?></a>
	</h2>
	<?php $Table->prepare_items();
	$Table->display(); ?>
</div>