<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SliderMe "Attachments" Widget Class
 *
 * Widget class for the "Attachments" widget for SliderMe.
 *
 * @package WordPress
 * @subpackage SliderMe
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - generate_slideshow()
 */
class SliderMe_Widget_Attachments extends SliderMe_Widget_Base {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct () {
		/* Widget variable settings. */
		$this->slider_type = 'attachments';
		$this->slider_me_widget_cssclass = 'widget_slider_me_slideshow_attachments';
		$this->slider_me_widget_description = __( 'A slideshow of the images attached to the current page/post', 'slider_me' );
		$this->slider_me_widget_idbase = 'slider_me_slideshow_attachments';
		$this->slider_me_widget_title = __('Attached Images Slideshow (SliderMe)', 'slider_me' );

		$this->init();

		$this->defaults = array(
						'title' => __( 'Images', 'slider_me' )
					);
	} // End Constructor

	/**
	 * Generate the HTML for this slideshow.
	 * @since  1.0.0
	 * @return string The generated HTML.
	 */
	protected function generate_slideshow ( $instance ) {
		if ( ! is_singular() ) { return ''; }

		global $slider_me;
		$settings = $slider_me->settings->get_settings();
		$settings['slider_type'] = $this->slider_type;

		$extra_args = array();

		foreach ( $instance as $k => $v ) {
			if ( ! in_array( $k, array_keys( $settings ) ) ) {
				$extra_args[$k] = esc_attr( $v );
				unset( $instance[$k] );
			}
		}

		// Make sure the various settings are applied.
		if ( isset( $instance['show_advanced_settings'] ) && ( $instance['show_advanced_settings'] == true ) ) {
			foreach ( $settings as $k => $v ) {
				if ( isset( $instance[$k] ) && ( $instance[$k] != $settings[$k] ) ) {
					$settings[$k] = esc_attr( $instance[$k] );
				}
			}
		}

		$html = slider_me( $settings, $extra_args, false );

		return $html;
	} // End generate_slideshow()
} // End Class
?>
