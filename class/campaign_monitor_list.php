<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( CAMPAIGN_MONITOR_CLASS_FOLDER . 'campaign_monitor_base.php' );

class CampaignMonitorList extends CampaignMonitorBase {
	private $tableName = 'cm_lists';
	public $id;
	public $name;
	public $comments;
	public $comments_text;
	public $registration;
	public $registration_text;

	private function get_defaults() {
		return array(
			'name'          => 'New List',
			'comments'      => 0,
			'comments_text' => '',
			'registration'      => 0,
			'registration_text' => '',
		);
	}

	function __construct() {
		$defaults                = $this->get_defaults();
		$this->name              = $defaults['name'];
		$this->comments          = $defaults['comments'];
		$this->comments_text     = $defaults['comments_text'];
		$this->registration      = $defaults['registration'];
		$this->registration_text = $defaults['registration_text'];
		$this->id = 0;
	}

	public function save( ) {

		if ( strlen($this->id) != 0 && $this->exists( $this->id ) ) {
			return $this->update();
		}

		return false;

	}

	public function exists( $id ) {
		$list = new self();
		$list->load( $id );

		return strlen($list->id) > 0;
	}

	private function update() {
		global $wpdb;
		if ( 1 == $this->comments ) {
			$wpdb->query(" UPDATE ". $wpdb->prefix.$this->tableName . " SET comments = 0;");
		}

		if ( 1 == $this->registration ) {
			$wpdb->query(" UPDATE ". $wpdb->prefix.$this->tableName . " SET registration = 0;");
		}

		$this->sanitize();

		$wpdb->update(
			$wpdb->prefix.$this->tableName,
			array(
				'name'              => $this->name,
				'comments'          => $this->comments,
				'comments_text'     => $this->comments_text,
				'registration'      => $this->registration,
				'registration_text' => $this->registration_text,
			),
			array( 'id'   => $this->id )
		);

		return true;
	}

	public function sanitize() {

		$this->name              = sanitize_text_field( $this->name );
		$this->comments          = sanitize_text_field( $this->comments );
		$this->comments_text     = sanitize_text_field( $this->comments_text );
		$this->registration      = sanitize_text_field( $this->registration );
		$this->registration_text = sanitize_text_field( $this->registration_text );

	}

	public function load( $id = null ) {
		if ( ! is_null( $id ) ) {
			$this->id = intval($id);
		}
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE id = '" . $this->id . "'";

		$result = $wpdb->get_results($sql);

		if( count($result) > 0 )
		{
			$result = $result[0];
			$this->name = $result->name;
			$this->comments = $result->comments;
			$this->comments_text = $result->comments_text;
			$this->registration = $result->registration;
			$this->registration_text = $result->registration_text;
		} else {
			$this->id = 0;
		}

	}

	public function get_registration_form() {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE registration = 1";

		$result = $wpdb->get_results($sql);

		if( count($result) > 0 )
		{
			$result = $result[0];
			$this->id = $result->id;
			$this->name = $result->name;
			$this->comments = $result->comments;
			$this->comments_text = $result->comments_text;
			$this->registration = $result->registration;
			$this->registration_text = $result->registration_text;
		} else {
			$this->id = 0;
		}
	}

	public function get_comment_form() {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix.$this->tableName." WHERE comments = 1";

		$result = $wpdb->get_results($sql);

		if( count($result) > 0 )
		{
			$result = $result[0];
			$this->id = $result->id;
			$this->name = $result->name;
			$this->comments = $result->comments;
			$this->comments_text = $result->comments_text;
			$this->registration = $result->registration;
			$this->registration_text = $result->registration_text;
		} else {
			$this->id = 0;
		}
	}

	function register_form() {
		?>
		<p>
			<input type="checkbox" name="s_<?php echo $this->id;?>" value="1" checked="checked">
			<label for="s_<?php echo $this->id;?>"><?php echo $this->registration_text; ?></label>
		</p>
		<?php
	}


	function user_register( $user_id ) {
		if ( ! empty( $_POST['s_'.$this->id] ) && 1 == $_POST['s_'.$this->id] ) {
			//
		}
	}

	function comment_form( $defaults ) {

		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );

		$defaults['fields']['s_'.$this->id] = '<p>'.
			'<input type="checkbox" name="s_'.$this->id.'" value="1"> '.
			$this->comments_text.
			'</p>';

		return $defaults;
	}

	function save_comment_meta_data( $comment_id ) {
		//submit form!
	}
}