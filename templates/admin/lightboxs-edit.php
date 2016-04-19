<?php
$lightbox = new CampaignMonitorLightbox();
$lightbox->load( $_GET['e'] );
?>
<div class="wrap">
	<h2><?php _e('Edit Lightbox', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-element' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row" id="r-enabled">
					<span><?php _e('Enable this Lightbox', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="enabled" value="1" <?php if ( 1 == $lightbox->enabled ): ?>checked="checked"<?php endif; ?>>
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-form">
					<span><?php _e('Form', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<select name="fields[form_id]" id="form_id">
						<?php $forms = new CampaignMonitorForm();
						foreach( $forms->get_all() as $form ): ?>
							<option value="<?php echo $form->id; ?>" <?php if( $form->id == $lightbox->data['form_id'] ): ?>selected="selected"<?php endif; ?>><?php echo $form->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Lightbox Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" value="<?php echo $lightbox->name; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Lightbox Text', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="fields[text]" id="text" value="<?php echo $lightbox->data['text']; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-oncepp">
					<span><?php _e('Show Once Per Page', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="fields[once_per_page]" value="1" <?php if ( 1 == $lightbox->data['once_per_page'] ): ?>checked="checked"<?php endif; ?>>
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-scroll">
					<span><?php _e('Show lightbox after page scroll', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="fields[scrolled]" value="1" <?php if ( 1 == $lightbox->data['scrolled'] ): ?>checked="checked"<?php endif; ?>>
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-scrolled">
					<span><?php _e('Scrolled Pixels or %', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="text" name="fields[scrolled_value]" id="title" value="<?php echo $lightbox->data['scrolled_value']; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-seconds">
					<span><?php _e('Seconds elapsed to show lightbox', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="text" name="fields[timed]" id="timed" value="<?php echo $lightbox->data['timed']; ?>" /> <?php _e('seconds', 'campaign-monitor'); ?>
				</td>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					<input type="radio" name="global" value="1" <?php if ( 1 == $lightbox->global ): ?>checked="checked"<?php endif; ?> id="show_globally"> Show Globally
					<input type="radio" name="global" value="0" <?php if ( 0 == $lightbox->global ): ?>checked="checked"<?php endif; ?> id="show_targeted" style="margin-left: 50px!important;"> <span id="r-urls">Show Only on Specific URLS</span>
				</th>
			</tr>
			<tr id="show_in" <?php if ( 1 == $lightbox->global ): ?>style="display:none;"<?php endif; ?>>
				<th scope="row" colspan="2">
					<textarea name="fields[show_in]" id="urls" cols="50" rows="15"><?php foreach( $lightbox->data['show_in'] as $url ):?><?php echo $url . "\r\n"; ?><?php endforeach; ?></textarea>
				</th>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					<input type="submit" value="<?php _e('Save Lightbox', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">
				</th>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	jQuery(function(){
		jQuery('#show_globally').on('click', function() {
			jQuery("#show_in").hide();
		});
		jQuery('#show_targeted').on('click', function() {
			jQuery("#show_in").show();
		});
	});
</script>
<script type="text/javascript" src="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>css/tooltipster.css" type="text/css" media="all">

<script type="text/javascript">
	(function( $ ) {
		$(function() {
			$('#r-enabled span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>If you select this option, your Lightbox will be enabled and will display either globally or in a targeted region of your site depending on your settings.</span>")
			});
			$('#r-form span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This is the form that you would like to use as the basis of this element.</span>")
			});
			$('#r-oncepp span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This is automatically selected for you. This sets it so that the lightbox will only display once per page and will not continue multiple times if you have set multiple triggers.</span>")
			});
			$('#r-scroll span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>Select this box  to show your lightbox to a customer after they have scrolled a specific length down your page. You can have both this and the time trigger applied at the same time.</span>")
			});
			$('#r-scrolled span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>Type in the specific distance you would like a customer to scroll down your page before the lightbox displays for them.</span>")
			});
			$('#r-seconds span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>Type in a number of seconds here if you would like to have your lightbox display for customers after a specific amount of time spent on the page. You can have both this and the scroll trigger applied at the same time.</span>")
			});
			$('#r-urls').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>Type in a URL here to be able to limit the display of your lightbox to specific pages of your site. If you would like to include the lightbox on a single page with the URL of www.site.com/button-post/ for example, you would include “/button-post/” in that box. Please keep in mind that any pages on your site with the root of their domain being www.site.com/button-post/ will also have this lightbox appear. So, for example, if you had www.site.com/button-post/1234, the lightbox will appear there and also on www.site.com/button-post/9876, because they both have the same “root.” If you would like it to only appear on one or the other, you would want to include “/button-post/1234” instead of just “/button-post/”.</span>")
			});

		});
	})(jQuery);
</script>