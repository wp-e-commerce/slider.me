<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SliderMe Post Types Class
 *
 * All functionality pertaining to the post types and taxonomies in SliderMe.
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
 * - setup_slide_post_type()
 * - setup_slide_pages_taxonomy()
 * - setup_post_type_labels_base()
 * - create_post_type_labels()
 * - setup_post_type_messages()
 * - create_post_type_messages()
 * - enter_title_here()
 */
class SliderMe_PostTypes {
	public $token;
	public $slider_labels;

	/**
	 * Constructor
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->labels = array();
		$this->setup_post_type_labels_base();
		add_action( 'init', array( &$this, 'setup_slide_post_type' ), 100 );
		add_action( 'init', array( &$this, 'setup_slide_pages_taxonomy' ), 100 );

		if ( is_admin() ) {
			global $pagenow;
			if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
				add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 10 );
				add_filter( 'post_updated_messages', array( &$this, 'setup_post_type_messages' ) );
			}
			add_filter( 'manage_edit-slide_columns', array( &$this, 'add_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'add_column_data' ), 10, 2 );
		}
	} // End __construct()

	/**
	 * Setup the "slide" post type, it's admin menu item and the appropriate labels and permissions.
	 * @since  1.0.0
	 * @uses  global $slider_me
	 * @return void
	 */
	public function setup_slide_post_type () {
		global $slider_me;

		$args = array(
		    'labels' => $this->create_post_type_labels( 'slide', $this->labels['slide']['singular'], $this->labels['slide']['plural'], $this->labels['slide']['menu'] ),
		    'public' => false,
		    'publicly_queryable' => true,
		    'show_ui' => true,
		    'show_in_menu' => true,
		    'query_var' => true,
		    'rewrite' => array( 'slug' => 'slider', 'with_front' => false, 'feeds' => false, 'pages' => false ),
		    'capability_type' => 'post',
		    'has_archive' => false,
		    'hierarchical' => false,
		    'menu_position' => 20, // Below "Pages"
		    'menu_icon' => esc_url( $slider_me->plugin_url . 'assets/images/icon_slide_16.png' ),
		    'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' )
		);

