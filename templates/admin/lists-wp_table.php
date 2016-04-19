<?php $Table = new CampaignMonitorWpTableLists(); ?>
<div class="wrap">
	<h2>
		<?php _e('Lists', 'campaign-monitor');?>
		<a href="<?php echo admin_url("admin.php?page=campaign-monitor-import-list"); ?>" class="add-new-h2"><?php _e('Use Existing Campaign Monitor List', 'campaign-monitor');?></a>
		<a href="<?php echo admin_url("admin.php?page=campaign-monitor-add-list"); ?>" class="add-new-h2"><?php _e('Create New', 'campaign-monitor');?></a>
	</h2>
	<?php $Table->prepare_items();
	$Table->display(); ?>
</div>

<style>
	#comments{
	width: 20%;
	}
</style>
<script type="text/javascript" src="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>css/tooltipster.css" type="text/css" media="all">

<script type="text/javascript">
	(function( $ ) {
		$(function() {
			$('#registration span:first').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				content: $("<span>Any list that is marked as “In Registration” will be the list that all subscribers are added to when checking the opt-in box upon registering for your site. You may only have one list selected at a time to receive subscribers at registration.</span>")
			});
			$('#comments span:first').tooltipster({
				iconDesktop:true,
				maxWidth:500,
				content: $("<span>Any list that is marked as “In Comments” will be the list that all subscribers are added to when checking the opt-in box upon creating and confirming a comment on your site.<br/> You may only have one list selected at a time to receive subscribers from your comments.</span>")
			});
		});
	})(jQuery);
</script>