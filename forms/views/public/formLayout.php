<?php
/**
 * Created by PhpStorm.
 * User: SunriseIntegration5
 * Date: 11/14/2016
 * Time: 10:46 AM
 */

$pageId=get_the_ID();
$campaignMonitorViewedIds = "";
if (!empty($_COOKIE["campaignMonitorViewedIds"]))
{
    $campaignMonitorViewedIds = $_COOKIE["campaignMonitorViewedIds"];
}
$publicPath = forms\core\Application::getPluginPath('forms/views/public/', true);
$form = null;
$abTestId = "";

$persistentFormTypeAr=array("slideoutTab", "embedded", "button"); // forms that always will display multiple times, ignoring the campaignMonitorViewedIds cookie

$selectedTestAr = \forms\core\ABTest::getByPost( $pageId );
global $wp;
$current_url =  $wp->request;

$forms=$this->getForms();

$shortCodeFormId = \forms\core\Application::$shortCodeId;

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
    return htmlentities($decoded, ENT_QUOTES);
}


if ($selectedTestAr !== null) {


    foreach ($selectedTestAr as $selectedTest)
    {
        $tests = $selectedTest->getTests();

        if (count($tests)>0)
        {

            //schuffle($tests); // randomly sort the tests. this will make a random test 1st, but if it has already been viewed, the 2nd one will be the backup
            $keys=array_keys($tests);
            shuffle($keys);
            foreach ($keys as $key)
            {
                $test = $tests[$key];
                $testForm = $test->getForm();
                $testFormId = $testForm->getId();
                $currentForm=null;
                $fId="";
                foreach ($forms as $f)
                {
                    $fId = $f->getId();
                    if ($fId==$testFormId)
                    {
                        $currentForm=$f;
                        break;
                    }
                }

                if (!is_null($currentForm))
                {
                    $currentFormType = $currentForm->getType();
                    $currentFormId=$currentForm->getId();
                    $isActive=$currentForm->getIsActive();

                    if ($isActive && (in_array($currentFormType, $persistentFormTypeAr) || strpos($campaignMonitorViewedIds, "(".$currentFormId.")")===false))
                    {
                        // update impression count
                        $impressions = $test->getImpressions();
                        $selectedTest->getTests($key)->setImpressions($impressions + 1);

                        $selectedTest->save($selectedTest->getId());

                        // set form
                        $form = $currentForm;
                        // set AB test info
                        $abTestId = $selectedTest->getId();

                        break 2; // break out of the loop of forms for the current AB test the loop of AB tests.
                    }
                }
            }

            //$numberOfTests = count( $tests );
            //$randomTestIndex = mt_rand(0, $numberOfTests-1);
//            $randomTestIndex =array_rand ( $tests );
//            //$testToDisplay = ($numberOfTests > 0) ? $tests[$randomTestIndex] : $tests;
//            $testToDisplay = $tests[$randomTestIndex];
//
//            $impressions = $testToDisplay->getImpressions();
//            $selectedTest->getTests($randomTestIndex)->setImpressions($impressions + 1);
//            $selectedTest->save($selectedTest->getId());
//            $form = $testToDisplay->getForm();

        }
    }
}


if (is_null($form) && $shortCodeFormId !== '') {

    // we didn't find the form through AB tests, look through the forms to see if their page for shortcode

    $formKeyAll = null;

    $currentForm = \forms\core\Form::getOne( $shortCodeFormId );

    if (!empty( $currentForm )) {
        $currentFormId = $currentForm->getId();
        $currentFormType = $currentForm->getType();
        $isActive = $currentForm->getIsActive();
        if ($isActive && in_array( $currentFormType, $persistentFormTypeAr ) || strpos( $campaignMonitorViewedIds, "(" . $currentFormId . ")" ) === false) {

            $form = $currentForm;
        }
    }


}


if (is_null($form))
{

    // we didn't find the form through AB tests, look through the forms to see if their page is set to this page or all pages

    $formKeyAll=null;

    foreach ($forms as $k=>$currentForm)
    { $addThisPage = false;
        $currentFormId = $currentForm->getId();
        $currentFormType = $currentForm->getType();
        $isActive=$currentForm->getIsActive();
        if ($isActive && in_array($currentFormType, $persistentFormTypeAr) || strpos($campaignMonitorViewedIds, "(".$currentFormId.")")===false)
        {
            $onPageAr=$currentForm->getOnPageAr();

            if (!empty( $onPageAr )) {

                foreach ($onPageAr as $currentPageId){
                    if ($currentPageId > 0) {
                        $pageUrl = basename(get_permalink( $currentPageId ));


                        if (!empty( $current_url )) {
                            if ($pageUrl === $current_url){
                                $addThisPage = true;

                            }
                        }
                    }
                }
            }


            if (in_array($pageId, $onPageAr) || $addThisPage)
            {
                $form=$currentForm;
                //break;
            }
            if (in_array(-1, $onPageAr) && is_null($formKeyAll))
            {
                $formKeyAll=$k;
                //break;
            }
        }
    }

    if (is_null($form) && !is_null($formKeyAll))
    {
        $form=$forms[$formKeyAll];
    }
}

