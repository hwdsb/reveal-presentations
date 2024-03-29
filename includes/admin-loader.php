<?php
namespace HWDSB\Reveal\Admin;

use HWDSB\Reveal as App;

/**
 * Current screen loader.
 */
add_action( 'current_screen', function( $screen ) {
	if ( App\get( 'post_type_slug' ) !== $screen->post_type ) {
		return;
	}

	// Admin post column loader.
	if ( 'edit' === $screen->base ) {
		require_once App\return_path( 'includes/admin-column.php' );

	// Admin block loader.
	} elseif ( 'post' === $screen->base ) {
		require_once App\return_path( 'includes/admin-block.php' );
	}

	// Remove core block patterns.
	add_filter( 'should_load_remote_block_patterns', '__return_false' );

	// Register block patterns.
	if ( function_exists( 'register_block_pattern' ) && $screen->is_block_editor ) {
		require_once App\return_path( 'includes/admin-block-patterns.php' );
	}
}, 0 );

// Parse the current admin URL.
$query = [];
$url = parse_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
if ( ! empty( $url['query'] ) ) {
	parse_str( $url['query'], $query );
}

/**
 * Unregister all block patterns on our post type pages.
 *
 * Requires manual URL parsing since if you attempt to unregister block
 * patterns after 'init', this will cause problems.
 */
add_action( 'init', function() use ( $url, $query ) {
	$unregister = false;

	// New slide.
	if ( false !== strpos( $url['path'], '/wp-admin/post-new.php' ) &&
		! empty( $query['post_type'] ) && App\get( 'post_type_slug' ) === $query['post_type'] ) {
		$unregister = true;

	// See if edited post is a slide.
	} elseif ( false !== strpos( $url['path'], '/wp-admin/post.php' ) &&
		! empty( $query['action'] ) && 'edit' === $query['action'] ) {
		if ( App\get( 'post_type_slug' ) === get_post_type( (int) $query['post'] ) ) {
			$unregister = true;
		}
	}

	// Unregister time!
	if ( true === $unregister && function_exists( 'unregister_block_pattern' ) ) {
		foreach ( \WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $pattern ) {
			unregister_block_pattern( $pattern['name'] );
		}
	}
}, 99 );

/**
 * Admin taxonomy page loader.
 *
 * We use the Carbon Fields library to generate our taxonomy meta fields.
 * Carbon Fields can only run before 'init', so we do our checks on
 * 'after_setup_theme' and manually parse the current URL.
 */
if ( ( ! empty( $_POST ) && false !== strpos( $url['path'], '/wp-admin/edit-tags.php' ) && App\get( 'presentation_slug' ) === $_POST['taxonomy'] ) ||
	( ! empty( $query['taxonomy'] ) && App\get( 'presentation_slug' ) === $query['taxonomy'] &&
	! empty( $query['post_type'] ) && App\get( 'post_type_slug' ) === $query['post_type'] )
) {
	require_once App\return_path( 'vendor/autoload.php' );
	\Carbon_Fields\Carbon_Fields::boot();

	require_once App\return_path( 'includes/admin-taxonomy.php' );
}
