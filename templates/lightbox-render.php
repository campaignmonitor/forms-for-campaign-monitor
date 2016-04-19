<?php
if ( !$this->preview ) {
	$form = new CampaignMonitorForm();
	$form->load($this->id);
	//$form->pre_info = $this->data['text'];
	$form->element_id = $this->id;
	$form->abtest_id  = $this->abtest_id;
} else {
	$form = $this->previewForm;
}

//CM Badge Display variables
$current_options = get_option('campaign_monitor_settings');
if (isset($this->data['hasBadge']) && $this->data['hasBadge'] == 1){$hasBadge = "yes";}else{$hasBadge = "no";}

?>
<div id="cm-lightbox-form-<?php echo $this->id; ?>" class="mfp-hide white-popup-block">
	<?php echo $form->render() ?>
</div>
<script>
	jQuery(document).ready(function() {
		var show = true;
		<?php if ( 'scroll' == $this->data['lightbox_delay'] ): ?>
		jQuery(document).on('scroll', function() {
			maxScroll = jQuery(document).height() - jQuery(window).height();
			<?php if ( strpos( $this->data['lightbox_delay_height'], "%" ) !== false ) :?>
			if ( (jQuery(document).scrollTop()/maxScroll) * 100 >= <?php echo intval($this->data['lightbox_delay_height']); ?> ) {
			<?php else: ?>
			if ( jQuery(document).scrollTop() >= <?php echo intval($this->data['lightbox_delay_height']); ?> ) {
			<?php endif; ?>
				if ( show ) {
					show = false;
					jQuery.magnificPopup.open({
						items: {
							type: 'inline',
							src: '#cm-lightbox-form-<?php echo $this->id; ?>'
						}
					});
				}
			}
		});
		<?php endif; ?>
		<?php if( 'interval' == $this->data['lightbox_delay'] ): ?>
		setTimeout( function(){
			jQuery.magnificPopup.open({
				items: {
					type: 'inline',
					src: '#cm-lightbox-form-<?php echo $this->id; ?>'
				}
			});
		}, <?php echo $this->data['lightbox_delay_seconds'] * 1000 ?>);
		<?php endif;?>
		<?php if ( 'immediately' == $this->data['lightbox_delay'] ): ?>
		jQuery.magnificPopup.open({
			items: {
				type: 'inline',
				src: '#cm-lightbox-form-<?php echo $this->id; ?>'
			}
		});
		<?php endif; ?>
	});
</script>