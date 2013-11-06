<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SliderMe Utilities Class
 *
 * Common utility functions for SliderMe.
 *
 * @package WordPress
 * @subpackage SliderMe
 * @category Utilities
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - get_slider_types()
 * - get_posts_layout_types()
 * - get_supported_effects()
 * - get_slider_themes()
 */
class SliderMe_Utils {
	/**
	 * Get an array of the supported slider types.
	 * @since  1.0.0
	 * @return array The slider types supported by SliderMe.
	 */
	public static function get_slider_types () {
		return (array)apply_filters( 'slider_me_slider_types', array(
																	'attachments' => array( 'name' => __( 'Attached Images', 'slider_me' ), 'callback' => 'method' ),
																	'slides' => array( 'name' => __( 'Slides', 'slider_me' ), 'callback' => 'method' ),
																	'posts' => array( 'name' => __( 'Posts', 'slider_me' ), 'callback' => 'method' )
																	)
									);
	} // End get_slider_types()

	/**
	 * Get an array of the supported posts layout types.
	 * @since  1.0.0
	 * @return array The posts layout types supported by SliderMe.
	 */
	public static function get_posts_layout_types () {
		return (array)apply_filters( 'slider_me_posts_layout_types', array(
																	'text-left' => array( 'name' => __( 'Text Left', 'slider_me' ), 'callback' => 'method' ),
																	'text-right' => array( 'name' => __( 'Text Right', 'slider_me' ), 'callback' => 'method' ),
																	'text-top' => array( 'name' => __( 'Text Top', 'slider_me' ), 'callback' => 'method' ),
																	'text-bottom' => array( 'name' => __( 'Text Bottom', 'slider_me' ), 'callback' => 'method' )
																	)
									);
	} // End get_posts_layout_types()

	/**
	 * Return an array of supported slider effects.
	 * @since  1.0.0
	 * @uses  filter: 'slider_me_supported_effects'
	 * @return array Supported effects.
	 */
	public static function get_supported_effects () {
		return (array)apply_filters( 'slider_me_supported_effects', array( 'fade', 'slide' ) );
	} // End get_supported_effects()

	/**
	 * Get the placeholder thumbnail image.
	 * @since  1.0.0
	 * @return string The URL to the placeholder thumbnail image.
	 */
	public static function get_placeholder_image () {
		global $slider_me;
		return esc_url( apply_filters( 'slider_me_placeholder_thumbnail', $slider_me->plugin_url . 'assets/images/placeholder.png' ) );
	} // End get_placeholder_image()

	/**
	 * Get an array of the supported slider themes.
	 * @since  1.0.4
	 * @return array The slider themes supported by SliderMe.
	 */
	public static function get_slider_themes () {
		return (array)apply_filters( 'slider_me_slider_themes', array(
																	'default' => array( 'name' => __( 'Default', 'slider_me' ), 'stylesheet' => '' )
																	)
									);
	} // End get_slider_themes()
} // End Class
?>
