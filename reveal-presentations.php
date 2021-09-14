<?php
/**
 * Plugin Name: Presentations
 * Description: Create presentations powered by Reveal.js. Each slide is an individual post. A taxonomy holds the slides for a presentation.
 * Version: 0.1
 * License: GPLv3
 */

namespace HWDSB\Reveal;

/** UTILITY */

/**
 * Return path to our plugin directory.
 *
 * @param  string $path Add relative path to end of directory.
 * @return string
 */
function return_path( $path = '' ) {
	return __DIR__  . '/' . $path;
}

/**
 * Return URL to our plugin directory.
 *
 * @param  string $path Add path to end of root URL.
 * @return string
 */
function return_url( $path = '' ) {
	return esc_url_raw( plugins_url( basename( __DIR__ ) . '/' . $path  ) );
}

/**
 * Returns property.
 *
 * @param string $param Property name.
 * @return mixed
 */
function get( $param = '' ) {
	switch ( $param ) {
		case 'post_type_slug' :
			return apply_filters( 'hwdsb_reveal_post_type_slug', 'slides' );
			break;

		case 'presentation_slug' :
			return apply_filters( 'hwdsb_reveal_presentation_slug', 'presentation' );
			break;

		default :
			return '';
	}
}

/** HOOKS */

/**
 * Register our post type and taxonomy.
 */
add_action( 'init', function() {
	// Set up the slide post type.
	$labels = [
		'name'               => _x( 'Presentation Slides', 'post type general name', 'reveal-presentations' ),
		'singular_name'      => _x( 'Presentation Slide', 'post type singular name', 'reveal-presentations' ),
		'add_new'            => _x( 'Add New Slide', 'slide', 'reveal-presentations' ),
		'add_new_item'       => esc_html__( 'Add New Slide', 'reveal-presentations' ),
		'edit_item'          => esc_html__( 'Edit Slide', 'reveal-presentations' ),
		'new_item'           => esc_html__( 'New Slide', 'reveal-presentations' ),
		'all_items'          => esc_html__( 'All Slides', 'reveal-presentations' ),
		'view_item'          => esc_html__( 'View Slide', 'reveal-presentations' ),
		'search_items'       => esc_html__( 'Search Slides', 'reveal-presentations' ),
		'not_found'          => esc_html__('No slides found', 'reveal-presentations' ),
		'not_found_in_trash' => esc_html__( 'No slides found in Trash', 'reveal-presentations' ),
		'parent_item_colon'  => '',
		'menu_name'          => esc_html__( 'Presentations', 'reveal-presentations' ),
	];
	$args = [
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_icon'          => 'dashicons-images-alt',
		'menu_position'      => apply_filters( 'hwdsb_reveal_menu_position', 20 ),
		'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions', 'unpublish' ],
		'show_in_rest'       => true,
	];
	register_post_type( get( 'post_type_slug' ), $args );

	foreach ( [ 'css', 'color', 'transition', 'background-transition', 'transition-speed' ] as $key ) {
	    register_post_meta( get( 'post_type_slug' ), "presentation-{$key}", [
	        'show_in_rest' => true,
	        'single'       => true,
	        'type'         => 'string',
	    ] );
	}

	// Set up the presentation taxonomy.
	$labels = [
		'name'                => _x( 'Presentations', 'taxonomy general name', 'reveal-presentations' ),
		'singular_name'       => _x( 'Presentation', 'taxonomy singular name', 'reveal-presentations' ),
		'search_items'        => esc_html__( 'Search Presentations', 'reveal-presentations' ),
		'popular_items'       => esc_html__( 'Popular Presentations', 'reveal-presentations' ),
		'all_items'           => esc_html__( 'All Presentations', 'reveal-presentations' ),
		'edit_item'           => esc_html__( 'Edit Presentation', 'reveal-presentations' ),
		'view_item'           => esc_html__( 'View Presentation', 'reveal-presentations' ),
		'update_item'         => esc_html__( 'Update Presentation', 'reveal-presentations' ),
		'add_new_item'        => esc_html__( 'Add New Presentation', 'reveal-presentations' ),
		'new_item_name'       => esc_html__( 'New Presentation Name', 'reveal-presentations' ),
		'back_to_items'       => esc_html__( '&larr; Back to Presentations', 'reveal-presentations' ),
		'parent_item'         => null,
		'parent_item_colon'   => null,
	];
	$args = [
		'labels'            => $labels,
		'public'            => true,
		'hierarchical'      => true,
		'rewrite'           => true,
		'sort'              => true,
		'show_in_rest'      => true,
		'rest_base'         => get( 'presentation_slug' ) . 's',
		'show_tagcloud'     => false,
		'show_admin_column' => true,
	];
	register_taxonomy( get( 'presentation_slug' ), get( 'post_type_slug' ), $args );

    register_term_meta( get( 'presentation_slug' ), '_presentation_theme', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
    ] );
} );

