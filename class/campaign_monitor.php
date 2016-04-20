<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Include additional callses. No Namespace used to assure proper work on older WP installs
 */

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_base.php' );
require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_admin.php');
require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_oauth.php');
require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_form.php');

/**
 * Class CampaignMonitor
 *
 * Main Plugin Class.
 */
class CampaignMonitor extends CampaignMonitorBase{

	/**
	 * @var string
	 */
	public $version = '1.5.3';

	/**
	 * @var CampaignMonitor The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * @var string
	 */
	protected static $_settings = 'campaign_monitor_settings';

	/**
	 * @var CampaignMonitorAdmin Exists only if we are in the admin area of WP
	 */
	public $admin = null;

	/**
	 * @var CampaignMonitorOAuth
	 */
	public $connection = null;

	/**
	 * @var Array CampaignMonitorElement Holds all global (and targeted Elements)
	 */
	public $elements = null;

	/**
	 * @var Array CampaignMonitorABTest Holds all possible ABTests.
	 */
	public $abtests = null;


	/**
	 * @var String holds current WP version
	 */
	 public $wp_version = "";


	/**
	 * Initialize plugin
	 */
	public function init() {
	    global $wp_version;
	    $this->wp_version = $wp_version;
		$this->connection = new CampaignMonitorOAuth();

		if ( $this->connection->enabled() ) {
			$this->register_shortcodes();
			$this->load_global_elements();
			$this->load_ab_tests();

            add_action('init', array($this,'cm_create_virtual') );
			add_action( 'wp_enqueue_scripts', array($this, 'add_scripts') );
			add_action( 'template_redirect', array($this, 'add_targeted_elements') );
			add_action( 'template_redirect', array($this, 'add_targeted_abtests') );
			add_action( 'wp_footer', array( $this, 'render_elements' ) );
			add_action( 'wp_ajax_cm_form_submission', array( $this, 'form_submission') );
			add_action( 'wp_ajax_nopriv_cm_form_submission', array( $this, 'form_submission') );
		}
		if( is_admin() ) {
			$this->enable_admin();
		}
	}


