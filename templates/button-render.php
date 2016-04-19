<?php // $this == CampaignMonitorButton
CampaignMonitorPluginInstance()->connection->enabled();
?>
<button type="button" id="cm-button-<?php echo $this->id; ?>" class="cm-button cm-button-<?php echo $this->id; ?>"><?php echo stripslashes($this->data['text']); ?></button>