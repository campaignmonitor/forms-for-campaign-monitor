<?php
if ( ! $this->preview ) {
	$form = new CampaignMonitorForm();
	$form->load( $this->id);
} else {
	$form = $this->previewForm;
	echo "The only thing I am allergic to is....";
}
?>
<div id="cm-button-form-<?php echo $this->id; ?>" class="mfp-hide white-popup-block">
	<?php echo $form->render() ?>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery('.cm-button-<?php echo $this->id; ?>').on('click', function(){
			jQuery.magnificPopup.open({
				items: {
					type: 'inline',
					src: '#cm-button-form-<?php echo $this->id; ?>'
				}
			});
		});
	});
</script>