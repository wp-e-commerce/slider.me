<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SliderMe Class
 *
 * Base class for SliderMe.
 *
 * @package WordPress
 * @subpackage SliderMe
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - register_widgets()
 * - load_localisation()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 * - ensure_post_thumbnails_support()
 */
class SliderMe {
	public $admin;
	public $frontend;
	public $post_types;
	public $token = 'slider_me';
	public $plugin_url;
	public $plugin_path;
	public $slider_count = 1;
	public $version;
	private $file;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
		$this->plugin_path = trailingslashit( dirname( $file ) );

		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $this->file, array( &$this, 'activation' ) );

		// Load the Utils class.
		require_once( 'class-slider_me-utils.php' );

		// Setup post types.
		require_once( 'class-slider_me-posttypes.php' );
		$this->post_types = new SliderMe_PostTypes();

		// Setup settings screen.
		require_once( 'class-slider_me-settings-api.php' );
		require_once( 'class-slider_me-settings.php' );
		$this->settings = new SliderMe_Settings();
		$this->settings->token = 'slider_me-settings';
		if ( is_admin() ) {
			$this->settings->has_tabs 	= true;
			$this->settings->name 		= __( 'Slideshow Settings', 'slider_me' );
			$this->settings->menu_label	= __( 'Settings', 'slider_me' );
			$this->settings->page_slug	= 'slider_me-settings';
		}

		$this->settings->setup_settings();

		// Differentiate between administration and frontend logic.
		if ( is_admin() ) {
			require_once( 'class-slider_me-admin.php' );
			$this->admin = new SliderMe_Admin();
			$this->admin->token = $this->token;
		} else {
			require_once( 'class-slider_me-frontend.php' );
			$this->frontend = new SliderMe_Frontend();
			$this->frontend->token = $this->token;
			$this->frontend->init();
		}

		add_action( 'widgets_init', array( &$this, 'register_widgets' ) );
		add_action( 'after_setup_theme', array( &$this, 'ensure_post_thumbnails_support' ) );
	} // End __construct()

	/**
	 * Register the widgets.
	 * @return [type] [description]
	 */
	public function register_widgets () {
		require_once( $this->plugin_path . 'widgets/widget-slider_me-base.php' );
		require_once( $this->plugin_path . 'widgets/widget-slider_me-attachments.php' );
		require_once( $this->plugin_path . 'widgets/widget-slider_me-posts.php' );
		require_once( $this->plugin_path . 'widgets/widget-slider_me-slides.php' );

		register_widget( 'SliderMe_Widget_Attachments' );
		register_widget( 'SliderMe_Widget_Posts' );
		register_widget( 'SliderMe_Widget_Slides' );
	} // End register_widgets()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'slider_me', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'slider_me';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'slider_me' . '-version', $this->version );
		}
	} // End register_plugin_version()

	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @since  1.0.1
	 * @return  void
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
	} // End ensure_post_thumbnails_support()
} // End Class
?>
