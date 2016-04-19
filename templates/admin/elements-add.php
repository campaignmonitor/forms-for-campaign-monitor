<div class="wrap">
	<h2><?php _e('Add Element', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-add-element' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Element Type', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="type" id="type">
						<?php
						foreach( CampaignMonitorPluginInstance()->admin->element_types as $type ): ?>
							<option value="<?php echo $type; ?>"><?php echo CampaignMonitorPluginInstance()->humanize( $type ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Element Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Form', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="form_id" id="form_id">
						<?php $forms = new CampaignMonitorForm();
						foreach( $forms->get_all() as $form ): ?>
							<option value="<?php echo $form->id; ?>"><?php echo $form->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="<?php _e('Add Element', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish"></td>
			</tr>
		</table>
	</form>
</div>