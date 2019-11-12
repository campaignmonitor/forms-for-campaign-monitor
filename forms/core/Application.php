<?php

namespace forms\core;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Application
{
    /**
     * @var CampaignMonitor
     */
    public static $CampaignMonitor = null;

    const VERSION = '2.5.6';

    public static $shortCodeId = '';
    /**
     * Returns plugin root
     *
     * @param string $directory
     * @param bool $returnUri return url instead of path
     * @return string
     */
    public static function getPluginPath($directory = '', $returnUri = false)
    {
        return Config::getRoot($directory, $returnUri);
    }

    public static function pluginActivation()
    {

    }

    public static function pluginDeactivation()
    {

    }

    /**
     * @return string
     */
    public static function getConnectUrl(){
        $adminUri = get_admin_url() . 'admin.php';
        $instantiateUrl = self::$CampaignMonitor->instantiate_url("campaign-monitor-for-wordpress", $adminUri, Helper::getCampaignMonitorPermissions());

        return $instantiateUrl;

    }

    public static function doUpdate() {
        if (Application::isConnected()) {
            $pluginUpdate = self::UpdateStatus();
            $updatePage = Request::get('page');
            if (!$pluginUpdate && $updatePage !== 'campaign_monitor_update_page') {
                $location = get_admin_url() . 'admin.php?page=campaign_monitor_update_page';
                \wp_safe_redirect( $location );
                exit();
            }
        }
    }

    public static function authenticate()
    {
        $error = Request::get( 'error' );
        $queriedPage = Request::get( 'page' );

        $isError = !empty($error) && ($queriedPage === 'campaign-monitor-for-wordpress');

        if ($isError) {
            $title = $error;
            $description = Request::get('error_description');
            $postError['title'] = $title;
            $postError['description'] = $description;

            Log::write( $error );
            // add this options so they can be access later
            Options::add( 'no_ssl', 1 );
            Options::add('post_errors', $postError);


            $settingsPage = get_admin_url() . 'admin.php?page=campaign_monitor_settings_page';
            Log::write( "No ssl: " . $settingsPage );
            \wp_redirect( $settingsPage );
            exit();
        } else {
            Options::update('post_errors', '');
        }

        $fileContent = file_get_contents("php://input");

        if (!empty($fileContent)){
            $credentials = json_decode($fileContent);

            if (!empty($credentials) && (isset($credentials->ClientId) && isset($credentials->ClientSecret))){

                // extract client id and client secret from post request
                $clientId = $credentials->ClientId;
                $clientSecret = $credentials->ClientSecret;

                // save for subsequent request
                Settings::add('client_secret', $clientSecret );
                Settings::add('client_id', $clientId);

                $authorizeUrl = self::$CampaignMonitor->authorize_url($clientId,Helper::getRedirectUrl() , Helper::getCampaignMonitorPermissions() );

                Log::write( "Authorizing: " . $authorizeUrl );
                // redirect to get an access token
                \wp_redirect($authorizeUrl);
                exit();
            }
        }

        if (!empty($error)) {
            $description = Request::get('error_description');

            $html = '<div class="wrap">';
            $html .= '<h1>Campaign Monitor</h1>';
            $html .= '<div  id="error" class="error">';
            $html .= $error;
            $html .= '</div><!-- end error-->';
            $html .= '</div><!-- end wrap-->';

            echo $html;
            exit;
        } else {
            // initial connect
            $appSettings  = Settings::get();
            $redirectUrl = Helper::getRedirectUrl();
            $code = Request::get( 'code' );

            if (!empty($code)) {
                Options::update('code', $code);
                $params = array('grant_type' => urlencode('authorization_code'),
                    'client_id' => urlencode($appSettings['client_id']),
                    'client_secret' => urlencode($appSettings['client_secret']),
                    'code' => $code,
                    'redirect_uri' =>  $redirectUrl);

                $postUrl = Connect::getTransport('oauth/token', $params);
                $endpoint = 'https://api.createsend.com/oauth/token';
                $results =  Connect::request($params,$endpoint);

                // Let's authenticate the user
                if (!empty($results)) {
                    $credentials = json_decode($results);

                    if (isset($credentials->error)) {
                        Settings::add('client_secret', '');
                        Settings::add('client_id', '');
                        $postError['title'] = $credentials->error;
                        $postError['description'] = $credentials->error_description;

                        // add this options so they can be access later
                        Options::add('post_errors', $postError);

                        $settingsPage = get_admin_url() . 'admin.php?page=campaign_monitor_settings_page';
                        \wp_redirect( $settingsPage );
                        exit();
                    } else {
                        Application::updateTokens($credentials->access_token, $credentials->refresh_token, time() + $credentials->expires_in);
                        // we are connected
                        Options::update('connected', TRUE );
                        unset($_GET['code']);
                    }
                }
            }
        }
    }

    public static function generateShortcode($attributes, $content = "" ){
        extract(shortcode_atts(array(
            'type' => FormType::EMBEDDED,
            'form_id' => '',
        ), $attributes));

        if (!empty($form_id)){
            Application::$shortCodeId = $form_id;
            $content .= '<div class="cmApp_embedFormContainer" style="display:none;"></div><div class="cmApp_FormButtonContainer" style="display:none;"></div>';

        }

        return $content;
    }

    public static function run()
    {
        $logPath = Config::getRoot() .  'var' . DIRECTORY_SEPARATOR . 'log';
        Log::setDirectoryName( $logPath );
        $debug = Settings::get( 'debug' );
        Log::$switch = $debug;
        if ($debug) {
            ini_set( 'log_errors', 1 );
            ini_set( 'error_log', Log::getFileName() );
        }

        $accessToken = Settings::get('access_token');
        $refreshToken = Settings::get('refresh_token');
        self::$CampaignMonitor = new CampaignMonitor($accessToken, $refreshToken);

	    if (!Application::isConnected()) {
		    Application::authenticate();
	    } else {
            Application::refreshTokenIfNeeded();
        }

        // install app
        Application::init();

        add_shortcode( 'cm_form', array(__CLASS__, 'generateShortcode') );

        // Listen for ajax requests
        Ajax::run();
    }


