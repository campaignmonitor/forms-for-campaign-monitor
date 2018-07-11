<?php

use forms\core\Helper;
use forms\core\Request;
use forms\core\Settings;
use forms\core\Application;
use \forms\core\FormType;


error_reporting(E_ALL);
ini_set('display_errors', 1);

$imagesDirectory = Application::getPluginPath( 'forms/views/admin/images', true );

$this->getAppSettings();
$notices = array();
?>
<div class="wrap">

    <h1>Select Form Type</h1>
    <br>

    <style>
    .theme-browser .theme.active h2.theme-name,
    .theme-browser .theme.active div.theme-actions
    { background-color: #E7E7E7; color:#23282d; -webkit-box-shadow: none; box-shadow:none; }
    </style>

    <div class="theme-browser rendered">
        <div class="wp-clearfix">
            <?php
            $url = get_admin_url().'admin.php?page=campaign_monitor_create_builder'.'&amp;'.'formType='.urlencode(FormType::SLIDE_OUT);
            ?>
            <div class="theme active" tabindex="0" aria-describedby="slideout-action slideout-name"  data-slug="slideout">
                <div class="theme-screenshot">
                    <a href="<?php echo $url; ?>"><img src="<?php echo $imagesDirectory; ?>/option-slide-out.png" alt="Slide Out"></a>
                </div>
                <a href="<?php echo $url; ?>"><span class="more-details" id="slideout-action">
                    Select
                </span></a>

                <h2 class="theme-name" id="slideout-name">
                    <span>Slide Out</span>
                </h2>
                <div class="theme-actions">
                    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $url; ?>">
                        Select
                    </a>

                </div>
            </div>
            <?php
            $url = get_admin_url().'admin.php?page=campaign_monitor_create_builder'.'&amp;'.'formType='.urlencode(FormType::LIGHTBOX);
            ?>
            <div class="theme active" tabindex="0" aria-describedby="lightbox-action lightbox-name"  data-slug="lightbox">
                <div class="theme-screenshot">
                    <a href="<?php echo $url; ?>"><img src="<?php echo $imagesDirectory; ?>/option-lightbox.png" alt="Lightbox"></a>
                </div>
                <a href="<?php echo $url; ?>"><span class="more-details" id="lightbox-action">
                    Select
                </span></a>

                <h2 class="theme-name" id="lightbox-name">
                    <span>Lightbox</span>
                </h2>
                <div class="theme-actions">
                    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $url; ?>">
                        Select
                    </a>

                </div>
            </div>
            <?php
            $url = get_admin_url().'admin.php?page=campaign_monitor_create_builder'.'&amp;'.'formType='.urlencode(FormType::EMBEDDED);
            ?>
            <div class="theme active" tabindex="0" aria-describedby="lightbox-action lightbox-name"  data-slug="lightbox">
                <div class="theme-screenshot">
                    <a href="<?php echo $url; ?>"><img src="<?php echo $imagesDirectory; ?>/option-embedded.png" alt=""></a>
                </div>
                <a href="<?php echo $url; ?>"><span class="more-details" id="lightbox-action">
                    Select
                </span></a>

                <h2 class="theme-name" id="lightbox-name">
                    <span>Embedded</span>
                </h2>
                <div class="theme-actions">
                    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $url; ?>">
                        Select
                    </a>

                </div>
            </div>
            <?php
            $url = get_admin_url().'admin.php?page=campaign_monitor_create_builder'.'&amp;'.'formType='.urlencode(FormType::BAR);
            ?>
            <div class="theme active" tabindex="0" aria-describedby="lightbox-action lightbox-name"  data-slug="lightbox">
                <div class="theme-screenshot">
                    <a href="<?php echo $url; ?>"><img src="<?php echo $imagesDirectory; ?>/option-bar.png" alt="Bar"></a>
                </div>
                <a href="<?php echo $url; ?>"><span class="more-details" id="lightbox-action">
                    Select
                </span></a>

                <h2 class="theme-name" id="lightbox-name">
                    <span>Bar</span>
                </h2>
                <div class="theme-actions">
                    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $url; ?>">
                        Select
                    </a>

                </div>
            </div>
            <?php

            $url = get_admin_url().'admin.php?page=campaign_monitor_create_builder'.'&amp;'.'formType='.urlencode(FormType::BUTTON);
            ?>
            <div class="theme active" tabindex="0" aria-describedby="lightbox-action lightbox-name"  data-slug="lightbox">
                 <div class="theme-screenshot">
                    <a href="<?php echo $url; ?>"><img src="<?php echo $imagesDirectory; ?>/option-button.png" alt="Button"></a>
                </div>
                <a href="<?php echo $url; ?>"><span class="more-details" id="lightbox-action">
                    Create Form
                </span></a>

                 <h2 class="theme-name" id="lightbox-name">
                    <span>Button</span>
                </h2>
                 <div class="theme-actions">
                    <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $url; ?>">
                        Select
                    </a>

                </div>
             </div>


        </div>
    </div>
    <div class="theme-overlay"></div>

    <p class="no-themes">No themes found. Try a different search.</p>


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
