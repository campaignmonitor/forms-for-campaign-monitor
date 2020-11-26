<?php

use \forms\core\Helper;
use \forms\core\Log;
use \forms\core\Settings;
use \forms\core\FormType;
use \forms\core\HtmlFields as Field;


$this->sanitize();

$campaignMonitorFieldAr = array(
    "Text"=>"Text",
    "Number"=>"Number",
    "MultiSelectOne"=>"Multi Select One",
    "MultiSelectMany"=>"Multi Select Many",
    "Date"=>"Date");//,
//"Country"=>"Country",
//"USState"=>"US State"

$maxFieldCount = 50;


/***
 * @var \forms\core\Form
 */

function htmlDecodeEncode($str)
{
    $str=str_replace(array("&lsquo;","&rsquo;","&#8216;","&#8217;", "&sbquo;", "&#8218;"),"'",$str);
    $str=str_replace(array("&ldquo;","&rdquo;","&#8220;","&#8221;", "&bdquo;", "&#8222;"),'"',$str);

    $decoded=html_entity_decode($str, ENT_QUOTES);
    while ($decoded!=$str)
    {
        $str=$decoded;
        $decoded=html_entity_decode($str, ENT_QUOTES);
    }
    $str=htmlentities($decoded, ENT_QUOTES);
    return $str;
}

function htmlDecodeAll($str)
{
    $str=str_replace(array("&lsquo;","&rsquo;","&#8216;","&#8217;", "&sbquo;", "&#8218;"),"'",$str);
    $str=str_replace(array("&ldquo;","&rdquo;","&#8220;","&#8221;", "&bdquo;", "&#8222;"),'"',$str);

    $decoded=html_entity_decode($str, ENT_QUOTES);
    while ($decoded!=$str)
    {
        $str=$decoded;
        $decoded=html_entity_decode($str, ENT_QUOTES);
    }
    //$str=htmlentities($decoded, ENT_QUOTES);
    return $str;
}


$form=$this->getForm();


//var_dump($_REQUEST);

/**
 * use thickbox wordpress on this page @see https://codex.wordpress.org/ThickBox
 */
//add_thickbox();

// add an empty option
$campaignMonitorClientAr = $form->getCampaignMonitorClientAr();

if (count($campaignMonitorClientAr)!=1)
{
    $campaignMonitorClientAr=array(""=>"-Select-")+$campaignMonitorClientAr;
}

$campaignMonitorClientId = $form->getcampaignMonitorClientId();
$campaignMonitorListId = $form->getcampaignMonitorListId();

$id = $form->getId();
$name = $form->getName();
$typeAr = FormType::getAll();
$type = $form->getType();
$listName = $form->getListName();
$form->initializePageAr();
$onPageAr = $form->getOnPageAr();
$pageAr = $form->getPageAr();
$maxPageOnCount = $form->getMaxPageOnCount();
$formAppearsLightbox = $form->getAppearsLightbox();
$lightboxSeconds = $form->getLightboxSeconds();
$lightboxScrollPercent = $form->getLightboxScrollPercent();
$formPlacement = $form->getFormPlacement();
$formPlacementBar = $form->getFormPlacementBar();
$formHeader = $form->getHeader();
$formSubHeader = $form->getSubHeader();
$hasNameField = $form->getHasNameField();
$hasDateOfBirthField = $form->getHasDateOfBirthField();
$hasOpenTextField = $form->getHasOpenTextField();
$openTextFieldLabel = $form->getOpenTextFieldLabel();
$hasGenderField = $form->getHasGenderField();
$hasCampMonLogo = $form->getHasCampMonLogo();
$submitButtonBgHex = $form->getSubmitButtonBgHex();
$submitButtonBgHex = str_replace( '#', '', filter_var($submitButtonBgHex, FILTER_SANITIZE_STRING));

$submitButtonTextHex = $form->getSubmitButtonTextHex();
$submitButtonTextHex = str_replace( '#', '', filter_var($submitButtonTextHex, FILTER_SANITIZE_STRING));
$backgroundHex = $form->getBackgroundHex();
$backgroundHex = str_replace( '#', '', filter_var($backgroundHex, FILTER_SANITIZE_STRING));
$textHex = $form->getTextHex();
$textHex = str_replace( '#', '', filter_var($textHex, FILTER_SANITIZE_STRING));
$submitButtonText = $form->getSubmitButtonText();
$formEmbedCode = $form->getFormEmbedCode();
$isActive = $form->getIsActive();

$hasNameFieldLabel=$form->getHasNameFieldLabel();
$hasEmailFieldLabel=$form->getHasEmailFieldLabel();

$formFields = $form->getFields();
$selectedFont = $form->getFont();

$successMessage = $form->getSuccessMessage();
$successMessage = htmlspecialchars_decode($successMessage);

$onPageAr=$form->getOnPageAr();
$pageAr=$form->getPageAr();

$createDate=$form->getCreateDate();
$captchaKey=Settings::get("recaptcha_key");
if (!empty($captchaKey))
{
    $isCaptchaEnabledOnSite=1;
}
else
{
    $isCaptchaEnabledOnSite=0;
}

$hasCaptcha=$form->getHasCaptcha();

if (is_null($hasCaptcha))
{
    $hasCaptcha=1;
}

$notices=$this->getNotices();

$self="";
$image_path="";

$clientAr=array(""=>"clients here");
$listAr=array(""=>"lists here");

$imageFolderUrl=plugins_url()."/forms-for-campaign-monitor/forms/views/admin/images/";

$isUpdated = intval(\forms\core\Request::get("isUpdated"));
if ($isUpdated)
{
    ?><div class="updated notice notice-success is-dismissible"><p>Form updated.</p></div><?php
}


