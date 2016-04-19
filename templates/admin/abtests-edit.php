<?php $abtest = new CampaignMonitorABTest();
$abtest->load($_GET['e']);
$e1 = new CampaignMonitorElement();
$e2 = new CampaignMonitorElement();
$e1->load( $abtest->data['first_element'] );
$e2->load( $abtest->data['second_element'] );
?>
<div class="wrap">
	<h2><?php _e('Edit A/B Test', 'campaign-monitor');?></h2>
	<form id="lists-add" method="post" name="post" class="lists-form">
		<?php wp_nonce_field( 'campaign-monitor-edit-abtest' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Enable this A/B Test', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="checkbox" name="enabled" value="1" <?php if ( 1 == $abtest->enabled ): ?>checked="checked"<?php endif; ?>>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('A/B Test Title', 'campaign-monitor'); ?>
				</th>
				<td>
					<input type="text" name="title" id="title" value="<?php echo stripslashes($abtest->name); ?>" />
				</td>
			</tr>
			<?php if ( ( isset( $e1->data['show_in'] ) && ! empty( $e1->data['show_in'] ) ) || ( isset( $e1->data['show_in'] ) && ! empty( $e2->data['show_in'] ) ) ):
				$urls = array();
				if ( isset($e1->data['show_in']) && ! empty( $e1->data['show_in'] )) {
					$urls = $e1->data['show_in'];
				}
				$temp = array();
				if ( ! empty( $urls ) && ( isset( $e1->data['show_in'] ) && ! empty( $e2->data['show_in'] ) ) ) {
					foreach ( $urls as $u ) {
						foreach ( $e2->data['show_in'] as $u2 ) {
							if ( $u == $u2 ) {
								$temp[] = $u;
							}
						}
					}
				} elseif ( empty( $urls ) && ( isset( $e1->data['show_in'] ) && ! empty( $e2->data['show_in'] ) ) ) {
					$temp = $e2->data['show_in'];
			    } else {
					$temp = $urls;
				}
				$urls = $temp;
				?>
			<tr>
				<th scope="row">
					<?php _e('Show only on these pages', 'campaign-monitor'); ?>
				</th>
				<td>
					<?php foreach ( $urls as $u ) : ?>
					<input type="checkbox" name="fields[show_in][]" value="<?php echo $u ?>" <?php if( in_array( $u,$abtest->data['show_in'] ) ): ?>checked="checked"<?php endif; ?>><?php echo $u; ?><br/>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php elseif( 1 == $e1->global && 1 == $e2->global ): ?>
			<tr>
				<th scope="row" colspan="2">
					<?php _e('Both Elements are global', 'campaign-monitor'); ?>
				</th>
			</tr>
			<?php elseif( in_array( $e1->type, ['button', 'simple_form'] ) && in_array( $e2->type, ['button', 'simple_form'] )  ) : ?>
			<tr>
				<th scope="row" colspan="2">
					<?php _e('Both Elements are shortcodes, this A/B Testing will be enabled whenever both shortcodes appear on the same rendered page.', 'campaign-monitor'); ?>
				</th>
			</tr>
			<?php else: ?>
			<tr>
				<th scope="row" colspan="2">
					<?php _e('As one element is a shortcode, this A/B Testing will be enabled whenever both elements appear on the same rendered page.', 'campaign-monitor'); ?>
				</th>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<?php _e('Elements', 'campaign-monitor'); ?>
				</th>
				<td>
					<table>
						<tr>
							<th><?php _e('Form', 'campaign-monitor'); ?></th>
							<th><?php _e('Impressions', 'campaign-monitor'); ?></th>
							<th><?php _e('Submissions', 'campaign-monitor'); ?></th>
							<th><?php _e('Submission Rate', 'campaign-monitor'); ?></th>
						</tr>
						<tr>
							<td><?php echo stripslashes($e1->name); ?> (<?php if ($e1->type != "slider"){echo CampaignMonitorPluginInstance()->humanize($e1->type);}else{ echo "Slide-Out"; } ?>)</td>
							<td><?php echo $abtest->data['first_element_shows']; ?></td>
							<td><?php echo $abtest->data['first_element_submissions']; ?></td>
							<td><?php echo (0==$abtest->data['first_element_shows'])? 0 : ($abtest->data['first_element_submissions']/$abtest->data['first_element_shows']) * 100; ?>%</td>
						</tr>
						<tr>
							<td><?php echo stripslashes($e2->name); ?> (<?php if ($e2->type != "slider"){echo CampaignMonitorPluginInstance()->humanize($e2->type);}else{ echo "Slide-Out"; } ?>)</td>
							<td><?php echo $abtest->data['second_element_shows']; ?></td>
							<td><?php echo $abtest->data['second_element_submissions']; ?></td>
							<td><?php echo (0==$abtest->data['second_element_shows'])? 0 : ($abtest->data['second_element_submissions']/$abtest->data['second_element_shows']) * 100; ?>%</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					<input type="submit" value="<?php _e('Save A/B Test', 'campaign-monitor'); ?>" class="button button-primary button-large" id="publish" name="publish">
				</td>
			</tr>
		</table>
	</form>
</div>