    public static function generateUpdatePage()
    {
        $updateView = new View();
        $updateView->render( 'update' );
    }

    public static function update()
    {
        global $wpdb;
        $elementsTable = $wpdb->prefix.'cm_elements';
        $abTestTable = $wpdb->prefix.'cm_abtests';

        $existElementTable = $wpdb->get_var("SHOW TABLES LIKE '$elementsTable'");
        $existAbTestTable = $wpdb->get_var("SHOW TABLES LIKE '$abTestTable'");

        /**
         * @var
         * Array
        (
        [api_key] => 0bf7e09debc9317038f3a4etestkey
        [has_badge] => no
        )
         */
        $globalOptions = get_option('campaign_monitor_settings');
        
        // TODO: Clean up this and replace update.php with new instructions
        // if the old plugin is installed grab old forms and convert it to to the new format
        if( $existElementTable === $elementsTable && $existAbTestTable === $abTestTable ) {

            // grab all the old forms ad abtests
            $forms = \CampaignMonitorWpTableForms::get_data();
            $abTests = \CampaignMonitorWpTableABTests::get_data();

            $createdForms = array();
            $formTypeMap = array();

            $now =  date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) );

            $formTypeMap['slider'] = FormType::SLIDE_OUT;
            $formTypeMap['lightbox'] = FormType::LIGHTBOX;
            $formTypeMap['bar'] = FormType::BAR;
            $formTypeMap['button'] = FormType::BUTTON;
            $formTypeMap['simple_form'] = FormType::EMBEDDED;

            $clients = array(); // set as empty as code is broken anyway
            
            $activeClients = array();

            foreach ($clients as $client) {
                $clientLists = Application::$CampaignMonitor->get_client_list( $client->ClientID );
                $activeClients[$client->ClientID] = $clientLists;

            }


            // iterate through the results and update old forms to new format
            // without deleting old data
            if (!empty( $forms ) && count( $forms ) > 0) {

                foreach ($forms as $form) {

                    $formId = $form['id'];
                    $formName = $form['name'];
                    $oldForm = maybe_unserialize( $form['information'] );
                    $fieldsToUpdate = $oldForm['fields'];


                    $newForm = new Form();
                    $newFields = array();
                    $campaignMonitorFields = array();

                    if (isset( $oldForm['list_id'] )) {

                        if (!empty( $activeClients )) {
                            foreach ($activeClients as $clientId => $clientLists){

                                foreach ($clientLists as $clientList) {
                                    if ($clientList->ListID === $oldForm['list_id']) {
                                        $newForm->setCampaignMonitorListId( $oldForm['list_id'] );
                                        $newForm->setCampaignMonitorClientId( $clientId );
                                        break 2;
                                    }
                                }
                            }
                        }

                        $campaignMonitorFields = Application::$CampaignMonitor->get_custom_fields( $oldForm['list_id'] );

                    }


                    foreach ($fieldsToUpdate as $key => $options) {


                        $required = isset( $options['required'] ) ? true : false;
                        $enabled = (isset( $options['enabled'] ) && $options['enabled'] == 1);
                        $fieldType = isset($options['DataType']) ? $options['DataType'] : 'Text';

                        $placeholder = isset( $options['placeholder'] ) ? $options['placeholder'] : '';
                        $css = isset( $options['css_classes'] ) ? $options['css_classes'] : '';

                        if ($key == 'userInformation') {

                            $newForm->setHasNameField($enabled);
                            $newForm->setHasNameFieldLabel($options['label']);
                            continue;
                        }

                        if ($key == 'email') {
                            $newForm->setHasEmailFieldLabel($options['label'] );
                            continue;
                        }

                        if (!$enabled) continue;

                        $field = new FormField( $key );
                        $fieldName = !empty($options['FieldName']) ?$options['FieldName'] : $key;
                        $fieldLabel = !empty($options['FieldName']) ? $options['FieldName'] : '';

                        if (null !== $campaignMonitorFields && isset($options['Options']) ){

                            foreach ($campaignMonitorFields as $singleField){

                                if ($singleField->Key == "[$key]"){
                                    $o =  $singleField->FieldOptions;
                                    $o = array_map(array(__CLASS__, 'encode'), $o);
                                    $field->setOptions($o);

                                }
                            }

                        }

                        $field->setLabel( Application::encode($fieldLabel) );

                        $field->setName( Application::encode($fieldName) );

                        $field->isShowLabel( $options['label'] );

                        $field->setEnable( $enabled );
                        $field->setIsRequired( $required );

                        $field->setType($fieldType);


                        $attributes = array( 'placeholder' => $placeholder, 'class' => $css );
                        $field->setAttributes( $attributes );

                        $field->setKey( $key );
                        $newFields[] = $field;
                    }



                    $newForm->setFields( $newFields );
                    $newForm->setName( Application::encode($formName) );



                    if (isset( $oldForm['success_message_title'] )) {
                        $newForm->setSuccessMessageTitle( $oldForm['success_message_title'] );
                    }

                    if (isset( $oldForm['success_message'] )) {
                        $newForm->setSuccessMessage( $oldForm['success_message'] );
                    }
                    if (isset( $oldForm['form_title'] )) {
                        $newForm->setheader( Application::encode($oldForm['form_title']) );
                    }

                    // START new fields - AK@SI 01/10/2017
                    if (isset( $oldForm['form_summary'] )) {
                        $summary = substr($oldForm['form_summary'], 0, 255);
                        $summary = Application::encode($summary);
                        $newForm->setSubHeader( $summary );
                    }
                    if (isset( $oldForm['submitText'] )) {
                        $submitButtonLabel = substr($oldForm['submitText'],0,12);
                        $submitButtonLabel = Application::encode($submitButtonLabel);
                        $newForm->setSubmitButtonText( $submitButtonLabel );
                    }
                    // END new fields - AK@SI 01/10/2017

                    if (isset( $form['type'] )) {
                        $formType = $form['type'];
                        if (array_key_exists( $formType, $formTypeMap )) {
                            $newForm->setType( $formTypeMap[$formType] );
                        }

                        if ($formType === 'bar' && $oldForm['bar_position'] === 'bottom') {
                            $newForm->setFormPlacementBar( 'bottom' );
                        }

                        if ($formType === 'slider' && $oldForm['slider_position'] === 'left') {
                            $newForm->setFormPlacement( 'topLeft' );

                        }

                        if ($formType === 'lightbox' && !empty($oldForm['lightbox_delay_seconds']))
                        {
                            $newForm->setAppearsLightbox( 'seconds' );
                            $seconds = !empty($oldForm['lightbox_delay_seconds']) ? $oldForm['lightbox_delay_seconds'] : 0;
                            $newForm->setLightboxSeconds( $seconds );

                        }

                        if ($formType === 'lightbox' && !empty($oldForm['lightbox_delay_height']))
                        {
                            $newForm->setAppearsLightbox( 'scroll' );
                            $height = 50;


                            if (strpos($oldForm['lightbox_delay_height'], '%') !== false) {
                                $height = filter_var($oldForm['lightbox_delay_height'], FILTER_SANITIZE_NUMBER_INT);

                                $height = (100 < $height) ? 95 : $height;
                            } else {
                                $height = filter_var($oldForm['lightbox_delay_height'], FILTER_SANITIZE_NUMBER_INT);

                                if ( 0 == $height ) {
                                    $height = 0;
                                } else {
                                    $height = 50;
                                }
                            }

                            $newForm->setLightboxScrollPercent( $height );
                        }


                        if ($formType === 'button' && !empty($oldForm['text']))
                        {
                            $newForm->setButtonTypeText( $oldForm['text'] );
                        }

                    }

                    $activePages = array();
                    if (!empty( $oldForm['show_in'] )) {

                        foreach ($oldForm['show_in'] as $page) {

                            $assignedPage = get_page_by_path( $page );

                            if ($page === '/') {
                                $assignedPage = new \stdClass();
                                $assignedPage->ID = get_option( 'page_on_front' );

                            }

                            if (!empty( $assignedPage )) {
                                $activePages[] = $assignedPage->ID;
                            }
                        }
                    }

                    if (empty( $activePages )) {
                        $activePages[] = '';
                    }

                    if (!empty( $form['global'] ) && $form['global'] == 1) {
                        $activePages[] = -1;
                    }

                    $newForm->setOnPageAr( $activePages );


                    if (empty( $oldForm['hasBadge'] ) ) {
                        $newForm->setHasCampMonLogo( false );
                    } else {
                        $newForm->setHasCampMonLogo(true);
                    }


                    if (!empty( $form['created'] )) {
                        $newForm->setCreateDate( $form['created'] );
                    }
                    $newForm->setUpdateDate( $now );


                    $createdFormId = $newForm->save();
                    $createdForms[$formId] = $createdFormId;


                }

            }

