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
                           families: ['<?php echo $this->getName(); ?>'] 
                     } 
         }); 
   </script>
   <style>
    #cmApp_signupContainer *,
    #signupFormPreviewCon *,
    .cmApp_signupContainer.cmApp_slideoutTab .cmApp_slideOutTab #cmApp_slideoutButton {
        font-family : '<?php echo $this->getName(); ?>';
    }
   </style>