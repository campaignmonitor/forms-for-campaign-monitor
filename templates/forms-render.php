<?php // $this == CampaignMonitorForm
CampaignMonitorPluginInstance()->connection->enabled();
$fields = CampaignMonitorPluginInstance()->connection->get_list_fields($this->data['list_id'])->response;
$addEmptyLabel = false;
if ( 1 == $this->get_field_data( 'userInformation', 'enabled' ) || 1 == $this->get_field_data( 'email', 'label' ) ) {
  $addEmptyLabel = true;
}
if ( !$addEmptyLabel ) {
  foreach ($fields as $field) {
	if (1 == $this->get_field_data($this->clean_key($field->Key), 'enabled')) {
	  $key  = $this->clean_key($field->Key);
	  $name = str_replace("\\", "", $field->FieldName);
	  if ( 1 == $this->get_field_data( $key, 'label' ) ) {
		$addEmptyLabel = true;
	  }
	}
  }
}

//CM Badge Display variables
$current_options = get_option('campaign_monitor_settings');
if (isset($this->data['hasBadge']) && $this->data['hasBadge'] == 1){$hasBadge = "yes";}else{$hasBadge = "no";}

?>
<form id="cm-form-<?php echo $this->id ?>-<?php echo $this->element_id; ?>">


<?php if ( !empty( $this->data['form_title']) || !empty( $this->data['form_summary'] ) ):?>

    <div class="cm-field-row pre-info">

        <?php if ( !empty( $this->data['form_title'] ) ):?>

            <h3>
    			<?php echo stripslashes( $this->data['form_title'] ); ?>
    		</h3>

        <?php endif; ?>

        <?php if ( !empty( $this->data['form_summary'] ) ):?>

            <p class="summary">
                <?php echo stripslashes( $this->data['form_summary'] ); ?>
            </p>

        <?php endif; ?>

    </div>

    <?php endif; ?>

    <span class="cm-form-error">

    </span>

	<?php if ( 1 == $this->get_field_data( 'userInformation', 'enabled' ) ): ?>
		<?php // if ( "full" == $this->get_field_data( 'userInformation', 'show_as' ) ): ?>
			<div class="cm-field-row">
				<?php if ( 1 == $this->get_field_data( 'userInformation', 'label' )):?>
					<label for="cc-form-<?php echo $this->id ?>-userInformation" class="cm-label"><?php echo __('Full Name', 'campaign-monitor'); ?></label>
				<?php elseif ( $addEmptyLabel ): ?>
				  	<label for="cc-form-<?php echo $this->id ?>-userInformation" class="cm-label">&nbsp;</label>
				<?php endif; ?>
				<input type="text" name="fullName" required="required" id="cc-form-<?php echo $this->id ?>-userInformation" class="cm-userInformation <?php echo $this->get_field_data( 'userInformation', 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( 'userInformation', 'placeholder' )); ?>">
			</div>
		<?php /* else: ?>
			<div class="cm-field-row">
				<?php if ( 1 == $this->get_field_data( 'userInformation', 'label' ) ):?>
					<label for="cm-form-<?php echo $this->id ?>-userInformation-first-name" class="cm-label"><?php echo __('First Name', 'campaign-monitor'); ?></label>
				  <?php elseif ( $addEmptyLabel ): ?>
				  <label for="cc-form-<?php echo $this->id ?>-userInformation-first-name" class="cm-label">&nbsp;</label>
				<?php endif; ?>
				<input type="text" name="firstName" required="required" id="cm-form-<?php echo $this->id ?>-userInformation-first-name" class="cm-userInformation <?php echo $this->get_field_data( 'userInformation', 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( 'userInformation', 'placeholder' )); ?>">
			</div>
			<div class="cm-field-row">
				<?php if ( 1 == $this->get_field_data( 'userInformation', 'label' ) ):?>
					<label for="cm-form-<?php echo $this->id ?>-userInformation-last-name" class="cm-label"><?php echo __('Last Name', 'campaign-monitor'); ?></label>
				  <?php elseif ( $addEmptyLabel ): ?>
				  <label for="cc-form-<?php echo $this->id ?>-userInformation-last-name" class="cm-label">&nbsp;</label>
				<?php endif; ?>
				<input type="text" name="lastName" required="required" id="cm-form-<?php echo $this->id ?>-userInformation-last-name" class="cm-userInformation <?php echo $this->get_field_data( 'userInformation', 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( 'userInformation', 'placeholder' )); ?>">
			</div>
		<?php //endif; */ ?>
	<?php endif; ?>
	<div class="cm-field-row">
		<?php if ( 1 == $this->get_field_data( 'email', 'label' ) ):?>
			<label for="cm-form-<?php echo $this->id ?>-email" class="cm-label"><?php echo __('Email', 'campaign-monitor'); ?></label>
		<?php elseif ( $addEmptyLabel ): ?>
		  <label for="cc-form-<?php echo $this->id ?>-email" class="cm-label">&nbsp;</label>
		<?php endif; ?>
		<input type="email" name="email" required="required" class="cm-email <?php echo $this->get_field_data( 'email', 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( 'email', 'placeholder' )); ?>">
	</div>
	<?php foreach( $fields as $field ):
		if( 1 == $this->get_field_data( $this->clean_key( $field->Key ) ,'enabled' ) ) :
			$key = $this->clean_key( $field->Key );
			$name = str_replace("\\","", $field->FieldName);
	?>
			<div class="cm-field-row">
				<?php if ( 1 == $this->get_field_data( $key, 'label' ) ):?>
					<label for="cm-form-<?php echo $this->id ?>-<?php echo $key ?>" class="cm-label"><?php echo $name ;?></label>
				<?php elseif ( $addEmptyLabel ): ?>
				  <label for="cc-form-<?php echo $this->id ?>-<?php echo $key ?>" class="cm-label">&nbsp;</label>
				<?php endif; ?>
				<?php if ( in_array( $field->DataType, array('MultiSelectOne')) ) : ?>
					<div class="cm-select-wrapper">
						<select data-name="<?php echo $name ;?>" name="<?php echo $key; ?>" class="<?php echo $this->get_field_data( $this->clean_key( $field->Key ), 'css_classes' ); ?>" <?php if ( 'MultiSelectMany' == $field->DataType ): ?>multiple="multiple"<?php endif ?> <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
							<?php foreach( $field->FieldOptions as $option ): ?>
								<option value="<?php echo CampaignMonitorPluginInstance()->clean_option($option); ?>" <?php if( in_array( $option, (array)$this->get_field_data( $key, 'defaults' ) ) ): ?>selected="selected"<?php endif; ?>><?php echo CampaignMonitorPluginInstance()->clean_option($option); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php elseif ( in_array( $field->DataType, array('MultiSelectMany')) ) : ?>

                    <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?><div class="required-checkboxes"><?php endif ?>
                        <?php $index = 0;
                         foreach( $field->FieldOptions as $option ): ?>
                            <input data-name="<?php echo $name ;?>" id="<?php echo $key; ?>_<?php echo $index; ?>" type="checkbox" name="<?php echo $key; ?>[]" value="<?php echo CampaignMonitorPluginInstance()->clean_option($option); ?>" class="<?php echo $this->get_field_data( $this->clean_key( $field->Key ), 'css_classes' ); ?>" <?php if( in_array( $option, (array)$this->get_field_data( $key, 'defaults' ) ) ): ?>checked="checked"<?php endif; ?> ><label for="<?php echo $key; ?>_<?php echo $index; ?>">&nbsp;&nbsp;<?php echo CampaignMonitorPluginInstance()->clean_option($option); ?></label><br />

                        <?php $index++;
                        endforeach; ?>
                    <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?></div><?php endif ?>
				<?php elseif ( in_array( $field->DataType, array('Date')) ) : ?>
					<div class="cm-date-wrapper">
					    <div class="cm-select-wrapper">
					        <select data-name="<?php echo $name ;?>-month" name="<?php echo $key; ?>-month" class="<?php echo $this->get_field_data( $this->clean_key( $field->Key ), 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
					            <option value="" selected="selected"><?php echo __("Month"); ?></option>
					            <?php for( $month = 1; $month <=12 ; $month++ ): ?>
					                <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
					            <?php endfor; ?>
					        </select>
					    </div>
					    <div class="cm-select-wrapper">
					        <select data-name="<?php echo $name ;?>-day" name="<?php echo $key; ?>-day" class="<?php echo $this->get_field_data( $this->clean_key( $field->Key ), 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
					            <option value="" selected="selected"><?php echo __("Day"); ?></option>
					            <?php for( $day = 1; $day <=31 ; $day++ ): ?>
					            <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
					            <?php endfor; ?>
					        </select>
					    </div>
					    <div class="cm-select-wrapper">
					        <select data-name="<?php echo $name ;?>-year" name="<?php echo $key; ?>-year" class="<?php echo $this->get_field_data( $this->clean_key( $field->Key ), 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
					            <option value="" selected="selected"><?php echo __("Year"); ?></option>
					            <?php for( $year = (date("Y") - 100 ); $year <= (date("Y") + 50 ) ; $year++ ): ?>
					            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
					            <?php endfor; ?>
					        </select>
					    </div>
					</div>
				<?php else : ?>
				<input data-name="<?php echo $name ;?>" type="text" name="<?php echo $key ?>" class="cm-<?php echo $key ?> <?php echo $this->get_field_data( $this->clean_key( $field->Key ), 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( $key, 'placeholder' )); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
				<?php endif; ?>
			</div>
	<?php else: continue; endif;
	endforeach; ?>
	<input type="hidden" name="form_id" value="<?php echo $this->id; ?>">
	<input type="hidden" name="element_id" value="<?php echo $this->element_id; ?>">
	<input type="hidden" name="abtest_id" value="<?php echo $this->abtest_id; ?>">
	<input type="hidden" name="action" value="cm_form_submission">
	<div class="cm-field-row cm-button">
		<button type="button" id="cm-form-<?php echo $this->id ?>-<?php echo $this->element_id; ?>-submit" class="cm-submit"><?php echo stripslashes($this->data['submitText']); ?></button>
	</div>
	<div style="clear: both; float:none;"></div>
	<?php if ((isset($current_options['has_badge']) && $current_options['has_badge'] == "yes" && $hasBadge == "yes") || (!isset($current_options['has_badge']) && $hasBadge == "yes") ){ ?>
		<a class="cm-logo-horizontal" href="https://www.campaignmonitor.com/?utm_campaign=widget&utm_source=subscriberforms&utm_medium=referral" rel="nofollow">
			<img src="<?php echo plugins_url( 'img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>"
				 srcset="<?php echo plugins_url( 'img/cm-logo-horizontal.png', dirname(__FILE__) ); ?>,
					 <?php echo plugins_url( 'img/cm-logo-horizontal@2x.png', dirname(__FILE__) ); ?> 2x"
				 alt="Powered by Campaign Monitor">
		</a>
	<?php } ?>
</form>