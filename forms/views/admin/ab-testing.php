<?php

use forms\core\Request;
use forms\core\FormType;
use forms\core\Form;
use forms\core\Helper;



/*
 * @var array
 */
$tests = $this->getTests();

$urlSelfStart = get_admin_url()."admin.php?page=campaign-monitor-for-wordpress";
$nameAscOrDesc="asc";

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

$searchText=Request::get('s');
$orderBy=Request::get('order_by');
$ascOrDesc=Request::get('asc_or_desc');
$searchStr=Request::get('s');
if (empty($orderBy))
{
    $orderBy="name";
}
if (empty($ascOrDesc))
{
    $ascOrDesc="asc";
}

if ($orderBy=="name" && $ascOrDesc=="asc")
{
    $nameAscOrDesc="desc";
}

$isActiveAscOrDesc="asc";
if ($orderBy=="isActive" && $ascOrDesc=="asc")
{
    $isActiveAscOrDesc="desc";
}

$typeAscOrDesc="asc";
if ($orderBy=="type" && $ascOrDesc=="asc")
{
    $typeAscOrDesc="desc";
}

$updateDateAscOrDesc="desc";
if ($orderBy=="updateDate" && $ascOrDesc=="desc")
{
    $updateDateAscOrDesc="asc";
}

$createDateAscOrDesc="desc";
if ($orderBy=="createDate" && $ascOrDesc=="desc")
{
    $createDateAscOrDesc="asc";
}
$notices = \forms\core\Request::get( 'notice' );

if (!empty( $notices )) {

    $html = '<div id="message" class="notice-success notice is-dismissible">';
    $html .= '<h2>';
    $html .= $notices['title'];
    $html .= '</h2>';
    $html .= '<p>';
    $html .=  $notices['description'];
    $html .= '</p>';
    $html .= '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
    $html .= '</div><!-- .updated -->';
    echo $html;
}

?>