            if (!empty( $abTests ) && count( $abTests ) > 0) {

                foreach ($abTests as $test) {
                    $oldAbTest = new \CampaignMonitorABTest();

                    // load a particular ab test
                    $oldAbTest->load($test['id']);

                    $primaryForm = $oldAbTest->data['first_element'];
                    $secondaryForm =$oldAbTest->data['second_element'] ;
                    $abTestShowInAr = $oldAbTest->data['show_in'] ;

                    $abTestShowIn=-1;
                    if (count($abTestShowInAr)>0)
                    {

                        $assignedPage = get_page_by_path( reset($abTestShowInAr) );
                        if (!empty($assignedPage))
                        {
                            $abTestShowIn =$assignedPage->ID ;
                        }
                    }


                    $primaryExist = array_key_exists( $primaryForm, $createdForms );
                    $secondaryExist = array_key_exists( $secondaryForm, $createdForms );

                    if ($primaryExist && $secondaryExist) {
                        $newAbTest = new ABTest( Application::encode($oldAbTest->name) );

                        $newAbTest->setEnableOn( $abTestShowIn );
                        $newAbTest->setIsActive( $oldAbTest->enabled );
                        $newAbTest->setCreatedAt( $oldAbTest->created );
                        $newAbTest->setModifiedAt( $now );

                        $primaryTestForm = Form::getOne( $createdForms[$primaryForm] );
                        $secondaryTestForm = Form::getOne( $createdForms[$secondaryForm] );


                        $primaryTest = new Test($primaryTestForm);
                        $primaryTest->setFormId( $primaryTestForm->getId() );
                        $primaryTest->setImpressions( $oldAbTest->data['first_element_shows'] );
                        $primaryTest->setSubmissions( $oldAbTest->data['first_element_submissions']);

                        $newAbTest->addTest( $primaryTest );

                        $secondaryTest = new Test($secondaryTestForm);
                        $secondaryTest->setFormId( $secondaryTestForm->getId() );
                        $secondaryTest->setImpressions( $oldAbTest->data['second_element_shows'] );
                        $secondaryTest->setSubmissions( $oldAbTest->data['second_element_submissions']);

                        $newAbTest->addTest( $secondaryTest );

                        $newAbTest->save();
                    }

                }
            }

        }


        Options::update( 'plugin_update', 2.0 );
    }

    public static function generateSettingsPage(){
        $settingsView = new View();
        $settingsView->render( 'settings' );
    }

    public static function generateConnectPage()
    {
        $appSettings = Settings::get();

        if (is_array( $appSettings ) && array_key_exists( 'client_secret', $appSettings ) && !empty( $appSettings['client_secret'] ) && Application::isConnected()) {
            $appSettings = (object)$appSettings;
            $auth = array( 'access_token' => $appSettings->access_token,
                'refresh_token' => $appSettings->refresh_token );
            

            $clients = Application::$CampaignMonitor->get_clients( $auth );
            Settings::add( 'campaign_monitor_clients', $clients );

	        if (!empty($clients)) {
		        if (\is_array($clients)) {
			        if (count($clients) === 1 && !empty($clients[0])) {
				        $CID = $clients[0]->ClientID;
				        Settings::add( 'default_client', $CID );
			        }
		        } else {
			        if (isset($clients->error)) {
				        Application::generateConnectionErrorMessage();
			        }
                }
	        }
        } else if (Application::isConnected() && (empty( $appSettings['client_secret'] ) || empty( $appSettings['client_id']))) {
            Application::generateConnectionErrorMessage();
        }

        $connectView = new View();
        $connectView->render( 'connect' );
    }

    public static function generateConnectionErrorMessage() {
        $message = 'To refresh your credentials please click on the "Disconnect Account" button';
        $message .= ' and then follow the on screen instruction to re-connect again!';
        $message = \urlencode( $message );
        ?>
        <div class="update-nag error is-dismissible notice">
            <p>
                There seems to be a problem with your credentials please disconnect and reconnect your account on the
                <a href="<?php echo  get_admin_url() . '/admin.php?page=campaign_monitor_settings_page&notice[description]='.filter_var($message,FILTER_SANITIZE_STRING).'&notice[title]=Notice!' ?>"> Campaign Monitor Settings </a> page
            </p>
        </div>
        <?php
    }

    public static function generateNewFormPage(){


        $appSettings  = Settings::get();
        $clientId = Settings::get( 'default_client' );
        $createView = new View();

        $createView->setClientId( $clientId  );
        $createView->setAppSettings( $appSettings );
        $createView->setSomeValue( 'test' );
        $createView->render( 'create' );
    }

    public static function generateFormBuilder() {
        $appSettings  = Settings::get();
        $clientId = Settings::get( 'default_client' );

        $formId=Request::getParam("formId");
        $formType=Request::getParam("formType");

        $noticeHtml="";
        switch (Request::getParam("action")) {
            case "delete":
                Form::remove( $formId );
                ?>
                <script>
                    window.location.href = "<?php echo Helper::getActionUrl(); ?>";
                </script>

                <?php
                die();
                break;
            case "save":

                break;
            default:
                // case "addForm":
                //
                $form = Form::getOne( $formId );

                if (null === $form) {

                    $form = new Form( $formType );


                    $nameField = new FormField('Name');
                    $nameField->setShowLabel( true );
                    $nameField->setType( DataType::TEXT );
                    $nameField->setName( 'Name' );
                    $nameField->setEnable( true );
                    $nameField->setIsRequired( true );
                    $nameField->setKey('[Name]');
                    $form->addField($nameField);

                    $emailField = new FormField('Email');
                    $emailField->setShowLabel( true );
                    $emailField->setType( DataType::TEXT );
                    $emailField->setName( 'Email' );
                    $emailField->setEnable( true );
                    $emailField->setIsRequired( true );
                    $emailField->setKey('[Email]');
                    $form->addField($emailField);

                }
                $form->setCampaignMonitorClientAr();
                break;

        }
        $createView = new View();

        $createView->setClientId( $clientId  );
        $createView->setAppSettings( $appSettings );
        $createView->setNotices($noticeHtml);

        $createView->setFormId($formId);

        $createView->setForm( $form );
        $createView->render( 'builder' );
    }

    protected static function UpdateStatus()
    {
        return Options::get( 'plugin_update' );
    }

    public static function adminRedirects() {
        global $pagenow;

        $isUpdateDone = self::UpdateStatus();
        $updatePage = Request::get('page');

        if (Application::isConnected()){
            if($pagenow === 'admin.php' && !$isUpdateDone && $updatePage !== 'campaign_monitor_update_page'){

                $goToUrl = admin_url('/admin.php?page=campaign_monitor_update_page');
                wp_redirect($goToUrl, 301);
                exit;
            }
        }

    }

    protected static function init()
    {

        register_activation_hook(Application::getPluginPath(), array(__CLASS__, 'pluginActivation'));
        register_deactivation_hook(Application::getPluginPath(), array(__CLASS__, 'pluginDeactivation'));

        add_action('admin_notices', array(__CLASS__, 'pluginNotices'));
        add_action('admin_menu', array(__CLASS__, 'createMenu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'loadAdminScripts'), 10);
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'loadPublicScripts'));
        }
        add_action('admin_post_handle_cm_form_request', array(__CLASS__, 'handleRequest'));
        add_action('admin_post_nopriv', array(__CLASS__, 'handleRequest'));
        add_action('admin_init', array(__CLASS__, 'adminRedirects'));

        add_action( 'wp', array(__CLASS__, 'loadCheckPage' ));
    }

    public static function removeScripts($hook_suffix) {
        global $wp_scripts;
        global $wp_styles;
        $screen = get_current_screen();
        $styles = array('admin-bar',      'colors',
            'ie',
            Helper::tokenize('custom_wp_admin_css'),
            Helper::tokenize('fontselect_css'),
            'wp-color-picker',
            'wp-auth-check',
            'jquery-ui-custom',
            'jquery-ui-theme-custom',
            'global_styles',
            'media-views',
        );

        if (!empty($screen) && $screen->id === 'admin_page_campaign_monitor_create_builder') {
            $wp_scripts->queue = array(Helper::tokenize('ajax-script'), Helper::tokenize('app-script'), Helper::tokenize('fontselect'), 'common', 'utils');
            $wp_styles->queue = $styles;
        }
    }

    public static function loadTranslations(){
        $langLocation =  \dirname( plugin_basename( __FILE__ ) ). '/forms/core/lang';
        $didTranslationsLoaded = load_plugin_textdomain( 'campaign-monitor-forms', false, $langLocation );

    }
    public static function loadCheckPage()
    {
        add_action( 'wp_footer', array(__CLASS__,'loadForm') );
    }

    public static function loadForm()
    {
        $pageId = get_the_ID();
        $forms = self::getFormByPage( $pageId );
        $formLayout = new View();
        $formLayout->setForms( $forms );
        $formLayout->render( 'formLayout', 'public' );
    }

    private static function getFormByPage($pageId)
    {
        //$pageId=intval($pageId);
        $forms=Form::getAll();
        global $wp;
//        $current_url = home_url(add_query_arg(array(),$wp->request));

        $foundForms = array();
        $addThisPage = false;
        $current_url =  $wp->request;

        $abTests=ABTest::get();

        foreach ($forms as $form)
        {
            $onPageAr=$form->getOnPageAr();
            $formPageIds = $form->getOnPageAr();
            $formId=$form->getId();


            if (!empty( $formPageIds )) {

                foreach ($formPageIds as $formPageId){
                    $pageUrl = basename(get_permalink( $formPageId ));

                    if (!empty($current_url)){
                        if (strpos($pageUrl, $current_url) !== False){
                            $addThisPage = true;

                        }
                    }

                }

            }

            foreach ($abTests as $abTest)
            {
                $abPageId=$abTest->getEnableOn();
                $abActive=$abTest->getIsActive();

                if (($abPageId==$pageId || $abTest->getEnableOn() == -1) && $abActive)
                {
                    foreach ($abTest->getTests() as $test)
                    {
                        $abForm=$test->getForm();
                        $abFormId=$abForm->getId();
                        if ($abFormId==$formId)
                        {
                            $addThisPage = true;
                        }
                    }
                }

            }/**/

            if (in_array($pageId, $onPageAr) || in_array(-1, $onPageAr) || in_array("-1", $onPageAr) || $addThisPage)
            {
                $foundForms[]=$form;
            }
        }

        return $foundForms;
    }


    public static function pluginNotices()
    {

    }

    /**
     * @return bool true if connected false otherwise
     */
    public static function isConnected()
    {
        return Options::get('connected');
    }

    public static function abTesting()
    {
        $appSettings  = Settings::get();
        $clientId = Settings::get( 'default_client' );


        $createView = new View();

        $createView->setClientId( $clientId  );
        $createView->setAppSettings( $appSettings );
        $tests = ABTest::get();
        $createView->setTests($tests);
        $createView->render( 'ab-testing' );
    }

    public static function abTestingEditing()
    {
        $appSettings  = Settings::get();
        $clientId = Settings::get( 'default_client' );

        $testId = Request::get( 'testId' );
        $editAction = Request::get( 'action' );


        if ($editAction === 'delete') {

            $testToDelete = Request::get('testId');

            ABTest::remove( $testToDelete );
            ?><script> window.location.href = "<?php echo get_admin_url(); ?>admin.php?page=campaign_monitor_ab_testing"; </script><?php
            //wp_redirect(get_admin_url() . 'admin.php?page=campaign_monitor_ab_testing');
            exit();
        }


        $createView = new View();

        $createView->setClientId( $clientId  );
        $createView->setAppSettings( $appSettings );
        $forms = Form::getAll();
        $createView->setForms($forms);
        $createView->render( 'ab-testing-edit' );
    }

    public static function createMenu()
    {
        //create new top-level menu
        $pageTitle = "Campaign Monitor for WordPress";
        $menuTitle = "Subscribe forms";
        $capability = 'administrator';
        $menuSlug = 'campaign-monitor-for-wordpress';
        $callable = 'generateConnectPage';
        $iconUrl = plugins_url('/forms-for-campaign-monitor/forms/views/admin/images/icon.svg');
        $position = 100;

        $menu = add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, array(__CLASS__, $callable), $iconUrl, $position);

        $settingsPageSlug = '';
        $abTestSlug = '';
        $hiddenMenuSlug = '';
        if (Application::isConnected() && self::UpdateStatus()) {
            $settingsPageSlug = $menuSlug;
            $abTestSlug = $menuSlug;
            $hiddenMenuSlug = $menuSlug;

        }



        $formsPage = add_submenu_page($settingsPageSlug,'Forms' , 'Forms' , $capability, 'campaign-monitor-for-wordpress', array(__CLASS__, $callable ));
        $abTestingPage = add_submenu_page($hiddenMenuSlug,'A/B Testing' , 'A/B Testing' , $capability, 'campaign_monitor_ab_testing', array(__CLASS__, 'abTesting'));
        $abEditingPage = add_submenu_page('','A/B Testing Editing' , 'A/B Testing Editing' , $capability, 'campaign_monitor_ab_testing_editing', array(__CLASS__, 'abTestingEditing'));
        $createFormPage = add_submenu_page('', 'Create Form' , 'Create Form' , $capability, 'campaign_monitor_create_form', array(__CLASS__, 'generateNewFormPage'));
        $formBuilderPage = add_submenu_page('', 'Form Builder' , 'Form Builder' , $capability, 'campaign_monitor_create_builder', array(__CLASS__, 'generateFormBuilder'));
        $settingsPage = add_submenu_page($settingsPageSlug,'Settings' , 'Settings' , $capability, 'campaign_monitor_settings_page', array(__CLASS__, 'generateSettingsPage') );

        add_submenu_page('','Update' , 'Update' , $capability, 'campaign_monitor_update_page', array(__CLASS__, 'generateUpdatePage') );

        //admin_print_styles-{$hook_suffix}