    function cm_create_virtual()
    {
        $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if (strpos($url,'preview-cm-form-page') !== false && !(empty($_POST)))
        {
			remove_filter( 'the_content', 'wpautop' );
          $this->elements = array();
          $this->abtests = array();
          $element = new CampaignMonitorElement();
          $element->preparePreview();
          $args = array('slug' => 'preview-cm-form-page',
                  'title' => 'Previewing a Form',
                  'content' => "<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer efficitur, lectus id porta commodo, lacus nibh convallis justo, eu vehicula lorem lacus ut tellus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam sed nunc ac purus consequat porttitor. Mauris luctus, diam non tempus mattis, justo mauris hendrerit nisi, quis sollicitudin diam risus eget mauris. Morbi ut pharetra nunc. Morbi finibus viverra vehicula. Quisque non metus nulla. Curabitur pretium ornare placerat. </p>
<p> Sed ultrices velit ut mattis gravida. Duis quis viverra justo. Proin sem risus, mattis et tincidunt non, consectetur in magna. Duis ornare sem nec augue cursus lobortis. In hac habitasse platea dictumst. Suspendisse ultrices arcu nec tortor feugiat vehicula. Etiam non enim sem. In sollicitudin neque ac purus dictum laoreet. Morbi sed elit sollicitudin, facilisis urna non, placerat risus. Nulla vitae laoreet dui. Aenean semper dapibus lectus et viverra. Duis sit amet dapibus lectus. Praesent semper interdum sem, in rhoncus metus ullamcorper ac. Duis lorem leo, congue a fringilla et, tristique ut neque. Etiam venenatis risus eu nulla malesuada, id sodales massa varius. Duis turpis ex, elementum id mi sed, consequat ornare purus. </p>
<p> Sed sed lacus vitae ligula tincidunt tincidunt nec a odio. Curabitur libero leo, accumsan eget sem vel, elementum mattis lorem. Suspendisse a orci et libero mollis facilisis. Etiam laoreet dui et suscipit porta. Donec gravida ornare mauris, sit amet dapibus nibh tristique at. Integer lacinia, mauris et volutpat dapibus, neque erat posuere lorem, vitae tempor elit nulla eget augue. Morbi et massa augue. </p>
<p> Nam in finibus orci. Proin sodales justo nec justo vestibulum laoreet. Sed at purus ligula. Donec non lacus pulvinar, condimentum urna vitae, facilisis lectus. Praesent in pellentesque nibh. Integer eget odio dictum quam pretium scelerisque. In imperdiet viverra risus a porta. Sed ut bibendum enim, in maximus quam. Vivamus malesuada consequat nibh quis dictum. Vestibulum sagittis mi nisl, eget lobortis libero aliquam at. </p>
<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris efficitur pretium diam. Praesent cursus pretium nisi, id pharetra augue varius eu. Integer velit erat, sagittis sed risus nec, placerat pretium dolor. Cras commodo vitae tellus at rhoncus. Cras egestas felis eu velit ornare ultrices. Donec a lectus nisl. Mauris dapibus lorem mattis tempor vestibulum. Praesent dignissim tempor libero sed sagittis. </p>".$element->renderPreview());
          new DJVirtualPage($args);
        }
    }


	/**
	 * Enqueue Scripts and styles
	 */
	function add_scripts() {
		wp_enqueue_style( 'lightbox', CAMPAIGN_MONITOR_PLUGIN_URL. 'css/lightbox.css' );
		wp_enqueue_script( 'lightboxjs', CAMPAIGN_MONITOR_PLUGIN_URL.'js/lightbox.js', array(), '1.0.0', true );
	}


	/**
	 * Generate new CampaignMonitorAdmin instance.
	 */
	private function enable_admin() {
		$this->admin = new CampaignMonitorAdmin();
	}

	public function get_option( $name=false, $default='' ) {
		$options = get_option( self::$_settings );

		if ( ! $name ) {

			return $options;
		}

		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}

		return $default;
	}

	public function save_option( $name, $value ) {
		$options = get_option( self::$_settings );

		$options[$name] = sanitize_text_field( $value );
		update_option( self::$_settings, $options);
	}

	public function remove_options() {
		delete_option( self::$_settings);
		$this->disconnect_elements();
	}

	public function register_shortcodes() {
		add_shortcode( 'cm_simple_form', array( $this, 'render_simple_form' ) );
		add_shortcode( 'cm_button', array( $this, 'render_button'));
	}

	public function render_simple_form( $atts ) {

		if ( ! isset( $atts['id'] ) || ! is_numeric( $atts['id'] ) ) {
			return '';
		} else {
            $checkForm = new CampaignMonitorForm();
            $checkForm->load( $atts['id'] );

            if ($checkForm->enabled != "1"){return '';}
        }


		$form = new CampaignMonitorSimpleForm();
		$form->load( $atts['id'] );
		$this->elements[] = $form;
		if ( $form->id > 0 ) {
			return $form->render();
		}

	}

	public function render_button( $atts ) {

		if ( ! isset( $atts['id'] ) || ! is_numeric( $atts['id'] ) ) {
			return '';
		}else{
            $checkForm = new CampaignMonitorForm();
            $checkForm->load( $atts['id'] );

            if ($checkForm->enabled != "1"){return '';}
        }

		$button = new CampaignMonitorButton();
		$button->load( $atts['id'] );
		$this->elements[] = $button;
		if ( $button->id > 0 ) {
			return $button->render();
		}
	}

	public function humanize( $string ) {
		return ucwords( str_replace( "_", " ", $string ) );
	}

	public function load_ab_tests() {
		$abtest = new CampaignMonitorABTest();
		$this->abtests = $abtest->load_all();
	}

