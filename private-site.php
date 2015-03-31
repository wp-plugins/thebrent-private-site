<?php
/*
  Plugin Name: Private Sites
  Plugin URI: http://thebrent.net/projects/private-site/
  Description: Quick plugin to enable the setting of a site private. Includes options for with multi-site capabilities.
  Version: 0.1.0
  Author: Brent Maxwell
  Author URI: http://thebrent.net/
  Text Domain: thebrent-private-site
  License: GPL2
*/

//todo: make option on global network sites screen
class theBrent_PrivateSite{
	const TEXT_DOMAIN = 'thebrent-private-site';
	const SETTING_SECTION_NAME = 'private_site_section';
	const SETTING_NAME = 'is_private';
	const OPTION_GROUP = 'general';
	
	public function __construct(){
		add_action('admin_init', array( $this, 'settings_init' ) );
		add_action('plugins_loaded', array($this,'redirect'));
		
		if(is_multisite()){
			add_filter('wpmu_blogs_columns', array($this,'wmpu_blogs_columns' ));
			add_action('manage_blogs_custom_column', array($this,'sites_private_column_field'), 1, 3 );
			add_action('manage_sites_custom_column', array($this,'sites_private_column_field'), 1, 3 );
		}
	}
	
	function settings_init() {
		add_settings_section(
			self::SETTING_SECTION_NAME,
			__( 'Private Site', self::TEXT_DOMAIN ),
			array( $this, 'settings_section_callback' ),
			self::OPTION_GROUP
		);

		add_settings_field(
			self::SETTING_NAME,
			__( 'Make site private', self::TEXT_DOMAIN ),
			array( $this, 'setting_option_callback' ),
			self::OPTION_GROUP,
			self::SETTING_SECTION_NAME
		);
		register_setting(
			self::OPTION_GROUP,
			self::SETTING_NAME
		);
		//if(is_multisite()){//&& is_plugin_active_for_network()){}
	}
	
	function settings_section_callback() {
		?><p><?php _e( 'Make your site private.', self::TEXT_DOMAIN ); ?></p><?php
	}
	
	function setting_option_callback() {
		?>
			<input name="<?php echo self::SETTING_NAME ?>" id="<?php echo self::SETTING_NAME ?>" <?php echo checked( get_option( self::SETTING_NAME, '0' ), true, false ); ?> type="checkbox" value="1" />
				<?php _e( 'Make site private.', self::TEXT_DOMAIN ); ?>
			</label>
		<?
	}
	
	public function wmpu_blogs_columns($columns){
		$columns[ self::SETTING_NAME ] = __( 'Private' );
		return $columns;
	}
	
	function sites_private_column_field( $column, $blog_id ) {
		if ( $column == self::SETTING_NAME ) {
			if(get_blog_option($blog_id,self::SETTING_NAME,0) == 1)
			{
				echo '<span class="dashicons dashicons-yes"></span>';
			}
		}
	}

	function redirect()
	{
		if(get_option( self::SETTING_NAME, '0' ))
		{
    		global $pagenow;
    		if ($pagenow != 'wp-login.php'
        		&& $pagenow != 'xmlrpc.php'
				&& $pagenow != 'wp-cron.php'
        		&& !is_user_logged_in()
        		&& !is_user_member_of_blog()) {
        		auth_redirect();
			}
    	}
	}
}

$thisPrivateSite = new theBrent_PrivateSite();