<div class="wrap">

    <h1>A/B Testing <a class="page-title-action" href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_ab_testing_editing" class="page-title-action">Create new test</a></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <!-- /post-body-content -->
            <div>


                <div style="clear:both;"></div>

                <div class="tablenav top">

                    <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($tests)." item"; if(count($tests)!=1) { echo "s"; } ?></span>
                        <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                                        <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                                        <span class="paging-input">
                                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                                            <input class="current-page" id="current-page-selector" name="paged" value="1" size="1" aria-describedby="table-paging" type="text">
                                            <span class="tablenav-paging-text"> of <span class="total-pages">1</span></span>
                                        </span>
                                        <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                                        <span class="tablenav-pages-navspan" aria-hidden="true">»</span></span>
                    </div>
                    <br class="clear">
                </div>
                <h2 class="screen-reader-text">Pages list</h2>

                <table class="wp-list-table widefat fixed striped pages">
                    <thead>
                    <tr>
                        <?php /*<td id="cb" class="manage-column column-cb check-column">
                                            <label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox">
                                        </td>*/ ?>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable <?php echo $nameAscOrDesc; ?>">
                            <?php /*<a href="<?php echo $urlSelfStart; ?>&amp;order_by=name&amp;asc_or_desc=<?php echo $nameAscOrDesc; ?>">*/ ?><span>Title</span><?php /*<span class="sorting-indicator"></span></a>*/ ?>
                        </th>
                        <th scope="col" id="author" class="manage-column column-status sortable <?php echo $isActiveAscOrDesc; ?>">
                            <span>Status</span>
                        </th>
                        <th scope="col" id="formType" class="manage-formType column-formType sortable <?php echo $typeAscOrDesc; ?>">
                            <span>Page</span>
                        </th>
                        <th scope="col" id="pages" class="pages-column column-pages pages" style="border-left:1px solid #AAA;">
                            <span>Form A</span>
                        </th>
                        <th scope="col" id="createDate" class="manage-createDate column-createDate sortable <?php echo $createDateAscOrDesc; ?>">
                            <span>Impressions</span>
                        </th>
                        <th scope="col" id="updateDate" class="manage-updateDate column-updateDate sortable <?php echo $updateDateAscOrDesc; ?>">
                            <span>Submissions</span>
                        </th>
                        <th scope="col" id="updateDate" class="manage-updateDate column-updateDate sortable <?php echo $updateDateAscOrDesc; ?>">
                            <span>Rate</span>
                        </th>
                        <th scope="col" id="pages" class="pages-column column-pages pages" style="border-left:1px solid #AAA;">
                            <span>Form B</span>
                        </th>
                        <th scope="col" id="createDate" class="manage-createDate column-createDate sortable <?php echo $createDateAscOrDesc; ?>">
                            <span>Impressions</span>
                        </th>
                        <th scope="col" id="updateDate" class="manage-updateDate column-updateDate sortable <?php echo $updateDateAscOrDesc; ?>">
                            <span>Submissions</span>
                        </th>
                        <th scope="col" id="updateDate" class="manage-updateDate column-updateDate sortable <?php echo $updateDateAscOrDesc; ?>">
                            <span>Rate</span>
                        </th>
                    </tr>
                    </thead><tbody id="the-list">



                    <?php if (count($tests) > 0) : ?>
                        <?php foreach ($tests as $test) : ?>

                            <?php
                            $pageTitle = 'All Pages';

                            if ($test->getEnableOn() != -1) {
                                $pageTitle=get_the_title($test->getEnableOn());
                            }


                            $aFormName="";
                            $aImpressions=$aSubmissions=$aFormId=$aSubmissionRate=0;
                            $bFormName="";
                            $bImpressions=$bSubmissions=$bFormId=$bSubmissionRate=0;
                            $t=$test->getTests();
                            if (isset($t[0]))
                            {
                                $f=$t[0]->getForm();

                                if (!empty($f))
                                {
                                    //continue;


                                    $fid=$f->getId();

                                    $aImpressions=$t[0]->getImpressions();
                                    $aSubmissions=$t[0]->getSubmissions();

                                    $form=Form::getOne($fid);

                                    if ($form)
                                    {
                                        $aFormName=$form->getName();
                                        $aFormId=$form->getId();
                                    }
                                    else
                                    {
                                        $aFormName=$f->getName(); // the form wasn't found, get the name from the AB test info
                                    }
                                }
                            }
                            if (isset($t[1]))
                            {
                                $f=$t[1]->getForm();

                                if (!empty($f))
                                {
                                    //continue;


                                    $fid=$f->getId();

                                    $bImpressions=$t[1]->getImpressions();
                                    $bSubmissions=$t[1]->getSubmissions();

                                    $form=Form::getOne($fid);

                                    if ($form)
                                    {
                                        $bFormName=$form->getName();
                                        $bFormId=$form->getId();
                                    }
                                    else
                                    {
                                        $bFormName=$f->getName(); // the form wasn't found, get the name from the AB test info
                                    }
                                }
                            }
                            if ($aImpressions > 0)
                            {
                                $aSubmissionRate=$aSubmissions/$aImpressions;
                            }
                            if ($bImpressions > 0)
                            {
                                $bSubmissionRate=$bSubmissions/$bImpressions;
                            }
                            ?>

                            <tr id="post-5" class="iedit author-self level-0 post-5 type-page status-publish hentry">
                                <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><strong>
                                        <a class="row-title" href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_ab_testing_editing&testId=<?php echo htmlentities($test->getId()); ?>" aria-label="“AB Test” (Edit)"><?php echo htmlDecodeEncode($test->getName());  ?></a></strong>
                                    <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>

                                    <div class="row-actions">
                                                <span class="edit">
                                                    <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_ab_testing_editing&testId=<?php echo urlencode($test->getId()); ?>" aria-label="View Form">View</a>
                                                     | </span><span class="trash">
                                                    <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_ab_testing_editing&testId=<?php echo urlencode($test->getId()); ?>&amp;action=delete" data-id="submitdelete_<?php echo $test->getId(); ?>" class="submitdelete" aria-label="Move “Cart” to the Trash">Trash</a>
                                                </span>
                                    </div><button type="button" class="toggle-row">
                                        <span class="screen-reader-text">Show more details</span></button></td>
                                <td class="status column-status" data-colname="Status">
                                    <?php
                                    if ($test->getIsActive())
                                    {
                                        ?><strong>Enabled</strong><?php
                                    }
                                    else
                                    {
                                        ?><em>Disabled<em><?php
                                    }
                                    ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo htmlDecodeEncode($pageTitle); ?></td>

                                <td class="type column-type" data-colname="Type" style="border-left:1px solid #AAA;"><?php
                                    if (!empty($aFormId))
                                    {
                                        echo "<a href=\"".get_admin_url()."admin.php?page=campaign_monitor_create_builder&amp;&formId=".urlencode($aFormId)."\" target=\"_blank\">".htmlDecodeEncode($aFormName)."</a>";
                                    }
                                    else
                                    {
                                        echo htmlDecodeEncode($aFormName);
                                    }
                                    ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo intval($aImpressions); ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo intval($aSubmissions); ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo number_format($aSubmissionRate*100,1); ?>%</td>

                                <td class="type column-type" data-colname="Type" style="border-left:1px solid #AAA;"><?php
                                    if (!empty($bFormId))
                                    {
                                        echo "<a href=\"".get_admin_url()."admin.php?page=campaign_monitor_create_builder&amp;formId=".urlencode($bFormId)."\" target=\"_blank\">".htmlDecodeEncode($bFormName)."</a>";
                                    }
                                    else
                                    {
                                        echo htmlDecodeEncode($bFormName);
                                    }
                                    ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo intval($bImpressions); ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo intval($bSubmissions); ?></td>
                                <td class="type column-type" data-colname="Type"><?php echo number_format($bSubmissionRate*100,1); ?>%</td>




                            </tr>
                        <?php  endforeach; ?>
                    <?php  else: ?>
                        <tr>
                            <td colspan="11">You currently don't have any tests.
                                <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_ab_testing_editing">Create new test</a>.</td></tr>
                    <?php endif; ?>

                    </tbody>

                </table>
            </div>
            <!-- /post-body -->
            <br class="clear">
        </div><!-- /poststuff -->

    </div>
