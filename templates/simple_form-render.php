<div id="cm-simple_form-<?php echo $this->id?>" class="cm-simple-form" >
<?php
if ( !$this->preview ) {
  $form = new CampaignMonitorForm();
  $form->load( $this->id);
} else {
  $form = $this->previewForm;
}
echo  $form->render(); ?>
</div>