<?php
$bar = new CampaignMonitorBar();
$bar->load( $_GET['e'] );
?>
<div class="wrap">
	<h2><?php _e('Edit Bar', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-element' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row" id="r-enabled">
					<span><?php _e('Enable this Bar', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="enabled" value="1" <?php if ( 1 == $bar->enabled ): ?>checked="checked"<?php endif; ?>>
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
							<option value="<?php echo $form->id; ?>" <?php if( $form->id == $bar->data['form_id'] ): ?>selected="selected"<?php endif; ?>><?php echo $form->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Bar Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" value="<?php echo $bar->name; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Bar Text', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="fields[text]" id="text" value="<?php echo $bar->data['text']; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-position">
					<span><?php _e('Bar Position', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<select name="fields[position]" id="position">
						<option value="top" <?php if ( "top" == $bar->data['position'] ):?>selected="selected"<?php endif;?>>Top</option>
						<option value="bottom" <?php if ( "bottom" == $bar->data['position'] ):?>selected="selected"<?php endif;?>>Bottom</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					<input type="radio" name="global" value="1" <?php if ( 1 == $bar->global ): ?>checked="checked"<?php endif; ?> id="show_globally"> Show Globally
					<input type="radio" name="global" value="0" <?php if ( 0 == $bar->global ): ?>checked="checked"<?php endif; ?> id="show_targeted" style="margin-left: 50px!important;"> <span id="r-urls">Show Only on Specific URLS</span>
				</th>
			</tr>
			<tr id="show_in" <?php if ( 1 == $bar->global ): ?>style="display:none;"<?php endif; ?>>
				<th scope="row" colspan="2">
					<textarea name="fields[show_in]" id="urls" cols="50" rows="15"><?php foreach( $bar->data['show_in'] as $url ):?><?php echo $url . "\r\n"; ?><?php endforeach; ?></textarea>
				</th>
			</tr>

			<tr>
				<th scope="row" colspan="2">
					<input type="submit" value="<?php _e('Save Bar', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">
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
				content: $("<span>If you select this option, your bar will be enabled and will display either globally or in a targeted region of your site depending on your settings.</span>")
			});
			$('#r-form span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This is the form that you would like to use as the basis of this element.</span>")
			});
			$('#r-position span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This allows you to select whether you would like the slider to slide in from the top or bottom of your page.</span>")
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