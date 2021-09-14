<?php
// Register block pattern category.
register_block_pattern_category( 'layouts', array( 'label' => __( 'Layouts', 'reveal-presentations' ) ) );

// Register block patterns.
register_block_pattern(
	'slides/heading-paragraph',
	[
		'title'       => __( 'Heading and Paragraph', 'reveal-presentations' ),
		'blockTypes'  => [ 'core/heading' ],
		'categories'  => [ 'layouts' ],
		'description' => _x( 'A main header and a paragraph.', 'Block pattern description', 'reveal-presentations' ),
		'content'     => '<!-- wp:heading {"level":1} --><h1></h1><!-- /wp:heading --><!-- wp:paragraph --><p></p><!-- /wp:paragraph -->',
		'viewportWidth' => '300',
	]
);

register_block_pattern(
	'slides/heading-text-columns',
	[
		'title'       => __( 'Heading and Text Columns', 'reveal-presentations' ),
		'blockTypes'  => [ 'core/heading' ],
		'categories'  => [ 'layouts' ],
		'description' => _x( 'A main header plus two columns for your content.', 'Block pattern description', 'reveal-presentations' ),
		'content'     => '<!-- wp:heading {"level":1} --><h1></h1><!-- /wp:heading --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p></p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p></p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
		'viewportWidth' => '300'
	]
);

register_block_pattern(
	'slides/text-media',
	[
		'title' => __( 'Text and Media', 'reveal-presentations' ),
		'blockTypes'  => [ 'core/heading' ],
		'categories'  => [ 'layouts' ],
		'description' => _x( 'Display an image on the left with a heading on the right.', 'Block pattern description', 'reveal-presentations' ),
		'content'     => '<!-- wp:media-text {"mediaType":"image","verticalAlignment":"top"} --><div class="wp-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-top"><figure class="wp-block-media-text__media"><img src="https://hwdsbcommons.s3.amazonaws.com/wp-content/uploads/2019/09/CommonsIcon-300x300.png" /></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":3,"className":"has-text-align-center"} --><h3 class="has-text-align-center"></h3><!-- /wp:heading --></div></div><!-- /wp:media-text -->'
	]
);