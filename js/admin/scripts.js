jQuery( document ).ready(function($) {
        
    // Scroll to top
    function scrollToTop() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    }
    
    $(document).on('keyup keypress', 'form#lists-add input[type=text], form#lists-add input[type=checkbox]', function(e) {
      if(e.which == 13) {
        e.preventDefault();
        return false;
      }
    });
    
    
    ///////////////////////////////////////////
    // FORWARD STEPS
    ///////////////////////////////////////////

    $('#submitStep1').on('click', function(e){
        // Simple Validation
        if ($("input[name='type']:checked").val() && $("#wizardFormName").val() && ($("#wizardExistingListName").val() || $("#wizardNewListName").val())){

            //IF valid:
            $("#wizard-error-message").fadeOut();
            $('.wizard-steps').fadeOut(400, function(){
                $('#wizardStep2').fadeIn(800);
            });
            scrollToTop();

        }else{

            //IF not valid:
            $("#wizard-error-message").fadeIn();
            scrollToTop();
        }
    });

    $('#submitStep2').on('click', function(e){
        
        hasEmptyTextarea = 0;
        $('#wizard-form-fields textarea.required-options').each(function(){
            if (!$.trim($(this).val())) {
                hasEmptyTextarea = 1;
            }
        });
        
        // Simple Validation
        if ($("#wizardFormTitle").val() && $("#wizardButtonText").val() && hasEmptyTextarea==0){

            //IF valid:
            $("#wizard-error-message").fadeOut();
            $('#wizardStep2').fadeOut(400, function(){
                
                displayStep3Fields();
                
                $('#wizardStep3').fadeIn(800);
            });
            scrollToTop();

        }else{

            //IF not valid:
            $("#wizard-error-message").fadeIn();
            scrollToTop();

        }
        
        hasEmptyTextarea = 0;
    });

    
    ///////////////////////////////////////////
    // BACKWARD STEPS
    ///////////////////////////////////////////
    
    $('.backToStep1').on('click', function(e){
        $('.wizard-steps').fadeOut(400, function(){
            $('#wizardStep1').fadeIn(800);
        });
    });

    $('.backToStep2').on('click', function(e){
        $('#wizardStep3').fadeOut(400, function(){
            $('#wizardStep2').fadeIn(800);
        });
    });

    
    ///////////////////////////////////////////
    // STEP 1
    ///////////////////////////////////////////
    
    $('#wizardNewList').on('click', function(e){
        $('.form-new-list').toggle();
        $('.form-existing-list').toggle();
        e.preventDefault();
    });
    $('#wizardExistingList').on('click', function(e){
        $('.form-new-list').toggle();
        $('.form-existing-list').toggle();
        $('#wizardNewListName').val("");
        e.preventDefault();
    });

    $('.options-type-of-form label').on('click', function(e){
        $('.options-type-of-form').addClass('options-type-of-form--selected');
    });
    
    $('#selectOptIn').on('change', function(e){
        if($('#selectOptIn').val() == "1"){
            $('.confirmation-url').fadeIn();
        }else{
            $('.confirmation-url').fadeOut();
        }
    });

//    $('input:radio[name=wizardTypeOfForm]').change(function () {
//
//        $('.form-type').fadeOut();
//
//        if ($("input[name='wizardTypeOfForm']:checked").val() == 'slide-out'){
//            $('#form-type-slide-out').fadeIn();
//        }
//        else if ($("input[name='wizardTypeOfForm']:checked").val() == 'lightbox'){
//            $('#form-type-lightbox').fadeIn();
//        }
//        else if ($("input[name='wizardTypeOfForm']:checked").val() == 'bar'){
//            $('#form-type-bar').fadeIn();
//        }
//        else if ($("input[name='wizardTypeOfForm']:checked").val() == 'button'){
//            $('#form-type-button').fadeIn();
//        }
//        else if ($("input[name='wizardTypeOfForm']:checked").val() == 'embedded'){
//            $('#form-type-embedded').fadeIn();
//        }
//
//    });

    
    ///////////////////////////////////////////
    // STEP 2
    ///////////////////////////////////////////
    
    // Index number for new fields
    var newFieldIndex = 1;
    
    
    // Display fields from selected List
    
    function displayFields(){
        
        var listId = $( "#wizardExistingListName" ).val();
        
        var listFields = php_variables['list'+listId];
        
        $( "#list-custom-fields" ).html( listFields );
        
        newFieldIndex = 1;

        $( "#preview_type").val($("input[name='type']:checked").val());
    
    }

    
    $('#submitStep1').on('click', function(){
        if ($("#wizardNewListName").val()==""){
            displayFields();
        }
    });
    
    

    $('#add-new-field').on('click', function(e){
        $('.field-generator').fadeIn();
        $('#add-new-field').fadeOut();
    });


    $('#bt-generate-field').on('click', function(e){
        if ( $('#wizardNewFieldName').val() != "" ){
            var newFieldName = $('#wizardNewFieldName').val();
            var newFieldDataType = $('#wizardNewFieldDataType').val();
            var textareaIsRequired = "";
            if (newFieldDataType == "MultiSelectMany" || newFieldDataType == "MultiSelectOne"){ textareaIsRequired = "required-options"}
                
            var fieldHtml = '<tr class="additional-field"><th class="check-column"><input id="wizardFieldsIsEnabledNew'+newFieldIndex+'" type="checkbox" name="fields['+newFieldIndex+'][enabled]" value="1" checked></th><td><label for="wizardFieldsIsEnabledNew'+newFieldIndex+'" class="label-is-enabled">'+newFieldName+'</label> <ul class="field-options"><li><a href="#" class="rename-field">'+php_variables['str_rename']+'</a></li><li><a href="#TB_inline?width=300&height=130&inlineId=TB_confirm" class="delete-field thickbox" id="delete'+newFieldIndex+'">'+php_variables['str_delete']+'</a></li></ul><div class="fields-extra-config"><input id="wizardFieldsHasLabelNew'+newFieldIndex+'" type="checkbox" name="fields['+newFieldIndex+'][label]" value="1" checked> <label for="wizardFieldsHasLabelNew'+newFieldIndex+'">'+php_variables['str_show_label']+'</label><input id="wizardFieldsIsRequiredNew'+newFieldIndex+'" type="checkbox" name="fields['+newFieldIndex+'][required]" value="1"> <label for="wizardFieldsIsRequiredNew'+newFieldIndex+'">'+php_variables['str_required']+'</label><input type="hidden" name="fields['+newFieldIndex+'][DataType]" id="wizardFieldsDataTypeNew'+newFieldIndex+'" value="'+newFieldDataType+'"></div><div class="fields-rename-field"><input type="text" name="fields['+newFieldIndex+'][FieldName]" id="wizardFieldsNameNew'+newFieldIndex+'" value="'+newFieldName+'" class="regular-text"><input type="button" value="'+php_variables['str_done']+'" class="button-secondary bt-change-name"></div>';
            
            
            if(newFieldDataType == "Text" || newFieldDataType == "Number" || newFieldDataType == "Date"){
                fieldHtml +='<p class="description">'+php_variables['str_placeholder_text']+'</p><input type="text" name="fields['+newFieldIndex+'][placeholder]" value="" class="regular-text">';
            }
                                                                       
            fieldHtml +='<p class="description description--class">'+php_variables['str_class_text']+'</p><input type="text" name="fields['+newFieldIndex+'][css_classes]" value="" class="regular-text" placeholder=""><div class="options-field-hidden is'+newFieldDataType+'"><p class="description">'+php_variables['str_field_options']+'</p><textarea name="fields['+newFieldIndex+'][Options]" cols="30" rows="10" class="options-field '+textareaIsRequired+'"></textarea></div></td></tr>';
            
            $('#wizard-form-fields #list-custom-fields').append(fieldHtml);

            newFieldIndex = newFieldIndex + 1;

            $('.field-generator').fadeOut();
            $('#add-new-field').fadeIn();
        }
        $('#wizardNewFieldName').val("");

    });
    
    $('#bt-cancel-field').on('click', function(e){
        $('.field-generator').fadeOut();
        $('#add-new-field').fadeIn();
        e.preventDefault();
    });

    $('#wizard-form-fields').on('click', '.rename-field', function(e){
        $(this).parent().parent().siblings('.fields-rename-field').fadeIn();
        $(this).parent().fadeOut();
        e.preventDefault();
    });

    $('#wizard-form-fields').on('click', '.bt-change-name', function(e){
        
        var newName = $(this).siblings('input[type=text]').val();
        
        if (newName){
            $(this).parent().siblings('.label-is-enabled').html(newName);
        }
        
        $(this).parent().fadeOut();
        $(this).parent().siblings('.field-options').children().fadeIn();
        
        e.preventDefault();
    });
    
    $('#wizard-form-fields').on('click', '.delete-field', function(e){
        var deleteId = $(this).attr('id');
        $('#TB_confirm').html("<p>"+php_variables['str_confirm_delete']+"</p><input type='button' value='"+php_variables['str_delete']+"' id='exclude"+deleteId.substr(6)+"' class='delete-button button button-primary'><input type='button' value='"+php_variables['str_cancel']+"' class='cancel-button button-secondary'>");
    });
    
    $('.wp-admin').on('click', '.delete-button', function(e){
        var deleteId = $(this).attr('id');
        var deleteLink = 'delete'+deleteId.substr(7);
        var escapedDeleteLink = deleteLink.replace(/(:|\.|\[|\])/g,'\\$1');
        $('#'+escapedDeleteLink).closest(".additional-field").remove();
        $("#lists-add").append("<input type='hidden' name=toDelete[] value='" + deleteId.substr(7) + "'>");
        self.parent.tb_remove()

        
        e.preventDefault();
    });
    
    $('.wp-admin').on('click', '.cancel-button', function(e){
         self.parent.tb_remove()
    });



    ///////////////////////////////////////////
    // STEP 3
    ///////////////////////////////////////////
    
    
    
    // Display fields from selected List
    
    function displayStep3Fields(){
        
        var selectedType = $("input[name='type']:checked").val();

        var listTypeFields = php_variables[selectedType];
        
        $( "#step-3-options" ).html( listTypeFields );
    
    }
    
    
    $('#step-3-options').on('click',function () {

        $('.label-show-after-time').removeClass('default-locked--unlocked');
        $('.label-show-after-time input').prop('disabled', true);
        $('.label-show-after-scroll').removeClass('default-locked--unlocked');
        $('.label-show-after-scroll input').prop('disabled', true);


        if ($("input[name='lightbox_delay']:checked").val() == 'interval') {
            $('.label-show-after-time').addClass('default-locked--unlocked');
            $('.label-show-after-time input').prop('disabled', false);
        }
        if ($("input[name='lightbox_delay']:checked").val() == 'scroll') {
            $('.label-show-after-scroll').addClass('default-locked--unlocked');
            $('.label-show-after-scroll input').prop('disabled', false);
        }
        
        if ($("input[name='isGlobal']:checked").val() == '0') {
            $('.insert-pages').fadeIn();
            
            if ($("input.page-checkbox").length){
                var checkedPages = [];
                $("input.page-checkbox:checked").each(function(){
                    checkedPages.push($(this).val());
                });

                $("textarea[name='pages_list']").val("");
                $("textarea[name='pages_list']").val(checkedPages.join("\n"));
            }
        }
        if ($("input[name='isGlobal']:checked").val() == '1') {
            $('.insert-pages').fadeOut();
            $("textarea[name='pages_list']").val("");
        }

        
        
        
    });
    
    
    $(".save-post-status").on("click", function (e){
        var selectedStatus = $("#post_status").val();
        if(selectedStatus == "1"){
            $("#post-status-display").html("Enabled");
        }else{
            $("#post-status-display").html("Disabled");
        }
        $("#post-status-select").fadeOut();
        $(".edit-post-status").fadeIn();
        e.preventDefault();
    });
    
    $(".edit-post-status").on("click", function (e){
        $("#post-status-select").fadeIn();
        $(".edit-post-status").fadeOut();
        e.preventDefault();
    });
    
    $(".cancel-post-status").on("click", function (e){
        $("#post-status-select").fadeOut();
        $(".edit-post-status").fadeIn();
        e.preventDefault();
    });
    

    
    
    
    // PREVIEW
    
    $("#preview-form").on("click", function(){
        
        hasEmptyTextarea = 0;
        $('#wizard-form-fields textarea.required-options').each(function(){
            if (!$.trim($(this).val())) {
                hasEmptyTextarea = 1;
            }
        });
        
        // Simple Validation
        if ($("#wizardFormTitle").val() && $("#wizardButtonText").val() && hasEmptyTextarea==0){

            //IF valid:
            $("#wizard-error-message").fadeOut();
            
            try { 
                window.open('about:blank', 'previewWindow'); 
                $('#lists-add').attr('target', 'previewWindow').attr('action', php_variables['preview_url']).submit().removeAttr('target').removeAttr('action');
            } catch(e) {}
            
            scrollToTop();

        }else{

            //IF not valid:
            $("#wizard-error-message").fadeIn();
            scrollToTop();

        }
        
        hasEmptyTextarea = 0;
        
    });
    
    
    // SUBMIT EDIT
    
    $("#major-publishing-actions #publish").on("click", function(e){
        
        hasEmptyTextarea = 0;
        $('#wizard-form-fields textarea.required-options').each(function(){
            if (!$.trim($(this).val())) {
                hasEmptyTextarea = 1;
            }
        });
        
        // Simple Validation
        if ($("#wizardFormTitle").val() && $("#wizardButtonText").val() && hasEmptyTextarea==0){

            //IF valid:
            $("#wizard-error-message").fadeOut();

        }else{

            //IF not valid:
            $("#wizard-error-message").fadeIn();
            scrollToTop();
            e.preventDefault();

        }
        
        hasEmptyTextarea = 0;
        
    });
    




});