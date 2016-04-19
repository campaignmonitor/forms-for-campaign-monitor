<?php $Table = new CampaignMonitorWpTableElements(); ?>
<div class="wrap">
	<h2>
		<?php _e('Elements', 'campaign-monitor');?>
		<a href="<?php echo admin_url("admin.php?page=campaign-monitor-add-element"); ?>" class="add-new-h2"><?php _e('Add New', 'campaign-monitor');?></a>
	</h2>
	<?php $Table->prepare_items();
	$Table->display(); ?>
</div>

<script type="text/javascript" src="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>css/tooltipster.css" type="text/css" media="all">

<script type="text/javascript">
	(function( $ ) {
		$(function() {
			$('#type span:first').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				content: $("<span>There are 5 different types of forms that you can create using Campaign Monitor for WordPress: a simple form, a slider, a lightbox, a button, or a bar.</span>")
			});
			$('#enabled span:first').tooltipster({
				iconDesktop:true,
				maxWidth:500,
				content: $("<span>Some elements will need to be enabled in order to show on your site. This allows you to easily see what is enabled and what isn’t. Lightboxes, sliders and bars all need to be enabled in order to function.</span>")
			});
			$('#global span:first').tooltipster({
				iconDesktop:true,
				maxWidth:500,
				content: $("<span>For lightboxes, sliders and bars you can choose to either have them appear globally, or only on certain pages of your site. This will show you whether they are globally applied or are targeted. If the element that is listed is a button or simple form, the short code to add the element to your post will display here instead.</span>")
			});
		});
	})(jQuery);
</script>