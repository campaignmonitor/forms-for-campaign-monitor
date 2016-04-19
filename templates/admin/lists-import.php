<div class="wrap">
	<h2><?php _e('Import List From Campaign Monitor', 'campaign-monitor');?></h2>
	<?php if ( CampaignMonitorPluginInstance()->connection->last_error ): ?>
		<div class="error notice is-dismissible below-h2" id="message">
			<p><?php _e( CampaignMonitorPluginInstance()->connection->last_error->Message );?></p>
			<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button>
		</div>
	<?php endif; ?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<?php _e('Client', 'campaign-monitor'); ?>
			</th>
			<td>
				<select name="Clients" id="clients">
					<?php foreach( CampaignMonitorPluginInstance()->connection->get_clients() as $client ): ?>
						<option value="<?php echo $client->ClientID; ?>"><?php echo $client->Name; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
	<?php foreach( CampaignMonitorPluginInstance()->connection->get_clients() as $client ): ?>
	<form id="client<?php echo $client->ClientID; ?>" method="post" name="post" style="display:none;" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-import-list' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('List', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="id">
						<?php foreach( CampaignMonitorPluginInstance()->connection->get_client_lists( $client->ClientID ) as $list ): ?>
						<option value="<?php echo $list->ListID ?>"><?php echo $list->Name ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="clear"></div>
		<input type="submit" value="<?php _e('Import List', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">
	</form>
	<?php endforeach; ?>
</div>
<script type="text/javascript">
	jQuery(function() {
		jQuery("#clients").on('change', function(){
			jQuery(".lists-form").hide();
			jQuery("#client" + jQuery( "#clients option:selected").val() ).show();
		});

		jQuery("#clients").change();
	});
</script>