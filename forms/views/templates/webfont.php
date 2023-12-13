<?php
/**
 * @var $this forms\core\Font
 *
 **/

?>
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>
  <script> 
        WebFont.load({
                    google: { 
                           families: ['<?php echo htmlspecialchars($this->getName(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'); ?>'] 
                     } 
         }); 
   </script>
   <style>
    #cmApp_signupContainer *,
    #signupFormPreviewCon *,
    .cmApp_signupContainer.cmApp_slideoutTab .cmApp_slideOutTab #cmApp_slideoutButton {
        font-family : '<?php echo htmlspecialchars($this->getName(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'); ?>';
    }
   </style>