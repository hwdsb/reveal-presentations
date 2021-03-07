<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

use HWDSB\Reveal as App;

Container::make( 'term_meta', __( 'Autoplay Properties', 'reveal-presentations' ) )
	->where( 'term_taxonomy', '=', App\get( 'presentation_slug' ) )
	->add_fields( array(
		Field::make( 'html', 'presentation_heading_autoplay', __( 'Autoplay', 'reveal-presentations' ) )
			->set_html( sprintf( '<strong>%s</strong>', __( 'Autoplay', 'reveal-presentations' ) ) ),
		Field::make( 'text', 'presentation_autoslide_interval', __( 'Interval', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'The interval in seconds to proceed to the next slide automatically. Set to 0 if you want to disable.', 'reveal-presentations' ) )
			->set_attribute( 'pattern', '\d*' )
			->set_attribute( 'placeholder', 0 ),
		Field::make( 'select', 'presentation_loop', __( 'Loop Presentation?', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'When the presentation ends, pressing forward will start from the beginning again. If an interval is set, the presentation will automatically start again.', 'reveal-presentations' ) )
			->set_options( array(
				'false' => __( 'No', 'reveal-presentations' ),
				'true' => __( 'Yes', 'reveal-presentations' ),
			) ),
	) );

Container::make( 'term_meta', __( 'Appearance', 'reveal-presentations' ) )
	->where( 'term_taxonomy', '=', App\get( 'presentation_slug' ) )
	->add_fields( array(
		Field::make( 'html', 'presentation_heading_appearance', __( 'Appearance', 'reveal-presentations' ) )
			->set_html( sprintf( '<strong>%s</strong>', __( 'Appearance', 'reveal-presentations' ) ) ),
		Field::make( 'select', 'presentation_theme', __( 'Presentation Theme', 'reveal-presentations' ) )
			->set_help_text( sprintf( __( 'Choose the theme to use for your presentation. You can preview these themes %1$shere%2$s.', 'reveal-presentations' ), '<a href="https://lab.hakim.se/reveal-js-leap/#/themes" target="_blank">', '</a>' ) )
			->set_options( array(
				'black' => __( 'Black - Black background, white text, blue links', 'reveal-presentations' ),
				'white' => __( 'White - White background, black text, blue links', 'reveal-presentations' ),
				'league' => __( 'League - Gray background, white text, blue links', 'reveal-presentations' ),
				'beige' => __( 'Beige - Beige background, dark text, brown links', 'reveal-presentations' ),
				'sky' => __( 'Sky - Blue background, thin dark text, blue links', 'reveal-presentations' ),
				'night' => __( 'Night -	Black background, thick white text, orange links', 'reveal-presentations' ),
				'serif' => __( 'Serif - Cappuccino background, gray text, brown links', 'reveal-presentations' ),
				'simple' => __( 'Simple - White background, black text, blue links', 'reveal-presentations' ),
				'solarized' => __( 'Solarized - Cream-colored background, dark green text, blue links', 'reveal-presentations' ),
				'blood' => __( 'Blood - Dark background, thick white text, red links', 'reveal-presentations' ),
				'moon' => __( 'Moon - Dark blue background, thick grey text, blue links', 'reveal-presentations' ),
			) ),
		Field::make( 'select', 'presentation_progress', __( 'Progress Bar', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'Display a thin progress bar at the bottom of the presentation.', 'reveal-presentations' ) )
			->set_options( array(
				'true' => __( 'Yes', 'reveal-presentations' ),
				'false' => __( 'No', 'reveal-presentations' ),
			) ),
		Field::make( 'select', 'presentation_controls', __( 'Controls', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'Display presentation control arrows.', 'reveal-presentations' ) )
			->set_options( array(
				'true' => __( 'Yes', 'reveal-presentations' ),
				'false' => __( 'No', 'reveal-presentations' ),
			) ),
		Field::make( 'select', 'presentation_controls_location', __( 'Control Location', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'Where should the control arrows be displayed?', 'reveal-presentations' ) )
			->set_options( array(
				'bottom-right' => __( 'Bottom-right corner', 'reveal-presentations' ),
				'edges' => __( 'Middle-right', 'reveal-presentations' ),
			) )
		    ->set_conditional_logic( array(
		        array(
		            'field' => 'presentation_controls',
		            'value' => 'true'
		        )
		    ) ),
	) );

