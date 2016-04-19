<?php
if ( !$this->preview ){
	$form = new CampaignMonitorForm();
	$form->load( $this->id);
	$form->element_id = $this->id;
	$form->abtest_id = $this->abtest_id;
} else {
	$form = $this->previewForm;
}
?>
<div id="cm-slider-<?php echo $this->id; ?>" class="cm-slider cm-slider-<?php echo $this->data['slider_position']?> <?php if( 'bottom' != $this->data['slider_position'] && is_admin_bar_showing() ): ?>cm-admin-bar-showing<?php endif; ?>">
<?php if ( ! in_array( $this->data['slider_position'], array('top') ) ): ?>
	<button class="cm-slider-button cm-slider-button-<?php echo $this->data['slider_position']; ?>" id="cm-slider-tab-<?php echo $this->id; ?>"><?php echo stripslashes($this->data['form_title']); ?></button>
<?php endif; ?>
	<div id="cm-slider-form-<?php echo $this->id; ?>" class="cm-slider-form">
	<?php echo $form->render() ?>
		<button title="<?php echo __('Close', 'campaign-monitor'); ?> (Esc)" type="button" class="cm-close">×</button>
	</div>
<?php if ( in_array( $this->data['slider_position'], array('top') ) ): ?>
	<button class="cm-slider-button cm-slider-button-<?php echo $this->data['slider_position']; ?>" id="cm-slider-tab-<?php echo $this->id; ?>"><?php echo stripslashes($this->data['form_title']); ?></button>
<?php endif; ?>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery('#cm-slider-tab-<?php echo $this->id; ?>').on('click', function(){
			jQuery(this).hide();
            jQuery('#cm-slider-form-<?php echo $this->id; ?>').addClass( "is-active" );
		});
		jQuery('#cm-slider-form-<?php echo $this->id; ?> .cm-close').on('click', function(){
            jQuery('#cm-slider-form-<?php echo $this->id; ?>').removeClass( "is-active" );
			jQuery('#cm-slider-tab-<?php echo $this->id; ?>').show();
		});

	});
</script>