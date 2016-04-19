<?php
$button = new CampaignMonitorButton();
$button->load( $_GET['e'] );
?>
<div class="wrap">
	<h2><?php _e('Edit Button', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-element' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Form', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="fields[form_id]" id="form_id">
						<?php $forms = new CampaignMonitorForm();
						foreach( $forms->get_all() as $form ): ?>
							<option value="<?php echo $form->id; ?>" <?php if( $form->id == $button->data['form_id'] ): ?>selected="selected"<?php endif; ?>><?php echo $form->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Button Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" value="<?php echo $button->name; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Button Text', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="fields[text]" id="title" value="<?php echo $button->data['text'];?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Additional CSS', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="fields[additional_css]" id="title" value="<?php echo $button->data['additional_css'];?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					<input type="submit" value="<?php _e('Save Button', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">
				</td>
			</tr>
		</table>
	</form>
</div>
