var $campaignMonitor = jQuery.noConflict();

(function( $v ) {

    window.abortAjax = false;

    window.jqDocumentReadyHasRun = false;
    window.customFieldInfo = [];
    window.previousCustomFieldListId = "";

    window.clearListDropdown = function() {
       $v("#campaignMonitorListId").
        empty().
        append($v('<option></option>').val("").html(""));
       $v("#campaignMonitorListIdCon").hide();
    };

    window.populateListDropdown = function(selected) {
        selected = selected || '';
        var action = 'handle_ajax_cm_forms';
        var type = 'getLists';

        if (!$v("#campaignMonitorClientId")) {
            return false;
        }

        var clientId =$v("#campaignMonitorClientId").val();


        if (typeof ($v("#campaignMonitorClientId").val()) == "undefined") {
            return false;
        }

        if (clientId.length < 1) {
            clearListDropdown();
            return false;
        }

       $v("#campaignMonitorListIdCon").hide();
       $v("#campaignMonitorListUpdateMessage").show();

        var dataToSend = {};
        dataToSend.action = action;
        dataToSend.type = type;
        dataToSend.clientId = clientId;

        $v.ajax({
            type: "POST",
            url: ajax_request.ajax_url,
            data: dataToSend,
            dataType: "text json",
            success: function (data, textStatus, request) {

                if (typeof data.clientLists === 'undefined') {
                    //clearListDropdown();
                   $v("#campaignMonitorListId").empty().append($v('<option></option>').val("").html("-- NO LISTS FOUND --"));
                   $v("#campaignMonitorListIdCon").show();
                } else {
                    var jqElem =$v("#campaignMonitorListId");
                    var listIdCurrent =$v("#campaignMonitorListId").val();
                    jqElem.empty();

                    if (data.clientLists.length < 1) {
                        jqElem.append($v('<option></option>').val("").html("-- NO LISTS FOUND --"));
                       $v("#campaignMonitorListIdCon").show();
                    }

                    else {
                        // more than 1 value, show empty option, plus all lists
                        jqElem.append($v('<option></option>').val("").html(""));


                        if (!listIdCurrent) {
                            listIdCurrent =$v("#campaignMonitorListIdCurrent").val();
                        }

                        for (var x = 0; x < data.clientLists.length; x++) {
                            cl = data.clientLists[x];
                            //alert(x+" "+listIdCurrent+" "+cl.ListID+" ("+cl.Name+")");
                            if (listIdCurrent == cl.ListID) {
                                jqElem.append($v('<option></option>').val(cl.ListID).html(htmlSpecialDecodeEncode(cl.Name)).prop('selected', true));
                            } else {
                                jqElem.append($v('<option></option>').val(cl.ListID).html(htmlSpecialDecodeEncode(cl.Name)));
                            }

                        }
                       $v("#campaignMonitorListIdCon").show();
                        populateCustomFieldList();
                    }

                }
               $v("#campaignMonitorListUpdateMessage").hide();

            },
            error: function (request, textStatus, errorThrown) {
                clearListDropdown();
               $v("#campaignMonitorListIdCon").show();
               $v("#campaignMonitorListUpdateMessage").hide();
            }
        });
    };

    window.populateCustomFieldList = function() {
        var existingFieldFound = 0;
        var elem =$v(".custom-field-container-list").find(".customFieldKey");
        for (var x = 0; x < elem.length; x++) {
            //console.log("b("+x+")");
            var theVal =$v(elem[x]).val();
            if (theVal.length > 0) {
                //console.log("d "+theVal);
                existingFieldFound = 0;
                break;
            }
        }


        var dataToSend = {};

        var elem =$v("#campaignMonitorListId");
        if (!elem) {
           $v(".custom-field-container-list").hide();
           $v(".custom-field-container-add").hide();
            return false;
        }
        var z = 1;
        var theVal = elem.val();
        if (!theVal) {
           $v(".custom-field-container-list").hide();
           $v(".custom-field-container-add").hide();
            //console.log("noval - EXIT");
            return false;
        }
        if (theVal.length < 1) {
           $v(".custom-field-container-list").hide();
           $v(".custom-field-container-add").hide();
            //populateCustomFieldListNow();
            return false;
        }

       $v(".custom-field-container-list").show();
       $v(".custom-field-container-add").show();

        dataToSend.selected_list = elem.val();

        //if (dataToSend.selected_list == previousCustomFieldListId)
        //console.log('if ('+dataToSend.selected_list+' == '+previousCustomFieldListId+')');
        if (dataToSend.selected_list == previousCustomFieldListId) {
            //console.log('c');
            return false;
        }

        var elem =$v(".custom-field-container-list").find(".customFieldKey");
        for (var x = 0; x < elem.length; x++) {
            var curElem =$v(elem[x]);
            if (curElem.val().length > 0) {
                var num = parseInt(curElem.attr("id").replace("customFieldKey", ""));
                custom_field_remove(num);
            }
        }

        //console.log('b');
        //console.log("dataToSend.selected_list="+dataToSend.selected_list);
        previousCustomFieldListId = dataToSend.selected_list;


       $v(".custom-field-container-add").hide();
       $v(".custom-field-container-list").hide();
       $v(".custom-field-container-loading").show();

       $v("#addCustomFieldSelect").empty().append($v('<option></option>').val("").html("-Create New Field-"));



        dataToSend.action = 'handle_ajax_cm_forms';
        dataToSend.type = 'get_custom_fields';

        $v.ajax({
            type: "POST",
            url: ajax_request.ajax_url,
            data: dataToSend,
            dataType: "text json",
            success: function (data, textStatus, request) {
                var str = "";
                //console.log(data);
                if (data.custom_fields) {
                    var currentSetFieldAr = [];
                    var currentListId =$v("#campaignMonitorListIdCurrent").val();
                    if (dataToSend.selected_list == currentListId) {
                        var fieldKeyElems =$v(".origCustomFieldKey").append();
                        for (var x = 0; x < fieldKeyElems.length; x++) {
                            var fieldKeyElem =$v(fieldKeyElems[x]);
                            var theElemId = fieldKeyElem.attr("name");
                            var theNum = theElemId.replace("origCustomFieldKey", "");

                            var customFieldKey = fieldKeyElem.val();
                            var customFieldName =$v("#origCustomFieldName" + theNum).val();
                            var customFieldType =$v("#origCustomFieldType" + theNum).val();
                            var customFieldLabel =$v("#origCustomFieldLabel" + theNum).val();
                            var customFieldOptions =$v("#origCustomFieldOptions" + theNum).val();
                            var customFieldShowLabel =$v("#origCustomFieldShowLabel" + theNum).val();
                            var customFieldRequired =$v("#origCustomFieldRequired" + theNum).val();

                            add_custom_form_field_html(customFieldName, customFieldType, customFieldKey, customFieldLabel, customFieldOptions, customFieldShowLabel, customFieldRequired, 0);

                            currentSetFieldAr[currentSetFieldAr.length] = htmlSpecialDecodeEncode(customFieldKey);
                        }

                    }

                    // Object { FieldName: "Shopify Last Order Amount", Key: "[ShopifyLastOrderAmount]", DataType: "Number", FieldOptions: Array[0], VisibleInPreferenceCenter: true }
                    for (var x = 0; x < data.custom_fields.length; x++) {
                        var f = data.custom_fields[x];


                        if (currentSetFieldAr.indexOf(f.Key) < 0) {
                           $v("#addCustomFieldSelect").append($v('<option></option>').val(f.Key).html(htmlSpecialDecodeEncode(f.FieldName)));
                        }
                        var k = customFieldInfo.length;
                        customFieldInfo[k] = f;

                        customFieldInfo[k] = f;

                    }

                   $v(".custom-field-container-add").show();
                   $v(".custom-field-container-list").show();
                }

               $v(".custom-field-container-loading").hide();

            },
            error: function (request, textStatus, errorThrown) {
               $v(".custom-field-container-add").hide();
               $v(".custom-field-container-list").hide();
               $v(".custom-field-container-loading").hide();
            }
        });
    };

    window.cmPreviewFormHeightUpdate = function()
    {
        var windowHeight =$v( window ).height();
        console.log("resized height="+windowHeight);
        var maxHeightInner=windowHeight-300;
        if (maxHeightInner<120)
        {
            maxHeightInner=120;
        }

        var maxHeightOuter=windowHeight-150;
        if (maxHeightOuter<300)
        {
            maxHeightOuter=300;
        }

        var lightboxFieldElem=$v("#signupFormPreview_lightbox");
        var slideoutElem=$v("#signupFormPreview_slideoutTab").find(".fieldWrap");
        slideoutElem.css("max-height",(maxHeightInner+"px")).css("overflow-y","auto");
        slideoutElem.find("input").css("margin-left",0).css("margin-right",0);
        lightboxFieldElem.css("max-height",(maxHeightOuter+"px")).css("overflow-y","auto");
        lightboxFieldElem.find("input").css("margin-left",0).css("margin-right",0);
    };

    window.cmPreviewFormScrollUpdate = function()
    {
        // distance from the top of the browser to the containter
        var containerScrollPx =$v("#signupFormPreviewCon").offset().top;

        // amount scrolled
        var scrollTopPx=$v(document).scrollTop();

        // amount scrolled past the container (will be negative when at the top of the page)
        var scrollPastConPx=scrollTopPx-containerScrollPx;

        // height of the container
        var containerHeight =$v("#signupFormForm").height();

        // space above and below the form
        var extraPaddingPx=100;

        var topPx=extraPaddingPx;

        var elem=$v("#signupFormPreview_lightbox");
        var elemHeight=elem.height();
        if (containerHeight > (elemHeight+(extraPaddingPx*4)))
        {
            if (scrollPastConPx+(extraPaddingPx*4) > topPx)
            {
                topPx=scrollPastConPx+extraPaddingPx*4;
            }
            if (containerHeight - elemHeight - (extraPaddingPx*2)  <0 )
            {
                topPx=5;
            }
        }
        elem.css("margin-top", topPx+"px");

        var elem=$v("#signupFormPreview_embedded");
        var elemHeight=elem.height();
        var topPx=100;
        if (containerHeight > (elemHeight+(extraPaddingPx*4)))
        {
            if (scrollPastConPx+(extraPaddingPx*4) > topPx)
            {
                topPx=scrollPastConPx+extraPaddingPx*4;
            }
            if (containerHeight - elemHeight - (extraPaddingPx*2)  <0 )
            {
                topPx=5;
            }
        }
        elem.css("margin-top", topPx+"px");
    };

   

})($campaignMonitor);

