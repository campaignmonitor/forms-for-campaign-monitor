var ajaxEventData = {};
var ajaxDoneEvent = new CustomEvent('_ajaxDone', {
    detail: {
        data:ajaxEventData
    }
});


var _ajaxCompleted = function(data, textStatus, request){
    ajaxEventData.test = true;
    document.dispatchEvent(ajaxDoneEvent);

};


jQuery(document).ready(function($) {

    $(document).on('click', '.post-ajax', function (e) {
        e.preventDefault();

        var action = $(this).attr('data-action');
        var type = $(this).attr('data-type');
        var formId = $(this).attr('data-form');

        var dataToSend = {};
        dataToSend.action = 'handle_ajax_cm_forms';
        dataToSend.type = type;
        dataToSend.clientId = $("#cliendId").val();

        var form = $('#'+formId);


        if (typeof form != 'undefined')
        {
            var formData = form.serializeArray();

            $(formData ).each(function(index, obj){
                dataToSend[obj.name] = obj.value;
            });
        }


        jQuery.ajax({
            type: "POST",
            url: ajax_request.ajax_url,
            data: dataToSend,
            dataType: "text json",
            success: function (data, textStatus, request) {

            },
            error: function (request, textStatus, errorThrown) {
                console.log(request);
                console.log(errorThrown);
            }
        });
    });


    cmAppDisplayTypeColorInput();



});



function cmAppDisplayTypeColorInput()
{
    var i = document.createElement("input");
    i.setAttribute("type", "color");
    if (i.type.toLowerCase() == "color")
    {
        jQuery(".inputTypeColorCon").addClass("inputTypeColorConDisplay");
    }
}

function ajaxCall(type, dataToSend, action) {

    action = action || 'handle_ajax_cm_forms';

    dataToSend.action = action;
    dataToSend.type = type;

    var response = {};

    jQuery.ajax({
        type: "POST",
        url: ajax_request.ajax_url,
        data: dataToSend,
        dataType: "text json",
        success: function (data, textStatus, request) {
            response.data = data;
            response.success = true;
            _ajaxCompleted(data, textStatus, request);
        },
        error: function (request, textStatus, errorThrown) {

            response.errorThrown = errorThrown;
            response.textStatus = textStatus;
            response.resquest = request;
            response.success = false;
        }
    });

    return response;

}




function htmlSpecialDecodeEncode(str)
{
    str=htmlSpecialCharsDecode(str);
    str=htmlSpecialChars(str);
    return str;
}

function htmlSpecialCharsDecode(str)
{
    var strDecoded=htmlSpecialChars(str, 1); // decode
    while (strDecoded!=str)
    {
        str=strDecoded;
        strDecoded=htmlSpecialChars(str, 1); // decode
    }
    return strDecoded;
}

function htmlSpecialChars(str, isDecode) {
    if (typeof isDecode == 'undefined')
    {
        isDecode=0;
    }

    // handle types other than string
    var theType=(typeof str).toLowerCase();

    if (theType == 'number')
    {
        return str.toString();
    }
    else if (theType == 'boolean')
    {
        if (str)
        {
            return "1";
        }
        else
        {
            return "0";
        }
    }
    else if (theType!="string") // anything else - null, object, etc
    {
        return "";
    }

    // replace characters
    var ar=[["&", "&amp;"], ["<", "&lt;"], [">", "&gt;"], ['"', "&#34;"], ["'", "&#39;"]];

    if (isDecode){
        ar[ar.length] = ['"', "&quot;"];
    }

    for (var x=0; x<ar.length; x++)
    {
        if (isDecode)
        {
            //str=str.replace(ar[x][1], ar[x][0]);
            str=cmReplaceAll(ar[x][1], ar[x][0], str);
        }
        else
        {
            //str=str.replace(ar[x][0], ar[x][1]);
            str=cmReplaceAll(ar[x][0], ar[x][1], str);
        }
    }

    return str;
}

function cmReplaceAll(findStr, replaceStr, origStr)
{
    return origStr.replace(new RegExp(findStr, 'g'), replaceStr);
}

