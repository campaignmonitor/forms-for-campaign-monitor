<?php

use forms\core\Helper;
use forms\core\Request;
use forms\core\Settings;
use forms\core\FormType;


$appSettings  = Settings::get();
$redirectUrl = Helper::getRedirectUrl();

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

$forms = \forms\core\Form::getAll($orderBy, $ascOrDesc, $searchStr);

$notices = array();

$isUpdated = (bool)\forms\core\Request::get("isUpdated");

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

?>
<div class="wrap">
    <h1>Campaign Monitor<?php if (\forms\core\Application::isConnected()) { ?> <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_form" class="page-title-action">Create a form</a> <?php } ?></h1>

    <?php if ( (is_array($notices) && !in_array('connected_list_notice',$notices, TRUE ) ) && !empty($currentList)) : ?>
        <div data-method="connected_list_notice" class="updated notice cm-plugin-ad is-dismissible">
            <p>Your Wordpress customer data can be accessed in the list, <strong><?php echo htmlDecodeEncode($currentList->Title); ?></strong>, in
                <a href="https://www.campaignmonitor.com?utm_source=wordpress-plugin&utm_medium=referral" target="_blank">Campaign Monitor</a>.&nbsp;
                We've also created 6 segments for you there.
            </p>
        </div>
    <?php endif; ?>

    <?php if ($isUpdated) : ?>

        <div id="message" class="notice-success notice is-dismissible"><p>Form updated.</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
    <?php endif; ?>
    <?php if (!\forms\core\Application::isConnected()) : ?>
        <p>Campaign Monitor for WordPress is almost ready.
            <b>Connect your Campaign Monitor account to get started.</b></p>

        <p><a id="btnConnect" class="static button button-primary" target="_blank" href="<?php echo \forms\core\Application::getConnectUrl(); ?>">Connect</a></p>
    <?php else : ?>

    <?php /*<h1><a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_form" class="page-title-action">Add New Form</a></h1>*/ ?>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder<?php /* columns-2*/ ?>">
            <!-- /post-body-content -->

            <?php /*<div id="postbox-container-1" class="postbox-container">
                            <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
                                <div id="submitdiv" class="postbox ">
                                    <button type="button" class="handlediv button-link" aria-expanded="true">
                                        <span class="screen-reader-text">Toggle panel: Publish</span>
                                        <span class="toggle-indicator" aria-hidden="true"></span>
                                    </button><h2 class="hndle ui-sortable-handle"><span>Log Out from your Account</span></h2>
                                    <div class="inside">
                                        <div class="submitbox" id="submitpost">
                                            <div id="minor-publishing">


                                                <div id="minor-publishing-actions">
                                                    To disconnect Campaign Monitor for WordPress and remove all in-app form customization, click below.
                                                    <div class="clear"></div>
                                                </div><!-- #minor-publishing-actions -->

                                                <div id="misc-publishing-actions">

                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div id="major-publishing-actions">

                                                <form action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
                                                    <input type="hidden" name="action" value="handle_cm_form_request">
                                                    <input type="hidden" name="data[type]" value="account_disconnect">
                                                    <input type="hidden" name="data[app_nonce]" value="<?php echo wp_create_nonce( 'app_nonce' ); ?>">
                                                    <button id="btnSaveSettings" type="submit" class="button button-primary regular-text ltr">
                                                        Disconnect
                                                    </button>
                                                </form>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>*/

            ?>
            <div><?php /*id="postbox-container-2" class="postbox-container"*/ ?>

                <?php
                $urlSelfStart = get_admin_url()."admin.php?page=campaign-monitor-for-wordpress";
                $nameAscOrDesc="asc";
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


                ?>


                <form id="posts-filter" method="get" action="<?php echo get_admin_url().'admin.php'; ?>">
                    <input type="hidden" name="orderby" value="<?php echo htmlDecodeEncode($orderBy); ?>" />
                    <input type="hidden" name="ascOrDesc" value="<?php echo htmlDecodeEncode($ascOrDesc); ?>" />
                    <input type="hidden" name="page" value="campaign-monitor-for-wordpress" />
                    <p class="search-box">
                        <label class="screen-reader-text" for="post-search-input">Search Forms:</label>
                        <input id="post-search-input" name="s" value="<?php echo htmlDecodeEncode($searchStr); ?>" type="search">
                        <input id="search-submit" class="button" value="Search Forms" type="submit">
                    </p>

                </form>
                <div style="clear:both;"></div>

                <div class="tablenav top">

                    <?php /*<div class="alignleft actions bulkactions">
                                        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
                                            <option value="-1">Bulk Actions</option>
                                            <option value="edit" class="hide-if-no-js">Edit</option>
                                            <option value="trash">Move to Trash</option>
                                        </select>
                                        <input id="doaction" class="button action" value="Apply" type="submit">
                                    </div>
                                    <div class="alignleft actions">
                                        <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
                                        <select name="m" id="filter-by-date">
                                            <option selected="selected" value="0">All dates</option>
                                            <option value="201608">August 2016</option>
                                        </select>
                                        <input name="filter_action" id="post-query-submit" class="button" value="Filter" type="submit">
                                    </div>*/ ?>
                    <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($forms)." item"; if(count($forms)!=1) { echo "s"; } ?></span>
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
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=name&amp;asc_or_desc=<?php echo $nameAscOrDesc; ?>"><span>Title</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="author" class="manage-column column-status sortable <?php echo $isActiveAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=isActive&amp;asc_or_desc=<?php echo $isActiveAscOrDesc; ?>"><span>Status</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="formType" class="manage-formType column-formType sortable <?php echo $typeAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=type&amp;asc_or_desc=<?php echo $typeAscOrDesc; ?>"><span>Form Type</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="pages" class="pages-column column-pages pages">
                            <span>Pages</span>
                        </th>
                        <th scope="col" id="list" class="pages-list column-list list">
                            <span>List</span>
                        </th>
                        <th scope="col" id="createDate" class="manage-createDate column-createDate sortable <?php echo $createDateAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=createDate&amp;asc_or_desc=<?php echo $createDateAscOrDesc; ?>"><span>Created</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="updateDate" class="manage-updateDate column-updateDate sortable <?php echo $updateDateAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=updateDate&amp;asc_or_desc=<?php echo $updateDateAscOrDesc; ?>"><span>Modified</span><span class="sorting-indicator"></span></a>
                        </th>
                    </tr>
                    </thead><tbody id="the-list">
                    <?php if (count($forms) > 0) :

                        $cmListAr=array();
                        ?>
                        <?php foreach ($forms as $form) :

                        $currentFormId = $form->getId();


                        $cmListId = trim($form->getCampaignMonitorListId());
                        if (!isset($cmListAr[$cmListId]))
                        {
                            $listName="";
                            if (!empty($cmListId))
                            {
                                $cmListInfo=\forms\core\Application::$CampaignMonitor->get_list_details($cmListId);

                                if (!empty($cmListInfo->Title))
                                {
                                    $listName=trim($cmListInfo->Title);
                                }
                            }
                            $cmListAr[$cmListId]=$listName;
                        }


                        ?>

                        <tr id="post-5" class="iedit author-self level-0 post-5 type-page status-publish hentry<?php if (empty($cmListAr[$cmListId])) { echo " cmTableErrorRow";}?>">
                            <?php /*<th scope="row" class="check-column">			<label class="screen-reader-text" for="cb-select-5">Select Cart</label>
                                            <input id="cb-select-5" name="post[]" value="5" type="checkbox">
                                            <div class="locked-indicator"></div>
                                        </th>*/ ?><td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><strong>
                                    <a class="row-title" href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_builder&formId=<?php echo htmlDecodeEncode($currentFormId, ENT_QUOTES); ?>" aria-label="“Subscription Form” (Edit)"><?php echo htmlDecodeEncode($form->getName());  ?></a></strong>
                                <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>

                                <div class="row-actions">
                                                <span class="edit">
                                                    <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_builder&formId=<?php echo urlencode($currentFormId); ?>" aria-label="Edit Form">Edit</a>
                                                     | </span><span class="trash">
                                                    <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_builder&formId=<?php echo urlencode($form->getId()); ?>&amp;action=delete" data-id="submitdelete_<?php echo filter_var($currentFormId, FILTER_SANITIZE_STRING); ?>" class="submitdelete" aria-label="Move “Cart” to the Trash">Trash</a><?php /* |
                                                </span><span class="view"><a href="http://localhost/wp/cart/" rel="permalink" aria-label="View “Cart”">View</a>*/ ?>
                                                </span></div><button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
                            <td class="status column-status" data-colname="Status"><?php
                                if ($form->getIsActive())
                                {
                                    ?><strong>Enabled</strong><?php
                                }
                                else
                                {
                                    ?><em>Disabled<em><?php
                                }
                                ?></td>
                            <td class="type column-type" data-colname="Type"><?php echo htmlDecodeEncode(FormType::camelCaseToReadable($form->getType())); ?></td>
                            <td class="pages column-pages" data-colname="Type"><?php
                                $onPageAr=$form->getOnPageAr();
                                $sep="";
                                foreach ($onPageAr as $onPage)
                                {
                                    if (intval($onPage)==-1)
                                    {
                                        echo $sep."<em><strong>ALL PAGES</strong></em>";
                                        $sep=", ";
                                    }
                                    else
                                    {
                                        $page=$form->getPageName($onPage);
                                        if (!empty($page))
                                        {
                                            echo $sep."".htmlDecodeEncode($page);
                                            $sep=", ";
                                        }
                                    }
                                }
                                if (empty($sep))
                                {
                                    echo "<i>none</i>";
                                }
                                ?></td>
                            <td><?php
                                if (empty($cmListAr[$cmListId]))
                                {
                                    echo "<strong>List Not Found</strong>";
                                }
                                else
                                {
                                    echo htmlDecodeEncode($cmListAr[$cmListId]);
                                }
                                ?></td>
                            <td class="updateDate column-updateDate" data-colname="updateDate"><?php echo $form->getCreateDate(1); ?></td>
                            <td class="createDate column-createDate" data-colname="createDate"><?php echo $form->getUpdateDate(1); ?></td>
                            <?php /*<td class="comments column-comments" data-colname="Comments">		<div class="post-com-count-wrapper">
                                                <span aria-hidden="true">—</span><span class="screen-reader-text">No comments</span><span class="post-com-count post-com-count-pending post-com-count-no-pending">
                                                    <span class="comment-count comment-count-no-pending" aria-hidden="true">0</span>
                                                    <span class="screen-reader-text">No comments</span></span>
                                            </div>
                                        </td>
                                        <td class="date column-date" data-colname="Date">Published<br><abbr title="2016/08/22 6:20:47 pm">2016/08/22</abbr>
                                        </td>	*/ ?></tr>

                        <?php
                    endforeach;
                    else:
                        ?><tr><td colspan="6">You currently don't have any forms. <a href="<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_create_form">Create a form</a>.</td></tr><?php
                    endif; ?>

                    </tbody>
                    <?php /*<tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                                            <label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox">
                                        </td>
                        <th scope="col" id="title2" class="manage-column column-title column-primary sortable <?php echo $nameAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=name&amp;asc_or_desc=<?php echo $nameAscOrDesc; ?>"><span>Title</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="author2" class="manage-column column-status sortable <?php echo $isActiveAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=isActive&amp;asc_or_desc=<?php echo $isActiveAscOrDesc; ?>"><span>Status</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="formType2" class="manage-formType column-formType sortable <?php echo $typeAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=type&amp;asc_or_desc=<?php echo $typeAscOrDesc; ?>"><span>Form Type</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="pages2" class="pages-column column-pages pages">
                            <span>Pages</span>
                        </th>
                        <th scope="col" id="list" class="pages-list column-list list">
                            <span>List</span>
                        </th>
                        <th scope="col" id="createDate2" class="manage-createDate column-createDate sortable <?php echo $createDateAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=createDate&amp;asc_or_desc=<?php echo $createDateAscOrDesc; ?>"><span>Created</span><span class="sorting-indicator"></span></a>
                        </th>
                        <th scope="col" id="updateDate2" class="manage-updateDate column-updateDate sortable <?php echo $updateDateAscOrDesc; ?>">
                            <a href="<?php echo $urlSelfStart; ?>&amp;order_by=updateDate&amp;asc_or_desc=<?php echo $updateDateAscOrDesc; ?>"><span>Modified</span><span class="sorting-indicator"></span></a>
                        </th>
                    </tr>
                    </tfoot>
                    */ ?>
                </table>


                <?php /*<div class="tablenav bottom">

                                    <div class="alignleft actions bulkactions">
                                        <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
                                            <option value="-1">Bulk Actions</option>
                                            <option value="edit" class="hide-if-no-js">Edit</option>
                                            <option value="trash">Move to Trash</option>
                                        </select>
                                        <input id="doaction2" class="button action" value="Apply" type="submit">
                                    </div>
                                    <div class="alignleft actions">
                                    </div>
                                    <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($forms)." item"; if(count($forms)!=1) { echo "s"; } ?></span>
                                        <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                                        <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                                        <span class="screen-reader-text">Current Page</span>
                                            <span id="table-paging" class="paging-input">
                                                <span class="tablenav-paging-text">1 of
                                                    <span class="total-pages">1</span>
                                                </span>
                                            </span>
                                        <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                                        <span class="tablenav-pages-navspan" aria-hidden="true">»</span></span>
                                    </div>
                                    <br class="clear">
                                </div> */ ?>

                <?php /*</form>*/ ?>
            </div>
            <!-- /post-body -->
            <br class="clear">
        </div><!-- /poststuff -->

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