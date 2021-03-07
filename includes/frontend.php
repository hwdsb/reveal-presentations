<?php

namespace HWDSB\Reveal\Frontend;

use HWDSB\Reveal as App;

/** UTILITY */

function locate_template( $template ) {
	$template .= '.php';
	$templates = [ $template ];
	
	// If theme has custom template, include it.
	$custom = \locate_template( $templates );
	if ( '' !== $custom ) {
		return $custom;
	}

	return App\return_path( 'templates/' . $template );
}

function get_template_part( $template ) {
	\load_template( locate_template( $template ), false );
}

/** HOOKS */

add_action( 'wp_enqueue_scripts', function() {
	$template_dir = get_template_directory_uri();

	// Remove current theme stylesheets.
	foreach ( wp_styles()->registered as $handle => $arg ) {
		if ( false !== strpos( $arg->src, $template_dir ) ) {
			wp_dequeue_style( $handle );
		}
	}

	// Speaker.
	if ( isset( $_GET[ 'speaker' ] ) ) {
		wp_enqueue_script( 'reveal-speaker', App\return_url( 'assets/speaker.js' ), array(), '20200710', true );
		wp_enqueue_style( 'reveal-speaker', App\return_url( 'assets/speaker.css' ), array(), '20200710' );
		return;
	}

	$contain = (bool) get_term_meta( get_queried_object_id(), '_presentation_contain', true ) ?: false;

	// JS.
	wp_enqueue_script( 'reveal.js',       'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/reveal.js',        array(), '4.0.2', true );
	wp_enqueue_script( 'reveal.js-notes', 'https://cdn.jsdelivr.net/npm/reveal.js@3.9.2/plugin/notes/notes.js', array( 'reveal.js' ), '3.9.2', true );
	wp_enqueue_script( 'reveal-frontend', App\return_url( 'assets/frontend.js' ), array( 'reveal.js', 'reveal.js-notes', 'wp-i18n' ), '0.1', true );
	wp_localize_script( 'reveal-frontend', 'slideTemplate', array(
		'revealSettings' => array(
			'autoSlide' => (int) get_term_meta( get_queried_object_id(), '_presentation_autoslide_interval', true ) * 1000 ?: 0,
			'loop' => (bool) get_term_meta( get_queried_object_id(), '_presentation_loop', true ) ?: false,
			'transition' => get_term_meta( get_queried_object_id(), '_presentation_transition', true ) ?: 'slide',
			'backgroundTransition' => get_term_meta( get_queried_object_id(), '_presentation_bg_transition', true ) ?: 'fade',
			'transitionSpeed' => get_term_meta( get_queried_object_id(), '_presentation_transition_speed', true ) ?: 'default',
			'controls' => (bool) get_term_meta( get_queried_object_id(), '_presentation_controls', true ) ?: true,
			'controlsLayout' => get_term_meta( get_queried_object_id(), '_presentation_controls_location', true ) ?: 'bottom-right',
			'progress' => (bool) get_term_meta( get_queried_object_id(), '_presentation_progress', true ) ?: true,
			'hash' => true,
			'history' => true,
			'preloadIframes' => true,
			'height' => 720,
			'width' => (int) get_term_meta( get_queried_object_id(), '_presentation_width', true ) ?: 960,
			'margin' => $contain ? 0 : 0.08,
			'keyboard' => true,
			'overview' => false,
			// We center in CSS.
			'center' => false,
			'pdfMaxPagesPerSlide' => 1,
		),
		'contain' => $contain
	) );

	// Styles
	wp_enqueue_style( 'reveal.js-reset', 'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/reset.css', array(), '4.0.2' );
	wp_enqueue_style( 'reveal.js', 'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/reveal.css', array( 'reveal.js-reset' ), '4.0.2' );
	wp_enqueue_style( 'reveal.js-theme', sprintf( 'https://cdn.jsdelivr.net/npm/reveal.js@4.0.2/dist/theme/%s.css', get_term_meta( get_queried_object_id(), '_presentation_theme', true ) ?: 'black' ), array( 'reveal.js' ), '4.0.2' );
	wp_enqueue_style( 'reveal-common', App\return_url( 'assets/common.css' ), array( 'reveal.js-theme' ), '20200710' );

	if ( isset( $_GET['print-pdf'] ) ) {
		wp_enqueue_style( 'reveal.js-pdf', 'https://cdn.jsdelivr.net/npm/reveal.js@3.9.2/css/print/pdf.css', array(), '4.0.2' );
	}
} );

add_filter( 'template_include', function( $retval ) {
	if ( isset( $_GET[ 'speaker' ] ) ) {
		return locate_template( 'taxonomy-' . App\get( 'presentation_slug' ) . '-speaker' );
	}
	
	return locate_template( 'taxonomy-' . App\get( 'presentation_slug' ) );
} );

add_filter( 'render_block', function( $retval, $block ) {
	if ( 'slide/slide' !== $block['blockName'] ) {
		return $retval;
	}

	// Inject slide post ID for permalinks.
	$block['innerContent'][0] = str_replace( '<section ', '<section id="slide-' . get_the_ID() . '" ', $block['innerContent'][0] );

	// Reconstruct inner block content.
	$innerContent = '';
	foreach ( $block['innerBlocks'] as $inner ) {
		if ( ! empty( $inner['innerHTML'] ) && is_string( $inner['innerHTML'] ) ) {
			$innerContent .= $inner['innerHTML'];
		}
	}
	$block['innerContent'][1] = $innerContent;

	// Add speaker notes if available.
	if ( ! empty( $block['attrs']['notes'] ) ) {
		$block['innerContent'][1] .= sprintf( '<aside class="notes">%s</aside>', $block['attrs']['notes'] );
	}

	return implode( '', $block['innerContent'] );
}, 10, 2 );
