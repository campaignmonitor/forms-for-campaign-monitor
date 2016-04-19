<?php
$list = CampaignMonitorPluginInstance()->connection->get_list($_GET['list'])->response;
$cmList = new CampaignMonitorList();
$cmList->load( $_GET['list'] );
$defaults = array(
	'title' => $list->Title,
	'optin' => ($list->ConfirmedOptIn?1:0),
	'ConfirmationSuccessPage' => $list->ConfirmationSuccessPage,
	'UnsubscribeSetting' => $list->UnsubscribeSetting,
);

if ( isset( $_POST ) ) {
	$defaults = array_merge( $defaults, $_POST );
}

?>
<div class="wrap">
	<h2><?php _e('Edit List', 'campaign-monitor');?></h2>
	<?php if ( CampaignMonitorPluginInstance()->connection->last_error ): ?>
		<div class="error notice is-dismissible below-h2" id="message">
			<p><?php _e( CampaignMonitorPluginInstance()->connection->last_error->Message );?></p>
			<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button>
		</div>
	<?php endif; ?>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-list' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('List Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" value="<?php echo $defaults['title']?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-optin">
					<span><?php _e('Confirmed Opt-in', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="ConfirmedOptIn" id="optin" value="1" <?php if ( 1 == $defaults['optin'] ) :?>checked="checked"<?php endif; ?> />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-sucess">
					<span><?php _e('Confirmation Success Page URL', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="text" name="ConfirmationSuccessPage" id="success" value="<?php echo $defaults['ConfirmationSuccessPage']?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('When the user unsubscribes', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="UnsubscribeSetting" id="unsubscribesetting">
						<option value="<?php echo CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST;?>" <?php if ( CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST == $defaults['UnsubscribeSetting'] ) :?>selected<?php endif; ?>><?php _e('Unsubscribe from this list only', 'campaign-monitor'); ?></option>
						<option value="<?php echo CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS;?>" <?php if ( CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS == $defaults['UnsubscribeSetting'] ) :?>selected<?php endif; ?>><?php _e('Unsubscribe from all lists', 'campaign-monitor'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-comments">
					<span><?php _e('Show on Comments', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="comments" id="comments" value="1" <?php if ( 1 == $cmList->comments ) :?>checked="checked"<?php endif; ?> />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-comments-text">
					<span><?php _e('Opt-in text for comments', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="text" name="comments_text" id="comments_text" value="<?php echo $cmList->comments_text?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-registration">
					<span><?php _e('Show on Registration Form', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="checkbox" name="registration" id="registration" value="1" <?php if ( 1 == $cmList->registration ) :?>checked="checked"<?php endif; ?> />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-registration-text">
					<span><?php _e('Opt-in text for registration form', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="text" name="registration_text" id="registration_text" value="<?php echo $cmList->registration_text?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="<?php _e('Save List', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish"></td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript" src="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>css/tooltipster.css" type="text/css" media="all">

<script type="text/javascript">
	(function( $ ) {
		$(function() {
			$('#r-optin span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				interactive:true,
				content: $("<span>Campaign Monitor supports two different kinds of list types. To read more about each one and what they are good for, check out our <a href='http://help.campaignmonitor.com/topic.aspx?t=16'>documentation</a>.</span>")
			});
			$('#r-sucess span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				interactive:true,
				content: $("<span><a href='http://help.campaignmonitor.com/topic.aspx?t=187' target='_blank'>Documentation</a></span>")
			});
			$('#r-comments span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This will apply an opt-in box to the comment form on your site. If you select this option, any subscribers that leave a comment and select the opt-in box will be sent to this subscriber list in Campaign Monitor.</span>")
			});
			$('#r-comments-text span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This is the copy that will display next to the opt-in box on your comment forms, if you choose to enable that option.</span>")
			});
			$('#r-registration span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This will apply an opt-in box to the registration form on your site. If you select this option, any subscribers that registered on your site and select the opt-in box will be sent to this subscriber list in Campaign Monitor.</span>")
			});
			$('#r-registration-text span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This is the copy that will display next to the opt-in box on your registration forms, if you choose to enable that option.</span>")
			});

		});
	})(jQuery);
</script>