?>
    <script type="text/javascript">

        if(typeof jQuery == 'undefined'){
            document.write('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></'+'script>');
        }

    </script>

    <div class="wrap">
        <h1 style="padding-right:0;"><?php if (empty($id)) { echo "Customize Your Form";} else { echo "Update ".htmlDecodeEncode($name); } ?><div style="display:inline-block;float:right;"><a href="<?php echo get_admin_url(); ?>admin.php?page=campaign-monitor-for-wordpress" class="button">Cancel</a></div></h1>
        <?php if (!empty($notices))
        {
            ?>
            <div id="message" class="updated notice notice-success is-dismissible"><p><?php echo $notices; ?></p></div>

            <?php
        }
        ?>

        <div id="createNewCustomField" style="display: none">
            <div class="new-custom-field">
                <form id="addNewFieldForm" action="<?php echo get_admin_url(); ?>admin.php" method="post">
                    <label>Field Name</label>
                    <input name="custom_field_name"  maxlength="100" type="text" placeholder="Field Name (100 character max)"/>

                    <label>Data Type</label>
                    <select title="Field Type" name="custom_field_type" id="custom_field_type" class="regular-text">
                        <option value="Text">Text</option>
                        <option value="Number">Number</option>
                        <option value="MultiSelectOne">Multiple Options (can only select one)</option>
                        <option value="MultiSelectMany">Multiple Options (can select many)</option>
                        <option value="Date">Date</option>
                        <option value="Country">Country</option>
                        <option value="USState">US State</option>
                    </select>
                    <br>
                    <small>
                        This will add the field to your form and to your campaign monitor list as a custom field.
                    </small> <br>

                    <button type="submit"  data-form="addNewFieldForm" data-type="create_custom_field"  class="button-secondary post-ajax">
                        Create New Custom
                    </button>
                </form>
            </div>

        </div>
        <div id="formDesignMainCon"><div><div style="padding:20px;width:255px;vertical-align: top;">

                    <form id="signupFormForm" action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
                        <input type="hidden" name="action" value="handle_cm_form_request">
                        <input type="hidden" name="data[type]" value="save">
                        <input type="hidden" name="formId" value="<?php echo filter_var($id, FILTER_SANITIZE_STRING); ?>">
                        <input type="hidden" name="createDate" value="<?php echo htmlDecodeEncode($createDate); ?>">
                        <input type="hidden" name="data[app_nonce]" value="<?php echo wp_create_nonce( 'app_nonce' ); ?>">

                        <div><label>Form name</label><?php echo Field::text('formName',htmlDecodeEncode($name), 'id="formName" maxlength="255"'); ?></div>

                        <div><label>Client</label><?php
                            echo Field::select('campaignMonitorClientId',$campaignMonitorClientAr, filter_var($campaignMonitorClientId, FILTER_SANITIZE_STRING),'id="campaignMonitorClientId" class="postAjax"');
                            ?></div>

                        <div id="campaignMonitorListIdCon" style="display:none;"><label>List &nbsp; <span style="float:right;">
                        <a id="refreshCampaignMonitorList" href="#" ><img src="<?php echo $imageFolderUrl;?>refresh-icon.png" alt="refresh" style="height:16px;display:inline-block;margin-bottom:-3px;" /></a> &nbsp;
                        <a href="javascript:void(0);" onclick="add_list_form();">+ Add List</a>
                        </span></label><?php



                            echo Field::select('campaignMonitorListId',array(), "", 'id="campaignMonitorListId"');
                            echo Field::hidden('campaignMonitorListIdCurrent', filter_var($campaignMonitorListId, FILTER_SANITIZE_STRING),'id="campaignMonitorListIdCurrent"');
                            ?></div>
                        <div id="campaignMonitorListUpdateMessage" style="display:none;"><em>Updating Lists...</em></div>

                        <div id="formStatusCon"><label>Status</label>
                            <div style="display:inline-block;"><input type="radio" name="isActive" class="styledRadio" id="isActiveEnabled" value="1"<?php if ($isActive)   {echo " checked=\"checked\"";} ?> />
                                <label for="isActiveEnabled"><span><span></span></span> Enabled</label></div> &nbsp;
                            <div style="display:inline-block;"><input type="radio" name="isActive" class="styledRadio" id="isActiveDisabled" value="0"<?php if (!$isActive) {echo " checked=\"checked\"";} ?> />
                                <label for="isActiveDisabled"><span><span></span></span> Disabled</label></div>
                        </div>

                        <div id="formCaptchaOptionCon"><label<?php if (!$isCaptchaEnabledOnSite) { echo ' style="color:#666;"'; } ?>>Captcha</label>
                            <?php
                            //if ($isCaptchaEnabledOnSite)
                            //{
                            ?>

                            <div style="display:inline-block;"><input type="radio" name="hasCaptcha" class="styledRadio" id="hasCaptchaOn" value="1"<?php if ($hasCaptcha)   {echo " checked=\"checked\"";} ?> />
                                <label for="hasCaptchaOn"<?php if (!$isCaptchaEnabledOnSite) { echo ' style="color:#666;"'; } ?>><span><span></span></span> Enabled</label></div> &nbsp;
                            <div style="display:inline-block;"><input type="radio" name="hasCaptcha" class="styledRadio" id="hasCaptchaOff" value="0"<?php if (!$hasCaptcha) {echo " checked=\"checked\"";} ?> />
                                <label for="hasCaptchaOff"<?php if (!$isCaptchaEnabledOnSite) { echo ' style="color:#666;"'; } ?>><span><span></span></span> Disabled</label></div>

                            <?php
                            //}
                            if (!$isCaptchaEnabledOnSite)
                            {
                                ?><div>Set up <a href="<?php echo admin_url(); ?>admin.php?page=campaign_monitor_settings_page" target="_blank">Captcha</a>.</div><?php
                                //echo Field::hidden("hasCaptcha",$hasCaptcha); // pass the current value.
                            }
                            ?>
                        </div>
                        <div></div>

                        <div><label>Form type</label><div><?php echo Field::select("formType",$typeAr, filter_var($type, FILTER_SANITIZE_STRING), 'id="formType"'); ?></div></div>

                        <div><label>Page(s) this form appears on</label><div class="newPageInputCon">
                                <?php
                                $ctr=0;
                                foreach ($onPageAr as $pageId)
                                {

                                    $availablePages = '';
                                    $availablePosts = '';
                                    foreach ($pageAr as $entityId => $entityTitle) {
                                        $selected = $entityId == $pageId ? 'selected' : '';
                                        $option = "<option value=\"$entityId\" $selected>".htmlspecialchars($entityTitle)."</option>";
                                        if (empty($entityId) || $entityId == -1) continue;
                                        $x = get_post( $entityId );

                                        if (!empty($x) && $x->post_type == 'page') {
                                            $availablePages .= $option;
                                        } else {
                                            $availablePosts .= $option;
                                        }
                                    }
                                $ctr++;
                                if ($ctr>1)
                                {
                                    $img="<a href=\"#\" class=\"remove_page_img\"><img src=\"".$imageFolderUrl."X@2x.png\" /></a>";
//                                    echo "<div id=\"formPageOn_".$ctr."_con\"><div>".Field::select("formPageOn_".$ctr, $pageAr, $pageId, 'id="formPageOn_'.$ctr.'"')."</div><div class='cmRemovePage'>".$img."</div></div>";

                                    ?>
                                    <div id="<?php echo "formPageOn_{$ctr}_con";  ?>">
                                        <div>
                                            <select name="<?php echo "formPageOn_$ctr"  ?>" id="<?php echo "formPageOn_$ctr"  ?>">
                                                <option value=""></option>
                                                <option value="-1">-- All pages --</option>
                                                <?php if ( $availablePosts !== '') : ?>
                                                    <optgroup label="Posts">
                                                        <?php echo $availablePosts; ?>
                                                    </optgroup>
                                                <?php endif; ?>
                                                <?php if ( $availablePages !== '') : ?>
                                                    <optgroup label="Pages">
                                                        <?php echo $availablePages; ?>
                                                    </optgroup>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class='cmRemovePage'><?php echo $img; ?></div>
                                    </div>
                                <?php
                                }
                                else
                                {
//                                echo '<div id="formPageOn_'.$ctr.'_con"><div>'.Field::select("formPageOn_".$ctr, $pageAr, $pageId, 'id="formPageOn_'.$ctr.'"').'</div></div>';

                                ?>
                                <div id="<?php echo "formPageOn_{$ctr}_con";  ?>">
                                    <div>
                                        <select name="<?php echo "formPageOn_$ctr"  ?>" id="<?php echo "formPageOn_$ctr"  ?>">
                                            <option value=""></option>
                                            <option value="-1">-- All pages --</option>
                                            <?php if ( $availablePosts !== '') : ?>
                                                <optgroup label="Posts">
                                                    <?php echo $availablePosts; ?>
                                                </optgroup>
                                            <?php endif; ?>
                                            <?php if ( $availablePages !== '') : ?>
                                                <optgroup label="Pages">
                                                    <?php echo $availablePages; ?>
                                                </optgroup>
                                            <?php endif; ?>
                                       </select>
                                    </div>
                                </div>
                                <?php

                                // end table and start a new one. line 1 needs to be in a separate table so that it can be full width with no x
                                ?>

                            </div>

                            <div class="newPageInputCon">
                                <?php
                                }

                                }
                                if ($ctr<1)
                                {

                                $availablePages = '';
                                $availablePosts = '';
                                foreach ($pageAr as $entityId => $entityTitle) {
                                    $option = "<option value=\"$entityId\" >".htmlspecialchars($entityTitle)."</option>";
                                    if (empty($entityId) || $entityId == -1) continue;
                                    $x = get_post( $entityId );

                                    if (!empty($x) && $x->post_type == 'page') {
                                        $availablePages .= $option;
                                    } else {
                                        $availablePosts .= $option;
                                    }
                                }
//                                echo '<div id="formPageOn_1_con"><div>'.Field::select("formPageOn_1", $pageAr, "", 'id="formPageOn_1"').'</div></div>';

                                ?>
                                <div id="formPageOn_1_con"><div>
                                <select name="<?php echo "formPageOn_1"  ?>" id="<?php echo "formPageOn_1"  ?>">
                                    <option value=""></option>
                                    <option value="-1">-- All pages --</option>
                                    <?php if ( $availablePosts !== '') : ?>
                                        <optgroup label="Posts">
                                            <?php echo $availablePosts; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if ( $availablePages !== '') : ?>
                                        <optgroup label="Pages">
                                            <?php echo $availablePages; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                                    </div></div>
                                <?php

                                // end table and start a new one. line 1 needs to be in a separate table so that it can be full width with no x
                                ?></div><div class="newPageInputCon"><?php
                                }
                                if ($ctr<$maxPageOnCount)
                                {
                                    ?><div id="addAppearsOnPageButtonCon"><div><a href="#" id="addAppearsOnPageButton">+ Add page</a></div><div></div></div><?php
                                }

                                ?></div></div>

                        <div id="formAppearsLightboxCon"><label>Form appears</label>
                            <div><input type="radio" name="formAppearsLightbox" class="styledRadio" id="formAppearsLightboxSeconds" value="seconds"<?php if ($formAppearsLightbox=="seconds") {echo " checked=\"checked\"";} ?> />
                                <label for="formAppearsLightboxSeconds" id="formAppearsLightboxSecondsLabel"><span><span></span></span>
                                    After <?php echo Field::text("lightboxSeconds", filter_var($lightboxSeconds, FILTER_SANITIZE_STRING), 'id="lightboxSeconds" maxlength="3"'); ?> seconds</label></div>
                            <div><input type="radio" name="formAppearsLightbox" class="styledRadio" id="formAppearsLightboxScroll" value="scroll"<?php if ($formAppearsLightbox=="scroll") {echo " checked=\"checked\"";} ?> />
                                <label for="formAppearsLightboxScroll" id="formAppearsLightboxScrollLabel"><span><span></span></span>
                                    After scrolling <?php echo Field::text("lightboxScrollPercent", filter_var($lightboxScrollPercent, FILTER_SANITIZE_STRING), 'id="lightboxScrollPercent"'); ?> %</label></div>
                        </div>

                        <div id="formPlacementCon"><label>Form slides out from</label>
                            <div><input type="radio" name="formPlacement" class="styledRadio" id="formPlacementTL" value="topLeft"<?php if ($formPlacement=="topLeft") {echo " checked=\"checked\"";} ?> />
                                <label for="formPlacementTL"><span><span></span></span> Top left</label></div>
                            <div><input type="radio" name="formPlacement" class="styledRadio" id="formPlacementTR" value="topRight"<?php if ($formPlacement=="topRight") {echo " checked=\"checked\"";} ?> />
                                <label for="formPlacementTR"><span><span></span></span> Top right</label></div>
                            <div><input type="radio" name="formPlacement" class="styledRadio" id="formPlacementBL" value="bottomLeft"<?php if ($formPlacement=="bottomLeft") {echo " checked=\"checked\"";} ?> />
                                <label for="formPlacementBL"><span><span></span></span> Bottom left</label></div>
                            <div><input type="radio" name="formPlacement" class="styledRadio" id="formPlacementBR" value="bottomRight"<?php if ($formPlacement=="bottomRight") {echo " checked=\"checked\"";} ?> />
                                <label for="formPlacementBR"><span><span></span></span> Bottom right</label></div>
                        </div>


                        <div id="formPlacementBarCon"><label>Form slides out from</label>
                            <div><input type="radio" name="formPlacementBar" class="styledRadio" id="formPlacementBarTop" value="top"<?php if ($formPlacementBar=="top") {echo " checked=\"checked\"";} ?> />
                                <label for="formPlacementBarTop"><span><span></span></span> Top</label></div>
                            <div><input type="radio" name="formPlacementBar" class="styledRadio" id="formPlacementBarBottom" value="bottom"<?php if ($formPlacementBar=="bottom") {echo " checked=\"checked\"";} ?> />
                                <label for="formPlacementBarBottom"><span><span></span></span> Bottom</label></div>
                        </div>

                        <div><label>Header</label><?php echo Field::text("formHeader",htmlDecodeEncode($formHeader), "id=\"formHeader\" maxlength=\"100\""); ?></div>

                        <div id="subHeaderInputCon"><label>Subheader</label><?php echo Field::text("formSubHeader",htmlDecodeEncode($formSubHeader), "id=\"formSubHeader\" maxlength=\"255\""); ?></div>

                        <div>
                            <label>Form fields</label>
                            <ul id="formFieldUl">
                                <li style="text-indent: 0;padding-left: 0;"><?php echo Field::checkBox("hasNameField", $hasNameField,1,"id=\"hasNameField\" class=\"styledCheckbox\""); ?><label for="hasNameField"><span></span>
                                        Name</label><br><?php echo Field::checkBox("hasNameFieldLabel", $hasNameFieldLabel,1,"id=\"hasNameFieldLabel\" class=\"styledCheckbox\""); ?><label for="hasNameFieldLabel"><span></span>
                                        Show <em>Name</em> Label</label></li>
                                <li style="text-indent: 0;padding-left: 0;"><?php echo Field::checkBox("hasEmailField", 1,1,"id=\"hasEmailField\" disabled=\"disabled\" readonly=\"readonly\" class=\"styledCheckbox\""); ?> <label for="hasEmailField"><span></span>
                                        Email (required)</label><br><?php echo Field::checkBox("hasEmailFieldLabel", $hasEmailFieldLabel,1,"id=\"hasEmailFieldLabel\" class=\"styledCheckbox\""); ?><label for="hasEmailFieldLabel"><span></span>
                                        Show <em>Email</em> Label</label></li>
                                <?php /*<li><?php echo Field::checkBox("hasDateOfBirthField",$hasDateOfBirthField,1,"id=\"hasDateOfBirthField\" class=\"styledCheckbox\""); ?> <label for="hasDateOfBirthField"><span></span>
                    Date of birth (MM/DD/YYYY)</label></li>
            <li><?php echo Field::checkBox("hasOpenTextField",$hasOpenTextField,1,"id=\"hasOpenTextField\" class=\"styledCheckbox\""); ?><label for="hasOpenTextField"><span></span>
                    Open text field</label>
                <div style="padding:4px 0 8px 0px;display:none;" id="openTextFieldLabelCon">
                    <div><label>Open text field label</label></div>
                    <div><input type="text" value="<?php echo htmlentities($openTextFieldLabel); ?>" id="openTextFieldLabel" name="openTextFieldLabel" maxlength="100"  /></div>
                </div></li>
            <li><?php echo Field::checkBox("hasGenderField",$hasGenderField,1,'id="hasGenderField" class="styledCheckbox"'); ?> <label for="hasGenderField"><span></span>
                    Gender</label></li>*/ ?>
                                <li><?php echo Field::checkBox("hasCampMonLogo",$hasCampMonLogo,1,'id="hasCampMonLogo" class="styledCheckbox"'); ?> <label for="hasCampMonLogo"><span></span>
                                        Show <em>Powered by Campaign Monitor</em></label>
                                </li>

                            </ul>
                        </div>

                        <?php
                        if (!empty($formFields)) :
                            $index = 0;
                            foreach ($formFields as $formField) :
                                $index++;

                                echo Field::hidden('origCustomFieldKey'.$index, $formField->getKey(), 'class="origCustomFieldKey" id="origCustomFieldKey'.$index.'"');
                                echo Field::hidden('origCustomFieldName'.$index, $formField->getName(), 'id="origCustomFieldName'.$index.'"');
                                echo Field::hidden('origCustomFieldLabel'.$index, $formField->getLabel(), 'id="origCustomFieldLabel'.$index.'"');
                                echo Field::hidden('origCustomFieldOptions'.$index, implode("\n", $formField->getOptions()), 'id="origCustomFieldOptions'.$index.'"');
                                echo Field::hidden('origCustomFieldType'.$index, $formField->getType(), 'id="origCustomFieldType'.$index.'"');
                                echo Field::hidden('origCustomFieldShowLabel'.$index, ($formField->isShowLabel()   ? 1 : 0), 'id="origCustomFieldShowLabel'.$index.'"');
                                echo Field::hidden('origCustomFieldRequired'.$index,  ($formField->getIsRequired() ? 1 : 0), 'id="origCustomFieldRequired'.$index.'"');
                            endforeach;
                        endif;
                        ?>

                        <div class="custom-field-container-list"><?php /* <?php if (!empty($formFields)) : ?>

                                <?php $index = 0; foreach ($formFields as $formField) : $index++; ?>
                                    <div id="customFieldCon<?php echo $index; ?>" class="customFieldCon">
                                    <div style="padding-bottom:12px;"><span id="customFieldDisplay<?php echo $index; ?>">
                                           <?php echo $formField->getName(); ?>
                                       </span> <div>
                                            <a href="javascript:void(0)" onclick="custom_field_form(<?php echo $index; ?>);">edit</a> &nbsp;
                                            <a href="javascript:void(0);" onclick="custom_field_remove(<?php echo $index; ?>);">remove</a>  &nbsp;
                                            <a href="javascript:void(0);" onclick="custom_field_sort(<?php echo $index; ?>,1);">up</a>
                                            <a href="javascript:void(0);" onclick="custom_field_sort(<?php echo $index; ?>,0);">down</a></div></div>
                                    <input type="hidden" name="customFieldKey[]" id="customFieldKey<?php echo $index; ?>" class="customFieldKey" value="<?php echo $formField->getKey(); ?>" />
                                    <input type="hidden" name="customFieldName[]" id="customFieldName<?php echo $index; ?>" value="<?php echo $formField->getName(); ?>" />
                                    <input type="hidden" name="customFieldLabel[]" id="customFieldLabel<?php echo $index; ?>" value="<?php echo $formField->getLabel(); ?>" />
                                    <input type="hidden" name="customFieldOptions[]" id="customFieldOptions<?php echo $index; ?>" value="" />
                                    <input type="hidden" name="customFieldType[]" id="customFieldType<?php echo $index; ?>" value="<?php echo $formField->getType(); ?>" />
                                    <input type="hidden" name="customFieldShowLabel[]" id="customFieldShowLabel<?php echo $index; ?>" value="<?php echo $formField->isShowLabel();  ?>" />
                                    <input type="hidden" name="customFieldRequired[]" id="customFieldRequired<?php echo $index; ?>" value="<?php echo $formField->getIsRequired(); ?>" />
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; */ ?></div>
                        <div class="custom-field-container-add" style="<?php echo empty($campaignMonitorListId) ? 'display:none;' : '' ?>" ><div style="width:75%;display:inline-block;vertical-align: middle;">
                                <select id="addCustomFieldSelect" name="addCustomFieldSelect">

                                    <?php if (!empty($campaignMonitorListId)) :  ?>
                                        <option value="">-Create New Field-</option>
                                        <?php

                                        $loadCustomFields =  \forms\core\Application::$CampaignMonitor->get_custom_fields($campaignMonitorListId);

                                        foreach ($loadCustomFields as $c) :  ?>
                                            <option value="<?php echo htmlDecodeEncode($c->Key); ?>"><?php echo htmlDecodeEncode($c->FieldName); ?></option>
                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </select>
                            </div><div style="width:25%;display:inline-block;vertical-align: middle;"><a class="button button-secondary" style="float:right" onclick="custom_field_form();" href="javascript:void(0);">add</a></div></div>

                        <div class="custom-field-container-loading" style="display:none;"><em>Loading Custom Fields...</em></div>
                        <?php // REFACTOR - use the values in the color picker rather than hidden fields for efficiency.  ?>
                        <div>
                            <label>Form Font</label>
                            <?php $currentSelectedFont =  (NULL != $selectedFont) ? $selectedFont->getFamily() : 'Open Sans'; ?>
                            <div>
                                <label id="currentFontLabel">Current Font:
                                    <span style="font-family: <?php echo $currentSelectedFont ?>;">
                                        <?php echo $currentSelectedFont ?>

                                    </span>

                                </label>
                                <p>
                                    <a class="button" id="fontReset" style="font-family: Open Sans,sans-serif;">Reset to Default Font
                                    </a>
                                </p>
                            </div>
                            <div>
                                <input id="textFont" class="google-font" name="text_font"  type="text" />
                                <input id="selectedFont" name="selectedFont"  type="hidden" value="<?php echo $currentSelectedFont; ?>"  />
                            </div>
                        </div>
                        <div>
                            <label>Button color</label>
                            <div>
                                <input name="fallbackButtonColor" type="text" data-parent="submitButtonBgHexCol submitButtonBgHex" value="#<?php echo $submitButtonBgHex; ?>" class="color-field"  />

                            </div>

                            <div class="colorInputCon hidden">
                                <div><?php echo Field::text("submitButtonBgHex", "#".str_replace("#","",$submitButtonBgHex), 'id="submitButtonBgHex" maxlength="7"'); ?></div>
                                <div class="inputTypeColorCon"><input type="color" id="submitButtonBgHexCol" name="submitButtonBgHexCol" value="#<?php echo htmlDecodeEncode($submitButtonBgHex); ?>" /></div>
                            </div>
                        </div>


                        <div>
                            <label>Button text color</label>
                            <div>
                                <input type="text" name="fallbackButtonTextColor" data-parent="submitButtonTextHex submitButtonTextHexCol" value="#<?php echo $submitButtonTextHex; ?>" class="color-field"   />

                            </div>
                            <div class="colorInputCon hidden">
                                <div><?php echo Field::text("submitButtonTextHex", "#".$submitButtonTextHex, 'id="submitButtonTextHex" maxlength="7"'); ?></div>
                                <div class="inputTypeColorCon"><input type="color" id="submitButtonTextHexCol" name="submitButtonTextHexCol" value="#<?php echo htmlDecodeEncode($submitButtonTextHex); ?>" /></div>
                            </div>
                        </div>

                        <div>
                            <label>Background</label>
                            <div>
                                <input type="text" name="fallbackFormBackgroundColor" data-parent="backgroundHexCol backgroundHex" value="#<?php echo $backgroundHex; ?>" class="color-field"   />

                            </div>
                            <div class="colorInputCon hidden">
                                <div><?php echo Field::text("backgroundHex", "#".$backgroundHex, 'id="backgroundHex" maxlength="7"'); ?></div>
                                <div class="inputTypeColorCon"><input type="color" id="backgroundHexCol" name="backgroundHexCol" value="#<?php echo htmlDecodeEncode($backgroundHex); ?>" /></div>
                            </div>
                        </div>

                        <div>
                            <label>Text color</label>
                            <div>
                                <input name="fallbackTextColor" type="text" data-parent="textHex textHexCol" value="#<?php echo str_replace('#', '', $textHex); ?>" class="color-field"   />
                                
                            </div>
                            <div class="colorInputCon hidden">
                                <div><?php echo Field::text("textHex", "#".$textHex, 'id="textHex" maxlength="7"'); ?></div>
                                <div class="inputTypeColorCon"><input type="color" id="textHexCol" name="textHexCol" value="#<?php echo htmlDecodeEncode($textHex); ?>" /></div>
                            </div>
                        </div>

                        <div><label>Success Message</label>

                            <textarea name="success_message"><?php echo $successMessage;?></textarea>
                            
                        </div>

                        <div><label>Button text</label>
                        <?php echo Field::text("submitButtonText", htmlDecodeEncode($submitButtonText), 'id="submitButtonText" maxlength="20"'); ?>
                        </div>

                        <?php if ($id !== '') : ?>
                            <div id="formEmbedCodeCon">
                                <label>Form embed code</label><?php echo Field::text("formEmbedCode", $formEmbedCode, 'id="formEmbedCode" readonly="readonly" style="background-color:#F8F8F8;"'); ?>
                                <div style="color:#788C9D;">This code allows you to embed the form wherever you need it on your Wordpress Site.</div>
                            </div>
                        <?php endif; ?>

                        <div style="text-align:right;"><?php
                            if (!empty($id))
                            {
                                $buttonTxt = "Update form";
                            }
                            else
                            {
                                $buttonTxt = "Create form";
                            }

                            ?><a href="<?php echo get_admin_url(); ?>admin.php?page=campaign-monitor-for-wordpress" class="button">Cancel</a> &nbsp; <?php

                            ?><input type="submit" value="<?php echo htmlDecodeEncode($buttonTxt); ?>" id="submitFormFormButton" class="button button-primary"><?php

                            ?></div>

                    </form>

                </div><div id="signupFormPreviewCon" style="vertical-align: top;">

                    <div id="signupFormPreview_lightbox">
                        <a class="cmCloseFormButton" style=""></a>
                        <div class="formHeaderPreview"></div>
                        <div class="formSubHeaderPreview"></div>
                        <div class="fieldWrap">
                            <div class="hasNameFieldPreview"><div class="hasNameLabelFieldPreview">Name</div><input type='text' value='' placeholder='Name' name='preview1' /></div>
                            <div class="hasEmailFieldPreview"><div class="hasEmailLabelFieldPreview">Email *</div><input type='text' value='' placeholder='Email *' name='preview2' /></div>

                            <div class="hasDateOfBirthFieldPreview"><input type='text' value='' placeholder='Date of birth (MM/DD/YYYY)' name='preview3' /></div>
                            <div class="hasOpenTextFieldPreview"><textarea placeholder='Open text field' name='preview5' ></textarea></div>
                            <div class="hasGenderFieldPreview">
                                <div class="cmSignupGenderFields cf">
                                    <div class="cmSignupGenderFieldsContainer cf">
                                        <input type="radio" name="gender4" value="male" class="styledRadio" id="genderM4">
                                        <label for="genderM4"><span><span></span></span> Male</label>
                                        <input type="radio" name="gender4" value="female" class="styledRadio" id="genderF4">
                                        <label for="genderF4"><span><span></span></span> Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="customFieldPreviewCon"></div><?php /**/ ?>
                        </div>
                        <div class="captchaConPreview hideFormSec">
                            <img src="<?php echo $imageFolderUrl; ?>google-recaptcha-preview.png" alt="recaptcha" />
                        </div>
                        <div><input type="submit" name="submit" value="" class="submitButtonPreview"></div>
                        <div class="hasCampMonLogoPreview"><img src="<?php echo $imageFolderUrl;?>PowerdByCampMon@2x.png" style="width:180px;" alt="" /></div>
                    </div>

                    <div id="signupFormPreview_bar"<?php if ($formPlacementBar=="bottom") { echo ' class="cmApp_placementBottom"'; }?>>
                        <a class="cmCloseFormButton"></a>
                        <div style="display:inline-block;margin-right:4px;" class="formHeaderPreview"></div><?php
                        ?><div class="hasNameFieldPreview hideFormSec"><div class="hasNameLabelFieldPreview">Name</div><input type='text' value='' placeholder='Name' name='preview1' /></div><?php
                        ?><div class="hasEmailFieldPreview hideFormSec"><div class="hasEmailLabelFieldPreview">Email *</div><input type='text' value='' placeholder='Email *' name='preview2' /></div><?php
                        ?><div class="hasDateOfBirthFieldPreview hideFormSec"><input type='text' value='' placeholder='Date of birth (MM/DD/YYYY)' name='preview3' /></div><?php
                        ?><div class="hasOpenTextFieldPreview hideFormSec"><textarea placeholder='Open text field' name='preview5' ></textarea></div><?php
                        ?><div class="hasGenderFieldPreview hideFormSec">
                            <div class="cmSignupGenderFields cf">
                                <div class="cmSignupGenderFieldsContainer cf">
                                    <input type="radio" name="gender1" value="male" class="styledRadio" id="genderM1">
                                    <label for="genderM1"><span><span></span></span> Male</label>
                                    <input type="radio" name="gender1" value="female" class="styledRadio" id="genderF1">
                                    <label for="genderF1"><span><span></span></span> Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="customFieldPreviewCon"></div><?php /**/ ?>
                        <div class="captchaConPreview hideFormSec">
                            <img src="<?php echo $imageFolderUrl;?>google-recaptcha-preview.png" alt="recaptcha" />
                        </div><?php
                        ?><div><input type="submit" name="submit" value="" class="submitButtonPreview" style=""></div><?php
                        ?><div class="hasCampMonLogoPreview hideFormSec" style="padding-top:13px;margin-left:5px;"><img src="<?php echo $imageFolderUrl;?>PowerdByCampMon@2x.png" style="width:180px;" alt="" /></div><?php
                        ?>
                    </div>

                    <div id="signupFormPreview_embedded">
                        <div class="formHeaderPreview hideFormSec"></div>
                        <div class="formSubHeaderPreview hideFormSec"></div>
                        <div class="fieldWrap">
                            <div class="hasNameFieldPreview"><div class="hasNameLabelFieldPreview">Name</div><input type='text' value='' placeholder='Name' name='preview1' /></div>
                            <div class="hasEmailFieldPreview hideFormSec"><div class="hasEmailLabelFieldPreview">Email *</div><input type='text' value='' placeholder='Email *' name='preview2' /></div>
                            <div class="hasDateOfBirthFieldPreview hideFormSec"><input type='text' value='' placeholder='Date of birth (MM/DD/YYYY)' name='preview3' /></div>
                            <div class="hasOpenTextFieldPreview hideFormSec"><textarea placeholder='Open text field' name='preview5' ></textarea></div>
                            <div class="hasGenderFieldPreview hideFormSec">
                                <div class="cmSignupGenderFields cf">
                                    <div class="cmSignupGenderFieldsContainer cf">
                                        <input type="radio" name="gender2" value="male" class="styledRadio" id="genderM2">
                                        <label for="genderM2"><span><span></span></span> Male</label>
                                        <input type="radio" name="gender2" value="female" class="styledRadio" id="genderF2">
                                        <label for="genderF2"><span><span></span></span> Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="customFieldPreviewCon"></div><?php /**/ ?>
                        </div><div class="captchaConPreview hideFormSec">
                            <img src="<?php echo $imageFolderUrl;?>google-recaptcha-preview.png" alt="recaptcha" />
                        </div>
                        <div><input type="submit" name="submit" value="" class="submitButtonPreview" /></div>
                        <div class="hasCampMonLogoPreview hideFormSec"><img src="<?php echo $imageFolderUrl;?>PowerdByCampMon@2x.png" style="width:180px;" alt="" /></div>
                    </div><?php /*#signupFormPreview_embedded .hasCampMonLogoPreview { padding-top:}*/ ?>

                    <div id="signupFormPreview_slideoutTab">
                        <a class="cmCloseFormButton" style=""></a>

                        <div class="cmSlideOutTab">
                            <a href="javascript:void(0);" id="slideoutButton">Subscribe</a>
                        </div>


                        <div class="formHeaderPreview hideFormSec"></div>
                        <div class="formSubHeaderPreview hideFormSec"></div>
                        <div class="fieldWrap">
                            <div class="hasNameFieldPreview hideFormSec"><div class="hasNameLabelFieldPreview">Name</div><input type='text' value='' placeholder='Name' name='preview1' /></div>
                            <div class="hasEmailFieldPreview"><div class="hasEmailLabelFieldPreview">Email *</div><input type='text' value='' placeholder='Email *' name='preview2' /></div>
                            <div class="hasDateOfBirthFieldPreview hideFormSec"><input type='text' value='' placeholder='Date of birth (MM/DD/YYYY)' name='preview3' /></div>
                            <div class="hasOpenTextFieldPreview hideFormSec"><textarea placeholder='Open text field' name='preview5' ></textarea></div>
                            <div class="customFieldPreview">

                            </div>

                            <div class="hasGenderFieldPreview hideFormSec">
                                <div class="cmSignupGenderFields cf">
                                    <div class="cmSignupGenderFieldsContainer cf">
                                        <input type="radio" name="gender3" value="male" class="styledRadio" id="genderM3">
                                        <label for="genderM3"><span><span></span></span> Male</label>
                                        <input type="radio" name="gender3" value="female" class="styledRadio" id="genderF3">
                                        <label for="genderF3"><span><span></span></span> Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="customFieldPreviewCon"></div><?php /**/ ?>
                        </div>
                        <div class="captchaConPreview hideFormSec">
                            <img src="<?php echo $imageFolderUrl;?>google-recaptcha-preview.png" alt="recaptcha" />
                        </div>
                        <div><input type="submit" name="submit" value="" class="submitButtonPreview"></div>
                        <div class="hasCampMonLogoPreview"><img src="<?php echo $imageFolderUrl;?>PowerdByCampMon@2x.png" style="width:180px;" alt="" /></div>
                    </div>



                </div></div></div>



        <div style="display:none;" class="modal_bg" id="modal_bg"></div>
        <div style="display:none;z-index:500" class="modal_container" id="modal_container_1">
            <div class="modal_container" id="modal_container_2">
                <div class="close_button_con"><button onclick="close_list_fields();"><img src="<?php echo $imageFolderUrl; ?>X@2x.png" style="width:12px;height:12px;" /></button></div>
                <div class="modal_content" id="modal_header"></div>
                <div class="modal_content" id="modal_loading">
                    <div class="separator separator_top"></div>
                    <?php /*<img src="<?php echo $LOADING_IMG_URL; ?>">*/ ?>
                    <div class="separator separator_bottom"></div>
                </div>
                <div class="modal_content" id="modal_content"></div>
            </div>
        </div>


        <style>


            #signupFormPreviewCon div div { text-align:left; max-width:300px; margin: 0 auto; }

            #signupFormPreviewCon > div > div
            {
                text-align:left; max-width:300px; margin: 0 auto;
            }

            #signupFormPreviewCon > div > div.hasCampMonLogoPreview img
            {
                margin: 10px auto 0 auto;
                display:block;
            }


            #signupFormPreviewCon .fieldWrap > .customFieldPreviewCon > div
            {
                display: block;
                text-align: left;
                margin: 6px auto;
                max-width:300px;
            }

            #signupFormPreviewCon .customFieldPreviewCon ul {
                list-style-type:none; margin:0;}
            #signupFormPreviewCon #signupFormPreview_bar input[type="checkbox"] {
                width:1rem;
            }
            #signupFormPreviewCon #signupFormPreview_bar .customFieldPreviewCon ul li { display:inline-block; margin: auto 0.5em;}
            #signupFormPreviewCon div.customFieldPreviewCon ul li input[type="checkbox"] { width:1rem; margin:0; }
            #signupFormPreviewCon .fieldWrap > div.customFieldPreviewCon:not(.hideFormSec) ul li { text-align:left; }

            #formStatusCon > div,
            #formPlacementCon > div,
            #formCaptchaOptionCon > div
            { display:inline-block;width:47%;padding:3px 0; }


            #signupFormPreviewCon .captchaConPreview
            {
                text-align:center;
            }
            #signupFormPreviewCon .captchaConPreview img
            {
                width:300px;
                max-width:100%;
            }
            #signupFormPreviewCon #signupFormPreview_bar div div {
                max-width: none;
                display:inline-block;
                width: auto;
            }
            #signupFormPreviewCon #signupFormPreview_bar select {
                max-width: none;
                display:inline-block;
                width: auto;
                margin: auto 0.3em;
            }
            #signupFormPreviewCon #signupFormPreview_bar .captchaConPreview img
            {
                width:280px;
                vertical-align:middle;
                margin-right:10px;
            }

            #signupFormPreviewCon  #signupFormPreview_bar > div > div {
                width: auto;
                display: inline-block;
                margin: auto 0.5em;

            }
            #signupFormPreviewCon  #signupFormPreview_bar .customFieldPreviewCon {
                width: auto;
                max-width: 100%;

            }

            #signupFormPreviewCon #signupFormPreview_bar .submitButtonPreview
            {
                padding-top:0;
                padding-bottom:0;
                height: 28px;
                margin-top:-4px;
                margin-bottom:0;
            }


            #formPlacementBarCon > div { display:block;padding:3px 0; }

            #signupFormPreviewCon.signupFormPreviewCon_slideoutTab .formHeaderPreview, #signupFormPreviewCon.signupFormPreviewCon_slideoutTab .formSubHeaderPreview,
            #signupFormPreviewCon.signupFormPreviewCon_lightbox .formHeaderPreview,    #signupFormPreviewCon.signupFormPreviewCon_lightbox .formSubHeaderPreview,
            #signupFormPreviewCon.signupFormPreviewCon_embedded .formHeaderPreview,    #signupFormPreviewCon.signupFormPreviewCon_embedded .formSubHeaderPreview
            {
                word-wrap: break-word;
                width:300px;
                margin-left: auto;
                margin-right: auto;
                text-align:center;
            }

            #signupFormPreviewCon.signupFormPreviewCon_slideoutTab .formHeaderPreview,
            #signupFormPreviewCon.signupFormPreviewCon_lightbox .formHeaderPreview,
            #signupFormPreviewCon.signupFormPreviewCon_embedded .formHeaderPreview {
                margin:6px auto 0;
            }

            #signupFormPreviewCon.signupFormPreviewCon_slideoutTab .formSubHeaderPreview,
            #signupFormPreviewCon.signupFormPreviewCon_lightbox .formSubHeaderPreview,
            #signupFormPreviewCon.signupFormPreviewCon_embedded .formSubHeaderPreview {
                margin: 0 auto 0;
            }

            #formFieldUl .styledCheckbox + label span:nth-child(1),
            #formFieldUl .styledRadio + label span:nth-child(1)
            {
                position:relative;
                top:2px;
            }

            #signupFormPreview_slideoutTab .cmSlideOutTab {
                position:absolute;
                display: block;
                left: -24.5%;
                top: 71px;
                min-width: 165px;
                -webkit-transform: rotate(270deg);
                -moz-transform: rotate(270deg);
                -ms-transform: rotate(270deg);
                -o-transform: rotate(270deg);
                transform: rotate(270deg);
                border-top-left-radius: 4px;
                border-top-right-radius: 4px;
                padding:7px;
            }


            #signupFormPreview_slideoutTab.cmApp_placementLeft .cmSlideOutTab {
                -webkit-transform: rotate(90deg);
                -moz-transform: rotate(90deg);
                -ms-transform: rotate(90deg);
                -o-transform: rotate(90deg);
                transform: rotate(90deg);
                left: 83.5%;
            }

            #signupFormPreview_slideoutTab .cmSlideOutTab #slideoutButton {
                display: block;
                text-align:center;
                color:#fff;
                white-space:nowrap;
                margin-left: auto;
                margin-right: auto;
                vertical-align: baseline;
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 14px;
                text-decoration:none;
            }

            <?php if (strlen($submitButtonText) > 12 ) :?>

            #signupFormPreview_slideoutTab .cmSlideOutTab #slideoutButton {
                font-size:11px;
                letter-spacing: 1px;
                line-height: 18px;
            }
            <?php endif; ?>


            #signupFormPreview_slideoutTab .cmSlideOutTab #slideoutButton:hover,
            #signupFormPreview_slideoutTab .cmSlideOutTab #slideoutButton:active,
            #signupFormPreview_slideoutTab .cmSlideOutTab #slideoutButton:visited {
                text-decoration:none;
            }


            .cf:after {
                visibility: hidden;
                display: block;
                font-size: 0;
                content: " ";
                clear: both;
                height: 0;
            }

            .newPageInputCon > div > div.cmRemovePage,
            {
                text-align:right;
            }

            #signupFormForm input[type="text"], #signupFormForm select, #signupFormForm textarea
            {
                max-width:100%;
                width:100%;
            }

            #signupFormPreviewCon .fieldWrap {
                margin:16px auto 0 ;
                text-align:center;
                display:block;
            }

            #signupFormPreviewCon .fieldWrap > div:not(.hideFormSec) {
                display:block;
                text-align:center;
                margin:6px auto;
            }

            #signupFormPreviewCon input[type="text"], #signupFormPreviewCon select, #signupFormPreviewCon textarea
            {
                max-width:100%;
            }

            #signupFormPreviewCon select { width:300px; }

            #signupFormForm .colorInputCon input[type="text"] { width:80px; }

            .colorInputCon { display:table;border:1px solid #CCC;border-radius:2px; }
            .colorInputCon > div { display:table-cell;vertical-align:middle; }
            .colorInputCon > div:first-child { padding:2px 1px 2px 0; }
            .colorInputCon > div:last-child { padding:2px 8px;border-left:1px solid #CCC; }
            .colorInputCon input { margin:0;border:none; }
            .colorInputCon input[type="text"], .colorInputCon input[type="text"]:focus, .colorInputCon input[type="text"]:hover { border:none; }
            .colorInputCon input[type="text"] { width:80px;height:auto;min-height:30px; }
            .colorInputCon input[type="color"] { padding:0;max-width:35px;display:block; }

            #signupFormForm .remove_page_img { display:inline-block;padding:5px 0; }
            #signupFormForm .remove_page_img img { width:10px;height:10px;margin-left:5px;cursor: pointer; position: relative; /*left: 9px;*/ }

            #signupFormPreviewCon
            {
                background-color:#AAA;
                padding:50px 10px 10px 10px;
                text-align:center;
            }

            #formDesignMainCon { display:table;width:100%;}
            #formDesignMainCon > div { display:table-row;}
            #formDesignMainCon > div > div { display:table-cell;background-color:#FFF;}


            @media (max-width: 500px) {
                #formDesignMainCon,
                #formDesignMainCon > div,
                #formDesignMainCon > div > div
                {
                    display:block;
                }
            }


            #signupFormForm > div
            {
                margin-bottom:12px;
            }

            #signupFormForm .mainInfo strong
            {
                font-weight:bold;
            }
            #signupFormForm > div > label
            {
                display:block;
            }

            ul#formFieldUl
            {
                margin:0;
                padding:0;
            }
            ul#formFieldUl li
            {
                padding:2px 0 2px 22px;
                text-indent:-22px;
                list-style-type: none;
            }

            #signupFormForm input[type="text"], #signupFormForm select
            {
                display:inline-block;
                margin:2px 0;
            }


            .newPageInputCon        {   display:table;  width:100%;    }
            .newPageInputCon > div  {   display:table-row; }
            .newPageInputCon > div > div {
                display:table-cell;
                text-align:left;
                vertical-align:middle;
            }

            /*.newPageInputCon .btn_red { margin-left:5px; }*/


            #signupFormPreviewCon > div
            {
                display:none;
                position:relative;
            }

            #signupFormPreviewCon.signupFormPreviewCon_bar          { background-color:#DDD; position:relative; padding:0; }
            #signupFormPreviewCon.signupFormPreviewCon_lightbox     { background-color:#AAA; padding:0 10px 10px 10px;text-align:center; }
            #signupFormPreviewCon.signupFormPreviewCon_embedded     { background-color:#DDD; padding:0; text-align:center; }
            #signupFormPreviewCon.signupFormPreviewCon_slideoutTab  { background-color:#DDD; position:relative; padding:0; }


            #signupFormPreviewCon.signupFormPreviewCon_bar .formHeaderPreview {
                margin-top:15px;
                padding-right:10px;
            }


            #signupFormPreviewCon .cmCloseFormButton:link,
            #signupFormPreviewCon .cmCloseFormButton:visited,
            #signupFormPreviewCon .cmCloseFormButton:hover,
            #signupFormPreviewCon .cmCloseFormButton:active,
            #signupFormPreviewCon .cmCloseFormButton:focus,
            #signupFormPreviewCon .cmCloseFormButton {
                cursor: pointer;
                position: absolute;

                right: 9px;
                top:9px;

                z-index: 12;
                display: inline-block;
                background-image: url('<?php echo $imageFolderUrl; ?>/X@2x.png');
                background-size: 10px 10px;
                background-repeat: no-repeat;
                width:9px;
                height:9px;
                /*
                min-height: 18px;
                min-width: 18px;
                */
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                -o-user-select: none;
                user-select: none;
                text-decoration: none;
            }

            #signupFormPreviewCon #signupFormPreview_bar .cmCloseFormButton {
                top: 40px;
            }


            #signupFormPreviewCon input { width:300px; }

            #signupFormPreviewCon #signupFormPreview_bar input {
                width:150px;
            }

            #signupFormPreviewCon #signupFormPreview_bar input[type="submit"] {
                min-width:120px;
            }

            #signupFormPreviewCon textarea {
                width:300px;
                height:80px;
                margin-top:0;
                padding:5px 5px;
            }

            #signupFormPreview_bar textarea {
                width:200px;
                height:38px;
            }

            #signupFormPreviewCon .hideFormSec { display:none; }

            #signupFormPreview_lightbox > div,
            #signupFormPreview_embedded > div,
            #signupFormPreview_slideoutTab > div
            {
                display:block;
                text-align:center;
                margin:6px 0;
            }


            #signupFormPreview_lightbox .cmSignupGenderFields {
                width:300px;
                margin: 0 auto;
                padding: 0 10px 0;
            }

            #signupFormPreview_lightbox .cmSignupGenderFields .spread_2to1,
            #signupFormPreview_embedded .cmSignupGenderFields .spread_2to1,
            #signupFormPreview_slideoutTab .cmSignupGenderFields .spread_2to1 {
                width:50%;
                float:left;
            }

            #signupFormPreview_bar .cmSignupGenderFields {
                width:150px;
                margin:0 auto;
                padding:0px;
            }

            #signupFormPreview_bar .cmSignupGenderFieldsContainer {
                width:50%;
                min-width:140px;
                margin-top:7px;
            }

            #signupFormPreview_embedded .cmSignupGenderFields {
                width:300px;
                margin:0 auto;
                padding:0px;
            }

            #signupFormPreview_slideoutTab .cmSignupGenderFields {
                width:300px;
                margin:0 auto;
                padding:0px;
            }


            #signupFormPreview_lightbox .cmSignupGenderFields input,
            #signupFormPreview_bar .cmSignupGenderFields input,
            #signupFormPreview_embedded .cmSignupGenderFields input,
            #signupFormPreview_slideoutTab .cmSignupGenderFields input {
                width:auto;
                float:left;
            }

            #signupFormPreview_lightbox .cmSignupGenderFields label,
            #signupFormPreview_bar .cmSignupGenderFields label,
            #signupFormPreview_embedded .cmSignupGenderFields label,
            #signupFormPreview_slideoutTab .cmSignupGenderFields label {
                margin:3px 0;
                float:left;
            }

            .cmSignupGenderFieldsContainer {
                width:150px;
                margin: 0 auto;
            }

            #signupFormPreview_lightbox .cmSignupGenderFields label:nth-child(even),
            #signupFormPreview_bar .cmSignupGenderFields label:nth-child(even),
            #signupFormPreview_embedded .cmSignupGenderFields label:nth-child(even),
            #signupFormPreview_slideoutTab .cmSignupGenderFields label:nth-child(even) {
                margin-left:10px;
            }

            #signupFormPreview_bar > div
            {
                display:inline-block;
                margin:6px 4px 6px 0;
                max-width:100%;
            }

            #signupFormPreviewCon .submitButtonPreview
            {
                max-width: 100%;
                height:38px;
                display: inline-block;
                padding: 8px 18px;
                margin:14px 0 0;
                font-size: 14px;
                font-weight: normal;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                cursor: pointer;
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
                user-select: none;
                background-image: none;
                text-decoration: none;
                border-radius: 4px;
                letter-spacing: 1px;
                border:none;
            }

            #signupFormPreview_bar .submitButtonPreview {
                margin:0;
            }



            #signupFormPreview_lightbox
            {
                display:block;
                width:80%;
                max-width:400px;
                min-width:300px;
                background-color:#FFF;
                padding:25px 10px;
                margin:100px auto 0 auto;
                text-align:center;
                border-radius:4px;
            }

            #signupFormPreview_embedded
            {
                display:block;
                width:80%;
                max-width:400px;
                min-width:300px;
                background-color:#FFF;
                padding:10px;
                margin:100px auto 0 auto;
                text-align:center;
            }

            #signupFormPreview_slideoutTab
            {
                position:absolute;
                display:block;
                width:80%;
                max-width:400px;
                min-width:300px;
                background-color:#FFF;
                padding:25px 50px;
                margin:0 0 0 auto;
                text-align:center;
                right:0;
                top:50px;
            }

            #signupFormPreviewCon > div#signupFormPreview_bar.cmApp_placementBottom
            {
                position:absolute;
                /*top:auto;
                bottom:0;*/
                top:740px;
            }

            #signupFormPreview_slideoutTab.cmApp_placementBottom
            {
                top:580px;
            }

            #signupFormPreview_slideoutTab.cmApp_placementLeft
            {
                left:0;
                right:auto;
                margin: 0 auto 0 0 ;
            }

            #signupFormPreview_bar
            {
                position:absolute;
                display:block;
                width:100%;
                /*max-width:400px;
                min-width:300px;*/
                background-color:#FFF;
                padding:20px;
                /*margin:0 auto;*/
                margin:0;
                text-align:left;
                left:0;
                top:0;
            }


            #formAppearsLightboxCon > label   {   padding-bottom:4px;     }
            #formAppearsLightboxCon label.isDisabled    {   color:#666; }
            #formAppearsLightboxCon label input[type="text"] { width:45px;height:26px; }
            #formAppearsLightboxCon label.isDisabled input[type="text"] { background-color:#EEE;color:#666; }









            .modal_bg {
                display: none;
                width: 100%;
                height: 100%;
                position: fixed;
                left: 0;
                top: 0;
                background-color: #000;
                opacity: .4;
                z-index: 1;
            }

            #modal_container_1 {
                display: none;
                height: 100%;
                width: 100%;
                position: absolute;
                left: 0;
                top: 0;
                z-index: 2;
            }

            #modal_container_2 {
                padding: 20px;
                width: 800px;
                max-width: 95%;
                /*max-height:400px;*/
                min-height: 200px;
                overflow-y: auto;
                overflow-x: hidden;
                border-radius: 2px;
                background-color: #FFF;
                opacity: 1;
                position: relative;
                margin: 40px auto 10px auto;
            }

            #modal_content table {
                width: 100%;
                margin-bottom: 20px;
            }

            #modal_content table td, #modal_content table th {
                padding: 15px 12px;
            }

            #modal_loading {
                text-align: center;
            }

            .modal_container .separator {
                height: 0px;
                border-top: 1px solid #EEE;
                width: 800px;
                width: calc(100% + 40px);
                margin: 25px -20px;
            }

            #modal_spinner_only {
                position: fixed;
                text-align: center;
                z-index: 5;
                width: 100%;
                top: 100px;
                left: 0;
            }

            .modal_container .separator.separator_top {
                margin-bottom: 10px;
            }

            .modal_container .separator.separator_bottom {
                margin-top: 10px;
            }

            .msg_info, .msg_success, .msg_error {

                border-width: 1px;
                border-style: solid;
                border-radius: 2px;
                /*font-size: 1.1em;
                font-weight: bold;*/
                padding: 18px;
                width: 100%;
                max-width: 1200px;
                margin: 0px auto 20px auto;
            }

            .msg_info {
                background-color: #C9E9F8;
                color: #000;
                border-color: #B5D2E0;
            }

            .msg_success {
                background-color: #DED;
                color: #3C763D;
                border-color: #BCB;
            }

            .msg_error {
                color: #000;
                background-color: #f99;
                border-color: #f88;
            }

            .msg_error span {
                vertical-align: middle;
            }

            .close_button_con button {
                border: none;
                background-color: transparent;
                color: #888;
                cursor: pointer;
                font-weight: bold;
            }

            .close_button_con {
                display: inline-block;
                float: right;
                margin-right: -15px;
                margin-top: -12px;
            }

            .close_button:hover {
                color: #000;
                text-shadow: 1px 1px 1px #888;
            }

            #formDesignMainCon *, #modal_bg *, #modal_container_1 * {
                box-sizing: border-box;
            }

            input[type="radio"].styledRadio {
                display: none;
            }

            .styledRadio + label {
                position: relative;
                top: -4px;
            }

            .styledRadio + label span:nth-child(1) {
                position: relative;
                top: 4px;
                display: inline-block;
                width: 18px;
                height: 18px;
                border: 1px solid #bfbfbf;
                cursor: pointer;
                background: white;
                border-radius: 100%;
            }

            .styledRadio:checked + label span:nth-child(1) {
                background-color: white;
            }

            .styledRadio + label span:nth-child(1) span {
                border: none;
                position: absolute;
                left: 0;
                top: 0;
                height: 16px;
                width: 16px;
                background: white;
                -ms-transform: scale(0, 0); /* IE 9 */
                -webkit-transform: scale(0, 0); /* Safari */
                transform: scale(0, 0);

            }

            .styledRadio:checked + label span:nth-child(1) span {

                background: #429bd1;
                -webkit-transition: background-color 100ms linear, -webkit-transform 100ms linear;
                -moz-transition: background-color 100ms linear, -moz-transform 100ms linear;
                -o-transition: background-color 100ms linear, -o-transform 100ms linear;
                -ms-transition: background-color 100ms linear, -ms-transform 100ms linear;
                transition: background-color 100ms linear, transform 100ms linear;

                -ms-transform: scale(.6, .6); /* IE 9 */
                -webkit-transform: scale(.6, .6); /* Safari */
                transform: scale(0.6, 0.6);
            }




        </style>

        <script>
            // no conflicts
            (function( $z ) {

                var maxPageOnCount=<?php echo intval($maxPageOnCount); ?>;
                var maxFieldCount=<?php echo intval($maxFieldCount); ?>;




                window.formPositionBarUpdate = function()
                {
                    var formPlacementBar = $z('#formPlacementBarCon').find('input[name=formPlacementBar]:checked').val();

                    var isBottom=0;
                    if (formPlacementBar=="bottom")
                    {
                        isBottom=1;
                    }

                    var elem=$z("#signupFormPreview_bar");

                    if (isBottom)
                    {
                        elem.addClass("cmApp_placementBottom");
                    }
                    else
                    {
                        elem.removeClass("cmApp_placementBottom");
                    }
                };

                window.formPositionUpdate = function()
                {
                    var formPlacement = $z('#formPlacementCon').find('input[name=formPlacement]:checked').val();

                    var isBottom=0;
                    var isLeft=0;

                    if (formPlacement=="topLeft")
                    {
                        isLeft=1;
                    }
                    else if (formPlacement=="bottomLeft")
                    {
                        isBottom=1;
                        isLeft=1;
                    }
                    else if (formPlacement=="bottomRight")
                    {
                        isBottom=1;
                    }

                    var elem=$z("#signupFormPreview_slideoutTab");

                    if (isLeft)
                    {
                        elem.addClass("cmApp_placementLeft");
                    }
                    else
                    {
                        elem.removeClass("cmApp_placementLeft");
                    }
                    if (isBottom)
                    {
                        elem.addClass("cmApp_placementBottom");
                    }
                    else
                    {
                        elem.removeClass("cmApp_placementBottom");
                    }
                };

                window.hexTextValUpd = function(jqElem, isKeyOnly)
                {
                    var elemId = jqElem.attr("id");

                    var theVal=jqElem.val().replace("#","").trim();

                    var ar=["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"];

                    var newHex="";
                    for (var x=0; x < theVal.length && x<6; x++)
                    {
                        var theChar=theVal.substr(x,1).toLowerCase();
                        if (ar.indexOf(theChar)>=0)
                        {
                            newHex=newHex+theChar;
                        }
                        else
                        {
                            newHex=newHex+"0";
                        }
                    }
                    for (x=newHex.length; x<6; x++)
                    {
                        newHex=newHex+"0"; // 0 fill additional values
                    }

                    $z("#"+elemId+"Col").val("#"+newHex);

                    if (!isKeyOnly)
                    {
                        $z("#"+elemId).val("#"+newHex);
                    }
                    updatePreviewForm();
                };

                window.hexColValUpd = function(jqElem)
                {
                    var elemId = jqElem.attr("id");
                    var newHex=jqElem.val().replace("#","").trim();
                    var txtElemId=elemId.substring(0,elemId.length-3);
                    $z("#"+txtElemId).val("#"+newHex);
                    updatePreviewForm();
                };


                window.removeAppearsOnPageSelect = function(jqElem)
                {
                    var elemId = jqElem.parent().parent().attr("id");
                    var theNum=elemId.replace("formPageOn_","").replace("_con","");

                    $z('#formPageOn_'+theNum)[0].selectedIndex = 0;

                    var jqElems=$z('#signupFormForm').find('.newPageInputCon select');

                    if (jqElems.length>1)
                    {
                        $z('#formPageOn_'+theNum+"_con").remove();
                    }

                    jqElems=$z('#signupFormForm').find('.newPageInputCon select');

                    if (jqElems.length < maxPageOnCount)
                    {
                        $z("#addAppearsOnPageButton").css("display","table-cell");
                    }
                };


                window.addAppearsOnPageSelect = function()
                {
                    var suffixNum=2;
                    var jqElem=$z("#formPageOn_"+suffixNum);
                    while ($z("#formPageOn_"+suffixNum).length>0)
                    {
                        suffixNum++;
                        if (suffixNum > maxPageOnCount)
                        {
                            $z("#addAppearsOnPageButtonCon").css("display","none");
                            return false;
                        }
                    }

                    var firstJqElem=$z('#signupFormForm').find('.newPageInputCon select').first();

                    <?php /*$z("#addAppearsOnPageButtonCon").before("<div id='formPageOn_"+suffixNum+"_con'><div><select id='formPageOn_"+suffixNum+"' name='formPageOn_"+suffixNum+"'></select></div><div><a href=\"#'\" class=\"btn_red btn_small\">&#10006;</a></div></div>");*/ ?>
                    $z("#addAppearsOnPageButtonCon").before("<div id='formPageOn_"+suffixNum+"_con'><div><select id='formPageOn_"+suffixNum+"' name='formPageOn_"+suffixNum+"'></select></div><div class='cmRemovePage'><a href='#' class='remove_page_img'><img src='<?php echo $imageFolderUrl; ?>X@2x.png' /></a></div></div>");
                    $z('#formPageOn_'+suffixNum).html(firstJqElem.html());
                    $z('#formPageOn_'+suffixNum)[0].selectedIndex = 0;

                    $z("#formPageOn_"+suffixNum+"_con").find(".remove_page_img").on('click', function(event ) { // bind the remove button
                        event.preventDefault();
                        removeAppearsOnPageSelect($z(this));
                    });

                    if (suffixNum == maxPageOnCount)
                    {
                        $z("#addAppearsOnPageButton").css("display","none");
                    }
                };

                var currentFormType = "";

                window.selectInputText =function(e)
                {
                    e.setSelectionRange(0, e.value.length);
                };

                window.formTypeUpdate = function()
                {
                    var formType = $z('#formType').val();


                    if (formType.toLowerCase()=='embedded' )
                    {
                        $z("#formEmbedCodeCon").show();
                    }
                    else
                    {
                        $z("#formEmbedCodeCon").hide();
                    }

                    if (formType.toLowerCase()=='lightbox')
                    {
                        $z("#formAppearsLightboxCon").show();
                        lighboxAppearsUpdate();
                    }
                    else
                    {
                        $z("#formAppearsLightboxCon").hide();
                    }

                    if (formType.toLowerCase()=="button")
                    {
                        $z("#formEmbedCodeCon").show();
                        formType="lightbox";
                    }

                    if (formType == currentFormType) {
                        return false;
                    }
                    if (currentFormType.length>0)
                    {
                        $z("#signupFormPreview_"+currentFormType).hide();
                        $z("#signupFormPreviewCon").removeClass("signupFormPreviewCon_"+currentFormType);
                        //alert("hide "+currentFormType);
                    }
                    currentFormType=formType;
                    $z("#signupFormPreviewCon").addClass("signupFormPreviewCon_" + formType);
                    $z("#signupFormPreview_"+formType).fadeIn(300);

                    if (formType.toLowerCase()=='bar')
                    {
                        $z("#subHeaderInputCon").hide();
                    }
                    else
                    {
                        $z("#subHeaderInputCon").show();
                    }




                    if (formType.toLowerCase()=='slideouttab')
                    {
                        $z("#formPlacementCon").show();
                    }
                    else
                    {
                        $z("#formPlacementCon").hide();
                    }

                    if (formType.toLowerCase()=='bar')
                    {
                        $z("#formPlacementBarCon").show();
                    }
                    else
                    {
                        $z("#formPlacementBarCon").hide();
                    }




                    //alert("add class signupFormPreviewCon_"+currentFormType);
                    //alert("show "+formType);
                };

                window.lighboxAppearsUpdate = function()
                {

                    if ($z("#formAppearsLightboxSeconds").is(':checked'))
                    {
                        $z("#formAppearsLightboxSecondsLabel").removeClass("isDisabled");
                        $z("#formAppearsLightboxScrollLabel").addClass("isDisabled");
                    }
                    else if ($z("#formAppearsLightboxScroll").is(':checked'))
                    {
                        $z("#formAppearsLightboxSecondsLabel").addClass("isDisabled");
                        $z("#formAppearsLightboxScrollLabel").removeClass("isDisabled");
                    }
                };

                window.updatePreviewForm = function()
                {

                    var theVal="";

                    var formConJq=$z("#signupFormPreviewCon");

                    theVal=$z("#formHeader").val();
                    if (theVal.length > 0)
                    {
                        formConJq.find(".formHeaderPreview").css("display","inline-block").css('font-size', '18px').html(""+htmlSpecialDecodeEncode(theVal)+"");
                    }
                    else
                    {
                        formConJq.find(".formHeaderPreview").css("display","none").html("");
                    }

                    theVal=$z("#formSubHeader").val();
                    if (theVal.length > 0)
                    {
                        formConJq.find(".formSubHeaderPreview").css("display","block").css('font-size', '14px').html(htmlSpecialDecodeEncode(theVal));
                    }
                    else
                    {
                        formConJq.find(".formSubHeaderPreview").css("display","none").html("");
                    }

                    var theLabel=$z("#openTextFieldLabel").val();
                    $z(".hasOpenTextFieldPreview textarea").attr("placeholder", theLabel);

                    showFieldIfChecked(formConJq, "hasNameField");
                    showFieldIfChecked(formConJq, "hasEmailField");
                    showFieldIfChecked(formConJq, "hasDateOfBirthField");
                    showFieldIfChecked(formConJq, "hasGenderField");
                    showFieldIfChecked(formConJq, "hasOpenTextField");
                    showFieldIfChecked(formConJq, "hasCampMonLogo");

                    if ($z("#hasCaptchaOn").is(':checked'))
                    {
                        formConJq.find(".captchaConPreview").removeClass("hideFormSec");
                    }
                    else
                    {
                        formConJq.find(".captchaConPreview").addClass("hideFormSec");
                    }

                    if ($z("#hasNameFieldLabel").is(':checked'))
                    {
                        formConJq.find(".hasNameLabelFieldPreview").removeClass("hideFormSec");
                        formConJq.find(".hasNameFieldPreview input").attr("placeholder", "");
                    }
                    else
                    {
                        formConJq.find(".hasNameLabelFieldPreview").addClass("hideFormSec");
                        formConJq.find(".hasNameFieldPreview input").attr("placeholder", "Name");
                    }

                    if ($z("#hasEmailFieldLabel").is(':checked'))
                    {
                        formConJq.find(".hasEmailLabelFieldPreview").removeClass("hideFormSec");
                        formConJq.find(".hasEmailFieldPreview input").attr("placeholder", "");
                    }
                    else
                    {
                        formConJq.find(".hasEmailLabelFieldPreview").addClass("hideFormSec");
                        formConJq.find(".hasEmailFieldPreview input").attr("placeholder", "Email *");
                    }


                    //if($z("#hasNameField").is(':checked'))           {   $z("#signupFormPreviewCon").find(".hasNameFieldPreview").css("display","block");       }       else    {   $z("#signupFormPreviewCon").find(".hasNameFieldPreview").css("display","none");  }
                    //if($z("#hasEmailField").is(':checked'))          {   $z("#hasEmailFieldPreview").css("display","block");      }       else    {   $z("#hasEmailFieldPreview").css("display","none");  }
                    //if($z("#hasDateOfBirthField").is(':checked'))    {   $z("#hasDateOfBirthFieldPreview").css("display","block");}       else    {   $z("#hasDateOfBirthFieldPreview").css("display","none");  }
                    //if($z("#hasGenderField").is(':checked'))         {   $z("#hasGenderFieldPreview").css("display","block");     }       else    {   $z("#hasGenderFieldPreview").css("display","none");  }
                    //if($z("#hasOpenTextField").is(':checked'))       {   $z("#hasOpenTextFieldPreview").css("display","block");   }       else    {   $z("#hasOpenTextFieldPreview").css("display","none");  }
                    //if($z("#hasCampMonLogo").is(':checked'))         {   $z("#hasCampMonLogoPreview").css("display","block");     }       else    {   $z("#hasCampMonLogoPreview").css("display","none");  }

                    theVal=$z("#backgroundHexCol").val();
                    //alert(theVal+" !");
                    formConJq.children("div").css("background-color","#"+theVal.replace("#",""));
                    $z("#signupFormPreview_embedded").css("background-color","transparent");

                    var btnJq = formConJq.find(".submitButtonPreview");

                    var theTxt = $z("#submitButtonText").val();



                    var theBgHex = $z("#submitButtonBgHexCol").val();
                    var theTxtHex = $z("#submitButtonTextHexCol").val();
                    var txtHex = $z("#textHexCol").val();
                    var slideOutTab = $z('#slideoutButton');


                    var innerRadioButtons = $z("#signupFormPreviewCon").find('.styledRadio + label span span');
                    innerRadioButtons.css('background-color', theBgHex);

                    btnJq.val(htmlSpecialCharsDecode(theTxt)).css("color",theTxtHex).css("background-color",theBgHex);
                    $z("#signupFormPreview_slideoutTab").find(".cmSlideOutTab").css("color",theTxtHex).css("background-color",theBgHex);
                    slideOutTab.html(htmlSpecialDecodeEncode(theTxt)).css('color', theTxtHex);

                    var theTxtHex = ($z("#textHexCol").val());

                    $z(".formHeaderPreview").css("color",theTxtHex);
                    $z(".formSubHeaderPreview").css("color",theTxtHex);
                    $z(".cmSignupGenderFields label").css("color",theTxtHex);
                    $z("#signupFormPreviewCon").css("color",theTxtHex);

                    $z(".customFieldPreviewCon").html("");

                    $z(".custom-field-container-list .customFieldCon").each(function( index ) {
                        var num = $z( this ).attr("id").replace("customFieldCon","");
                        var labelStr="";
                        var placeHolderStr="";
                        var inputStr="";

                        var showLabel=parseInt($z("#customFieldShowLabel"+num).val());

                        var fieldType=$z("#customFieldType"+num).val();

                        var fieldOptions=$z("#customFieldOptions"+num).val();

                        var fieldLabel=$z("#customFieldLabel"+num).val();

                        var fieldIsRequired=parseInt($z("#customFieldRequired"+num).val());


                        if (fieldLabel.length<1)
                        {
                            fieldLabel=$z("#customFieldName"+num).val();
                        }

                        ///var fieldRequiredStr="";
                        if (fieldIsRequired)
                        {
                            fieldLabel+=" *";
                        }

                        if (showLabel)
                        {
                            labelStr='<div>'+htmlSpecialDecodeEncode(fieldLabel)+'</div>';
                        }
                        else
                        {
                            placeHolderStr=' placeholder="'+htmlSpecialDecodeEncode(fieldLabel)+'"';
                        }

                        // Text, Number, MultiSelectOne, MultiSelectMany, Date, Country and USState
                        if (fieldType=="MultiSelectOne")
                        {

                            str= '<div>';
                            str+='<div>'+htmlSpecialDecodeEncode(fieldLabel)+'</div><select><option value=""></option>';
                            var fieldOptionAr=fieldOptions.split("\n");
                            for (var x=0; x<fieldOptionAr.length ;x++)
                            {
                                str+='<option value="'+x+'">'+htmlSpecialDecodeEncode(fieldOptionAr[x])+'</option>';
                            }
                            str+="</select>";
                            str+="</div>";
                        }
                        else if (fieldType=="MultiSelectMany")
                        {
                            str= '<div>';
                            str+='<ul><li>'+htmlSpecialDecodeEncode(fieldLabel)+'</li>';
                            var fieldOptionAr=fieldOptions.split("\n");
                            var cbIdStart="multiSelect_"+Math.floor((Math.random() * 10000000));
                            for (var x=0; x<fieldOptionAr.length ;x++)
                            {
                                var cbId=cbIdStart+"_"+x;
                                str+='<li><input type="checkbox" value="1" id="'+cbId+'"> <label for="'+cbId+'">'+htmlSpecialDecodeEncode(fieldOptionAr[x])+'</label></li>';
                            }
                            str+="</ul>";
                            str+="</div>";
                        }
                        else if (fieldType=="Date")
                        {
                            var str='<div><div>'+htmlSpecialDecodeEncode(fieldLabel)+'</div><input type="date" value="" /></div>';
                        }
                        else if (fieldType=="Number")
                        {
                            var str='<div><div>'+htmlSpecialDecodeEncode(fieldLabel)+'</div><input type="number" value="" /></div>';
                        }
                        <?php /*
                    // show date as 3 separate inputs
                    else if (fieldType=="Date")
                    {
                        str='<div>'+htmlSpecialChars(fieldLabel)+' (MM/DD/YY)</div>';
                        str+='<div style="width:300px;max-width:100%;margin:0 auto;display:table"><div style="display:table-row">'+
                            '<div style="display:table-cell;"><input type="text" value="" placeholder="MM" style="width:85px;"/></div>' +
                            '<div style="display:table-cell;"> / </div>' +
                            '<div style="display:table-cell;"><input type="text" value="" placeholder="DD" style="width:85px;" /></div>' +
                            '<div style="display:table-cell;"> / </div>' +
                            '<div style="display:table-cell;"><input type="text" value="" placeholder="YYYY" style="width:85px;" /></div>' +
                            '</div></div>';
                    }*/ ?>
                        else if (fieldType=="Country")
                        {
                            str='<div>'+labelStr+' = Country</div>';
                        }
                        else if (fieldType=="USState")
                        {
                            str='<div>'+labelStr+' = USState</div>';
                        }
                        else
                        {
                            var str='<div>'+labelStr+'<input type="text" value=""'+placeHolderStr+' /></div>';
                        }
                        $z(".customFieldPreviewCon").append(str);
                    });<?php /**/ ?>



                    if($z("#hasOpenTextField").is(':checked'))
                    {
                        $z("#openTextFieldLabelCon").slideDown(100);
                    }
                    else
                    {
                        $z("#openTextFieldLabelCon").slideUp(200);
                    }

                    cmPreviewFormHeightUpdate();
                };

                window.showFieldIfChecked = function(formConJq, theId)
                {
                    if($z("#"+theId).is(':checked'))
                    {
                        formConJq.find("."+theId+"Preview").removeClass("hideFormSec");
                        //formConJq.find("."+theId+"Preview").css("display","block");
                        //$z("#signupFormPreview_bar").find("."+theId+"Preview").css("display","inline-block");
                        //$z("#signupFormPreview_lightbox").find("."+theId+"Preview").css("display","block");
                        //$z("#signupFormPreview_embedded").find("."+theId+"Preview").css("display","block");
                        //$z("#signupFormPreview_slideoutTab").find("."+theId+"Preview").css("display","block");
                    }
                    else
                    {
                        //formConJq.find("."+theId+"Preview").css("display","none");
                        //console.log('.'+theId+"Preview");
                        formConJq.find("."+theId+"Preview").addClass("hideFormSec");
                    }
                };

                var formIsSubmitted=0;
                window.validate_signup_form_form = function()
                {
                    if (formIsSubmitted)
                    {
                        return false;
                    }

                    var errorStr="";
                    if ($z("#formName").val().length < 1)
                    {
                        errorStr="Form name is required";
                    }
                    else if($z("#submitButtonText").val().length < 1)
                    {
                        errorStr="Submit Button Text is required";
                    }
                    else if ($z("#hasOpenTextField").prop("checked") && $z("#openTextFieldLabel").val().length<1)
                    {
                        errorStr="Open text field label is required";
                    }

                    if (errorStr.length<1)
                    {
                        var sel=$z(".newPageInputCon").find("select");
                        var pageIsFound=0;
                        for (var x=0; x<sel.length; x++)
                        {

                            if (sel.eq(x).val().length>0)
                            {
                                pageIsFound=1;
                            }
                        }
//                    if (!pageIsFound)
//                    {
//                        errorStr="Please select a page this form appears on.";
//                    }
                    }

                    if (errorStr.length<1)
                    {
                        if ($z("#campaignMonitorListId").val().length<1)
                        {
                            errorStr="Please select a Campaign Monitor Client and List.";
                        }
                    }

                    if (errorStr.length<1)
                    {
                        var formType=$z("#formType").find("option:selected").val();

                        // check lightbox "Form appears" values
                        if (formType.toLowerCase()=="lightbox")
                        {
                            var formAppearsLightbox=$z("#formAppearsLightboxCon").find("input[name=formAppearsLightbox]:checked").val();
                            if (formAppearsLightbox=="seconds")
                            {
                                var sec=$z("#lightboxSeconds").val();
                                var secNum=parseFloat(sec);

                                if (sec.length<1)
                                {
                                    // nothing entered. we'll convert to 0. no error.
                                }
                                else if ((isNaN(secNum) || !isFinite(sec)) || secNum<0)
                                {
                                    errorStr="Please enter a valid Form Appears value";
                                }
                                else if (secNum>999)
                                {
                                    errorStr="Form Appears Seconds must be less than 999";
                                }
                            }
                            else if (formAppearsLightbox=="scroll")
                            {
                                var scroll=$z("#lightboxScrollPercent").val();
                                var scrollNum=parseFloat(scroll);
                                if (scroll.length<1)
                                {
                                    // nothing entered. we'll convert to 0. no error.
                                }
                                else if ((isNaN(scrollNum) || !isFinite(scroll)) || scrollNum<0)
                                {
                                    errorStr="Please enter a valid Form Appears value";
                                }
                                else if (scrollNum>100)
                                {
                                    errorStr="Form Appears Scroll Percent cannot be more than 100%";
                                }
                            }
                        }
                    }

                    if (errorStr.length>0)
                    {
                        //var errorHtml="<div class='msg_error' style='margin-top:15px;'><img src='<?php echo $imageFolderUrl; ?>Crossed-Out-Circle.png' style='vertical-align: middle;width:auto;height:20px;margin-right:8px;'> <span>"+errorStr+"</span></div>";
                        var errorHtml="<div><p>"+errorStr+"</p></div>";

                        //scroll_to_top();
                        $z('html, body').animate({ scrollTop: 0 }, 300);

                        $z("#modal_loading").hide();
                        $z("#modal_spinner_only").hide();
                        $z("#modal_bg").show();

                        $z("#modal_header").html("<h2>Form error<h2>").show();
                        $z("#modal_content").html("<div class='separator'></div>"+errorHtml+"").show();
                        $z("#modal_container_1").show();

                        return false;
                    }

                    //alert("FORM SUBMITTED!");

                    formIsSubmitted=1;
                    return true;
                }

                window.validate_field_form = function()
                {
                    var customFieldNum=parseInt($z("#customFieldNum").val());

                    var customFieldName=$z("#customFieldName").val();
                    var customFieldType=$z("#customFieldType").val();
                    var customFieldKey=$z("#customFieldKey").val();
                    var customFieldLabel=$z("#customFieldLabel").val();
                    var customFieldOptions=$z("#customFieldOptions").val();
                    var customFieldShowLabel=0;
                    var customFieldRequired=0;
                    if ($z("#customFieldShowLabel").is(":checked"))
                    {
                        customFieldShowLabel=1;
                    }
                    if ($z("#customFieldRequired").is(":checked"))
                    {
                        customFieldRequired=1;
                    }

                    if (customFieldName.length<1)
                    {
                        $z("#fieldNameError").html("Custom Field Name is required").show();
                        return false;
                    }

                    add_custom_form_field_html(customFieldName, customFieldType, customFieldKey, customFieldLabel, customFieldOptions, customFieldShowLabel, customFieldRequired, customFieldNum)

                    close_list_fields();

                    // scroll to fields
                    //$z("html, body").animate({ scrollTop: $z('.custom-field-container-list').offset().top-40 }, 300);


                    updatePreviewForm();
                }

                window.add_custom_form_field_html = function(customFieldName, customFieldType, customFieldKey, customFieldLabel, customFieldOptions, customFieldShowLabel, customFieldRequired, customFieldNum)
                {
                    if (customFieldNum>0)
                    {
                        var num=customFieldNum;
                    }
                    else
                    {
                        var num=1;
                        while ($z("#customFieldKey"+num).length>0)
                        {
                            num++;
                        }
                    }

                    if (customFieldKey.length>0)
                    {
                        $z('#addCustomFieldSelect option').each(function() {

                            //console.log("A------------");
                            //console.log(htmlSpecialDecodeEncode($z(this).val()));
                            //console.log(htmlSpecialDecodeEncode(customFieldKey ));
                            if ( htmlSpecialDecodeEncode($z(this).val()) == htmlSpecialDecodeEncode(customFieldKey )) {
                                //console.log("$z(this).remove()");
                                $z(this).remove();
                            }
                        });
                    }

                    //alert(".custom-field-container-list = "+customFieldName);

                    if (customFieldLabel.length > 0)
                    {
                        var nameDisplay = customFieldLabel+" ("+customFieldName+")";
                    }
                    else
                    {
                        var nameDisplay = customFieldName;
                    }

                    var str='<div style="padding-bottom:12px;"><span id="customFieldDisplay'+num+'">'+htmlSpecialDecodeEncode(nameDisplay)+'</span> <div>'+
                        '<a href="javascript:void(0)" onclick="custom_field_form('+num+');">edit</a> &nbsp; '+
                        '<a href="javascript:void(0);" onclick="custom_field_remove('+num+');">disable</a>  &nbsp; '+
                        '<a href="javascript:void(0);" onclick="custom_field_sort('+num+',1);">up</a> '+
                        '<a href="javascript:void(0);" onclick="custom_field_sort('+num+',0);">down</a></div></div>';
                    str+='<input type="hidden" name="customFieldKey[]" id="customFieldKey'+num+'" class="customFieldKey" value="'+htmlSpecialDecodeEncode(customFieldKey)+'" />';
                    str+='<input type="hidden" name="customFieldName[]" id="customFieldName'+num+'" value="'+htmlSpecialDecodeEncode(customFieldName)+'" />';
                    str+='<input type="hidden" name="customFieldLabel[]" id="customFieldLabel'+num+'" value="'+htmlSpecialDecodeEncode(customFieldLabel)+'" />';
                    str+='<input type="hidden" name="customFieldOptions[]" id="customFieldOptions'+num+'" value="'+htmlSpecialDecodeEncode(customFieldOptions)+'" />';
                    str+='<input type="hidden" name="customFieldType[]" id="customFieldType'+num+'" value="'+htmlSpecialDecodeEncode(customFieldType)+'" />';
                    str+='<input type="hidden" name="customFieldShowLabel[]" id="customFieldShowLabel'+num+'" value="'+htmlSpecialDecodeEncode(customFieldShowLabel)+'" />';
                    str+='<input type="hidden" name="customFieldRequired[]" id="customFieldRequired'+num+'" value="'+htmlSpecialDecodeEncode(customFieldRequired)+'" />';


                    var elem=$z("#customFieldCon"+customFieldNum);
                    if (elem.length>0)
                    {
                        elem.html(str);
                    }
                    else
                    {
                        str='<div id="customFieldCon'+num+'" class="customFieldCon">'+str+'</div>';
                        $z(".custom-field-container-list").append(str);
                    }

                    updatePreviewForm();
                }


                window.close_list_fields = function()
                {
                    $z("#modal_container_1").hide();
                    $z("#modal_spinner_only").hide();
                    $z("#modal_content").html("");
                    $z("#modal_bg").fadeOut(200);
                };


                window.add_list_form = function()
                {
                    var url="<?php echo $self; ?>?action=add_client_list";

                    var str="";
                    str+="<div class=\"separator\"></div>";
                    str+="<div class=\"msg_error\" id=\"create_list_error\" style=\"display:none;padding-top:10px; padding-bottom:10px;\"><?php /*<!--div class=\"close_button_con\" style=\"margin-top:-8px;\"><button onclick=\"close_elem('create_list_error');return false;\"><img src="<? echo BASE_SITE_URL; ?>images/x.png" style="width:12px;height:12px;" /></button></div-->*/ ?>";
                    str+="<img src=\"<?php /* echo BASE_SITE_URL;*/ ?>images/Crossed-Out-Circle.png\" style=\"vertical-align: middle;width:auto;height:20px;margin-right:8px;\"> <span></span></div>";
                    str+="<form action=\""+url+"\" method=\"post\" id=\"create_list_form\">";
                    str+="<input type=\"hidden\" name=\"clientId\" value=\"\">";
                    str+="<input type=\"hidden\" name=\"action\" value=\"add_client_list\">";
                    str+="<input type=\"text\" value=\"\" autocomplete=\"off\" style=\"width:100%;\" name=\"newListName\" id=\"newListName\" class=\"newListName\" placeholder=\"Type list title in here\" />";
                    str+="</form>";
                    str+="<div class=\"separator\"></div>";
                    str+="<div style=\"text-align:right;margin-top:8px;\">";
                    str+="<button onclick=\"close_list_fields();\" class=\"btn_white button-secondary\">Cancel</button> &nbsp; ";
                    str+="<button id=\"createCustomList\" class=\"button-primary btn_blue\" name=\"submit\">Create list</button>";
                    str+="</div>";

                    $z('html, body').animate({ scrollTop: 0 }, 300);

                    $z("#modal_loading").hide();
                    $z("#modal_header").html("<h1>Create List</h1>");
                    $z("#modal_content").html(str).show();
                    $z("#modal_container_1").fadeIn(800);
                    $z("#modal_bg").fadeIn(300);
                };

                window.custom_field_remove = function(num)
                {
                    // make sure element exists before removing
                    var elem=$z("#customFieldCon"+num);
                    if (elem.length<1)
                    {
                        return false;
                    }

                    // get the key of field to remove
                    var fieldKey=$z("#customFieldKey"+num).val();

                    // remove from the list
                    elem.remove();


                    // add back to select options
                    var sel=$z("#addCustomFieldSelect");

                    var keyFound = 0;


                    sel.find('option').each(function(){
                        //console.log("c------------");
                        //console.log(this.value);
                        //console.log(fieldKey);
                        if (this.value == fieldKey) {
                            keyFound = 1;
                            //console.log("keyFound");
                        }
                    });

                    if (!keyFound)
                    {
                        for (var x=0; x<customFieldInfo.length; x++)
                        {
                            //console.log("b------------");
                            //console.log(htmlSpecialDecodeEncode(customFieldInfo[x].Key));
                            //console.log(htmlSpecialDecodeEncode(fieldKey));
                            if (htmlSpecialDecodeEncode(customFieldInfo[x].Key)==htmlSpecialDecodeEncode(fieldKey))
                            {
                                //console.log("append");
                                $z("#addCustomFieldSelect").append( $z('<option></option>').val(customFieldInfo[x].Key).html(htmlSpecialDecodeEncode(customFieldInfo[x].FieldName)) ).prop('selected',true);
                                //return true;
                                break;
                            }
                        }
                    }
                    updatePreviewForm();
                };

                window.custom_field_sort = function(num,isUp)
                {
                    var cfElem=$z(".custom-field-container-list").find(".customFieldKey");
                    var idPrefix="customFieldKey";
                    var theKey=-1;
                    for (var x=0; x<cfElem.length; x++)
                    {
                        var theNum=$z(cfElem[x]).attr("id").replace(idPrefix, "");
                        if (theNum==num)
                        {
                            theKey=x;
                            break;
                        }
                    }
                    if (theKey<0)
                    {
                        return false;
                    }

                    var x1, x2;
                    if (isUp)
                    {
                        if (theKey<=0)
                        {
                            // trying to move the first item up
                            return false;
                        }
                        x1=$z(cfElem[theKey-1]).attr("id").replace(idPrefix, "");
                        x2=$z(cfElem[theKey]).attr("id").replace(idPrefix, "");
                    }
                    else
                    {
                        if (theKey>=cfElem.length-1)
                        {
                            // trying to move the last item down
                            return false;
                        }
                        x1=$z(cfElem[theKey]).attr("id").replace(idPrefix, "");
                        x2=$z(cfElem[theKey+1]).attr("id").replace(idPrefix, "");
                    }

                    switchElemVals("customFieldDisplay"+x1, "customFieldDisplay"+x2, 1);
                    switchElemVals("customFieldKey"+x1, "customFieldKey"+x2, 0);
                    switchElemVals("customFieldName"+x1, "customFieldName"+x2, 0);
                    switchElemVals("customFieldLabel"+x1, "customFieldLabel"+x2, 0);
                    switchElemVals("customFieldOptions"+x1, "customFieldOptions"+x2, 0);
                    switchElemVals("customFieldType"+x1, "customFieldType"+x2, 0);
                    switchElemVals("customFieldShowLabel"+x1, "customFieldShowLabel"+x2, 0);
                    switchElemVals("customFieldRequired"+x1, "customFieldRequired"+x2, 0);
                    updatePreviewForm();
                };

                window.switchElemVals = function(id1, id2, isHtml)
                {
                    if (typeof isHtml=="undefined")
                    {
                        isHtml=0;
                    }

                    var elem1=$z("#"+id1);
                    var elem2=$z("#"+id2);
                    if (isHtml)
                    {
                        var temp=elem1.html();
                        elem1.html( elem2.html() );
                        elem2.html( temp );
                    }
                    else
                    {
                        var temp=elem1.val();
                        elem1.val( elem2.val() );
                        elem2.val( temp );
                    }
                };

                window.custom_field_form = function(fieldNum)
                {
                    if (typeof fieldNum === 'undefined') { fieldNum = 0; }

                    if (fieldNum==0)
                    {
                        var fieldKey = $z("#addCustomFieldSelect").val();

                        if (fieldKey.length < 1)
                        {
                            var elem = $z(".custom-field-container-list").find(".customFieldCon");

                            if (elem.length >= <?php echo intval($maxFieldCount); ?>)
                            {
                                //alert("Field can not be added to list. List limit of <?php echo intval($maxFieldCount); ?> custom fields reached.");
                                return false;
                            }
                        }
                    }

                    var url="<?php echo $self; ?>?action=add_client_list";

                    var str="";
                    str+="<div class=\"msg_error\" id=\"create_list_error\" style=\"display:none;padding-top:10px; padding-bottom:10px;\">";
                    str+="<img src=\"<?php /* echo BASE_SITE_URL;*/ ?>images/Crossed-Out-Circle.png\" style=\"vertical-align: middle;width:auto;height:20px;margin-right:8px;\"> <span></span></div>";
                    str+='<form action="'+url+'" method="post" id="create_list_form">';
                    str+='<input type="hidden" name="action" value="add_client_list" />';
                    str+='<input type="hidden" name="customFieldKey" value="" id="customFieldKey" />';
                    str+='<input type="hidden" name="customFieldNum" value="'+fieldNum+'" id="customFieldNum" />';
                    str+='<div style="padding:5px 0;"><div id="fieldNameError" style="display:none;" class="error notice"></div>Field Name<br /><input type="text" value="" autocomplete="off" style="width:100%;" name="customFieldName" id="customFieldName" class="customFieldName" placeholder="Field Name" maxlength="250" /></div>';
                    str+='<div style="padding:5px 0;">Field Label (optional)<input type="text" value="" autocomplete="off" style="width:100%;" name="customFieldLabel" id="customFieldLabel" class="customFieldLabel" placeholder="Field Label (optional)" maxlength="100" /></div>';
                    str+='<div style="padding:5px 0;">Field Type<br /><select name="customFieldType" id="customFieldType" class="customFieldType">';
                    str+='<?php
                        //foreach ($campaignMonitorFieldAr as $campaignMonitorFieldKey=>$campaignMonitorField)
                        foreach ($campaignMonitorFieldAr as $campaignMonitorFieldKey=>$campaignMonitorField)
                        {
                            echo '<option value="'.htmlDecodeEncode($campaignMonitorFieldKey).'">'.htmlDecodeEncode($campaignMonitorField).'</option>';
                        }
                        ?>';
                    str+="</select></div>";
                    str+="<div id=\"customFieldOptionCon\" style=\"display:none;padding:5px 0;\">Field Options (One Per Line)<br /><textarea name=\"customFieldOptions\" id=\"customFieldOptions\" style=\"width:100%;height:85px;\" maxlength=\"500\"></textarea></div>";
                    str+="<div style='padding:8px 0;display:none;' id=\"customFieldShowLabelCon\"><input type=\"checkbox\" name=\"customFieldShowLabel\" id=\"customFieldShowLabel\" class=\"customFieldShowLabel\" /> <label for=\"customFieldShowLabel\">Show Label</label></div>";
                    str+="<div style='padding:8px 0;'><input type=\"checkbox\" name=\"customFieldRequired\" id=\"customFieldRequired\" class=\"customFieldRequired\" /> <label for=\"customFieldRequired\">Required</label></div>";
                    str+="</form>";
                    str+="<div style=\"text-align:right;margin-top:8px;\">";
                    str+="<button onclick=\"close_list_fields();\" class=\"btn_white button-secondary\">Cancel</button> &nbsp; ";
                    str+="<button id=\"createCustomList\" class=\"button-primary btn_blue\" name=\"submit\" onclick=\"return validate_field_form();\">Save Field</button>";
                    str+="</div>";

                    $z('html, body').animate({ scrollTop: 0 }, 300);

                    $z("#modal_loading").hide();
                    $z("#modal_header").html("<h1>Campaign Monitor Custom Field</h1>");
                    $z("#modal_content").html(str).show();
                    $z("#modal_container_1").fadeIn(800);
                    $z("#modal_bg").fadeIn(300);


                    var name="";
                    var type="";
                    var key="";
                    var ops="";
                    var showLabel=0;
                    var isRequired=0;
                    var label="";

                    if (fieldNum==0)
                    {
                        var fieldKey = $z("#addCustomFieldSelect").val();
                        if (fieldKey.length>0)
                        {
                            for (var x=0; x<customFieldInfo.length; x++)
                            {

                                if (htmlSpecialDecodeEncode(customFieldInfo[x].Key)==htmlSpecialDecodeEncode(fieldKey))
                                {
                                    name=customFieldInfo[x].FieldName;
                                    type=customFieldInfo[x].DataType;
                                    key=customFieldInfo[x].Key;
                                    ops="";
                                    var sep="";
                                    for (var y=0; y<customFieldInfo[x].FieldOptions.length;y++)
                                    {
                                        ops+=sep+customFieldInfo[x].FieldOptions[y];
                                        sep="\n";
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    else
                    {
                        name=$z("#customFieldName"+fieldNum).val();
                        type=$z("#customFieldType"+fieldNum).val();
                        key=$z("#customFieldKey"+fieldNum).val();
                        ops=$z("#customFieldOptions"+fieldNum).val();
                        label=$z("#customFieldLabel"+fieldNum).val();
                        showLabel=parseInt($z("#customFieldShowLabel"+fieldNum).val(),10);
                        isRequired=parseInt($z("#customFieldRequired"+fieldNum).val(),10);
                    }


                    $z("#customFieldName").val(htmlSpecialCharsDecode(name));
                    $z("#customFieldType").val(htmlSpecialCharsDecode(type)) ;
                    $z("#customFieldKey").val( htmlSpecialCharsDecode(key));
                    $z("#customFieldLabel").val( htmlSpecialCharsDecode(label) );
                    $z("#customFieldOptions").val(htmlSpecialCharsDecode(ops));
                    $z("#customFieldShowLabel").prop("checked", showLabel);
                    $z("#customFieldRequired").prop("checked", isRequired);

                    if (key.length>0)
                    {
                        $z('#customFieldType').prop('disabled', true);
                        $z('#customFieldOptions').prop('readonly', true);
                    }

                    $z( "#customFieldType" ).off();

                    $z( "#customFieldType" ).on( "change", function() {
                        customFieldTypeUpdated();
                    });

                    customFieldTypeUpdated();
                };

                window.customFieldTypeUpdated = function()
                {
                    var val = $z( "#customFieldType" ).val();

                    if (val=="MultiSelectMany" || val=="MultiSelectOne")
                    {
                        $z("#customFieldOptionCon").slideDown();
                    }
                    else
                    {
                        $z("#customFieldOptionCon").slideUp();
                    }

                    if (val=="Text")
                    {

                        $z("#customFieldShowLabelCon").css("display","block");
                    }
                    else
                    {
                        $z("#customFieldShowLabelCon").css("display","none");
                    }

                };

                var fromElem = $z('#signupFormForm');

                // when elements are ready
                $z(window).load(function () {

                    fromElem.on('submit', function (e) {
                        return validate_signup_form_form();
                    });

                    updatePreviewForm();

                    fromElem.find('input[type="text"]').on('keyup', function (e) {
                        updatePreviewForm();
                    }).on('blur', function (e) {
                        updatePreviewForm();
                    }).on('change', function (e) {
                        updatePreviewForm();
                    });
                    fromElem.find('select').on('change', function (e) {
                        updatePreviewForm();
                    });
                    fromElem.find('input[type="checkbox"]').on('change', function (e) {
                        updatePreviewForm();
                    });
                    fromElem.find('input[type="radio"]').on('change', function (e) {
                        updatePreviewForm();
                    });

                    fromElem.find('.remove_page_img').on('click', function (event) {
                        event.preventDefault();
                        removeAppearsOnPageSelect($z(this));
                    });

                    $z('#addAppearsOnPageButton').on('click', function (event) {
                        event.preventDefault();
                        addAppearsOnPageSelect();
                    });


                    $z("#submitButtonTextHex").add("#submitButtonBgHex").add("#backgroundHex").add("#textHex").on('change', function (event) {
                        event.preventDefault();
                        hexTextValUpd($z(this), 0);
                    }).on('keyup', function (event) {
                        event.preventDefault();
                        hexTextValUpd($z(this), 1);
                    });
                    $z("#submitButtonTextHexCol").add("#submitButtonBgHexCol").add("#backgroundHexCol").add("#textHexCol").on('change', function (event) {
                        event.preventDefault();
                        hexColValUpd($z(this));
                    });

                    $z('#formType').on('change', function (e) {
                        formTypeUpdate();
                    });
                    formTypeUpdate();

                    $z('#formEmbedCode').bind("focus click", function () {
                        selectInputText(this);
                    });

                    $z('#formAppearsLightboxScroll').bind("change", function () {
                        lighboxAppearsUpdate();
                    });
                    $z('#formAppearsLightboxSeconds').bind("change", function () {
                        lighboxAppearsUpdate();
                    });

                    $z('#lightboxSeconds').bind("click focus", function () {
                        $z('#formAppearsLightboxSeconds').prop('checked', true);
                        lighboxAppearsUpdate();
                    });
                    $z('#lightboxScrollPercent').bind("click focus", function () {
                        $z('#formAppearsLightboxScroll').prop('checked', true);
                        lighboxAppearsUpdate();
                    });

                    $z('#formPlacementCon').find("input").bind("change", function () {
                        formPositionUpdate();
                    });

                    $z('#formPlacementBarCon').find("input").bind("change", function () {
                        formPositionBarUpdate();
                    });

                    formPositionUpdate();
                    var color_is_supported = 0;

                    var input = document.createElement("input");
                    input.type = "color";

                    if (input.type === "color") {
                        color_is_supported = 1;
                    }

                    if (!color_is_supported) {

                    }

                    $z("#lightboxScrollPercent").bind("change", function () {
                        var elem = $z(this);
                        elem.val(elem.val().replace("%", ""));
                    });

                });
            })(jQuery.noConflict());
        </script>

    </div><?php // end main "wrap" div ?>

    <!-- font handler -->
    <?php if (!empty($selectedFont))  { $selectedFont->toHtml(); } ?>