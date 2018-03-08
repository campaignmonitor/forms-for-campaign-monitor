<?php

$version = '2.0';

?>
<div class="wrap">
    <div id="message" class="notice-error notice">
        <p>This is a major update. Please check and customize the appearance of your forms after you upgrade the plugin.</p>

    </div>
    <form id="appUpgradeForm" action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
        <h1>Forms for Campaign Monitor Update</h1>
        <input type="hidden" name="action" value="handle_cm_form_request">
        <input type="hidden" name="data[type]" value="upgrade">
        <input type="hidden" name="data[app_nonce]" value="<?php echo wp_create_nonce( 'app_nonce' ); ?>">
        <h3><?php echo __('Welcome', 'campaign-monitor-forms'); ?></h3>
        <p><?php echo sprintf( __( 'We have significantly updated the plugin.', 'campaign-monitor-forms' ), $version ); ?></p>
        <ul style="list-style:disc; padding-left: 1em">
            <li>Completely customize each form's colors and styling.</li>
            <li>Add captcha to your forms to prevent spam.</li>
            <li>Connect Campaign Monitor and Wordpress using <a href="https://en.wikipedia.org/wiki/OAuth">OAuth</a> for better security.</li>
            <li>Performance improvements.</li>
            <li>Tons of bug fixes.</li>
        </ul>

        <br>
        <button id="appUpgradeButton" type="submit" class="button-primary">Update Now</button>
    </form>
</div>
<div class="modal-update">

    <h4>Updating please wait...</h4>
    <div class="sk-cube-grid">
        <div class="sk-cube sk-cube1"></div>
        <div class="sk-cube sk-cube2"></div>
        <div class="sk-cube sk-cube3"></div>
        <div class="sk-cube sk-cube4"></div>
        <div class="sk-cube sk-cube5"></div>
        <div class="sk-cube sk-cube6"></div>
        <div class="sk-cube sk-cube7"></div>
        <div class="sk-cube sk-cube8"></div>
        <div class="sk-cube sk-cube9"></div>
    </div>
</div>