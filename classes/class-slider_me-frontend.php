<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SliderMe Frontend Class
 *
 * All functionality pertaining to the frontend of SliderMe.
 *
 * @package WordPress
 * @subpackage SliderMe
 * @category Frontend
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - init()
 * - generate_slider_javascript()
 * - generate_single_slider_javascript()
 * - generate_slider_settings_javascript()
 * - load_slider_javascript()
 * - enqueue_scripts()
 * - enqueue_styles()
 * - is_valid_theme()
 * - sanitize_theme_key()
 * - get_theme_data()
 * - maybe_load_theme_stylesheets()
 */
class SliderMe_Frontend {
	public $token;

	/**
	 * Constructor.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct () {
		require_once( 'class-slider_me-sliders.php' );
		$this->sliders = new SliderMe_Sliders();
		$this->sliders->token = $this->token;
	} // End __construct()

	/**
	 * Initialise the code.
	 * @since  1.0.0
	 * @return void
	 */
	public function init () {
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( &$this, 'load_slider_javascript' ) );
	} // End init()

	/**
	 * Generate the JavaScript code for each slider in use on the current screen.
	 * @since  1.0.0
	 * @return void
	 */
	private function generate_slider_javascript () {
		$html = '';

		// Remove slideshows with no slides, to prevent their JavaScript being generated.
		if ( is_array( $this->sliders->sliders ) && count( $this->sliders->sliders ) > 0 ) {
			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( ! is_array( $v['slides'] ) || 0 >= count( $v['slides'] ) ) {
					unset( $this->sliders->sliders[$k] );
				}
			}
		}

		if ( is_array( $this->sliders->sliders ) && count( $this->sliders->sliders ) > 0 ) {
			$html .= '<script type="text/javascript">' . "\n";
			$html .= 'jQuery(window).load(function() {' . "\n";
			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( isset( $v['args']['id'] ) ) {
					$html .= $this->generate_single_slider_javascript( $v['args']['id'], $v['args'], $v['extra'] );
				}
			}
			$html .= "\n" . '});' . "\n";
			$html .= '</script>' . "\n";
		}

		return $html;
	} // End generate_slider_javascript()

	/**
	 * Generate the JavaScript for a specified slideshow.
	 * @uses   generate_slider_settings_javascript()
	 * @since  1.0.0
	 * @param  int $id The ID of the slider for which to generate the JavaScript.
	 * @param  array $args Arguments to be used in the slider JavaScript.
	 * @param  array $extra Additional, contextual arguments to use when generating the slider JavaScript.
	 * @return string     The JavaScript code pertaining to the specified slider.
	 */
	private function generate_single_slider_javascript ( $id, $args, $extra = array() ) {
		$html = '';

		// Convert settings to a JavaScript-readable string.
		$args_output = $this->generate_slider_settings_javascript( $args, $extra );

		$html .= "\n" . 'jQuery( \'#' . esc_js( sanitize_key( $id ) ) . '\' ).flexslider2(' . $args_output . ');' . "\n";

		return $html;
	} // End generate_single_slider_javascript()

	/**
	 * Generate a JavaScript-friendly string of an object containing the slider arguments.
	 * @since  1.0.0
	 * @param  array $args 	Arguments for this slideshow.
	 * @param  array $extra Additional, contextual arguments to use when generating the slider JavaScript.
	 * @return string       A JavaScript-friendly string of arguments.
	 */
	private function generate_slider_settings_javascript ( $args, $extra = array() ) {
		// Begin the arguments output
		$args_output = '{';

		$args_output .= 'namespace: "slider_me-"' . "\n";

		// Process itemWidth for carousel
		$is_carousel = false;
		if ( isset( $args['item_width'] ) &&
			 '' !=  $args['item_width']   &&
			 0   <  $args['item_width'] ) {
			$is_carousel = true;
			$args_output .= ', ' . esc_js( 'itemWidth' ) . ': ' . esc_js( $args['item_width'] );
		}

		// Animation
		if ( isset( $args['animation'] ) && in_array( $args['animation'], SliderMe_Utils::get_supported_effects() ) ) {
			// only support slide for carousels
			if ( $is_carousel )
				$args_output .= ', animation: \'' . 'slide' . '\'';
			else
				$args_output .= ', animation: \'' . $args['animation'] . '\'';
		}

		// Direction
		if ( ( $args['animation'] == 'slide' ) && isset( $args['direction'] ) && in_array( $args['direction'], array( 'horizontal', 'vertical' ) ) ) {
			$args_output .= ', direction: \'' . $args['direction'] . '\'';
		}

		// Slideshow Speed
		if ( isset( $args['slideshow_speed'] ) && is_numeric( $args['slideshow_speed'] ) && ( floatval( $args['slideshow_speed'] ) > 0 ) ) {
			$args_output .= ', slideshowSpeed: ' . ( $args['slideshow_speed'] ) * 1000;
		}

		// Animation Duration
		if ( isset( $args['animation_duration'] ) && is_numeric( $args['animation_duration'] ) && ( floatval( $args['animation_duration'] ) > 0 ) ) {
			$args_output .= ', animationSpeed: ' . ( $args['animation_duration'] ) * 1000;
		}

		// Checkboxes.
		$options = array(
						'autoslide' => 'slideshow',
						'direction_nav' => 'directionNav',
						'keyboard_nav' => 'keyboard',
						'mousewheel_nav' => 'mousewheel',
						'playpause' => 'pausePlay',
						'randomize' => 'randomize',
						'animation_loop' => 'animationLoop',
						'pause_on_action' => 'pauseOnAction',
						'pause_on_hover' => 'pauseOnHover',
						'smoothheight' => 'smoothHeight',
						'touch' => 'touch'
						);

		if ( isset( $extra['thumbnails'] ) && ( $extra['thumbnails'] == 'true' || $extra['thumbnails'] == 1 ) ) {
			$args_output .= ', controlNav: "thumbnails"' . "\n";
		} else {
			$options['control_nav'] = 'controlNav';
		}

		// Process the checkboxes.
		foreach ( $options as $k => $v ) {
			$status = 'false';
			if ( isset( $args[$k] ) && ( ( $args[$k] == 'true' && $args[$k] != 'false' ) || $args[$k] == 1 ) ) {
				$status = 'true';
			}

			$args_output .= ', ' . esc_js( $v ) . ': ' . $status;
		}

		// Text fields.
		$options = array(
						'prev_text' => array( 'key' => 'prevText', 'default' => __( 'Previous', 'slider_me' ) ),
						'next_text' => array( 'key' => 'nextText', 'default' => __( 'Next', 'slider_me' ) ),
						'play_text' => array( 'key' => 'playText', 'default' => __( 'Play', 'slider_me' ) ),
						'pause_text' => array( 'key' => 'pauseText', 'default' => __( 'Pause', 'slider_me' ) )
						);

		// Process the text fields.
		foreach ( $options as $k => $v ) {
			if ( isset( $args[$k] ) && ( $args[$k] != $v['default'] ) ) {
				$args_output .= ', ' . esc_js( $v['key'] ) . ': \'' . esc_js( $args[$k] ) . '\'';
			}
		}

		// CSS Selector fields.
		$options = array(
						'sync' => array( 'key' => 'sync', 'default' => '' ),
						'as_nav_for' => array( 'key' => 'asNavFor', 'default' => '' )
						);

		// Process the CSS selector fields.
		foreach ( $options as $k => $v ) {
			if ( isset( $extra[$k] ) && ( $extra[$k] != $v['default'] ) ) {
				$args_output .= ', ' . esc_js( $v['key'] ) . ': \'' . esc_js( '#' . $extra[$k] ) . '\'';
			}
		}

		// Process the include title as nav checkbox
		if ( isset( $args['title_nav'] ) &&
			( ( $args['title_nav'] == 'true' && $args['title_nav'] != 'false' ) ||
				$args['title_nav'] == 1 ) ) {
			$id = esc_js( sanitize_key( $args['id'] ) );
			$args_output .= ', ' . "controlsContainer: '#" . $id . ".slider_me', manualControls: '#" . $id . " .slider_me-title-nav li'";
			$args_output .= ', ' . "start: function(slider) {slider.controlNav.eq(slider.getTarget('prev')).addClass('slider_me-control-prev'); slider.controlNav.eq(slider.getTarget('next')).addClass('slider_me-control-next');}";
			$args_output .= ', ' . "before: function(slider) {var animatingNext = slider.getTargetFor('next', slider.animatingTo);var animatingPrev = slider.getTargetFor('prev', slider.animatingTo);slider.controlNav.removeClass('slider_me-control-prev slider_me-control-next');slider.controlNav.eq(animatingNext).addClass('slider_me-control-next');slider.controlNav.eq(animatingPrev).addClass('slider_me-control-prev');}";
		}

		$args_output = apply_filters( 'slider_me_js_args_output', $args_output, $args, $extra );

		// End the arguments output
		$args_output .= '}';

		return $args_output;
	} // End generate_slider_settings_javascript()

	/**
	 * Load the slider JavaScript in the footer.
	 * @since  1.0.6
	 * @return void
	 */
	public function load_slider_javascript () {
		echo $this->generate_slider_javascript();

		// Conditionally load the theme stylesheets in the footer as well.
		$this->maybe_load_theme_stylesheets();
	} // End load_slider_javascript()

	/**
	 * Enqueue frontend JavaScripts.
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		global $slider_me;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( $this->token . '-mousewheel', esc_url( $slider_me->plugin_url . 'assets/js/jquery.mousewheel' . $suffix . '.js' ), array( 'jquery' ), '2.1.0-20121206', true );
		wp_register_script( $this->token . '-flexslider', esc_url( $slider_me->plugin_url . 'assets/js/jquery.flexslider' . $suffix . '.js' ), array( 'jquery', $this->token . '-mousewheel' ), '2.1.0-20121206', true );
		wp_enqueue_script( $this->token . '-flexslider' );
	} // End enqueue_scripts()

	/**
	 * Enqueue frontend CSS files.
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		global $slider_me;

		wp_register_style( $this->token . '-flexslider', esc_url( $slider_me->plugin_url . 'assets/css/flexslider.css' ), '', '1.0.1', 'all' );
		wp_register_style( $this->token . '-common', esc_url( $slider_me->plugin_url . 'assets/css/style.css' ), array( $this->token . '-flexslider' ), '1.0.1', 'all' );

		wp_enqueue_style( $this->token . '-common' );
	} // End enqueue_styles()

	/**
	 * Make sure the desired theme is valid. If not, return 'default'.
	 * @since  1.0.4
	 * @param  array $args  Arguments for the current slideshow.
	 * @return string       The slug of the theme, or 'default'.
	 */
	public function is_valid_theme ( $args ) {
		$response = false;
		if ( is_array( $args ) && isset( $args['theme'] ) && in_array( $args['theme'], array_keys( SliderMe_Utils::get_slider_themes() ) ) ) {
			$response = true;
		}
		return $response;
	} // End is_valid_theme()

	/**
	 * Make sure the desired theme is valid. If not, return 'default'.
	 * @since  1.0.4
	 * @param  array $args  Arguments for the current slideshow.
	 * @return string       The slug of the theme, or 'default'.
	 */
	public function get_sanitized_theme_key ( $args ) {
		$theme = 'default';
		if ( is_array( $args ) && isset( $args['theme'] ) && in_array( $args['theme'], array_keys( SliderMe_Utils::get_slider_themes() ) ) ) {
			$theme = esc_attr( strtolower( $args['theme'] ) );
		}
		return $theme;
	} // End get_sanitized_theme_key()

	/**
	 * Get data for a specified theme.
	 * @since  1.0.4
	 * @param  array $args  Arguments for the current slideshow.
	 * @return string       The slug of the theme, or 'default'.
	 */
	public function get_theme_data ( $key ) {
		$theme = array( 'name' => 'default', 'stylesheet' => '' );
		if ( in_array( $key, array_keys( SliderMe_Utils::get_slider_themes() ) ) ) {
			$themes = SliderMe_Utils::get_slider_themes();
			$theme = $themes[esc_attr( $key )];
		}
		return $theme;
	} // End get_theme_data()

	/**
	 * Maybe load stylesheets for the themes in use.
	 * @since  1.0.4
	 * @return void
	 */
	public function maybe_load_theme_stylesheets () {
		if ( isset( $this->sliders->sliders ) && ( 0 < $this->sliders->sliders ) ) {
			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( isset( $v['extra']['theme'] ) && ( '' != $v['extra']['theme'] ) ) {
					$theme_data = $this->get_theme_data( $v['extra']['theme'] );
					if ( isset( $theme_data['stylesheet'] ) && ( '' != $theme_data['stylesheet'] ) ) {
						wp_enqueue_style( 'slider_me-theme-' . esc_attr( $v['extra']['theme'] ), esc_url( $theme_data['stylesheet'] ) );
					}
				}
			}
		}
	} // End maybe_load_theme_stylesheets()
} // End Class
?>