/**
 * @var \forms\core\FormField
 */
$customFormFields = null;


if (!is_null($form))
{

    $submitButtonStyle = "";

    $formType = $form->getType();
    $formId = $form->getId();

    $customFormFields = $form->getFields();


    $backgroundColorHex=$form->getBackgroundHex();

    $boxBgStyle = "";
    if (!empty($backgroundColorHex) && $formType !== 'embedded') {
        $boxBgStyle = "background-color:#" . str_replace("#", "", filter_var($backgroundColorHex, FILTER_SANITIZE_STRING));
    }

    $submitButtonText = $form->getSubmitButtonText();
    if (empty($submitButtonText)) {
        $submitButtonText = "SUBSCRIBE";
    }

    $buttonColorHex =     "#".$form->getSubmitButtonBgHex();
    $buttonTextColorHex = "#".$form->getSubmitButtonTextHex();
    $backgroundColorHex = "#".$form->getBackgroundHex();

    $openTextFieldLabel = $form->getOpenTextFieldLabel();
    $textHexColor =       "#".str_replace('#', '', $form->getTextHex());


    $formHeader = $form->getHeader();

    $formSubHeader = $form->getSubheader();
    if ($formSubHeader)
    {
        $hasSubHeader="cmApp_hasSubHeader";
    }
    else
    {
        $hasSubHeader="";
    }


    $placementCssClasses="";

    if ($formType=="slideoutTab")
    {
        $formPlacement=$form->getFormPlacement();
        if ($formPlacement=="topLeft" || $formPlacement=="bottomLeft")
        {
            $placementCssClasses.=" cmApp_placementLeft";
        }
        if ($formPlacement=="bottomLeft" || $formPlacement=="bottomRight")
        {
            $placementCssClasses.=" cmApp_placementBottom";
        }
    }
    elseif ($formType=="bar")
    {
        $formPlacement=$form->getFormPlacementBar();
        if ($formPlacement=="bottom")
        {
            $placementCssClasses.=" cmApp_placementBottom";
        }
    }

    $lightboxSeconds = $form->getLightboxSeconds();
    $lightboxScrollPercent = $form->getLightboxScrollPercent();

    $image_path=plugins_url()."/forms-for-campaign-monitor/forms/views/public/images/";

    $hasDateOfBirthField=$form->getHasDateOfBirthField();
    $hasOpenTextField=$form->getHasOpenTextField();
    $openTextFieldLabel=$form->getOpenTextFieldLabel();
    $hasGenderField=$form->getHasGenderField();
    $hasCampMonLogo=$form->getHasCampMonLogo();

    $hasNameField=$form->getHasNameField();

    $hasNameFieldLabel=$form->getHasNameFieldLabel();
    $hasEmailFieldLabel=$form->getHasEmailFieldLabel();
    $selectedFont = $form->getFont();

    /*$x=get_defined_vars();
    echo "<div style='background-color:#FFF;'>";
    foreach ($x as $k=>$v)
    {
        if (!is_object($v))
        {
            echo $k."=";var_dump($v);echo "<br><Br>";
        }
    }
    echo "</div>";*/

    $noAutoOpen=0;
    if ($formType=="button")
    {
        $formType="lightbox";
        $noAutoOpen=1;
    }

    ?>
    <div id="cmApp_modalBackground" class="cmApp_hidden"></div>
    <div class="cmApp_signupContainer <?php echo $hasSubHeader?> cmApp_<?php echo filter_var($formType, FILTER_SANITIZE_STRING) ?> cmApp_hidden<?php echo $placementCssClasses; ?>" id="cmApp_signupContainer" style="<?php echo $boxBgStyle; ?>">
        <?php if ($formType !== 'embedded'): ?>
            <a class="cmApp_closeFormButton"></a>
        <?php endif; ?>
        <div class="cmApp_signupFormWrapper">
            <?php if ($formType == 'slideoutTab') { ?>
                <div class="cmApp_slideOutTab" style="background-color:<?php echo filter_var($buttonColorHex, FILTER_SANITIZE_STRING); ?>;">
                    <a href="javascript:void(0);" id="cmApp_slideoutButton" style="color:<?php echo filter_var($buttonTextColorHex, FILTER_SANITIZE_STRING); ?>;"><?php _e(htmlDecodeEncode($submitButtonText)); ?></a>
                </div>
            <?php } ?>
            <input type="hidden" id="cmApp_formType" value="<?php echo filter_var($formType, FILTER_SANITIZE_STRING); ?>" />
            <?php
            if (!empty($lightboxSeconds))        { ?><input type="hidden" id="lightboxSeconds" value="<?php echo intval($lightboxSeconds) ?>" /><?php }
            if (!empty($lightboxScrollPercent))  { ?><input type="hidden" id="lightboxScrollPercent" value="<?php echo intval($lightboxScrollPercent) ?>" /><?php }
            if (!empty($noAutoOpen))             { ?><input type="hidden" id="noAutoOpen" value="<?php echo intval($noAutoOpen) ?>" /><?php }
            ?>

            <div id="cmApp_statusContainer" class="cmApp_statusContainer cmApp_hidden">
                <div id="cmApp_thankYouCheck">
                    <img src="<?php echo \forms\core\Application::getPluginPath('forms/views/public/images/', true);?>success-icon.svg" alt="">
                </div>
                <div class="cmApp_processingMsg">Processing...</div>
                <div class="cmApp_successMsg">
                    <span>Thank you!</span>
                    <span>Your subscription has been confirmed.</span>
                    <span>You'll hear from us soon.</span>
                </div>
            </div>


            <form action="<?php echo get_admin_url(); ?>admin-post.php" method="post" id="cmApp_signupForm" data-uuid="<?php echo htmlDecodeEncode($formId); ?>" class="cmApp_cf cm-form-handler">

                <input type="hidden" name="action" value="ajax_handler_nopriv_cm_forms">
                <input type="hidden" name="no_js" value="1">
                <?php
                if (!empty($formHeader)) { ?>
                    <div class="cmApp_formHeader"
                         style="color:<?php echo filter_var($textHexColor, FILTER_SANITIZE_STRING); ?>"><?php _e(htmlDecodeEncode($formHeader)); ?></div><?php }
                if (!empty($formSubHeader) && $formType !== 'bar') { ?>
                    <div class="cmApp_formSubHeader"
                         style="color:<?php echo filter_var($textHexColor, FILTER_SANITIZE_STRING); ?>"><?php _e(htmlDecodeEncode($formSubHeader)); ?></div><?php }
                ?>

                <div class="cmApp_errorMsg" id="cmApp_errorAll"></div>

                <input type="hidden" name="formId" id="cmApp_FormId" value="<?php echo htmlDecodeEncode($formId); ?>"/>
                <input type="hidden" name="abTestId" id="cmApp_AbTestId" value="<?php echo htmlDecodeEncode($abTestId); ?>"/>


                <?php
                if($formType !== 'bar'):?>
                <div class="cmApp_fieldWrap">
                    <?php endif;
                    if (!empty($hasNameField)) {
                        if ($hasNameFieldLabel)
                        {
                            $labelConHtmlStr="<label>Name</label>";
                            $placeHolderStr="";
                        }
                        else
                        {
                            $labelConHtmlStr="";
                            $placeHolderStr="Name";
                        }
                        ?>
                        <div class="cmApp_formInput"><?php echo $labelConHtmlStr ;?><input type="text" name="name" id="cmApp_signupName" value="" placeholder="<?php echo $placeHolderStr; ?>"/></div><?php }
                    ?>
                    <div class="cmApp_errorMsg" id="cmApp_emailError">ErrorHere</div>
                    <?php
                    if ($hasEmailFieldLabel)
                    {
                        $labelConHtmlStr="<label>Email *</label>";
                        $placeHolderStr="";
                    }
                    else
                    {
                        $labelConHtmlStr="";
                        $placeHolderStr="Email *";
                    }

                    ?>
                    <div class="cmApp_formInput"><?php echo $labelConHtmlStr; ?><input type="text" name="email" id="cmApp_signupEmail" value="" placeholder="<?php echo $placeHolderStr; ?>"/></div>
                    <?php if (!empty($hasDateOfBirthField)) { ?>
                        <div class="cmApp_errorMsg" id="cmApp_dobError">ErrorHere</div>
                        <div class="cmApp_formInput"><input type="text" name="dateOfBirth" id="cmApp_signupDateOfBirth" value=""
                                                            placeholder="Date of birth (MM/DD/YYYY)"/></div><?php }
                    if (!empty($hasOpenTextField)) { ?>
                        <div class="cmApp_formInput cmApp_textArea"><textarea name="openText" id="cmApp_signupOpenText"
                                                                              placeholder="<?php _e(htmlDecodeEncode($openTextFieldLabel)); ?>" rows="2"
                                                                              style=""></textarea></div><?php }
                    if (!empty($hasGenderField)):?>
                        <div class="cmApp_signupGenderFields cmApp_cf cmApp_formInput">
                            <div class="cmApp_signupGenderFieldsContainer cmApp_cf">
                                <input type="radio" name="gender" value="male" id="cmApp_radioMale" class="cmApp_styledRadio">
                                <label for="cmApp_radioMale" style="color:<?php echo filter_var($textHexColor, FILTER_SANITIZE_STRING); ?>">
                                        <span>
                                            <span></span>
                                        </span>
                                    Male
                                </label>
                                <input type="radio" name="gender" value="female" class="cmApp_styledRadio" id="cmApp_radioFemale">
                                <label for="cmApp_radioFemale" style="color:<?php echo filter_var($textHexColor, FILTER_SANITIZE_STRING); ?>">
                                        <span style="margin-left: 10px;">
                                            <span></span>
                                        </span>
                                    Female
                                </label>
                            </div>
                        </div>
                    <?php endif;

                    ?>
                    <?php if($formType !== 'bar'): ?>
                </div>
            <?php endif; ?>

                <?php if (!empty($customFormFields)) : ?>

                    <?php $customFieldIndex = 0; foreach ($customFormFields as $customField) : ?>

                        <?php echo $customField->getHtml(); ?>

                        <?php $customFieldIndex++; endforeach; // end foreach custom field ?>
                <?php endif; ?>
                <div style="height:2px;"></div>

                <?php

                if ($form->getHasCaptcha())
                {
                    \forms\core\Security::getCaptcha();
                }
                ?>

                <div>
                    <input type="submit" name="submit" value="<?php echo _e(htmlDecodeEncode($submitButtonText)); ?>"
                           class="cmApp_formSubmitButton post-ajax"
                           style="background-color:<?php echo filter_var($buttonColorHex, FILTER_SANITIZE_STRING); ?>; color:<?php echo filter_var($buttonTextColorHex, FILTER_SANITIZE_STRING); ?>;" data-submit="">
                </div>

                <?php if (!empty($hasCampMonLogo)) { ?>
                    <div class="cmApp_logo"><img src="<?php echo $image_path ?>PowerdByCampMon@2x.png" style="width:180px;" alt="Powered By Campaign Monitor"/></div>
                <?php } ?>
            </form>
        </div>
    </div>
    <style>

        .cmApp_cf:after {
            visibility: hidden;
            display: block;
            font-size: 0;
            content: " ";
            clear: both;
            height: 0;
        }

        #cmApp_modalBackground.cmApp_hidden {
            opacity: 0;
            display: none;
        }

        .cmApp_signupFormWrapper {
            position: relative;
        }

        .cmApp_signupContainer {
            border: 1px solid #666;
            box-shadow: 1px 2px 2px;
            display: block;
            width: 80%;
            max-width: 400px;
            min-width: 200px;
            background-color: #FFF;
            padding: 25px 50px;
            text-align: center;
            color: #000;
            z-index: 1999999999;
        }

        .cmApp_signupContainer.cmApp_embedded,
        .cmApp_signupContainer.cmApp_lightbox,
        .cmApp_signupContainer.cmApp_slideoutTab
        {
            max-width:415px;
        }

        #cmApp_signupForm { text-align:left;}

        .cmApp_signupContainer * {
            line-height:1.6;
        }

        /* Bar Styles */

        .cmApp_signupContainer.cmApp_bar {
            position: fixed;
            border: none;
            top: 0;
            left: 0;
            width: 100%;
            max-width: none;
            padding: 6px 10px 0px;
            -webkit-transform: translateY(0);
            -moz-transform: translateY(0);
            -ms-transform: translateY(0);
            -o-transform: translateY(0);
            transform: translateY(0);
            -webkit-transition: transform .3s ease-in-out;
            -moz-transition: transform .3s ease-in-out;
            -ms-transition: transform .3s ease-in-out;
            -o-transition: transform .3s ease-in-out;
            transition: transform .3s ease-in-out;
            box-shadow: none;
            min-height:70px;
        }

        .cmApp_signupContainer.cmApp_bar.cmApp_placementBottom
        {
            bottom: 0px;
            top: auto;
        }

        .cmApp_signupContainer.cmApp_bar.cmApp_hidden {
            -webkit-transform: translateY(-102%);
            -moz-transform: translateY(-102%);
            -ms-transform: translateY(-102%);
            -o-transform: translateY(-102%);
            transform: translateY(-102%);
        }

        .cmApp_signupContainer.cmApp_bar.cmApp_placementBottom.cmApp_hidden {
            -webkit-transform: translateY(102%);
            -moz-transform: translateY(102%);
            -ms-transform: translateY(102%);
            -o-transform: translateY(102%);
            transform: translateY(102%);
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_signupFormWrapper {
            /*width: 100%;*/
            padding-right: 25px;
            margin:5px 0 0;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_signupGenderFieldsContainer {
            margin: 10px auto 7px;
        }

        .cmApp_signupContainer.cmApp_bar div {
            float: left;
            /*margin: 5px .5% 0;*/
            margin-top: 5px;
            margin-bottom:0;
        }


        .cmApp_signupContainer.cmApp_bar .cmApp_formHeader {
            margin: 13px 0 0;
            font-weight: normal;
            padding-right: 10px;
            padding-top: 6px;
            line-height:1.1;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_formSubHeader {
            margin: 19px 3px;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_textArea {
            width: 230px;
            margin-bottom:0;
        }


        .cmApp_signupContainer.cmApp_bar #cmApp_signupDateOfBirth {
            margin-bottom:0;
        }

        .cmApp_signupContainer.cmApp_bar form {
            display: inline-block;
            width: auto ;
        }

        .cmApp_signupContainer.cmApp_bar form div:not(.cmApp_errorMsg):not(.cmApp_processingMsg) {
            display: inline-block;
        }

        .cmApp_signupContainer #cmApp_statusContainer {
            position: relative;
            white-space:nowrap;
            display:flex;
            margin:5px 0 0;
        }

        .cmApp_signupContainer #cmApp_statusContainer.cmApp_hidden {
            display:none;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_successMsg {
            margin: 2px auto 0 10px;
            padding: 10px 0;
            font-weight:normal;
            font-size:12px;
            color: #4A4A4A;
        }

        .cmApp_signupContainer.cmApp_bar #cmApp_thankYouCheck {
            margin: -2px 0 0;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_successMsg span {
            font-size:12px;
            font-weight:normal;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_successMsg span:first-child {
            font-size:18px;
            margin-right:15px;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_processingMsg {
            padding: 8px 15%;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_logo {
            float: left;
            display: inline-block;
            margin: 18px 20px 0;
            padding-top:6px;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_errorMsg {
            margin: 20px 0;
        }

        /* Lightbox Styles */

        .cmApp_signupContainer.cmApp_lightbox {
            position: fixed;
            top: 50px;
            left: 50%;
            -webkit-transform: translateX(-50%);
            -moz-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            -o-transform: translateX(-50%);
            transform: translateX(-50%);
            border: none;
            box-shadow: none;
            opacity: 1;
            -webkit-transition: opacity .15s ease-in-out;
            -moz-transition: opacity .15s ease-in-out;
            -ms-transition: opacity .15s ease-in-out;
            -o-transition: opacity .15s ease-in-out;
            transition: opacity .15s ease-in-out;
            border-radius: 4px;
        }

        .cmApp_signupContainer.cmApp_lightbox.cmApp_hidden {
            opacity: 0;
            display: none;
        }

        .cmApp_signupContainer.cmApp_lightbox #cmApp_statusContainer {
            white-space:normal;
            display:block;
        }

        #cmApp_statusContainer.cmApp_processing #cmApp_thankYouCheck {

        }

        #cmApp_statusContainer.cmApp_processing .cmApp_processingMsg {
            display:block;
        }

        .cmApp_signupContainer.cmApp_lightbox #cmApp_statusContainer.cmApp_hidden {
            display:none;
        }

        .cmApp_signupContainer #cmApp_statusContainer.cmApp_hidden #cmApp_thankYouCheck {
            visibility: hidden;
            opacity:0;
        }

        .cmApp_signupContainer.cmApp_lightbox .cmApp_closeFormButton {
            right: 10px;
        }

        .cmApp_signupContainer.cmApp_lightbox #cmApp_thankYouCheck {
            width:100%;
            box-sizing:border-box;
            opacity:1;
            margin:16px 0 0;
        }

        .cmApp_signupContainer.cmApp_lightbox .cmApp_successMsg {
            padding:10px 20px 30px;
            margin-top:0;
        }

        .cmApp_signupContainer.cmApp_lightbox .cmApp_successMsg span {
            display:block;
            font-size:12px;
        }

        .cmApp_signupContainer.cmApp_lightbox .cmApp_successMsg span:first-child {
            font-size:18px;
            margin-bottom:10px;
        }

        /* Slideout Styles */

        .cmApp_signupContainer.cmApp_slideoutTab {
            position: fixed;
            right: 0;
            top: 50px;
            -webkit-transform: translateX(0);
            -moz-transform: translateX(0);
            -ms-transform: translateX(0);
            -o-transform: translateX(0);
            transform: translateX(0);
            -webkit-transition: -webkit-transform .3s ease-in-out;
            -moz-transition: transform .3s ease-in-out;
            -ms-transition: transform .3s ease-in-out;
            -o-transition: transform .3s ease-in-out;
            transition: transform .3s ease-in-out;
            box-shadow: none;
            border: none;
            padding:25px 50px;
        }

        .cmApp_signupContainer.cmApp_slideoutTab.cmApp_placementBottom
        {
            bottom: 50px;
            top: auto;
        }

        .cmApp_signupContainer.cmApp_slideoutTab.cmApp_placementLeft
        {
            left: 0;
            right: auto;
        }

        .cmApp_signupContainer.cmApp_slideoutTab.cmApp_placementLeft .cmApp_slideOutTab
        {
            /*border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;*/
            /*left:95% ;*/
            left:95.5%
        }

        .cmApp_signupContainer.cmApp_slideoutTab.cmApp_hidden {
            -webkit-transform: translateX(100%);
            -moz-transform: translateX(100%);
            -ms-transform: translateX(100%);
            -o-transform: translateX(100%);
            transform: translateX(100%);
        }

        .cmApp_signupContainer.cmApp_slideoutTab.cmApp_placementLeft.cmApp_hidden {
            -webkit-transform: translateX(-100%);
            -moz-transform: translateX(-100%);
            -ms-transform: translateX(-100%);
            -o-transform: translateX(-100%);
            transform: translateX(-100%);
        }

        .cmApp_signupContainer.cmApp_slideoutTab .cmApp_slideOutTab {
            position: absolute;
            display: block;
            /*left: -50%;*/
            left: -48%;
            top: 40px;
            min-width: 165px;
            -webkit-transform: rotate(270deg);
            -moz-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            -o-transform: rotate(270deg);
            transform: rotate(270deg);
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            padding: 7px;
        }

        .cmApp_signupContainer.cmApp_slideoutTab.cmApp_placementLeft .cmApp_slideOutTab {
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            transform: rotate(90deg);
        }


        .cmApp_signupContainer.cmApp_slideoutTab .cmApp_slideOutTab #cmApp_slideoutButton {
            display: block;
            margin-left: auto;
            margin-right: auto;
            vertical-align: baseline;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            /*text-transform: uppercase;*/
            font-size: 14px;
            outline:none;
        }


        .cmApp_signupContainer.cmApp_slideoutTab #cmApp_statusContainer {
            display:inline-block;
        }

        .cmApp_signupContainer.cmApp_slideoutTab #cmApp_statusContainer.cmApp_hidden {
            display:none;
        }

        .cmApp_signupContainer.cmApp_slideoutTab #cmApp_thankYouCheck {
            width:100%;
            box-sizing:border-box;
            opacity:1;
            margin:16px 0 0;
        }

        .cmApp_signupContainer.cmApp_slideoutTab .cmApp_successMsg {
            padding:10px 20px 30px;
            margin-top:0;
        }

        .cmApp_signupContainer.cmApp_slideoutTab .cmApp_successMsg span {
            display:block;
            font-size:12px;
        }

        .cmApp_signupContainer.cmApp_slideoutTab .cmApp_successMsg span:first-child {
            font-size:18px;
            margin-bottom:10px;
        }

        /* Embedded Styles */

        .cmApp_signupContainer.cmApp_embedded {
            margin: 0 auto;
            box-shadow: none;
            background: transparent;
            border: none;
        }


        .cmApp_signupContainer #cmApp_statusContainer * {
            line-height:1.1;
        }

        .cmApp_signupContainer.cmApp_embedded #cmApp_statusContainer {
            display:block;
        }

        .cmApp_signupContainer.cmApp_embedded #cmApp_statusContainer.cmApp_hidden {
            display:none;
        }

        .cmApp_signupContainer.cmApp_embedded #cmApp_thankYouCheck {
            width:100%;
            box-sizing:border-box;
            opacity:1;
            margin:16px 0 0;
        }

        .cmApp_signupContainer.cmApp_embedded .cmApp_successMsg {
            padding:10px 20px 30px;
            margin-top:0;
        }

        .cmApp_signupContainer.cmApp_embedded .cmApp_successMsg span {
            display:block;
            font-size:12px;
        }

        .cmApp_signupContainer.cmApp_embedded .cmApp_successMsg span:first-child {
            font-size:18px;
            margin-bottom:10px;
        }

        .cmApp_signupContainer .cmApp_processingMsg,
        .cmApp_signupContainer .cmApp_successMsg {
            padding: 40px 20px;
            font-size: 16px;
            font-weight: normal;
            display: none;
            color: <?php echo filter_var($textHexColor, FILTER_SANITIZE_STRING); ?>;
        }

        .cmApp_signupContainer div.cmApp_errorMsg {
            display: none;
            color: red;

            text-align: left;
        }

        .cmApp_signupContainer .cmApp_closeFormButton {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 10px;
            z-index: 1000002;
            background-image: url('<?php echo $image_path ?>X@2x.png');
            background-size: 9px 9px;
            background-repeat: no-repeat;
            display: inline-block;
            background-color: transparent;

            min-height: 9px;
            min-width: 9px;


            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -o-user-select: none;
            user-select: none;
            text-decoration: none;
        }

        .cmApp_signupContainer .cmApp_closeFormButton:hover,
        .cmApp_signupContainer .cmApp_closeFormButton:active,
        .cmApp_signupContainer .cmApp_closeFormButton:visited,
        .cmApp_signupContainer .cmApp_closeFormButton:focus {
            text-decoration: none;
            color: #333;
        }

        .cmApp_signupContainer.cmApp_bar .cmApp_closeFormButton {
            top: 35px;
            right: 10px;
        }

        .cmApp_signupContainer input.cmApp_inputError {
            box-shadow: 0px 0px 5px 0px #a94442;
            background-color: #f2dede;
            border: 2px solid #a94442;
        }

        .cmApp_signupContainer,
        .cmApp_signupContainer div,
        .cmApp_signupContainer input,
        .cmApp_signupContainer textarea,
        .cmApp_signupContainer select {
            box-sizing: border-box;
            font-size: 100%;
            font: inherit;
            /*line-height:1.6;*/
            vertical-align: baseline;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .cmApp_signupContainer form {
            margin: 0;
            padding: 0;
        }

        .cmApp_signupContainer div {
            padding: 0;
            margin: 6px 0 0;
            line-height: 1.1;
        }

        #cmApp_signupForm .cmApp_fieldWrap {
            margin:16px 0 0 0;
        }

        #cmApp_signupForm.cmApp_bar .cmApp_fieldWrap {
            margin:0;
        }


        .cmApp_signupContainer .cmApp_formInput input:not([type="radio"]):not([type="checkbox"]) {
            height:38px;
        }

        .cmApp_signupContainer .cmApp_formInput textarea {
            min-height:0;
            height:38px;
            padding:7px 11px;
        }

        .cmApp_signupContainer input:not([type="radio"]):not([type="checkbox"]),
        .cmApp_signupContainer textarea,
        .cmApp_signupContainer select {
            background-color: #FFF;
            margin: 5px 0 0;
            width: 100%;
            font-size: 14px;
            height: 38px;
            border: 1px solid #d1d1d1;
            padding: 0.625em 0.4375em;
        }

        .cmApp_signupContainer input[type="radio"] {
            float: left;
            margin-top: 5px;
            margin-left: 3px;
        }

        .cmApp_signupContainer label {
            font-size: 14px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: 400;
            color: <?php echo filter_var($textHexColor, FILTER_SANITIZE_STRING); ?>;
            /*float: left;*/
            margin-left: 5px;
        }

        .cmApp_signupContainer input.cmApp_formSubmitButton {
            background-color: #429BD0;
            color: #FFF;
            letter-spacing: normal;
            margin:14px 0 0;
            padding:8px 18px;
            height:38px;
            text-transform: none;
        }

        .cmApp_signupContainer.cmApp_bar input.cmApp_formSubmitButton {
            margin:5px 0 0 ;
        }

        .cmApp_signupContainer .cmApp_formHeader {
            font-size: 18px;
            font-weight: normal;
            line-height:1.3;
        }

        .cmApp_signupContainer .cmApp_formSubHeader {
            font-size: 14px;
            margin-top: 0;
            line-height:1.3;
        }

        #cmApp_modalBackground {
            width: 100%;
            height: 100%;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #000;
            opacity: .7;
            z-index: 1000000;
        }

        body {
            height: 100% !important;
            min-height: 100% !important;
        }

        .cmApp_signupContainer .cmApp_signupGenderFieldsContainer {
            width: 150px;
            margin: 0 auto;
        }

        input[type="radio"].cmApp_styledRadio {
            display: none;
        }

        .cmApp_styledRadio {
            display: none;
        }

        .cmApp_styledRadio + label {
            position: relative;
            top: 4px;
        }

        .cmApp_styledRadio + label span:nth-child(1) {
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

        .cmApp_styledRadio:checked + label span:nth-child(1) {
            background-color: white;
        }

        .cmApp_styledRadio + label span:nth-child(1) span {
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

        .cmApp_styledRadio:checked + label span:nth-child(1) span {

            background: <?php echo filter_var($buttonColorHex, FILTER_SANITIZE_STRING); ?>;
            -webkit-transition: background-color 100ms linear, -webkit-transform 100ms linear;
            -moz-transition: background-color 100ms linear, -moz-transform 100ms linear;
            -o-transition: background-color 100ms linear, -o-transform 100ms linear;
            -ms-transition: background-color 100ms linear, -ms-transform 100ms linear;
            transition: background-color 100ms linear, transform 100ms linear;

            -ms-transform: scale(.6, .6); /* IE 9 */
            -webkit-transform: scale(.6, .6); /* Safari */
            transform: scale(0.6, 0.6);
        }

        .cmApp_formHeader {
            margin: 5px 0 0;
        }

        .cmApp_signupContainer.cmApp_bar > div > form > div:not(.cmApp_formHeader):not(.cmApp_errorMsg):not(.g-recaptcha) {
            /*height:50px;*/
            min-height:40px;
            margin-bottom:0;
        }

        @media all and (max-width:600px) {
            .cmApp_signupContainer.cmApp_bar .cmApp_textArea textarea {
                height: 40px;
            }
        }


        .cmApp_signupContainer #cmApp_signupForm > div
        {
            padding:0 6px;
        }

        .cmApp_signupContainer #cmApp_signupForm input:not([type="radio"]),
        .cmApp_signupContainer #cmApp_signupForm textarea,
        .cmApp_signupContainer #cmApp_signupForm select
        {
            margin:0;
        }

        .cmApp_signupContainer #cmApp_signupForm label
        {
            margin-left:0;
        }

        .cmApp_signupContainer #cmApp_signupForm ul {
            display: inline-block;
            margin: 0;
            list-style: none;
            padding-left: 0;
        }

        .cmApp_signupContainer #cmApp_signupForm ul li {
            line-height: normal;
            text-align:left;
        }

        .cmApp_signupContainer #cmApp_signupForm label
        {
            text-align:left;
        }
        .cmApp_signupContainer #cmApp_signupForm ul label
        {
            width:auto;
            display:inline-block;
        }

        .cmFormElemButton
        {
            display:inline-block;
            background-color:#0000FF;
            color:#FFF;
            padding:2px 10px;
        }

        #cmApp_signupForm  .cmApp_logo {
            text-align: center;
        }

        <?php

         $submitText = $form->getSubmitButtonText();
         if (strlen($submitText) > 12) : ?>
        .cmApp_signupContainer.cmApp_slideoutTab .cmApp_slideOutTab #cmApp_slideoutButton {
            font-size: 12px;
            line-height: 24px;
        }
        <?php endif; ?>

    </style>

    <script type="text/javascript">
        function cmApp_signup_writeCookie(var1, val1)
        {
            document.cookie = var1 + "=" + encodeURIComponent(val1) + "; path=/";
        }
    </script>
    <?php

    // add form id to cookie
    if (strpos($campaignMonitorViewedIds, "(".$formId.")")===false)
    {
        $updatedCampaignMonitorViewedIds=$campaignMonitorViewedIds."(" . $formId . ")";
        ?>
        <script>

        cmApp_signup_writeCookie("campaignMonitorViewedIds", "<?php echo htmlspecialchars($updatedCampaignMonitorViewedIds); ?>");
    </script>
        <?php
    }

}

?>

<!-- font handler -->
<?php if (!empty($selectedFont))  { $selectedFont->toHtml(); } ?>
    