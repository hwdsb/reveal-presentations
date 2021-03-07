<?php
namespace HWDSB\Reveal\PreInsert;

use HWDSB\Reveal as App;

add_filter( 'rest_pre_insert_' . App\get( 'post_type_slug' ), function( $retval, $request ) {
	// Get our presentation term ID.
	$json    = $request->get_json_params();
	$term_id = 0;
	if ( 'publish' === $json['status'] && ! empty( $json[ App\get( 'presentation_slug' ) . 's' ] ) ) {
		$term_id = $json[ App\get( 'presentation_slug' ) . 's'][0];
	}

	// Something went wrong, so bail.
	if ( empty( $term_id ) ) {
		return $retval;
	}

	// Auto-generate a post title if empty.
	if ( empty( $retval->post_title ) ) {
		$blocks = parse_blocks( $retval->post_content );
		if ( ! empty( $blocks[0]['innerBlocks'] ) ) {
			$blocks = $blocks[0]['innerBlocks'];
			$retval->post_title = strip_tags( $blocks[0]['innerHTML'] );
		}
	}

	// Query for the last slide in the presentation.
	$q = new \WP_Query( [
		'post_type' => App\get( 'post_type_slug' ),
		'tax_query' => [ [
			'taxonomy' => App\get( 'presentation_slug' ),
			'terms' => $term_id
		] ],
		'posts_per_page' => 1,
		'page' => 1,
		'order' => 'DESC',
		'orderby' => 'menu_order'
	] );

	// Ensure our new slide is positioned at the end.
	if ( $q->have_posts() ) {
		$retval->menu_order = ++$q->post->menu_order;
	}

	return $retval;
}, 10, 2 );