	public function load_global_elements() {
		$element = new CampaignMonitorElement();
		$this->elements = $element->load_global();
	}

	public function add_targeted_elements() {
		$cm = new CampaignMonitorElement();

        $blogUrl = str_replace("http://", "", get_bloginfo('url'));
        $hostUrl = $_SERVER['HTTP_HOST'];

        $folder = str_replace($hostUrl, "", $blogUrl);
        $realSlug = str_replace($folder, "", $_SERVER['REQUEST_URI']);

		$this->elements =  array_merge( $this->elements, $cm->enableFromSlug( $realSlug ) );
	}

	public function add_targeted_abtests() {

        $blogUrl = str_replace("http://", "", get_bloginfo('url'));
        $hostUrl = $_SERVER['HTTP_HOST'];

        $folder = str_replace($hostUrl, "", $blogUrl);
        $realSlug = str_replace($folder, "", $_SERVER['REQUEST_URI']);

		for( $i = 0; $i < count($this->abtests); $i++ ) {
			$this->abtests[$i]->with_slug( $realSlug );
		}
	}

	public function setup_forms() {
		$list = new CampaignMonitorList();
		$list->get_registration_form();
		if ( $list->id != '0' ) {
			add_action( 'register_form', array( $list, 'register_form' ) );
			add_action( 'user_register', array( $list, 'user_register' ) );
		}
		$commentList = new CampaignMonitorList();
		$commentList->get_comment_form();
		if ( $commentList->id != '0' ) {
			add_filter( 'comment_form_defaults', array( $commentList, 'comment_form' ) );
		}

	}

	public function render_elements() {
		for( $i = 0; $i < count( $this->abtests ); $i++ ) {
			if ( 1 == $this->abtests[$i]->enabled ) {
				for( $k =0; $k < count( $this->elements ); $k++ ) {
					for ( $j = count( $this->elements ) - 1; $j > 0; $j-- ) {
						if ( $this->abtests[$i]->contains( $this->elements[$k], $this->elements[$j] ) ) {
							$this->elements[$j]->to_be_rendered = false;
							$this->elements[$k]->to_be_rendered = false;
						}
					}
				}
			}
		}

		foreach ( $this->abtests as $a ){
			$a->render();
		}

		foreach ( $this->elements as $e ) {
			if ( $e->to_be_rendered) {
				$e->render2();
			}
		}

		echo '<script type="text/javascript">CM_OBJECT = { ajax_url : "' . admin_url( 'admin-ajax.php' ) . '"}</script>';
	}

	public function clean_option( $option ) {
		$option = str_replace( "\\", "", $option );
		$option = sanitize_text_field( $option );
		return $option;
	}

	public function disable_elements() {
		global $wpdb;
		$table = 'cm_elements';
		$wpdb->update(
				"{$wpdb->prefix}{$table}",
				array( 'enabled' => 0 ),
				array( 'enabled' => 1 )
		);
	}

    public function disconnect_elements() {
		global $wpdb;
		$table = 'cm_elements';
		$wpdb->update(
				"{$wpdb->prefix}{$table}",
			array( 'enabled' => 2 ),
			array( 'enabled' => 0)
		);
        $wpdb->update(
				"{$wpdb->prefix}{$table}",
			array( 'enabled' => 2 ),
			array( 'enabled' => 1)
		);
	}

	public function reconnect_elements() {
		global $wpdb;
		$table = 'cm_elements';

		$form = new CampaignMonitorForm();
		$allForms = $form->get_all();
		$clients = CampaignMonitorPluginInstance()->connection->get_clients();
		$lists = array();
		foreach( $clients as $client ) {
			$lists = array_merge( $lists, CampaignMonitorPluginInstance()->connection->get_client_lists($client->ClientID) );
		}
		foreach( $allForms as $form ) {
			$information = maybe_unserialize($form->information);
			if ( is_array($information) ) {
				foreach ( $lists as $list ) {
					if ($information['list_id'] == $list->ListID) {
						$wpdb->update(
							"{$wpdb->prefix}{$table}",
							array( 'enabled' => 0 ),
							array( 'id' => $form->id )
						);
					}
				}
			}
		}
	}

