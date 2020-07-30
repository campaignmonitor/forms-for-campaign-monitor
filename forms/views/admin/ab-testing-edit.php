<?php

use forms\core\Request;
use forms\core\FormType;
use forms\core\ABTest;
use forms\core\Helper;

/**
 * Delete item please see Application::abTesting();
 */


$forms = $this->getForms();

$testId = Request::get( 'testId' );
$editAction = Request::get( 'action' );

$currentTest = null;

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

if ($testId !== '' && $testId !== null) {
    $currentTest = ABTest::get( $testId );

    if ($currentTest) {
        $pageId = $currentTest->getEnableOn();
        $enableOn = 'none';

        if ($pageId > 0) {
            $selectedPage =  get_post( $pageId );

            if ($selectedPage !== null) {
//            $enableOn = '<a target="_blank" href="'. esc_url( get_permalink( $pageId ) ) .'">';
                $enableOn = $selectedPage->post_title;
//            $enableOn .= '</a>';
            }
        }

    }


}


$availableOptions = '';

if (!empty( $forms )) {
    foreach ($forms as $form) {
        $availableOptions .= '<option value="'.$form->getId().'">';
        $availableOptions .= htmlDecodeEncode( $form->getName() );
        $availableOptions .= '</option>';

    }
}

$pages = Helper::getPages();
?>


