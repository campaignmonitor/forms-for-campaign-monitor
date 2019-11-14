<?php

use forms\core\Settings;
use forms\core\Options;
use forms\core\Helper;
use forms\core\Translator;

//\forms\core\Application::update();

$clientSecret = Settings::get('client_secret');
$clientId = Settings::get('client_id');
$recaptchaKey = Settings::get( 'recaptcha_key' );
$recaptchaPublic = Settings::get( 'recaptcha_public' );
$debugMode = Settings::get( 'debug' );

$connected = Options::get('connected');
$noSSL = Options::get('no_ssl');
$error = Options::get('post_errors');

if (!empty($error)) {
    Options::update('no_ssl', false);

    $html = '<div id="message" class="notice-error notice is-dismissible">';
    $html .= '<h2>';
    $html .= $error['title'];
    $html .= '</h2>';
    $html .= '<p>';
    $html .=  $error['description'];
    $html .= '</p>';
    $html .= '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
    $html .= '</div><!-- .updated -->';
    echo $html;
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
        <div class="content">
            <div class="post-body-content">
                <h1>Campaign Monitor Settings</h1>
                <?php if (!$connected) { ?>
                    <h2>Campaign Monitor Client ID and Client Secret</h2>
                    <p>Please enter your client ID and client secret.</p>
                    <p>To retrieve them:</p>
                    <ol>
                        <li>In your Campaign Monitor account, select <strong>App Store</strong> tab in the top navigation.
                        If you don't see it, you are using the multi-client edition of Campaign Monitor, and will need to select a client first. </li>
                    <li>
                        In the "OAuth Registrations" section, find Wordpress, then select <strong>View</strong> next to the Wordpress icon.
                    </li>
                        <li>
                            Copy paste the client ID and client secret into the fields below, then select <strong>Save Changes.</strong>
                        </li>
                    </ol>
                <?php } ?>
            
                <form action="<?php echo get_admin_url(); ?>admin-post.php" method="post">
                    <input type="hidden" name="action" value="handle_cm_form_request">
                    <input type="hidden" name="data[type]" value="save_settings">
                    <input type="hidden" name="data[app_nonce]" value="<?php echo wp_create_nonce( 'app_nonce' ); ?>">
                    <table class="form-table cm-settings-fields">
                        <tbody><tr>
                            <th><label for="client_id">Client ID</label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo filter_var($clientId, FILTER_SANITIZE_STRING); ?>" id="client_id" name="client_id" <?php echo $connected ? 'disabled' : ''?>>
                                <br>
                                <span class="description"></span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="client_secrect">Client Secret</label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo filter_var($clientSecret, FILTER_SANITIZE_STRING); ?>" id="client_secret" name="client_secret" <?php echo $connected ? 'disabled' : ''?>>
                                <br>
                                <span class="description"></span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="client_secrect">Google ReCaptcha Site Key</label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo filter_var($recaptchaPublic, FILTER_SANITIZE_STRING); ?>" id="recaptcha_public" name="recaptcha_public">
                                <br>
                                <span class="description">

                                </span>
                            </td>
                        </tr>                            <tr>
                            <th><label for="client_secrect">Google ReCaptcha Secret Key</label></th>
                            <td>
                                <input type="text" class="regular-text" value="<?php echo filter_var($recaptchaKey, FILTER_SANITIZE_STRING); ?>" id="recaptcha_key" name="recaptcha_key">
                                <br>
                                <span class="description">
                                    reCAPTCHA is a free service that protects your site from spam and abuse.<br>
                                    It uses advanced risk analysis techniques to tell humans and bots apart.<br>
                                    With the new API, a significant number of your valid human users will pass the<br>
                                    reCAPTCHA challenge without having to solve a CAPTCHA. reCAPTCHA comes in<br>
                                    the form of a widget that you can easily add to your blog, forum, registration form, etc.
                                    <a href="http://www.google.com/recaptcha/admin" target="_blank">
                                        GET STARTED
                                    </a>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="debug_switch">Debug Mode</label></th>
                            <td>
                                <select  class="regular-text" style="width: 25em;" id="debug_switch" name="debug_switch">
                                    <option value="0">No</option>
                                    <option value="1" <?php echo $debugMode ? 'selected' : ''; ?>>Yes</option>
                                </select>
                                <span class="description">

                                </span>
                            </td>
                        </tr>
                        </tbody>

                    </table>

                    <button id="btnSaveSettings" type="submit" class="button button-primary regular-text ltr">
                        Save Changes
                    </button>
                    <button id="btnLogOut" type="submit" name="disconnect" value="true"  class="button button-secondary regular-text ltr">
                        Disconnect Account
                    </button>

                </form>

            <!-- Debug Information-->
                <?php if ($debugMode) : ?>
                <div>
                    <?php

                    global $wpdb;

                    //WP_DEBUG
                    $debug = defined( 'WP_DEBUG' ) && WP_DEBUG ? Translator::translate( 'Yes' ) : Translator::translate( 'No' );

                    //WPLANG
                    $lang = defined( 'WPLANG' ) && WPLANG ? WPLANG : __( 'Default' );

                    //SUHOSIN
                    $suhosin = extension_loaded( 'suhosin' ) ? __('Yes') : __( 'No');
                    $default_timezone = get_option( 'timezone_string' );

                    //Check for active plugins
                    $active_plugins = (array) get_option( 'active_plugins', array() );

                    if ( is_multisite() ) {
                        $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                    }

                    $all_plugins = array();

                    foreach ( $active_plugins as $plugin ) {
                        $plugin_data    = get_plugin_data( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin );
                        $dirname        = dirname( $plugin );
                        $version_string = '';

                        if ( ! empty( $plugin_data['Name'] ) ) {

                            // link the plugin name to the plugin url if available
                            $plugin_name = $plugin_data['Name'];
                            if ( ! empty( $plugin_data['PluginURI'] ) ) {
                                $plugin_name = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin homepage' ) . '">' . $plugin_name . '</a>';
                            }

                            $all_plugins[] = $plugin_name . ' ' . __( 'by') . ' ' . $plugin_data['Author'] . ' ' . __( 'version') . ' ' . $plugin_data['Version'] . $version_string;
                        }
                    }

                    $site_wide_plugins = '-';
                    if ( count( $all_plugins ) !== 0 ) {
                        $site_wide_plugins = implode( ', <br/>', $all_plugins );
                    }


                    $host_name = gethostname();

                     $campaignMonitorPluginData = get_plugin_data(\forms\core\Application::getPluginPath('campaign-monitor.php'));

                    $pluginVersion = !empty( $campaignMonitorPluginData ) && array_key_exists( 'Version', $campaignMonitorPluginData ) ? $campaignMonitorPluginData['Version'] : \forms\core\Application::VERSION;
                    $server_ip = gethostbyname($host_name);

//                    Helper::display( $_SERVER );
                    //Output array
                    $environment = array(
                        Translator::translate( 'Home URL') => home_url(),
                        Translator::translate( 'Site URL') => site_url(),
                        Translator::translate( 'Forms for Campaign Monitor Version') => esc_html( $pluginVersion),
                        Translator::translate( 'WP Version') => get_bloginfo('version'),
                        Translator::translate( 'WP Multisite Enabled') => is_multisite() ? 'Yes' : 'No',
                        Translator::translate( 'Web Server Info') => esc_html( $_SERVER['SERVER_SOFTWARE'] ),
                        Translator::translate( 'PHP Version') => esc_html( phpversion() ),
                        Translator::translate( 'MySQL Version') => $wpdb->db_version(),
                        Translator::translate( 'WP Memory Limit') => WP_MEMORY_LIMIT,
                        Translator::translate( 'WP Debug Mode') => $debug,
                        Translator::translate( 'WP Language') => $lang,
                        Translator::translate( 'WP Max Upload Size') => size_format( wp_max_upload_size() ),
                        Translator::translate('PHP Post Max Size') => ini_get( 'post_max_size' ),
                        Translator::translate('Max Input Nesting Level') => ini_get('max_input_nesting_level'),
                        Translator::translate('PHP Time Limit') => ini_get('max_execution_time'),
                        Translator::translate( 'PHP Max Input Vars') => ini_get('max_input_vars'),
                        Translator::translate( 'SUHOSIN Installed') => $suhosin,
                        Translator::translate( 'Server IP Address') => $server_ip,
                        Translator::translate( 'Host Name') => $host_name,
                        Translator::translate( 'SMTP') => ini_get('SMTP'),
                        Translator::translate( 'smtp_port') => ini_get('smtp_port'),
                        Translator::translate( 'Default Timezone') => $default_timezone,
                        Translator::translate( 'Site Plugins') => $site_wide_plugins,

                    );

                    $logFilename = \forms\core\Log::getFileName();
                    $logFilename = !empty($logFilename) ? $logFilename : \forms\core\Application::getPluginPath('var/log');

                    $logContent = '';
                    if (!is_dir( $logFilename )) {
                        $logContent = file_get_contents( $logFilename );
                        $logContent = substr( $logContent, 0, 10000 );
                    }

                    $date = new \DateTime();
                    $today = $date->format('Y-m-d H:i:s');
                    $date->modify( '+1 day' );
                    $tomorrow = $date->format('Y-m-d H:i:s');
                    $cleanDate = Settings::get('log_clean_date');
                    $now     = strtotime($today);
                    $tomorrow  = strtotime($tomorrow);

                    $future = Settings::get('log_clean_date');
                    if ($now > $future){
                        forms\core\Log::trash();
                        Settings::add('log_clean_date',$tomorrow);
                    }
                    ?>
                    <br><br>
                    <h2>Debug Information</h2>
                    <table class="form-table cm-settings-fields">
                        <tbody>
                        <tr>
                            <th><label for="client_id">System Report</label></th>
                            <td>
                                <textarea onclick="this.select();" style="width: 65%; min-height: 15em" row="5" cols="30" readonly><?php echo json_encode($environment) ?></textarea>
                                <br><span class="description"></span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="client_id">Log file location</label></th>
                            <td>
                                <?php echo $logFilename ?>
                                <br><span class="description">Logs files are clean/deleted daily</span>
                            </td>
                        </tr>
                        <?php if (!empty($logContent)) : ?>
                        <tr>
                            <th><label for="client_id">Log Content</label></th>
                            <td>
                                <textarea onclick="this.select();" style="width: 65%; min-height: 15em" row="5" cols="30" readonly><?php echo $logContent ?></textarea>
                                <br><span class="description"></span>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php foreach ($environment as $settingTitle => $settingValue) : ?>
                            <tr>
                                <th><label ><?php echo $settingTitle;?></label></th>
                                <td>
                                   <?php echo $settingValue; ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>

                </div>
                <?php endif; ?>
            <!-- Debug Information-->


            </div> <!-- post-body-content end-->
        </div>
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