	public function form_submission() {
		$form = new CampaignMonitorForm();
		$form->load( intval( $_POST['form_id'] ) );
		if ( 0 == $form->id ) {
			die();
		}
		if ( 0 != $_POST['abtest_id'] ) {
			$abtest = new CampaignMonitorABTest();
			$abtest->load( intval( $_POST['abtest_id'] ) );
			if ( 0 != $abtest->id ) {
				if ( $_POST['element_id'] == $abtest->data['first_element'] ){
					$abtest->data['first_element_submissions'] += 1;
				} else  {
					$abtest->data['second_element_submissions'] += 1;
				}
				$abtest->save();
			}
		}
		$fields = CampaignMonitorPluginInstance()->connection->get_list_fields($form->data['list_id'])->response;

		$subscriber = array(
         'EmailAddress' => sanitize_email( $_POST['email'] ),
         'Name' => '',
         'CustomFields' => array(),
         'Resubscribe' => false,
         'RestartSubscriptionBasedAutoResponders' => false
		);

		if ( isset( $_POST['fullName'] ) )
			$subscriber['Name'] = $this->clean_option($_POST['fullName']);

		foreach ( $fields as $field ) {
			$key = $form->clean_key( $field->Key );

            //Check outside the main loop as they will not exist on the $_POST.
            if ( 'Date' == $field->DataType ) {
               if ( array_key_exists( $key."-day", $_POST ) &&
                    array_key_exists( $key."-month", $_POST ) &&
                    array_key_exists( $key."-year", $_POST )
               ) {
                 $subscriber['CustomFields'][] = array(
                   'Key' => $field->Key,
                   'Value' => $this->clean_option($_POST[$key."-year"]."/".$_POST[$key."-month"]."/".$_POST[$key."-day"])
				 );
               }
            }

			if ( array_key_exists( $key, $_POST) ) {
				$add = true;
				if ( in_array( $field->DataType, array('MultiSelectMany','MultiSelectOne')) ) {
					if ( 'MultiSelectMany' == $field->DataType ) {
						foreach( $_POST[$key] as $submission ) {
                            $submission = str_replace( array( "\\'" ), array( "'" ), $submission);
							if ( ! in_array( $submission, $field->FieldOptions ) ) {

								$add = false;
							}
						}
						if ( $add ) {
							foreach( $_POST[$key] as $submission ) {
								$subscriber['CustomFields'][] = array(
									'Key' => $field->Key,
									'Value' => $this->clean_option($submission)
								);
							}
							$add = false;
						}
					} else {
                        $escaped_key = str_replace( array( "\\'" ), array( "'" ), $_POST[$key]);
						if ( ! in_array( $escaped_key, $field->FieldOptions ) ) {
							$add = false;
						}
					}
				}
				if ( $add ) {

                    if ($field->DataType == "MultiSelectOne"){
                        $subscriber['CustomFields'][] = array(
                            'Key' => $field->Key,
                            'Value' => $this->clean_option($_POST[$key])
						);
                    }else{
                        $subscriber['CustomFields'][] = array(
                            'Key' => $field->Key,
                            'Value' => $this->clean_option($_POST[$key])
						);
                    }

				}
			}
		}

		CampaignMonitorPluginInstance()->connection->subscribe($form->data['list_id'], $subscriber);

		echo __('Thank you. The information has been received.','campaign-monitor');
		die();
	}

	/**
	 * Main CampaignMonitor Instance
	 *
	 * Ensures only one instance of CampaignMonitor is loaded or can be loaded.
	 *
	 * @static
	 * @see CampaignMonitorPluginInstance()
	 * @return CampaignMonitor - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
