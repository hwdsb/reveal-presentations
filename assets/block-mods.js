wp.domReady( function() {
	var subscribe = wp.data.subscribe,
		el = wp.element.createElement,
		PluginPrePublishPanel = wp.editPost.PluginPrePublishPanel,
		registerPlugin = wp.plugins.registerPlugin,
		currentTheme = window.slidePresentation.initialTheme,
		currentTermId = window.slidePresentation.initialTermId,
		changedTheme = '',
		presThemes = [],
		switchTheme,
		slide,
		lock = false,
		__ = wp.i18n.__;

	/*
	 * This sucks, but has to be done since we can't determine when Gutenberg
	 * is fully loaded.
	 */
	let blockLoadedInterval = setInterval( function() {
		slide = jQuery( '.wp-block-slide-slide__body' );
		if ( slide.length ) {
			slide.addClass( 'reveal reveal-theme-' + currentTheme );

			if ( 'rgba(0, 0, 0, 0)' === slide.css( 'backgroundColor' ) ) {
				slide.addClass( 'reveal-background-image' );
			};

	        clearInterval( blockLoadedInterval );
	    }
	}, 500 );

	// Remove Featured Image panel.
	wp.data.dispatch('core/edit-post').removeEditorPanel('featured-image');

	presThemes[currentTermId] = currentTheme;

	switchTheme = function( newTheme ) {
		if ( currentTheme !== newTheme ) {
			document.getElementById("reveal.js-theme-css").setAttribute( 'href', window.slidePresentation.themeBase + newTheme + '.css' );
			currentTheme = newTheme;
			slide.attr('class', function(i, c){
				return c.replace(/(^|\s)reveal-theme-\S+/g, '');
			});
			slide.addClass( 'reveal-theme-' + currentTheme );
		}
	};

	// See if a presentation was selected or not.
	subscribe( function() {
		var currentPres = wp.data.select("core/editor").getEditedPostAttribute( window.slidePresentation.presentationSlug );

		// data.subscribe runs too early, so set defaults.
		if ( typeof currentPres === 'undefined' ) {
			currentPres = false;
		}

		if ( false !== currentPres && currentPres[0] != currentTermId ) {
			// Presentation was selected.
			if ( currentPres.hasOwnProperty( 0 ) ) {
				// Allow saving.
				if ( lock ) {
					lock = false;
					wp.data.dispatch('core/editor').unlockPostSaving();
				}

				currentTermId = currentPres[0];

			// No presentations selected.
			} else {
				// Disallow saving.
				if ( ! lock ) {
					lock = true;
					wp.data.dispatch('core/editor').lockPostSaving();
				}

				currentTermId = 0;
			}

			// Logic to switch the current reveal theme.
			if ( 0 === currentTermId ) {
				switchTheme( window.slidePresentation.defaultTheme );

			} else if ( presThemes.hasOwnProperty( currentTermId ) ) {
				switchTheme( presThemes[ currentTermId ] );

			// Fetch the theme for the presentation and switch it if necessary.
			} else {
				wp.apiFetch( {
					path: '/wp/v2/' + window.slidePresentation.presentationSlug + '/' + currentTermId
				} ).then( term => {
					changedTheme = term.meta._presentation_theme;
					if ( '' === changedTheme ) {
						changedTheme = window.slidePresentation.defaultTheme;
					}

					presThemes[ currentTermId ] = changedTheme;

					switchTheme( changedTheme );
				} );
			}
		}
	} );

	/* Set up our PrePublishPanel */

	function SlidePrePublishContent() {
		if ( lock ) {
			return el( 'div', {className: 'slide-prepublish-lock'},
				el( 'h3', {}, __( 'Missing presentation', 'reveal-presentations' ) ),
				el( 'p', {}, __( 'Please select a presentation under "Document > Presentations" in order to enable publishing.', 'reveal-presentations' ) )
			);
		} else {
			return el(
				'p', {
					className: 'slide-prepublish-unlock'
				}, __( "You're ready to publish!", 'reveal-presentations' )
			);
        }
	}

	function SlidePrePublish() {
	    return el( PluginPrePublishPanel, {
    	        icon: 'none',
        	    title: __( 'Presentations:', 'reveal-presentations' ),
            	initialOpen: true,
	        },
	        el( SlidePrePublishContent, {} )
	    );
	}

	registerPlugin( 'slide-prepublish', {
		render: SlidePrePublish,
	});
} );
