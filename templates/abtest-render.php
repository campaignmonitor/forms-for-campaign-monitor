<?php
	$elements = array(
		'first_element' => new CampaignMonitorElement(),
		'second_element' => new CampaignMonitorElement()
	);

	$non_rendered = ( 'first_element' == $this->render )? 'second_element' : 'first_element'
	;
	foreach ( $elements as $k=>$e) {
		$e->load( $this->data[ $k ] );
	}

	//forms or buttons are already rendered!!!
	switch( $elements[ $this->render ]->type ){
		case 'lightbox':
			$l = new CampaignMonitorLightbox();
			$l->load( $elements[ $this->render ]->id );
			$l->abtest_id = $this->id;
			$l->render();
			break;
		case 'bar':
			$b = new CampaignMonitorBar();
			$b->load( $elements[ $this->render ]->id );
			$b->abtest_id = $this->id;
			echo $b->render();
			break;
		case 'slider':
			$s = new CampaignMonitorSlider();
			$s->load( $elements[ $this->render ]->id );
			$s->abtest_id = $this->id;
			echo $s->render();
			break;
	}

	//we should hide only if second element is button or form
	switch ( $elements[$non_rendered]->type ) {
		case 'button':
		case 'simple_form':
			?>
<script>
	jQuery(document).ready(function() {
		jQuery('#cm-<?php echo $elements[$non_rendered]->type; ?>-<?php echo $elements[$non_rendered]->id; ?>').hide();
	});
</script>
	<?php break;
	}