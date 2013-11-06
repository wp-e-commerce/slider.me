<?php
/*
Plugin Name: slider.me
Plugin URI: http://www.instinct.co.nz/
Description: Responsive full-width slider that can display posts, images or custom slides.
Version: 1.0.0
Author: Instinct Entertainment
Author URI: http://www.instinct.co.nz/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  Copyright 2013  Instinct Entertainment  (email : dan@instinct.co.nz)

    This program incorporates work covered by the following copyright and
    permission notices:

    Copyright 2012  WooThemes  (email : info@woothemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	require_once( 'classes/class-slider_me.php' );
	if ( ! is_admin() ) require_once( 'inc/slider_me-template.php' );

	global $slider_me;
	$slider_me = new SliderMe( __FILE__ );
	$slider_me->version = '1.0.0';
?>
