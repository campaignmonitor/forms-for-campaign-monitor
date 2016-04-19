<div class="wrap">
	<h2><?php _e('Add Form', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-add-form' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Campaign Monitor List', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="list_id" id="list_id">
						<?php foreach( CampaignMonitorPluginInstance()->admin->lists->get_registered_lists() as $list ): ?>
							<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Form Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="<?php _e('Add Form', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish"></td>
			</tr>
		</table>
	</form>
</div>