<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enable the usage of do_action( 'slider_me' ) to display a slideshow within a theme/plugin.
 *
 * @since  1.0.6
 */
add_action( 'slider_me', 'slider_me', 10, 2 );

if ( ! function_exists( 'slider_me' ) ) {
/**
 * SliderMe template tag.
 * @since  1.0.0
 * @param  array   $args 	Optional array of arguments to customise this instance of the slider.
 * @param  array   $extra_args 	Optional array of extra arguments to customise this instance of the slider.
 * @param  boolean $echo 	Whether or not to echo the slider output (default: true)
 * @return string/void      Returns a string of $echo is false. Otherwise, returns void.
 */
function slider_me ( $args = array(), $extra_args = array(), $echo = true ) {
	global $slider_me;

	$defaults = $slider_me->settings->get_settings();
	$defaults['slider_type'] = 'attachments';

	$settings = wp_parse_args( $args, $defaults );

	// Generate an ID for this slider.
	if ( isset( $extra_args['id'] ) ) {
		$settings['id'] = $extra_args['id'];
	} else {
		$settings['id'] = 'slider_me-id-' . $slider_me->slider_count++;
	}

	$slides = $slider_me->frontend->sliders->get_slides( $settings['slider_type'], $extra_args, $settings );

	$slider_me->frontend->sliders->add( $slides, $settings, $extra_args );

	$theme = 'default';
	if ( $slider_me->frontend->is_valid_theme( $extra_args ) ) {
		$theme = $slider_me->frontend->get_sanitized_theme_key( $extra_args );
	}

	$classes = 'slider_me ' . esc_attr( $settings['id'] ) . ' slider_me-type-' . esc_attr( $settings['slider_type'] ) . ' slider_me-theme-' . esc_attr( $theme );

	$title_nav = false;
	if ( isset($settings['title_nav']) && (1 == $settings['title_nav']) ) {
		$title_nav = true;
		$classes .= ' slider_me-nav-by-title';
	}

	$slides_html = $slider_me->frontend->sliders->render( $slides, $extra_args );

	$html = '';
	if ( '' != $slides_html ) {
		$html .= '<div id="' . esc_attr( $settings['id'] ) . '" class="' .
					apply_filters('slider_me_slider_class', $classes) .  '"><ul class="slides">' . "\n";
		$html .= $slides_html;
		$html .= '</ul>';
		if ( $title_nav ) {
			$html .= '<ol class="slider_me-control-nav slider_me-title-nav">';
			foreach( $slides as $slide ) {
				$html .= '<li><a>' . $slide['attributes']['data-title'] . '</a></li>';
			}
			$html .= '</ol>';
		}

		$html .= '</div>' . "\n";
	}

	if ( $echo == true ) { echo $html; }

	return $html;
} // End slider_me()
}

if ( ! function_exists( 'slider_me_shortcode' ) ) {
/**
 * SliderMe shortcode wrapper.
 * @since  1.0.0
 * @param  array $atts    	Optional shortcode attributes, used to customise slider settings.
 * @param  string $content 	Content, if the shortcode supports wrapping of content.
 * @return string          	Rendered SliderMe.
 */
function slider_me_shortcode ( $atts, $content = null ) {
	global $slider_me;
	$args = $slider_me->settings->get_settings();
	$args['slider_type'] = 'attachments';
	$settings = shortcode_atts( $args, $atts );
	$extra_args = array();

	foreach ( (array)$atts as $k => $v ) {
		if ( ! in_array( $k, array_keys( $args ) ) ) {
			$extra_args[$k] = $v;
		}
	}

	return slider_me( $settings, $extra_args, false );
} // End slider_me_shortcode()
}

add_shortcode( 'slider_me', 'slider_me_shortcode' );
?>