$campaignMonitor(function ($v) {
       $v('#textFont').fontselect().on('change', function(){
                // replace + signs with spaces for css
                var font =$v(this).val().replace(/\+/g, ' ');


                // split font into family and weight
                font = font.split(':');

                // set family on paragraphs
                var selected = font[0];
               $v('#signupFormPreviewCon *').css('font-family', selected);
               $v('#selectedFont').attr('value', selected);
            });


    $v('#fontReset').on('click', function () {
        var  defaultFont = 'Open Sans';
        $v('#textFont').val(defaultFont);
        $v('#selectedFont').attr('value', defaultFont);
        $v('div.font-select a span').text(defaultFont);
        $v('#currentFontLabel span').text(defaultFont);
        $v('#currentFontLabel span').css('font-family', defaultFont);

        alert('Please update form to apply changes');
    });

       $v( window ).on('resize', function() {
            cmPreviewFormHeightUpdate();
        });

        cmPreviewFormHeightUpdate();

       $v('#refreshCampaignMonitorList').on('click', function(e){
            e.preventDefault();
            populateListDropdown();
        });


       $v('.notifications').hide();
       $v('.ab-test-edit #btnSaveSettings').on('click', function (e) {


            var testTitle =$v('#testTitle');
            var selects =$v('.ab-test-edit select');


            if (testTitle.val().length === 0) {
                e.preventDefault();
                testTitle.css('outline', '1px solid #d81818');
                testTitle.attr('placeholder', 'required');
                testTitle.focus();
                return;
            }

            var values = [];
            var notice = $v('<p></p>');
            selects.each(function () {
                var selectedValue =$v(this).val();

                if (selectedValue.toLowerCase() === 'select') {
                    e.preventDefault();
                    notice.text('Please select the forms to use on this test');
                   $v('.notifications').html(notice).show();
                   $v(this).css('outline', '1px solid #d81818');
                    return;
                }

                // prevent duplicates
                if (values.indexOf(selectedValue) > -1) {
                    e.preventDefault();
                    notice.text('Primary and secondary form cannot be the same.')
                   $v(this).css('outline', '1px solid #d81818');
                   $v('.notifications').html(notice).show();
                    return false;
                } else {
                    values.push(selectedValue);
                }

            });
        });


        var colorPickerOptions = {
            // a callback to fire whenever the color changes to a valid color
            change: function (event, ui) {
                var parent =$v(this).attr('data-parent');
                var parents = parent.split(' ');
                var color = ui.color.toString();
               $v(parents).each(function () {
                   $v('#' + this).val(color);
                });
                updatePreviewForm();
            },
            // a callback to fire when the input is emptied or an invalid color
            clear: function () {},
            // hide the color picker controls on load
            hide: true,
            // show a group of common colors beneath the square
            // or, supply an array of colors to customize further
            palettes: true
        };

       $v('.color-field').wpColorPicker(colorPickerOptions);


       $v(".submitdelete").on("click", function () {
            var title =$v(this).attr('data-parent-title');
            var message = 'Are you sure you want to delete this item?';

            if (title != '' && typeof title != 'undefined') {
                message = 'Are you sure you want to delete ' + title + '?';
            }
            return confirm(message);
        });

       $v(document).on('click', '#btnLogOut', function (e) {
            if (confirm('This action cannot be undone. Are you sure you want to log out?')) {

            } else {
                e.preventDefault();
            }
        });


       $v(document).on('change', '.postAjax', function (e) {
            e.preventDefault();
            populateListDropdown();

        });

       $v('.modal-update').hide();
       $v(document).on('click', '#appUpgradeButton', function () {
           $v('#appUpgradeForm').slideUp();
           $v('.modal-update').slideDown();
        });

       $v(document).on('click', '#createCustomList', function () {
            var dataToSend = {};
            dataToSend.selected_client =$v('#campaignMonitorClientId').val();
            dataToSend.list_title =$v('#newListName').val();
            // ajaxCall('create_custom_list', dataToSend);
            dataToSend.action = 'handle_ajax_cm_forms';
            dataToSend.type = 'create_custom_list';
            dataToSend.clientId =$v("#cliendId").val();

            $v.ajax({
                type: "POST",
                url: ajax_request.ajax_url,
                data: dataToSend,
                dataType: "text json",
                success: function (data, textStatus, request) {

                    setTimeout(function(){ populateListDropdown(); }, 1000);

                    close_list_fields();
                },
                error: function (request, textStatus, errorThrown) {
                    console.log(request);
                    console.log(errorThrown);
                }
            });

        });

       $v('#campaignMonitorListId').on('change', function () {
            populateCustomFieldList();
        }); /**/

       $v('#campaignMonitorListId').trigger('change');

       $v('#addNewFieldButton').on('click', function (e) {

            var selectedList =$v('#campaignMonitorListId').val();
            var selectedClient =$v('#campaignMonitorClientId').val();

            if (selectedList == '') {
                alert('please select a list before adding new custom fields');
                e.stopPropagation();
                return false;
            }

           $v('input[name="selected_client"]:visible').attr('value', selectedClient);
           $v('input[name="selected_list"]:visible').attr('value', selectedList);

        });



       $v(document).on('click', '.display-modal', function (e) {

            var body = '';
            var title = '';
            var html = '';

            var bodyContentId =$v(this).attr('data-body');
            title =$v(this).attr('data-title');

            body =$v('#' + bodyContentId);

            body.hide();
            html += '<div id="SR_overlay" class="SR_overlayBG"></div>';
            html += '<div id="SR_window" class="thickbox-loading " style="visibility: visible;" >';
            html += '<div id="SR_title">';
            html += '<div id="SR_ajaxWindowTitle">';
            html += title;
            html += '</div>';
            html += '<div id="SR_closeAjaxWindow"><button type="button" id="SR_closeWindowButton" class="close-custom-modal">';
            html += '<span class="screen-reader-text">Close</span><span class="tb-close-icon">';
            html += '</span></button></div></div>';
            html += '<div id="SR_ajaxContent">';
            html += '<div id="' + bodyContentId + '">';
            html += body.html();
            html += '</div>';
            html += '</div>';
            html += '</div>';
            body.remove();
           $v('body').append(html);
        });

       $v(document).on('click', '.close-custom-modal', function (e) {
            var content =$v('#SR_ajaxContent');
           $v('body').append(content.html());
           $v('#SR_overlay').remove();
           $v('#SR_window').remove();
        });

       $v(document).on('click', '.SR_overlayBG', function (e) {
            var content =$v('#SR_ajaxContent');
           $v('body').append(content.html());
           $v('#SR_overlay').remove();
           $v('#SR_window').remove();
        });

        if (!jqDocumentReadyHasRun) {
            populateListDropdown();
        }
        jqDocumentReadyHasRun = true;
    });
