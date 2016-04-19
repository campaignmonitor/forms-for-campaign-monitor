<?php $element = new CampaignMonitorElement(); ?>
<div class="wrap">
	<h2><?php _e('Add A/B Test', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-add-abtest' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('A/B Test Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('First Form', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="first_element" id="type">
						<?php
						foreach( $element->get_all() as $e ):
							if ($e->enabled == 2) continue;?>
							<option value="<?php echo $e->id; ?>"><?php echo stripslashes($e->name); ?> (<?php if ($e->type != "slider"){echo CampaignMonitorPluginInstance()->humanize($e->type);}else{ echo "Slide-Out"; } ?>)</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Second Form', 'campaign-monitor'); ?>
				</th>
				<td>
					<select name="second_element" id="type">
						<?php
						foreach( $element->get_all() as $e ):
							if ($e->enabled == 2) continue;?>
							<option value="<?php echo $e->id; ?>"><?php echo stripslashes($e->name); ?> (<?php if ($e->type != "slider"){echo CampaignMonitorPluginInstance()->humanize($e->type);}else{ echo "Slide-Out"; } ?>)</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="<?php _e('Add A/B testing', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish"></td>
			</tr>
		</table>
	</form>
</div>