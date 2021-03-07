# Presentations

A WordPress plugin to create a presentation using a custom post type for slides and a custom taxonomy to hold the presentation.

## Installation

1. Clone this repo.
2. In your shell environment, navigate to the directory and run `composer install`.
3. Activate the plugin in WordPress.
4. Install and activate the [Simple Page Ordering](https://wordpress.org/plugins/simple-page-ordering) plugin to easily sort your slides via drag-n-drop in the admin dashboard.
5. Start creating a presentation!
   - Navigate to `Presentations > Presentations` to create a presentation.
   - Next, go to `Presentations > Add New Slide` to create a new slide and remember to select the presentation you created before publishing.


## Acknowledgements

This plugin utilizes much of the code from [Slides & Presentations](https://wordpress.org/plugins/slide/) by Ella van Durpe and draws inspiration from [Reveal.js Presentations](https://github.com/cgrymala/reveal-js-presentations) by Curtiss Grymala.

Presentation taxonomy fields in the backend were easily created using the [Carbon Fields](https://github.com/htmlburger/carbon-fields) library.

Presentations on the frontend is powered by [reveal.js](https://github.com/hakimel/reveal.js/).
