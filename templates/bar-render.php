<?php
if ( !$this->preview ) {
	$form = new CampaignMonitorForm();
	$form->load( $this->id);
	$form->element_id = $this->id;
	$form->abtest_id = $this->abtest_id;
	$form->pre_info = $this->data['text'];
} else {
	$form = $this->previewForm;
}

//CM Badge Display variables
$current_options = get_option('campaign_monitor_settings');
if (isset($this->data['hasBadge']) && $this->data['hasBadge'] == 1){$hasBadge = "yes";}else{$hasBadge = "no";}

?>
<div id="cm-bar-form-<?php echo $this->id; ?>" class="cm-bar-<?php echo $this->data['bar_position']?> <?php echo $form->single_line(); ?> <?php if( 'top' == $this->data['bar_position'] && is_admin_bar_showing() ): ?>cm-admin-bar-showing<?php endif; ?>">
	<?php echo $form->render() ?>
	<button title="<?php echo __('Close', 'campaign-monitor'); ?> (Esc)" type="button" class="cm-close">×</button>
</div>
<script type="text/javascript">
	jQuery(function(){
		jQuery( "#cm-bar-form-<?php echo $this->id; ?> .cm-close").on('click', function(){
			jQuery("#cm-bar-form-<?php echo $this->id; ?>").hide();
		});
	});
</script>