Container::make( 'term_meta', __( 'Slide Transitions', 'reveal-presentations' ) )
	->where( 'term_taxonomy', '=', App\get( 'presentation_slug' ) )
	->add_fields( array(
		Field::make( 'html', 'presentation_heading_transitions', __( 'Slide Transitions', 'reveal-presentations' ) )
			->set_html( sprintf( '<strong>%s</strong>', __( 'Slide Transitions', 'reveal-presentations' ) ) ),
		Field::make( 'select', 'presentation_transition', __( 'Transition', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'Set the default slide transition for your presentation. This can be overriden if a slide has set a custom transition.', 'reveal-presentations' ) )
			->set_options( array(
				'slide' => __( 'Slide', 'reveal-presentations' ),
				'none' => __( 'None', 'reveal-presentations' ),
				'fade' => __( 'Fade', 'reveal-presentations' ),
				'convex' => __( 'Convex', 'reveal-presentations' ),
				'concave' => __( 'Concave', 'reveal-presentations' ),
				'zoom' => __( 'Zoom', 'reveal-presentations' ),
			) ),
		Field::make( 'select', 'presentation_bg_transition', __( 'Background Transition', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'Set the default slide background transition for your presentation. This can be overriden if a slide has set a custom background transition.', 'reveal-presentations' ) )
			->set_options( array(
				'fade' => __( 'Fade', 'reveal-presentations' ),
				'none' => __( 'None', 'reveal-presentations' ),
				'slide' => __( 'Slide', 'reveal-presentations' ),
				'convex' => __( 'Convex', 'reveal-presentations' ),
				'concave' => __( 'Concave', 'reveal-presentations' ),
				'zoom' => __( 'Zoom', 'reveal-presentations' ),
			) ),
		Field::make( 'select', 'presentation_transition_speed', __( 'Transition Speed', 'reveal-presentations' ) )
			->set_help_text( esc_html__( 'Set the slide transition speed. This can be overriden if a slide has set a custom transition speed.', 'reveal-presentations' ) )
			->set_options( array(
				'default' => __( 'Default', 'reveal-presentations' ),
				'fast' => __( 'Fast', 'reveal-presentations' ),
				'slow' => __( 'Slow', 'reveal-presentations' ),
			) ),
	) );

/**
 * Alter "Count" column name.
 */
add_filter( 'manage_edit-' . App\get( 'presentation_slug' ) . '_columns', function( $retval ) {
	$retval['posts'] = esc_html__( 'Slides', 'reveal-presentations' );
	return $retval;
} );

/**
 * Add "View" link after updating a presentation.
 */
add_filter( 'term_updated_messages', function( $retval ) {
	$retval['_item'][3] = esc_html__( 'Presentation updated.', 'reveal-presentations' );

	// Only add "View" link if the presentation has slides.
	if ( ! empty( $_GET['tag_ID'] ) ) {
		$q = new WP_Query( [
			'post_type' => App\get( 'post_type_slug' ),
			'tax_query' => [ [
				'taxonomy' => App\get( 'presentation_slug' ),
				'terms' => $_GET['tag_ID']
			] ],
			'posts_per_page' => 1,
			'page' => 1
		] );
		if ( $q->have_posts() ) {
			$retval['_item'][3] .= sprintf( ' (<a href="%1$s">%2$s</a>)', get_term_link( (int) $_GET['tag_ID'], App\get( 'presentation_slug' ) ), esc_html__( 'View', 'reveal-presentations' ) );
		}
	}
	return $retval;
} );

/**
 * CSS adjustments.
 *
 * - Hide "Parent Presentation" dropdown field.
 * - On "Edit Presentation" page, fix Carbon Fields layout on mobile devices.
 */
add_action( 'admin_head', function() {
	$css = <<<CSS

	<style type="text/css">
	.term-parent-wrap {display:none}
	@media screen and (max-width: 782px) {
		body.term-php #wpbody-content .cf-container-term-meta .cf-field__label {margin:-2.2em 0 0 1em !important}
	}
	</style>

CSS;

	echo $css;
} );
