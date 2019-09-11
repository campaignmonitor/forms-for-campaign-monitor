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
                           families: ['<?php echo filter_var($this->getName(), FILTER_SANITIZE_STRING); ?>'] 
                     } 
         }); 
   </script>
   <style>
    #cmApp_signupContainer *,
    #signupFormPreviewCon *,
    .cmApp_signupContainer.cmApp_slideoutTab .cmApp_slideOutTab #cmApp_slideoutButton {
        font-family : '<?php echo filter_var($this->getName(), FILTER_SANITIZE_STRING); ?>';
    }
   </style>