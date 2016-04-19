<?php
$form = new CampaignMonitorForm();
$form->load( $_GET['form'] );
$list = CampaignMonitorPluginInstance()->connection->get_list( $form->data['list_id'] )->response;
$fields = CampaignMonitorPluginInstance()->connection->get_list_fields( $list->ListID )->response;
?>
<div class="wrap">
	<h2><?php _e('Add Form', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-form' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Campaign Monitor List', 'campaign-monitor'); ?>
				</th>
				<td>
					<?php echo $list->Title; ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Form Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" value="<?php echo $form->name;?>" />
				</td>
			</tr>
			<tr>
				<th scope="row" id="r-title">
					<span><?php _e('Submit Button Copy', 'campaign-monitor'); ?></span>
				</th>
				<td>
					<input type="text" name="submitText" id="title" value="<?php echo $form->data['submitText'];?>" />
				</td>
			</tr>
		</table>

		<h2><?php _e('Fields', 'campaign-monitor'); ?></h2>
		<div id="tabs">
			<ul>
				<li><a href="#tabs-email">Email</a></li>
				<li><a href="#tabs-userInformation">Name</a></li>
				<?php foreach( $fields as $field ): ?>
					<li><a href="#tabs-<?php echo $form->clean_key( $field->Key, true ); ?>"><?php echo str_replace( "\\", "", $field->FieldName); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div id="tabs-email">
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php _e('Show Label', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="checkbox" name="fields[email][label]" value="1" <?php if ( 1 == $form->get_field_data( 'email', 'label' ) ): ?>checked="checked"<?php endif; ?> >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('CSS Additional Classes', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="text" name="fields[email][css_classes]" id="title" value="<?php echo $form->get_field_data( 'email', 'css_classes'); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Placeholder', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="text" name="fields[email][placeholder]" id="placeholder"  value="<?php echo $form->get_field_data( 'email', 'placeholder'); ?>">
						</td>
					</tr>
				</table>
			</div>
			<div id="tabs-userInformation">
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php _e('Enable this field', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="checkbox" name="fields[userInformation][enabled]" value="1" <?php if ( 1 == $form->get_field_data( 'userInformation', 'enabled' ) ): ?>checked="checked"<?php endif; ?>>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Show Label', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="checkbox" name="fields[userInformation][label]" value="1" <?php if ( 1 == $form->get_field_data( 'userInformation', 'label' ) ): ?>checked="checked"<?php endif; ?> >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('CSS Additional Classes', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="text" name="fields[userInformation][css_classes]" id="title"  value="<?php echo $form->get_field_data( 'userInformation', 'css_classes'); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Show User Information As', 'campaign-monitor'); ?>
						</th>
						<td>
							<select name="fields[userInformation][show_as]" id="show_as">
									<option value="full" <?php if ( "full" == $form->get_field_data( 'userInformation', 'show_as' ) ): ?>selected="selected"<?php endif; ?> >Full Name</option>
									<option value="split" <?php if ( "split" == $form->get_field_data( 'userInformation', 'show_As' ) ): ?>selected="selected"<?php endif; ?> >First Name & Last Name</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<?php foreach( $fields as $field ):
				$key = str_replace( [ "\\'"], [""], $field->Key);
				?>
			<div id="tabs-<?php echo $form->clean_key( $field->Key, true ); ?>">
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php _e('Enable this field', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="checkbox" name="fields<?php echo $key;?>[enabled]" value="1" <?php if ( 1 == $form->get_field_data( $form->clean_key( $field->Key ), 'enabled' ) ): ?>checked="checked"<?php endif; ?>>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Show Label', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="checkbox" name="fields<?php echo $key;?>[label]" value="1"  <?php if ( 1 == $form->get_field_data( $form->clean_key( $field->Key ), 'label' ) ): ?>checked="checked"<?php endif; ?>>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('CSS Additional Classes', 'campaign-monitor'); ?>
						</th>
						<td>
							<input type="text" name="fields<?php echo $key;?>[css_classes]" id="title" value="<?php echo $form->get_field_data( $form->clean_key( $field->Key ), 'css_classes' ); ?>" />
						</td>
					</tr>
					<?php if ( in_array( $field->DataType, ['MultiSelectMany', 'MultiSelectOne'] ) ): ?>
						<tr>
							<th scope="row">
								<?php _e('Default Selected Value', 'campaign-monitor'); ?>
							</th>
							<td>
								<select name="fields<?php echo $key;?>[defaults][]" id="defaults" <?php if ( 'MultiSelectMany' == $field->DataType ) :?>multiple <?php endif; ?>>
									<?php foreach( $field->FieldOptions as $option ): ?>
										<option value="<?php echo $option; ?>" <?php if ( in_array( $option, (array)$form->get_field_data( $form->clean_key( $field->Key ), 'defaults' ) ) ): ?>selected="selected"<?php endif; ?>><?php echo CampaignMonitorPluginInstance()->clean_option($option);?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php else: ?>
						<tr>
							<th scope="row">
								<?php _e('Placeholder', 'campaign-monitor'); ?>
							</th>
							<td>
								<input type="text" name="fields<?php echo $key;?>[placeholder]" id="placeholder" value="<?php echo $form->get_field_data( $form->clean_key( $field->Key ), 'placeholder' ); ?>">
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
			<?php endforeach; ?>
		</div>
		<input type="submit" value="<?php _e('Save Form', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">

	</form>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
(function( $ ) {
	$(function () {
		$("#tabs").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
		$("#tabs li").removeClass("ui-corner-top").addClass("ui-corner-left");
	});
})(jQuery);
</script>
<style>
	.ui-tabs-vertical { width: 55em; }
	.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; overflow: hidden; }
	.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
	.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
	.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
	.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
	.ui-tabs-anchor { width: 100%; }
</style>

<script type="text/javascript" src="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>css/tooltipster.css" type="text/css" media="all">

<script type="text/javascript">
	(function( $ ) {
		$(function() {
			$('#r-title span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				content: $("<span>This is the text that will be displayed on the submit button for your form.</span>")
			});
		});
	})(jQuery);
</script>