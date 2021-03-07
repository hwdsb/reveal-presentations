<?php
namespace HWDSB\Reveal\AdminColumn;

use HWDSB\Reveal as App;

/**
 * Adds presentation name and edit link on main Slides page.
 *
 * Only when a presentation is filtered though.
 */
add_action( 'manage_posts_extra_tablenav', function( $location ) {
	if ( 'top' === $location && ! empty( $_GET[ App\get( 'presentation_slug' ) ] ) ) {
		$term = get_term_by( 'slug', $_GET[ App\get( 'presentation_slug' ) ], App\get( 'presentation_slug' ) );
		printf( '<br class="presentation-clear clear" /><h2 class="presentation-heading">%1$s</h2><a class="button presentation-edit" href="%2$s">%3$s</a>',
			get_term_field( 'name', $term, App\get( 'presentation_slug' ) ),
			admin_url( 'term.php?taxonomy=' . App\get( 'presentation_slug' ) . '&tag_ID=' . $term->term_id . '&post_type=' . App\get( 'post_type_slug' ) ),
			esc_html__( 'Edit', 'reveal-presentations' )
		);
	}
} );

/**
 * Add "All Presentations" dropdown filter.
 */
add_action( 'restrict_manage_posts', function() {
	$tax = get_object_taxonomies( App\get( 'post_type_slug' ), 'objects' );
	$walker = function( $taxonomy ) {
		wp_dropdown_categories( array(
			'show_option_all' => sprintf( esc_html__( 'All %s', 'reveal-presentations' ), $taxonomy->label ),
			'orderby'         => 'name',
			'order'           => 'ASC',
			'hide_empty'      => true,
			'hide_if_empty'   => true,
			'selected'        => filter_input( INPUT_GET, $taxonomy->query_var, FILTER_SANITIZE_STRING ),
			'hierarchical'    => true,
			'name'            => $taxonomy->query_var,
			'taxonomy'        => $taxonomy->name,
			'value_field'     => 'slug',
		) );
	};

	array_walk( $tax, $walker );
} );

/**
 * Don't show "Presentations" column when filtering by presentation.
 */
add_filter( 'manage_taxonomies_for_' . App\get( 'post_type_slug' ) . '_columns', function( $retval ) {
	if ( ! empty( $_GET[ App\get( 'presentation_slug' ) ] ) ) {
		return [];
	}

	return $retval;
} );

/**
 * Enable drag-n-drop sorting only when filtering by presentation.
 *
 * Drag-n-drop sorting is handled by the "Simple Page Ordering" plugin.
 */
add_filter( 'simple_page_ordering_is_sortable', function( $retval ) {
	if ( empty( $_GET[ App\get( 'presentation_slug' ) ] ) ) {
		return false;
	}

	return $retval;
} );

/**
 * Increase slides per page to 50 in admin area.
 */
add_filter( 'edit_posts_per_page', function( $retval ) {
	return 50;
} );

/**
 * Inline CSS.
 */
add_action( 'admin_head', function() {
	if ( empty( $_GET[ App\get( 'presentation_slug' ) ] ) ) {
		return;
	}

	$css = <<<CSS

	<style type="text/css">
	h2.presentation-heading, #posts-filter a.presentation-edit {display:inline-block; margin-top:1em}
	#posts-filter a.presentation-edit {margin-left:.5em}
	table.widefat {margin-top:4em;}
	@media screen and (max-width: 782px) {
		.tablenav br.presentation-clear {display:none}
		h2.presentation-heading {margin:0 0 0 .2em}
		#posts-filter a.presentation-edit {margin-top:0}
		table.widefat {margin-top:1.3em}
	}
	</style>

CSS;

	echo $css;
}, 999 );
