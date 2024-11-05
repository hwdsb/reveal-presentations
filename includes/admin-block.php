<?php

namespace HWDSB\Reveal\AdminBlock;

use HWDSB\Reveal as App;

function palette() {
	remove_editor_styles();
	remove_theme_support( 'editor-color-palette' );
	remove_theme_support( 'editor-font-sizes' );
	add_theme_support( 'align-wide' );
	
	if ( ! isset( $_GET['post'] ) ) {
		return;
	}
	
	$post = get_post( $_GET['post'] );
	
	if ( ! $post ) {
		return;
	}
	
	$palette = get_post_meta( $post->ID, 'presentation-color-palette', true );
	
	if ( ! $palette ) {
		return;
	}
	
	$palette = explode( ',', $palette );
	$palette = array_map( 'trim', $palette );
	$palette = array_map( 'sanitize_hex_color', $palette );
	$palette = array_map( function( $hex ) {
		return array( 'color' => $hex );
	}, $palette );
	
	if ( count( $palette ) ) {
		add_theme_support( 'editor-color-palette', $palette );
	}
}

palette();

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

	wp_enqueue_script(
		'slide',
		App\return_url( 'assets/block.js' ),
		array(
			'wp-element',
			'wp-i18n',
			'wp-blocks',
			'wp-rich-text',
			'wp-plugins',
			'wp-edit-post',
			'wp-data',
			'wp-components',
			'wp-block-editor',
			'wp-url',
			'wp-compose',
			'wp-hooks'
		),
		'20210913',
		true
	);

	wp_enqueue_script(
		'slide-mods',
		App\return_url( 'assets/block-mods.js' ),
		array(
			'wp-blocks',
			'wp-edit-post',
		),
		'20210305'
	);


	$theme   = 'black';
	$term_id = 0;
	if ( ! empty( $_GET['post'] ) ) {
		$term = (array) get_the_terms( $_GET['post'], App\get( 'presentation_slug' ) );
		if ( ! empty( $term[0] ) ) {
			$theme   = get_term_meta( $term[0]->term_id, '_presentation_theme', true ) ?: $theme;
			$term_id = (int) $term[0]->term_id;
		}
	}

	wp_localize_script( 'slide-mods', 'slidePresentation', array(
		'presentationSlug' => App\get( 'presentation_slug' ) . 's',
		'initialTheme' => $theme,
		'defaultTheme' => 'black',
		'initialTermId' => $term_id,
		'themeBase' => 'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/theme/',
	) );

	wp_enqueue_style(
		'slide',
		App\return_url( 'assets/block.css' ),
		array(),
		'20210913'
	);

	wp_deregister_style( 'wp-block-library-theme' );
	wp_register_style(
		'wp-block-library-theme',
		App\return_url( 'assets/common.css' ),
		array(),
		'20210907'
	);

	wp_enqueue_style( 'reveal.js', 'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/reveal.css', array(), '4.0.2' );
	wp_enqueue_style( 'reveal.js-theme', sprintf( 'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/theme/%s.css', $theme ), array(), '4.0.2' );

	wp_enqueue_style(
		'reveal-theme-preview',
		App\return_url( 'assets/admin-theme.css' ),
		array(),
		'20200710'
	);

	$template_directory_uri = get_template_directory_uri();

	foreach ( $GLOBALS['wp_styles']->queue as $handle ) {
		$info = $GLOBALS['wp_styles']->registered[ $handle ];

		if ( substr( $info->src, 0, strlen( $template_directory_uri ) ) === $template_directory_uri ) {
			wp_dequeue_style( $handle );
		}
	}
}, 99999 );


/*
 * WP 5.8 deprecated 'block_editor_settings'.
 *
 * Remove this when we're not supporting older versions.
 */
$all = function_exists( '_wp_array_set' ) ? '_all' : '';

/**
 * Wipe out inline styles.
 */
add_filter( "block_editor_settings{$all}", function( $settings ) {
	$settings['styles'] = [];
	return $settings;
}, 99999 );

add_filter( "allowed_block_types{$all}", function( $retval ) {
	return [
		'core/paragraph',
		'core/image',
		'core/heading',
		'core/list',
		'core/quote',
		'core/code',
		'core/media-text',
		'core/columns',
		'core/column'
	];
} );

add_filter( 'default_content', function( $post_content, $post ) {
	// Allow slide template from block pattern.
	if ( ! empty( $_GET['slide-template'] ) && check_admin_referer( 'reveal-slide-template-' . $_GET['slide-template'] ) ) {
		return get_post_field( 'post_content', $_GET['slide-template'], 'raw' );
	}

	return file_get_contents( App\return_path( 'templates/default-content.html' ) );
}, 10, 2 );

/**
 * Hook to do things on an admin block page.
 */
do_action( 'hwdsb_reveal_load_admin_block' );
