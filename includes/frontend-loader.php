<?php
namespace HWDSB\Reveal\Frontend;

use HWDSB\Reveal as App;

/**
 * Presentation taxonomy loader.
 */
add_action( 'pre_get_posts', function( $q ) {
	// Bail if we're not on our taxonomy archive page.
	if ( ! $q->is_main_query() || ! is_tax( App\get( 'presentation_slug' ) ) ) {
		return;
	}

	// Set custom query args.
	$q->set( 'orderby',  'menu_order' );
	$q->set( 'order',    'ASC' );
	$q->set( 'nopaging', true );

	// Require frontend code.
	require_once App\return_path( 'includes/frontend-presentation.php' );
} );

/**
 * Redirect single slides to presentation permalink.
 */
add_action( 'template_redirect', function() {
	if ( ! is_singular( App\get( 'post_type_slug' ) ) ) {
		return;
	}

	$term = (array) get_the_terms( get_the_ID(), App\get( 'presentation_slug' ) );
	if ( ! empty( $term[0] ) && ! empty( $term[0]->slug ) ) {
		$link = get_term_link( $term[0]->slug, App\get( 'presentation_slug' ) );
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
 * REST API insert post mods.
 */
add_filter( 'rest_dispatch_request', function( $retval, $request ) {
	// Only load up our mods on our slides CPT endpoint.
	if ( false !== strpos( $request->get_route(), '/' . App\get( 'post_type_slug' ) . '/' ) ) {
		require_once App\return_path( 'includes/rest-pre-insert.php' );
	}
	return $retval;
}, 10, 2 );