		register_post_type( 'slide', $args );
	} // End setup_slide_post_type()

	/**
	 * Setup the "slide-page" taxonomy, linked to the "slide" post type.
	 * @since  1.0.0
	 * @return void
	 */
	public function setup_slide_pages_taxonomy () {
		// "Slide Groups" Custom Taxonomy
		$labels = array(
			'name' => _x( 'Slide Groups', 'taxonomy general name', 'slider_me' ),
			'singular_name' => _x( 'Slide Group', 'taxonomy singular name', 'slider_me' ),
			'search_items' =>  __( 'Search Slide Groups', 'slider_me' ),
			'all_items' => __( 'All Slide Groups', 'slider_me' ),
			'parent_item' => __( 'Parent Slide Group', 'slider_me' ),
			'parent_item_colon' => __( 'Parent Slide Group:', 'slider_me' ),
			'edit_item' => __( 'Edit Slide Group', 'slider_me' ),
			'update_item' => __( 'Update Slide Group', 'slider_me' ),
			'add_new_item' => __( 'Add New Slide Group', 'slider_me' ),
			'new_item_name' => __( 'New Slide Group Name', 'slider_me' ),
			'menu_name' => __( 'Slide Groups', 'slider_me' ),
			'popular_items' => null // Hides the "Popular" section above the "add" form in the admin.
		);

		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'slide-page' )
		);

		register_taxonomy( 'slide-page', array( 'slide' ), $args );
	} // End setup_slide_pages_taxonomy()

	/**
	 * Setup the singular, plural and menu label names for the post types.
	 * @since  1.0.0
	 * @return void
	 */
	private function setup_post_type_labels_base () {
		$this->labels = array( 'slide' => array() );

		$this->labels['slide'] = array( 'singular' => __( 'Slide', 'slider_me' ), 'plural' => __( 'Slides', 'slider_me' ), 'menu' => __( 'Slideshows', 'slider_me' ) );
	} // End setup_post_type_labels_base()

	/**
	 * Create the labels for a specified post type.
	 * @since  1.0.0
	 * @param  string $token    The post type for which to setup labels (used to provide context)
	 * @param  string $singular The label for a singular instance of the post type
	 * @param  string $plural   The label for a plural instance of the post type
	 * @param  string $menu     The menu item label
	 * @return array            An array of the labels to be used
	 */
	private function create_post_type_labels ( $token, $singular, $plural, $menu ) {
		$labels = array(
		    'name' => sprintf( _x( '%s', 'post type general name', 'slider_me' ), $plural ),
		    'singular_name' => sprintf( _x( '%s', 'post type singular name', 'slider_me' ), $singular ),
		    'add_new' => sprintf( _x( 'Add New %s', $token, 'slider_me' ), $singular ),
		    'add_new_item' => sprintf( __( 'Add New %s', 'slider_me' ), $singular ),
		    'edit_item' => sprintf( __( 'Edit %s', 'slider_me' ), $singular ),
		    'new_item' => sprintf( __( 'New %s', 'slider_me' ), $singular ),
		    'all_items' => sprintf( __( 'All %s', 'slider_me' ), $plural ),
		    'view_item' => sprintf( __( 'View %s', 'slider_me' ), $singular ),
		    'search_items' => sprintf( __( 'Search %s', 'slider_me' ), $plural ),
		    'not_found' =>  sprintf( __( 'No %s found', 'slider_me' ), strtolower( $plural ) ),
		    'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'slider_me' ), strtolower( $plural ) ),
		    'parent_item_colon' => '',
		    'menu_name' => $menu
		  );

		return $labels;
	} // End create_post_type_labels()

	/**
	 * Setup update messages for the post types.
	 * @since  1.0.0
	 * @param  array $messages The existing array of messages for post types.
	 * @return array           The modified array of messages for post types.
	 */
	public function setup_post_type_messages ( $messages ) {
		global $post, $post_ID;

		$messages['slide'] = $this->create_post_type_messages( 'slide' );

		return $messages;
	} // End setup_post_type_messages()

	/**
	 * Create an array of messages for a specified post type.
	 * @since  1.0.0
	 * @param  string $post_type The post type for which to create messages.
	 * @return array            An array of messages (empty array if the post type isn't one we're looking to work with).
	 */
	private function create_post_type_messages ( $post_type ) {
		global $post, $post_ID;

		if ( ! isset( $this->labels[$post_type] ) ) { return array(); }

		$messages = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( '%s updated.' ), esc_attr( $this->labels[$post_type]['singular'] ) ),
			2 => __( 'Custom field updated.', 'slider_me' ),
			3 => __( 'Custom field deleted.', 'slider_me' ),
			4 => sprintf( __( '%s updated.', 'slider_me' ), esc_attr( $this->labels[$post_type]['singular'] ) ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision']) ? sprintf( __('%2$s restored to revision from %1$s', 'slider_me' ), wp_post_revision_title( (int) $_GET['revision'], false ), esc_attr( $this->labels[$post_type]['singular'] ) ) : false,
			6 => sprintf( __('%2$s published.' ), esc_url( get_permalink($post_ID) ), esc_attr( $this->labels[$post_type]['singular'] ) ),
			7 => sprintf( __( '%s saved.', 'slider_me' ),  esc_attr( $this->labels[$post_type]['singular'] ) ),
			8 => sprintf( __( '%2$s submitted.', 'slider_me' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), esc_attr( $this->labels[$post_type]['singular'] ) ),
			9 => sprintf( __( '%s scheduled for: <strong>%1$s</strong>.', 'slider_me' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( ' M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ), strtolower( esc_attr( $this->labels[$post_type]['singular'] ) ) ),
			10 => sprintf( __( '%s draft updated.', 'slider_me' ), esc_attr( $this->labels[$post_type]['singular'] ) ),
		);

		return $messages;
	} // End create_post_type_messages()

	/**
	 * Change the "Enter Title Here" text for the "slide" post type.
	 * @access public
	 * @since  1.0.0
	 * @param  string $title
	 * @return string $title
	 */
	public function enter_title_here ( $title ) {
		if ( get_post_type() == 'slide' ) {
			$title = __( 'Enter a title for this slide here', 'slider_me' );
		}

		return $title;
	} // End enter_title_here()

	/**
	 * Add column headings to the "slides" post list screen.
	 * @access public
	 * @since  1.0.0
	 * @param  array $defaults
	 * @return array $new_columns
	 */
	public function add_column_headings ( $defaults ) {
		$new_columns['cb'] = '<input type="checkbox" />';
		// $new_columns['id'] = __( 'ID' );
		$new_columns['title'] = _x( 'Slide Title', 'column name', 'slider_me' );
		$new_columns['slide-thumbnail'] = _x( 'Featured Image', 'column name', 'slider_me' );
		$new_columns['slide-page'] = _x( 'Slide Groups', 'column name', 'slider_me' );

		if ( isset( $defaults['date'] ) ) {
			$new_columns['date'] = $defaults['date'];
		}

		return $new_columns;
	} // End add_column_headings()

	/**
	 * Add data for our newly-added custom columns.
	 * @access public
	 * @since  1.0.0
	 * @param  string $column_name
	 * @param  int $id
	 * @return void
	 */
	public function add_column_data ( $column_name, $id ) {
		global $wpdb, $post;

		switch ( $column_name ) {
			case 'id':
				echo $id;
			break;

			case 'slide-page':
				$value = __( 'No Slide Groups Specified', 'slider_me' );
				$terms = get_the_terms( $id, 'slide-page' );

				if ( $terms && ! is_wp_error( $terms ) ) {
					$term_links = array();

					foreach ( $terms as $term ) {
						$term_links[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => 'slide', 'tag_ID' => $term->term_id, 'taxonomy' => 'slide-page', 'action' => 'edit' ), 'edit-tags.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'slide-page', 'display' ) )
						);
					}

					$value = join( ', ', $term_links );
				}
				echo $value;
			break;

			case 'slide-thumbnail':
				echo '<a href="' . esc_url( admin_url( add_query_arg( array( 'post' => intval( $id ), 'action' => 'edit' ), 'post.php' ) ) ) . '">' . "\n";
				if ( has_post_thumbnail( $id ) ) {
					the_post_thumbnail( array( 75, 75 ) );
				} else {
					echo '<img src="' . esc_url( SliderMe_Utils::get_placeholder_image() ) . '" width="75" />' . "\n";
				}
				echo '</a>' . "\n";
			break;

			default:
			break;
		}
	} // End add_column_data()
} // End Class
?>
