<?php // $this == CampaignMonitorForm
$fields = $this->data['fields'];

//CM Badge Display variables
$current_options = get_option('campaign_monitor_settings');
if (isset($this->data['hasBadge']) && $this->data['hasBadge'] == 1){$hasBadge = "yes";}else{$hasBadge = "no";}

$addEmptyLabel = false;
if ( 1 == $this->get_field_data( 'userInformation', 'enabled' ) || 1 == $this->get_field_data( 'email', 'label' ) ) {
	$addEmptyLabel = true;
}
if ( !$addEmptyLabel ) {
	foreach ($fields as $key => $field) {
		if ( 1 == $this->get_field_data( $key, 'label' ) ) {
			$addEmptyLabel = true;
		}
	}
}
$countries ="Afghanistan\r\nAlbania\r\nAlgeria\r\nAmerican Samoa\r\nAndorra\r\nAngola\r\nAnguilla\r\nAntigua & Barbuda\r\nArgentina\r\nArmenia\r\nAruba\r\nAustralia\r\nAustria\r\nAzerbaijan\r\nAzores\r\nBahamas\r\nBahrain\r\nBangladesh\r\nBarbados\r\nBelarus\r\nBelgium\r\nBelize\r\nBenin\r\nBermuda\r\nBhutan\r\nBolivia\r\nBonaire\r\nBosnia & Herzegovina\r\nBotswana\r\nBrazil\r\nBritish Indian Ocean Ter\r\nBrunei\r\nBulgaria\r\nBurkina Faso\r\nBurundi\r\nCambodia\r\nCameroon\r\nCanada\r\nCanary Islands\r\nCape Verde\r\nCayman Islands\r\nCentral African Republic\r\nChad\r\nChannel Islands\r\nChile\r\nChina\r\nChristmas Island\r\nCocos Island\r\nColombia\r\nComoros\r\nCongo\r\nCongo Democratic Rep\r\nCook Islands\r\nCosta Rica\r\nCote D'Ivoire\r\nCroatia\r\nCuba\r\nCuracao\r\nCyprus\r\nCzech Republic\r\nDenmark\r\nDjibouti\r\nDominica\r\nDominican Republic\r\nEast Timor\r\nEcuador\r\nEgypt\r\nEl Salvador\r\nEquatorial Guinea\r\nEritrea\r\nEstonia\r\nEthiopia\r\nFalkland Islands\r\nFaroe Islands\r\nFiji\r\nFinland\r\nFrance\r\nFrench Guiana\r\nFrench Polynesia\r\nFrench Southern Ter\r\nGabon\r\nGambia\r\nGeorgia\r\nGermany\r\nGhana\r\nGibraltar\r\nGreat Britain\r\nGreece\r\nGreenland\r\nGrenada\r\nGuadeloupe\r\nGuam\r\nGuatemala\r\nGuernsey\r\nGuinea\r\nGuinea-Bissau\r\nGuyana\r\nHaiti\r\nHonduras\r\nHong Kong\r\nHungary\r\nIceland\r\nIndia\r\nIndonesia\r\nIran\r\nIraq\r\nIreland\r\nIsle of Man\r\nIsrael\r\nItaly\r\nJamaica\r\nJapan\r\nJersey\r\nJordan\r\nKazakhstan\r\nKenya\r\nKiribati\r\nKorea North\r\nKorea South\r\nKuwait\r\nKyrgyzstan\r\nLaos\r\nLatvia\r\nLebanon\r\nLesotho\r\nLiberia\r\nLibya\r\nLiechtenstein\r\nLithuania\r\nLuxembourg\r\nMacau\r\nMacedonia\r\nMadagascar\r\nMalawi\r\nMalaysia\r\nMaldives\r\nMali\r\nMalta\r\nMarshall Islands\r\nMartinique\r\nMauritania\r\nMauritius\r\nMayotte\r\nMexico\r\nMidway Islands\r\nMoldova\r\nMonaco\r\nMongolia\r\nMontenegro\r\nMontserrat\r\nMorocco\r\nMozambique\r\nMyanmar\r\nNamibia\r\nNauru\r\nNepal\r\nNetherland Antilles\r\nNetherlands\r\nNevis\r\nNew Caledonia\r\nNew Zealand\r\nNicaragua\r\nNiger\r\nNigeria\r\nNiue\r\nNorfolk Island\r\nNorway\r\nOman\r\nPakistan\r\nPalau Island\r\nPalestine\r\nPanama\r\nPapua New Guinea\r\nParaguay\r\nPeru\r\nPhilippines\r\nPitcairn Island\r\nPoland\r\nPortugal\r\nPuerto Rico\r\nQatar\r\nReunion\r\nRomania\r\nRussia\r\nRwanda\r\nSaipan\r\nSamoa\r\nSamoa American\r\nSan Marino\r\nSao Tome & Principe\r\nSaudi Arabia\r\nSenegal\r\nSerbia\r\nSerbia & Montenegro\r\nSeychelles\r\nSierra Leone\r\nSingapore\r\nSlovakia\r\nSlovenia\r\nSolomon Islands\r\nSomalia\r\nSouth Africa\r\nSouth Sudan\r\nSpain\r\nSri Lanka\r\nSt Barthelemy\r\nSt Eustatius\r\nSt Helena\r\nSt Kitts-Nevis\r\nSt Lucia\r\nSt Maarten\r\nSt Pierre & Miquelon\r\nSt Vincent & Grenadines\r\nSudan\r\nSuriname\r\nSwaziland\r\nSweden\r\nSwitzerland\r\nSyria\r\nTahiti\r\nTaiwan\r\nTajikistan\r\nTanzania\r\nThailand\r\nTogo\r\nTokelau\r\nTonga\r\nTrinidad & Tobago\r\nTunisia\r\nTurkey\r\nTurkmenistan\r\nTurks & Caicos Is\r\nTuvalu\r\nUganda\r\nUkraine\r\nUnited Arab Emirates\r\nUnited Kingdom\r\nUnited States of America\r\nUruguay\r\nUzbekistan\r\nVanuatu\r\nVatican City State\r\nVenezuela\r\nVietnam\r\nVirgin Islands (Brit)\r\nVirgin Islands (USA)\r\nWake Island\r\nWallis & Futana Is\r\nYemen\r\nZambia\r\nZimbabwe";
$states = "Alabama\r\nAlaska\r\nArizona\r\nArkansas\r\nCalifornia\r\nColorado\r\nConnecticut\r\nDelaware\r\nDistrict of Columbia\r\nFlorida\r\nGeorgia\r\nHawaii\r\nIdaho\r\nIllinois\r\nIndiana\r\nIowa\r\nKansas\r\nKentucky\r\nLouisiana\r\nMaine\r\nMaryland\r\nMassachusetts\r\nMichigan\r\nMinnesota\r\nMississippi\r\nMissouri\r\nMontana\r\nNebraska\r\nNevada\r\nNew Hampshire\r\nNew Jersey\r\nNew Mexico\r\nNew York\r\nNorth Carolina\r\nNorth Dakota\r\nOhio\r\nOklahoma\r\nOregon\r\nPennsylvania\r\nRhode Island\r\nSouth Carolina\r\nSouth Dakota\r\nTennessee\r\nTexas\r\nUtah\r\nVermont\r\nVirginia\r\nWashington\r\nWest Virginia\r\nWisconsin\r\nWyoming";
?>
<form id="cm-form-<?php echo $this->id ?>-<?php echo $this->element_id; ?>">
	<?php if ( !empty( $this->data['form_title'] ) ):?>
		<h3 class="cm-field-row pre-info">
			<?php echo stripslashes( $this->data['form_title'] ); ?>
		</h3>
	<?php endif; ?>
	<div class="cm-form-error">

	</div>
	<?php if ( !empty( $this->data['form_summary'] ) ):?>
		<p class="cm-field-row summary">
			<?php echo stripslashes( $this->data['form_summary'] ); ?>
		</p>
	<?php endif; ?>
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
	<?php endif; ?>
	<div class="cm-field-row">
		<?php if ( 1 == $this->get_field_data( 'email', 'label' ) ):?>
			<label for="cm-form-<?php echo $this->id ?>-email" class="cm-label"><?php echo __('Email', 'campaign-monitor'); ?></label>
		<?php elseif ( $addEmptyLabel ): ?>
			<label for="cc-form-<?php echo $this->id ?>-email" class="cm-label">&nbsp;</label>
		<?php endif; ?>
		<input type="email" name="email" required="required" class="cm-email <?php echo $this->get_field_data( 'email', 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( 'email', 'placeholder' )); ?>">
	</div>
	<?php foreach( $fields as $key => $field ):
			if ( in_array( $key, ['email', 'userInformation'] )  || empty( $field['FieldName'] ) || !isset($field['enabled']) || 1 != $field['enabled'] ) {
				continue;
			}
			$name = str_replace("\\","", $field['FieldName']);
			if ($field['DataType'] == "Country") {
				$field['DataType'] = 'MultiSelectOne';
				$field['Options'] = $countries;
			}
			if ($field['DataType'] == "USState") {
				$field['DataType'] = 'MultiSelectOne';
				$field['Options'] = $states;
			}
			?>
			<div class="cm-field-row">
				<?php if ( 1 == $this->get_field_data( $key, 'label' ) ):?>
					<label for="cm-form-<?php echo $this->id ?>-<?php echo $key ?>" class="cm-label"><?php echo $name ;?></label>
				<?php elseif ( $addEmptyLabel ): ?>
					<label for="cc-form-<?php echo $this->id ?>-<?php echo $key ?>" class="cm-label">&nbsp;</label>
				<?php endif; ?>
				<?php if ( in_array( $field['DataType'], ['MultiSelectOne']) ) : ?>
					<div class="cm-select-wrapper">
						<select data-name="<?php echo $name ;?>" name="<?php echo $key; ?>" class="<?php echo $this->get_field_data( $key, 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
							<?php foreach(  explode( "\r\n", $field['Options'] ) as $option ):
								if ( ! empty($option) ): ?>
								<option value="<?php echo CampaignMonitorPluginInstance()->clean_option($option); ?>" <?php if( in_array( $option, (array)$this->get_field_data( $key, 'defaults' ) ) ): ?>selected="selected"<?php endif; ?>><?php echo CampaignMonitorPluginInstance()->clean_option($option); ?></option>
							<?php endif; endforeach; ?>
						</select>
					</div>
				<?php elseif ( in_array( $field['DataType'], ['MultiSelectMany']) ) : ?>

					<?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?><div class="required-checkboxes"><?php endif ?>
					<?php $index = 0;
					foreach(  explode( "\r\n", $field['Options'] ) as $option ):
								if ( ! empty($option) ): ?>
						<input data-name="<?php echo $name ;?>" id="<?php echo $key; ?>_<?php echo $index; ?>" type="checkbox" name="<?php echo $key; ?>[]" value="<?php echo $option; ?>" class="<?php echo $this->get_field_data( $key, 'css_classes' ); ?>" <?php if( in_array( $option, (array)$this->get_field_data( $key, 'defaults' ) ) ): ?>checked="checked"<?php endif; ?> ><label for="<?php echo $key; ?>_<?php echo $index; ?>">&nbsp;&nbsp;<?php echo $option; ?></label><br />

						<?php $index++;
					endif; endforeach; ?>
					<?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?></div><?php endif ?>
				<?php elseif ( in_array( $field['DataType'], ['Date']) ) : ?>
					<div class="cm-date-wrapper">
						<div class="cm-select-wrapper">
							<select data-name="<?php echo $name ;?>-month" name="<?php echo $key; ?>-month" class="<?php echo $this->get_field_data( $key, 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
								<option value="" selected="selected"><?php echo __("Month"); ?></option>
								<?php for( $month = 1; $month <=12 ; $month++ ): ?>
									<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
								<?php endfor; ?>
							</select>
						</div>
						<div class="cm-select-wrapper">
							<select data-name="<?php echo $name ;?>-day" name="<?php echo $key; ?>-day" class="<?php echo $this->get_field_data( $key, 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
								<option value="" selected="selected"><?php echo __("Day"); ?></option>
								<?php for( $day = 1; $day <=31 ; $day++ ): ?>
									<option value="<?php echo $day; ?>"><?php echo $day; ?></option>
								<?php endfor; ?>
							</select>
						</div>
						<div class="cm-select-wrapper">
							<select data-name="<?php echo $name ;?>-year" name="<?php echo $key; ?>-year" class="<?php echo $this->get_field_data( $key, 'css_classes' ); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
								<option value="" selected="selected"><?php echo __("Year"); ?></option>
								<?php for( $year = (date("Y") - 100 ); $year <= (date("Y") + 50 ) ; $year++ ): ?>
									<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
								<?php endfor; ?>
							</select>
						</div>
					</div>
				<?php else : ?>
					<input data-name="<?php echo $name ;?>" type="text" name="<?php echo $key ?>" class="cm-<?php echo $key ?> <?php echo $this->get_field_data( $key, 'css_classes' ); ?>" placeholder="<?php echo stripslashes($this->get_field_data( $key, 'placeholder' )); ?>" <?php if ( '1' == $this->get_field_data( $key, 'required' ) ): ?>required="required"<?php endif ?>>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	<input type="hidden" name="form_id" value="<?php echo $this->id; ?>">
	<input type="hidden" name="element_id" value="<?php echo $this->element_id; ?>">
	<input type="hidden" name="abtest_id" value="<?php echo $this->abtest_id; ?>">
	<input type="hidden" name="action" value="cm_form_submission">
	<div class="cm-field-row cm-button">
		<button type="button" id="cm-form-<?php echo $this->id ?>-<?php echo $this->element_id; ?>-submit" class="cm-submit" disabled="disabled"><?php echo stripslashes($this->data['submitText']); ?></button>
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