//        add_action('admin_print_styles-'.$formBuilderPage, array(__CLASS__, 'removeScripts'), PHP_INT_MAX);

        // remove_submenu_page($menuSlug,$menuSlug);
    }

    public static function loadAdminScripts($hook_suffix)
    {
        if (strpos($hook_suffix, 'campaign-monitor-for-wordpress') !== false || strpos($hook_suffix, 'campaign-monitor_page_campaign_monitor') !== FALSE
            || strpos($hook_suffix, 'campaign_monitor_create_builder') !== false || strpos($hook_suffix, 'campaign_monitor_update_page') !== FALSE
            || strpos($hook_suffix, 'campaign_monitor_ab_testing_editing') !== FALSE){
            $plugins_url = plugins_url('forms-for-campaign-monitor');


            wp_register_style(Helper::tokenize('custom_wp_admin_css'), $plugins_url . '/forms/views/admin/css/main.css', false, '1.0.0');
            wp_register_style(Helper::tokenize('fontselect_css'), $plugins_url . '/forms/views/admin/css/fontselect.css', false, '1.0.0');
            wp_enqueue_style(Helper::tokenize('custom_wp_admin_css'));
            wp_enqueue_style(Helper::tokenize('fontselect_css'));
            // wp_register_script(Helper::tokenize('jquery-admin'), "https//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js", false, null);
            // wp_enqueue_script(Helper::tokenize('jquery-admin'));
            wp_enqueue_script(Helper::tokenize('fontselect'), $plugins_url . '/forms/views/admin/js/fontselect.js', array('jquery'));
            
            wp_enqueue_script(Helper::tokenize('app-script'), $plugins_url . '/forms/views/admin/js/app.js', array('jquery', 'wp-color-picker'), true);
            wp_enqueue_script(Helper::tokenize('ajax-script'), $plugins_url . '/forms/views/admin/js/ajax.js', array(), true);

            // Add the color picker css file
            wp_enqueue_style( 'wp-color-picker' );
            
            
            // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
            wp_localize_script(Helper::tokenize('ajax-script'), 'ajax_request', array(
                'ajax_url' => admin_url('admin-ajax.php')
            ));
        }
    }

    public static function loadPublicScripts()
     {
        $plugins_url = plugins_url('forms-for-campaign-monitor');

        wp_enqueue_script(Helper::tokenize('ajax-script-public'), $plugins_url . '/forms/views/public/js/app.js', array(), false,true);
        wp_register_style(Helper::tokenize('custom_cm_monitor_css'), $plugins_url . '/forms/views/public/css/app.css', false, '1.0.0');
        wp_enqueue_style(Helper::tokenize('custom_cm_monitor_css') );

        // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script(Helper::tokenize('ajax-script-public'), 'ajax_request', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    public static function successNotice()
    {
        $html = '';
        $html .= '<div class="notice notice-success is-dismissible">';
        $html .= '<p>' . __( 'Done!', 'campaign-monitor-forms' ) . '</p>';
        $html .= '</div>';

        echo $html;
    }

    public static function handleRequest()
    {

        status_header(200);
        $data = Request::getPost('data');
        $nonce = $data['app_nonce'];
        $type = $data['type'];

        $nonce = wp_verify_nonce($nonce, 'app_nonce');

        switch ($nonce) {
            case TRUE :

                if ($type === 'save_settings'){

                    $disconnect = Request::getPost( 'disconnect' );
                    $debugMode = Request::getPost( 'debug_switch' );
                    Settings::add( 'debug', $debugMode );

                    if ($disconnect) {
                        Settings::add('recaptcha_key', '');
                        Settings::add('recaptcha_public', '');
                        Settings::add('access_token', '');
                        Settings::add('refresh_token', '');
                        Options::update('connected', null);
                        Settings::clear();
                        Settings::add( 'debug', $debugMode );
                        $message = urlencode('You have logged out');
                        wp_redirect( get_admin_url() . '/admin.php?page=campaign-monitor-for-wordpress&notice[description]='.$message.'&notice[title]=Success!' );
                        exit();
                    }


                    // 2 instantiate app will send client id and secret
                    $clientId = Request::getPost( 'client_id' );
                    $clientSecret = Request::getPost( 'client_secret' );
                    $recaptchaKey = Request::getPost( 'recaptcha_key' );
                    $recaptchaPublic = Request::getPost( 'recaptcha_public' );


                    Settings::add('recaptcha_key', $recaptchaKey);
                    Settings::add('recaptcha_public', $recaptchaPublic);


                    if (Application::isConnected()) {

                        add_action('admin_notices', array(__CLASS__, 'successNotice'));
                        $message = urlencode('Settings have been successfully saved');
                        wp_redirect( get_admin_url() . '/admin.php?page=campaign_monitor_settings_page&notice[description]='.$message.'&notice[title]=Success!' );
                        exit();
                    } else {
                        if (!empty($clientId) && !empty($clientSecret)) {

                            Options::update('connected', null);
                            Settings::clear();

                            // extract client id and client secret from post request
                            $credentials = (object)Request::getPost();
                            if (!empty($credentials)){
                                $clientId = $credentials->client_id;
                                $clientSecret = $credentials->client_secret;
                                // save for subsequent request
                                Settings::add('client_secret', $clientSecret );
                                Settings::add('client_id', $clientId);
                                Settings::add('recaptcha_key', $recaptchaKey);
                                Settings::add('recaptcha_public', $recaptchaPublic);
                                Settings::add( 'debug', $debugMode );
                                $authorizeUrl = self::$CampaignMonitor->authorize_url($clientId,Helper::getRedirectUrl() , Helper::getCampaignMonitorPermissions() );
                                // redirect to get an access token
                                wp_redirect($authorizeUrl);
                                die();
                            } else {
                                Log::write("There was a problem with your credentials");
                            }
                        }
                    }
                }

                if ($type === 'upgrade') {

                    Application::update();

                }

                if ($type === 'account_disconnect') {

                    Settings::add( 'client_secret', null );
                    Settings::add( 'client_id', null );
                    Settings::add( 'access_token', null );
                    Settings::add( 'default_client', null );
                    Options::update('connected', FALSE);
                    $debugMode = Request::getPost( 'debug_switch' );
                    Settings::add( 'debug', $debugMode );

                    wp_redirect( Helper::getRedirectUrl( 'false' ) );
                    exit();
                }

                if ($type === 'save_ab_test') {

                    $primaryForm = Request::getPost( 'form_primary' );
                    $secondaryForm = Request::getPost( 'form_secondary' );
                    $testToUpdate = Request::getPost('test_id');
                    $testTitle = Request::getPost( 'test_title' );
                    $testEnableOn = Request::getPost( 'enable_on' );

                    if ($testToUpdate === NULL){
                        $testToUpdate = '';
                    }

                    if (!empty( $testToUpdate )) {
                        $abTest = ABTest::get( $testToUpdate );
                        $abTest->setName( $testTitle );
                        $abTest->setIsActive( true );
                        $abTest->setEnableOn( $testEnableOn );

                        $abTest->save($testToUpdate);

                    } else {
                        if (!empty( $primaryForm ) && !empty( $secondaryForm )) {
                            $primaryForm = Form::getOne($primaryForm);
                            $secondaryForm = Form::getOne($secondaryForm);


                            $abTest = new ABTest( $testTitle );
                            $primaryTest = new Test( $primaryForm );
                            $abTest->addTest( $primaryTest );
                            $secondaryTest = new Test( $secondaryForm );
                            $abTest->addTest( $secondaryTest );
                            $abTest->setIsActive( true );
                            $abTest->setEnableOn( $testEnableOn );
                            $abTest->save($testToUpdate);
                        }
                    }

                    $message = urlencode('AB Testing have been successfully saved');

                    wp_redirect( get_admin_url() . '/admin.php?page=campaign_monitor_ab_testing&notice[description]='.$message.'&notice[title]=Success!' );
                    exit();
                }

                if ($type === 'save') {

                    $appSettings  = Settings::get();
                    $clientId = Settings::get( 'default_client' );

                    $successMessage = Request::getPost('success_message');
                    $successMessage = Security::sanitize($successMessage);
                    $submitButtonTextColor = Request::getPost('fallbackButtonTextColor');
                    $submitButtonColor = Request::getPost('fallbackButtonColor');
                    $formBackgroundColor = Request::getPost('fallbackFormBackgroundColor');
                    $textColor =Request::getPost('fallbackTextColor');

                    $submitButtonTextColor = !empty($submitButtonTextColor) ? $submitButtonTextColor : Request::getPost( "submitButtonTextHex" );
                    $submitButtonColor = !empty($submitButtonColor) ?$submitButtonColor : Request::getPost( "submitButtonBgHex" ) ;
                    $formBackgroundColor = !empty($formBackgroundColor) ? $formBackgroundColor : Request::getPost( "backgroundHex" ) ;
                    $textColor = !empty($textColor) ? $textColor : Request::getPost( "textHex" ) ;


                    // TODO need put them in array
                    $customFieldKeys = Request::getPost( 'customFieldKey' );
                    $customFieldName = Request::getPost( 'customFieldName' );
                    $customFieldLabel = Request::getPost( 'customFieldLabel' );
                    $customFieldOptions = Request::getPost( 'customFieldOptions' );
                    $customFieldType = Request::getPost( 'customFieldType' );
                    $customFieldShowLabel = Request::getPost( 'customFieldShowLabel' );
                    $customFieldRequired = Request::getPost( 'customFieldRequired' );
                    $listId = Request::getPost( 'campaignMonitorListId' );
                    $hasName = Request::getPost( 'hasNameField' );
                    $selectedFont = Request::getPost( 'selectedFont' );

                    $formCustomFields = array();
                    $formId=Request::getPost("formId");
                    $formType=Request::getPost("formType");

                    if (empty($listId)) {
                        $actionUrl = get_admin_url() . 'admin.php?page=campaign-monitor-for-wordpress&formId=' . $formId. '&'.'isUpdated=1';
                        wp_redirect($actionUrl);
                    }


                    if (!empty( $customFieldKeys )) {
                        $campaignMonitorFields = Application::$CampaignMonitor->get_custom_fields( $listId );

                        /**
                         * TODO revision
                         *
                         */
                        $customFieldKeys = array_map(array(__CLASS__, 'decode'), $customFieldKeys);
                        $customFieldName = array_map(array(__CLASS__, 'decode'), $customFieldName);
                        $customFieldOptions = array_map(array(__CLASS__, 'decode'), $customFieldOptions);


                        foreach ($customFieldKeys as $index => $fieldKey) {
                            $field = New FormField( $customFieldName[$index] );

                            foreach ($campaignMonitorFields as $cmField) {
                                if ($cmField->Key === $fieldKey) {

                                    if ($cmField->FieldName !== $customFieldName[$index]) {
                                        $updateField = Application::$CampaignMonitor->update_custom_field( $listId, $fieldKey, $customFieldName[$index] );
                                        if (!isset( $updateField->response )) {
                                            $field->setKey( $updateField );
                                        } else {
                                            Log::write( " There was a problem adding the custom field to your list: " . print_r( $updateField, true ) );
                                        }
                                    }
                                }
                            }


                            $field->setOptions( $customFieldOptions[$index] );
                            $field->setShowLabel( (bool)$customFieldShowLabel[$index] );
                            $field->setType( $customFieldType[$index] );
                            $field->setName( $customFieldName[$index] );
                            $field->setLabel( $customFieldLabel[$index] );
                            $field->setEnable( true );
                            $field->setIsRequired( $customFieldRequired[$index] );


                            if ($fieldKey === '') {
                                $createdFieldKey = Application::$CampaignMonitor->create_custom_field( $listId, $customFieldName[$index], $customFieldType[$index] , $field->getOptions() );

                                if (!isset( $createdFieldKey->response )) {
                                    $field->setKey( $createdFieldKey );
                                } else {
                                    Log::write( " There was a problem adding the custom field to your list: " . print_r( $createdFieldKey, true ) );
                                }

                            } else {
                                $field->setKey( $fieldKey );
                            }

                            $formCustomFields[] = $field;

                        }


                    }




                    $form = new Form( $formType );
                    $form->setFields( $formCustomFields );
                    $form->setName( Request::getPost( "formName" ) );

                    $noticeHtml = "Form has been saved: " . $form->getName();

                    $form->setHeader( Request::getPost( "formHeader" ) );
                    $form->setSubHeader( Request::getPost( "formSubHeader" ) );

                    $form->setSubmitButtonBgHex( $submitButtonColor );
                    $form->setSubmitButtonTextHex( $submitButtonTextColor );
                    $form->setBackgroundHex( $formBackgroundColor );
                    $form->setTextHex( $textColor );
                    $form->setSubmitButtonText( Request::getPost( "submitButtonText" ) );

                    $form->setHasNameFieldLabel( Request::getPost( "hasNameFieldLabel" ) );
                    $form->setHasEmailFieldLabel( Request::getPost( "hasEmailFieldLabel" ) );

                    $form->setCampaignMonitorClientId( Request::getPost( "campaignMonitorClientId" ) );
                    $form->setCampaignMonitorListId( Request::getPost( "campaignMonitorListId" ) );
                    $form->setSuccessMessage($successMessage);

                    if (Request::getPost( "isActive" )) {
                        $form->setIsActive( 1 );
                    } else {
                        $form->setIsActive( 0 );
                    }

                    if (Request::getPost( "hasCaptcha" )) {
                        $form->setHasCaptcha( 1 );
                    } else {
                        $form->setHasCaptcha( 0 );
                    }

                    if (Request::getPost( "hasNameField" )) {
                        $form->setHasNameField( 1 );
                    } else {
                        $form->setHasNameField( 0 );
                    }

                    if (Request::getPost( "hasGenderField" )) {
                        $form->setHasGenderField( 1 );
                    } else {
                        $form->setHasGenderField( 0 );
                    }

                    if (Request::getPost( "hasCampMonLogo" )) {
                        $form->setHasCampMonLogo( 1 );
                    } else {
                        $form->setHasCampMonLogo( 0 );
                    }

                    if (Request::getPost( "hasDateOfBirthField" )) {
                        $form->setHasDateOfBirthField( 1 );
                    } else {
                        $form->setHasDateOfBirthField( 0 );
                    }

                    if (Request::getPost( "hasOpenTextField" )) {
                        $form->setHasOpenTextField( 1 );
                        $form->setOpenTextFieldLabel( Request::getPost( "openTextFieldLabel" ) );
                    } else {
                        $form->setHasOpenTextField( 0 );
                    }

                    if ($formType == FormType::SLIDE_OUT) {
                        $form->setFormPlacement( Request::getPost( "formPlacement" ) );
                    } elseif ($formType == FormType::BAR) {
                        $form->setFormPlacementBar( Request::getPost( "formPlacementBar" ) );
                    } elseif ($formType == FormType::LIGHTBOX) {
                        $formAppearsLightbox = Request::getPost( "formAppearsLightbox" );
                        if ($formAppearsLightbox == "scroll") {
                            $form->setAppearsLightbox( "scroll" );
                            $form->setLightboxScrollPercent( intval( Request::getPost( "lightboxScrollPercent" ) ) );
                        } else {
                            $form->setAppearsLightbox( "seconds" );
                            $form->setLightboxSeconds( intval( Request::getPost( "lightboxSeconds" ) ) );
                        }
                    }

                    $form->setUpdateDate( date( "Y-m-d H:i:s" ) );

                    if (!empty($selectedFont)){
                        $font = new Font();
                        $font->setName($selectedFont);
                        $font->setFamily($selectedFont);
                        $form->setFont($font);
                    }

                    $createDate = Request::getPost( "createDate" );

                    if (strlen( $createDate ) < 1) {
                        $createDate = $form->getUpdateDate();
                    }
                    $form->setCreateDate( $createDate );

                    $maxPageOnCount = $form->getMaxPageOnCount();
                    $pageOnAr = array();

                    for ($x = 1; $x <= $maxPageOnCount; $x++) {
                        $pageOn = Request::getPost( "formPageOn_" . $x );
                        if (!empty( $pageOn ) && !in_array( $pageOn, $pageOnAr )) {
                            $pageOnAr[] = $pageOn;
                        }
                    }
                    $form->setOnPageAr( $pageOnAr );


                    $formId = $form->save( $formId );

                    $form->setCampaignMonitorClientAr();

//                    $actionUrl = get_admin_url() . 'admin.php?page=campaign_monitor_create_builder&formId=' . $formId. '&'.'isUpdated=1';
                    $actionUrl = get_admin_url() . 'admin.php?page=campaign-monitor-for-wordpress&formId=' . $formId. '&'.'isUpdated=1';
                    wp_redirect($actionUrl);
                    exit();
                }


                break;
            case 1:
//                echo 'Nonce is less than 12 hours old';
                break;

            case 2:
//                echo 'Nonce is between 12 and 24 hours old';
                break;
            default:
                die('You killed the app!');

        }

        $actionUrl = Helper::getActionUrl();
        wp_redirect($actionUrl);
        exit();

    }

    public static function decode($string){
        return htmlspecialchars_decode($string, ENT_QUOTES);
    }


    public static function encode($string){
        return stripslashes(htmlspecialchars($string));
    }

    public static function refreshTokenIfNeeded() {
		$auth = array(
			'access_token' => Settings::get('access_token'),
			'refresh_token' => Settings::get('refresh_token')
		);

		if (!empty($auth['access_token']) && Settings::get('expiry') < time()) {
            $newCredentials = Application::$CampaignMonitor->refresh_token($auth);
			
			if (isset($newCredentials->error)) {
				error_log('Failed to refresh token');
				return false;
			}

            Application::updateTokens($newCredentials->access_token, $newCredentials->refresh_token, time() + $newCredentials->expires_in);
			return true;
		}

		return false;
    }
    
    public static function updateTokens($accessToken, $refreshToken, $expiry) {
        Settings::add('access_token', $accessToken);
        Settings::add('refresh_token', $refreshToken);
        Settings::add('expiry', $expiry);

        Application::$CampaignMonitor->update_tokens($accessToken, $refreshToken);
    }
}