/**
 * Frontend loader.
 */
add_action( 'pre_get_posts', function( $q ) {
	// Bail if we're not on our taxonomy archive page.
	if ( ! $q->is_main_query() || ! is_tax( get( 'presentation_slug' ) ) ) {
		return;
	}

	// Set custom query args.
	$q->set( 'orderby',  'menu_order' );
	$q->set( 'order',    'ASC' );
	$q->set( 'nopaging', true );

	// Require frontend code.
	require_once return_path( 'includes/frontend.php' );
} );

/**
 * Frontend: Redirect single slides to presentation permalink.
 */
add_action( 'template_redirect', function() {
	if ( ! is_singular( get( 'post_type_slug' ) ) ) {
		return;
	}

	$term = (array) get_the_terms( get_the_ID(), get( 'presentation_slug' ) );
	if ( ! empty( $term[0] ) && ! empty( $term[0]->slug ) ) {
		$link = get_term_link( $term[0]->slug, get( 'presentation_slug' ) );
		if ( ! empty( $_GET['print-pdf'] ) ) {
			$link = add_query_arg( 'print-pdf', 1, $link );
		} else {
		 	$link .= '#/slide-' . get_the_ID();
		}

		wp_safe_redirect( $link );
		die();
	}
} );

/**
 * Admin block loader.
 */
function admin_block() {
	if ( get( 'post_type_slug' ) === get_current_screen()->post_type ) {
		require_once return_path( 'includes/admin-block.php' );
	}
}
add_action( 'load-post.php',     __NAMESPACE__ . '\\admin_block', 999 );
add_action( 'load-post-new.php', __NAMESPACE__ . '\\admin_block', 999 );

/**
 * Admin post column loader.
 */
add_action( 'load-edit.php', function() {
	if ( get( 'post_type_slug' ) === get_current_screen()->post_type ) {
		require_once return_path( 'includes/admin-column.php' );
	}
}, 0 );

/**
 * REST API insert post mods.
 */
add_filter( 'rest_dispatch_request', function( $retval, $request ) {
	// Only load up our mods on our slides CPT endpoint.
	if ( false !== strpos( $request->get_route(), '/' . get( 'post_type_slug' ) . '/' ) ) {
		require_once return_path( 'includes/rest-pre-insert.php' );
	}
	return $retval;
}, 10, 2 );

/**
 * Admin taxonomy page loader.
 *
 * We use the Carbon Fields library to generate our taxonomy meta fields.
 * Carbon Fields can only run before 'init', so we do our checks on
 * 'after_setup_theme' and manually parse the current URL.
 */
add_action( 'after_setup_theme', function() {
	// Bail if not admin area.
	if ( ! defined( 'WP_NETWORK_ADMIN' ) ) {
		return;
	}

	// Parse the current admin URL.
	$url = parse_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	if ( ! empty( $url['query'] ) ) {
		parse_str( $url['query'], $query );
	}

	/**
	 * Only register our taxonomy fields during POST submission or when we're
	 * on our taxonomy page.
	 */
	if ( ( ! empty( $_POST ) && false !== strpos( $url['path'], '/wp-admin/edit-tags.php' ) && get( 'presentation_slug' ) === $_POST['taxonomy'] ) ||
		( ! empty( $query['taxonomy'] ) && get( 'presentation_slug' ) === $query['taxonomy'] &&
		! empty( $query['post_type'] ) && get( 'post_type_slug' ) === $query['post_type'] )
	) {
		require_once return_path( 'vendor/autoload.php' );
		\Carbon_Fields\Carbon_Fields::boot();

		require_once return_path( 'includes/admin-taxonomy.php' );
	}
} );

/**
 * Remove core block patterns when on our post type admin page.
 */
add_filter( 'should_load_remote_block_patterns', function( $retval ) {
	$screen = get_current_screen();

	if ( get( 'post_type_slug' ) === $screen->post_type ) {
		$retval = false;
	}

	return $retval;
} );

/**
 * Register our block patterns only on our post type admin page.
 */
add_action( 'current_screen', function( $screen ) {
	if ( ! function_exists( 'register_block_pattern' ) || ! $screen->is_block_editor || get( 'post_type_slug' ) !== $screen->post_type ) {
		return;
	}

	require_once return_path( 'includes/admin-block-patterns.php' );
} );