<?php
$list = CampaignMonitorPluginInstance()->connection->get_list($_GET['list'])->response;
$fields = CampaignMonitorPluginInstance()->connection->get_list_fields($_GET['list'])->response;
?>
<div class="wrap">
	<h2 id="info-header"><span><?php printf( __('Edit Custom Fields for %s', 'campaign-monitor'), $list->Title );?></span></h2>
	<?php if ( CampaignMonitorPluginInstance()->connection->last_error ): ?>
		<div class="error notice is-dismissible below-h2" id="message">
			<p><?php _e( CampaignMonitorPluginInstance()->connection->last_error->Message );?></p>
			<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button>
		</div>
	<?php endif; ?>
	<button type="button" id="add-field"><?php _e( 'Add New Field', 'campaign-monitor' ); ?></button>
	<form id="table-fields" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-list-fields' ); ?>
	<div id="tabs">
		<ul>
			<?php foreach( $fields as $field ): ?>
			<li><a href="#tabs-<?php echo str_replace( ["[","]","\\'", "'", "\\"], ["","","","",""], $field->Key );?>"><?php echo str_replace( "\\", "", $field->FieldName); ?></a> <span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span></li>
			<?php endforeach; ?>
		</ul>
	<?php foreach( $fields as $field ): ?>
	<div id="tabs-<?php echo str_replace( ["[","]","\\'", "'", "\\"], ["","","","",""], $field->Key );?>">
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Field Name (max. 30 chars)', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="fields<?php echo $field->Key ?>[FieldName]" id="title" value="<?php echo str_replace( "\\", "", $field->FieldName); ?>" maxlength="30" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Data Type', 'campaign-monitor'); ?>
				</th>
				<td>
					<?php echo $field->DataType; ?>
				</td>
			</tr>
			<?php if ( in_array( $field->DataType, ['MultiSelectOne', 'MultiSelectMany'] ) ): ?>
			<tr>
				<th scope="row">
					<?php _e('Enter Field Options<br>(One per line)', 'campaign-monitor'); ?>
				</th>
				<td>
					<textarea name="fields<?php echo $field->Key ?>[Options]" cols="30" rows="10"><?php foreach( $field->FieldOptions as $option):?><?php
						echo CampaignMonitorPluginInstance()->clean_option($option) . "\n";
						?><?php endforeach; ?></textarea>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		</div>
		<?php endforeach; ?>
		<div class="clear"></div>
	</div>
		<input type="submit" value="<?php _e('Save Changes', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">
	</form>
<div class="clear"></div>
</div>
<style type="text/css">
	#tabs li .ui-icon-close {
		cursor: pointer;
		float: left;
		margin: 0.4em 0.2em 0 0;
	}
	#wpfooter {
		position: relative;
	}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
(function( $ ) {
	$(function() {
		var tabCounter = 1,
			tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>",
			tabs = $("#tabs").tabs();
		// close icon: removing the tab on click
		tabs.delegate( "span.ui-icon-close", "click", function() {
			var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
			$( "#" + panelId ).remove();
			$("#table-fields").append("<input type='hidden' name=toDelete[] value='[" + panelId.substr(5) + "]'>");
			tabs.tabs( "refresh" );
		});

		$("#add-field").on("click", function(){
			addTab();
		});

		function addTab() {
			var label = "New Field " + tabCounter,
				id = "tabs-" + tabCounter,
				li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) ),
				tabContentHtml = "";

			tabContentHtml = '<table class="form-table">' +
				'<tr>'+
				'<th scope="row">' +
				'<?php _e('Field Name (max. 30 chars)', 'campaign-monitor'); ?>' +
				'</th>' +
				'<td>' +
				'<input type="text" name="fields[' + tabCounter + '][FieldName]" id="title" value="" maxlength="30" />' +
				'</td>' +
				'</tr>' +
				'<tr>' +
				'<th scope="row">' +
				'<?php _e('Data Type', 'campaign-monitor'); ?>' +
				'</th>' +
				'<td>' +
				'<select name="fields[' + tabCounter + '][DataType]" id="type-' + tabCounter +'" class="field-type">' +
					<?php foreach( ['Text', 'Number', 'MultiSelectOne', 'MultiSelectMany', 'Date', 'Country' , 'USState'] as $fld ) :?>
					'<option value="<?php echo $fld; ?>"><?php echo $fld;?></option>' +
					<?php endforeach; ?>
				'</td>' +
				'</tr>' +
				'<tr id="row-' + tabCounter + '" style="display:none">' +
				'<th scope="row" >' +
				'<?php _e('Enter Field Options<br>(One per line)', 'campaign-monitor'); ?>' +
				'</th>' +
				'<td>' +
				'<textarea name="fields[' + tabCounter + '][Options]" cols="30" rows="10"></textarea>' +
				'</td>' +
				'</tr>' +
				'</table>';

			tabs.find( ".ui-tabs-nav" ).append( li );
			tabs.append( "<div id='" + id + "'><p>" + tabContentHtml + "</p></div>" );
			tabs.tabs( "refresh" );
			tabs.delegate( '.field-type', 'change', function(){
				if ( this.value == 'MultiSelectOne' || this.value == 'MultiSelectMany') {
					$('#row-' + this.id.substr(5) ).show();
				} else {
					$('#row-' + this.id.substr(5) ).hide();
				}
			});
			tabCounter++;
		}
		<?php if ( empty( $fields ) ): ?>
		addTab();
		$('#ui-id-1').click();
		<?php endif;?>
	});
})(jQuery);
</script>

<script type="text/javascript" src="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="<?php echo CAMPAIGN_MONITOR_PLUGIN_URL?>css/tooltipster.css" type="text/css" media="all">

<script type="text/javascript">
	(function( $ ) {
		$(function() {
			$('#info-header span').tooltipster({
				iconDesktop: true,
				maxWidth:500,
				position:'bottom',
				interactive:true,
				content: $("<span>Custom fields are used to send information from your forms over to Campaign Monitor. To find out more, check out our <a href='http://help.campaignmonitor.com/topic.aspx?t=154'>documentation</a>.</span>")
			});
		});
	})(jQuery);
</script>