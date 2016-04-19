<?php /*
		*         'Title' => string The list title
		*         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
		*         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
		*         'ConfirmationSuccessPage' => string The page to redirect subscribers to when
		*             they confirm their subscription
		*         'UnsubscribeSetting' => string Unsubscribe setting must be
		*             CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS or
		*             CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST.
		*             See the documentation for details: http://www.campaignmonitor.com/api/lists/#updating_a_list
		*         'AddUnsubscribesToSuppList' => boolean When UnsubscribeSetting
		*             is CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS,
		*             whether unsubscribes from this list should be added to the
		*             suppression list.
		*         'ScrubActiveWithSuppList' => boolean When UnsubscribeSetting
		*             is CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS,
		*             whether active subscribers should be scrubbed against the
		*             suppression list.
		*
		*
		*/
$defaults = array(
	'client' => '',
	'title' => '',
	'optin' => 0,
	'ConfirmationSuccessPage' => '',
	'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST,
);

if ( isset( $_POST ) ) {
	$defaults = array_merge( $defaults, $_POST );
}

?>
<div class="wrap">
	<h2><?php _e('Add List', 'campaign-monitor');?></h2>
	<?php if ( CampaignMonitorPluginInstance()->connection->last_error ): ?>
		<div class="error notice is-dismissible below-h2" id="message">
			<p><?php _e( CampaignMonitorPluginInstance()->connection->last_error->Message );?></p>
			<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button>
		</div>
	<?php endif; ?>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-add-list' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Client', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="client" id="client">
						<?php foreach( CampaignMonitorPluginInstance()->connection->get_clients() as $client ): ?>
							<option value="<?php echo $client->ClientID; ?>" <?php if ( $client->ClientID == $defaults['client'] ) :?>selected<?php endif; ?>><?php echo $client->Name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
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
				<td colspan="2"><input type="submit" value="<?php _e('Add List', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish"></td>
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
		});
	})(jQuery);
</script>