<div class="wrap ab-test-edit">
    <div class=" notifications notice-error notice">
        <p></p>

    </div>
    <?php if (count($forms) < 2) :  ?>
        <div class="notice-error notice">
            <p>You need at least two forms in order to create AB Tests.  <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_form" class="page-title-action">Create a form</a> </p>

        </div>
    <?php else: ?>
        <div class="content">
            <div class="post-body-content">
                <form action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
                    <input type="hidden" name="action" value="handle_cm_form_request">
                    <input type="hidden" name="data[type]" value="save_ab_test">
                    <input type="hidden" name="data[app_nonce]" value="<?php echo wp_create_nonce( 'app_nonce' ); ?>">
                    <input type="hidden" name="test_id" value="<?php echo ($currentTest==null) ? "" : filter_var($currentTest->getId(), FILTER_SANITIZE_STRING); ?>" />

                    <?Php if ($currentTest !== null ) : ?>
                        <lable>Test Title: </lable>
                        <input type="text" class="regular-text" value="<?php echo htmlDecodeEncode($currentTest->getName()); ?>" id="testTitle" name="test_title">
                        <lable>Enable On: </lable>
                        <select name="enable_on" class="regular-text wide-select" id="form_secondary" style="vertical-align: baseline" >
                            <option value="">--- none ---</option>
                            <option value="-1">--- All Pages ---</option>

                            <?php if (!empty($pages)) : ?>
                                <?php foreach ( $pages as $pageId => $pageTitle ) : ?>
                                    <option value="<?php echo htmlDecodeEncode($pageId); ?>" <?php echo ($pageTitle == $enableOn) ? 'selected' : '' ?>><?php echo htmlDecodeEncode($pageTitle); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <table class="wp-list-table widefat fixed striped pages">
                            <thead>
                            <tr>
                                <th scope="col" id="title" class="manage-column column-title column-primary sortable">
                                    <span><?php _e('Form'); ?></span><span class="sorting-indicator"></span>
                                </th>
                                <th scope="col" id="author" class="manage-column column-status sortable ">
                                    <span><?php echo esc_html__('Impressions'); ?></span><span class="sorting-indicator"></span>
                                </th>
                                <th scope="col" id="formType" class="manage-formType column-formType sortable ">
                                    <span>Submissions</span><span class="sorting-indicator"></span>
                                </th>
                                <th scope="col" id="pages" class="pages-column column-pages pages">
                                    <span>Submission Rate</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="the-list">
                            <?php foreach ($currentTest->getTests() as $test) :  ?>
                                <tr id="post-5" class="iedit author-self level-0 post-5 type-page status-publish hentry">
                                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><strong>
                                            <?php echo htmlDecodeEncode($test->getForm()->getName());  ?>
                                        </strong>
                                        <div class="locked-info">
                                            <span class="locked-avatar"></span>
                                            <span class="locked-text"></span></div>

                                        <div class="row-actions">
                                        </div><button type="button" class="toggle-row">
                                            <span class="screen-reader-text">Show more details</span></button>
                                    </td>
                                    <td class="type column-type" data-colname="Type">
                                        <?php echo filter_var($test->getImpressions(), FILTER_SANITIZE_STRING); ?>
                                    </td>
                                    <td class="pages column-pages" data-colname="Type">
                                        <?php echo filter_var($test->getSubmissions(), FILTER_SANITIZE_STRING);; ?>
                                    </td>
                                    <td class="pages column-pages" data-colname="Type">
                                        <?php echo filter_var(round($test->getSubmissionRate(), 2) * 100, FILTER_SANITIZE_STRING); ?>%
                                    </td>
                                </tr>
                            <?php  endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th scope="col" id="title" class="manage-column column-title column-primary sortable">
                                    <span>Form</span><span class="sorting-indicator"></span>
                                </th>
                                <th scope="col" id="author" class="manage-column column-status sortable ">
                                    <span>Impressions</span><span class="sorting-indicator"></span>
                                </th>
                                <th scope="col" id="formType" class="manage-formType column-formType sortable ">
                                    <span>Submissions</span><span class="sorting-indicator"></span>
                                </th>
                                <th scope="col" id="pages" class="pages-column column-pages pages">
                                    <span>Submission Rate</span>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    <?php else : ?>
                        <h1><?php _e('AB Testing '); ?></h1>

                        <table class="form-table cm-settings-fields">
                            <tbody><tr>
                                <th><label for="test_title"><?php _e('A/B Test Title'); ?></label></th>
                                <td>
                                    <input type="text" class="regular-text" value="" id="testTitle" name="test_title">
                                    <br>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="form_primary">Primary Form</label></th>
                                <td>
                                    <select name="form_primary" class="regular-text wide-select" id="form_primary" >
                                        <option>Select</option>
                                        <?php echo $availableOptions; ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <th><label for="form_secondary">Secondary Form</label></th>
                                <td>
                                    <select name="form_secondary" class="regular-text wide-select" id="form_secondary" >
                                        <option>Select</option>
                                        <?php echo $availableOptions; ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <th><label for="enable_on">Enable this test on</label></th>
                                <td>
                                    <select name="enable_on" class="regular-text wide-select" id="form_secondary" >
                                        <?php if (!empty($pages)) : ?>
                                            <?php foreach ( $pages as $pageId => $pageTitle ) : ?>
                                                <option value="<?php echo htmlDecodeEncode($pageId); ?>"><?php echo htmlDecodeEncode($pageTitle); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </select>

                                </td>
                            </tr>
                            </tbody>

                        </table>


                    <?php endif; ?>

                    <button id="btnSaveSettings" type="submit" class="button button-primary regular-text ltr">
                        Save Changes
                    </button>

                </form>
            </div>
        </div>
    <?php endif; ?>

</div>
<div class="progress-notice">
    <div class="sk-circle">
        <div class="sk-circle1 sk-child"></div>
        <div class="sk-circle2 sk-child"></div>
        <div class="sk-circle3 sk-child"></div>
        <div class="sk-circle4 sk-child"></div>
        <div class="sk-circle5 sk-child"></div>
        <div class="sk-circle6 sk-child"></div>
        <div class="sk-circle7 sk-child"></div>
        <div class="sk-circle8 sk-child"></div>
        <div class="sk-circle9 sk-child"></div>
        <div class="sk-circle10 sk-child"></div>
        <div class="sk-circle11 sk-child"></div>
        <div class="sk-circle12 sk-child"></div>
    </div>
</div>
