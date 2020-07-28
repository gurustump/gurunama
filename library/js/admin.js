/*
 * Admin Scripts File
 * Author: Matthew Stumphy
 *
 * Just any extra javascript to run in the admin area.
*/


jQuery(document).ready(function($) {
	toggleMetaboxes($);
	$('#page_template').change(function() {
		toggleMetaboxes($);
	});
});

function toggleMetaboxes($) {
	if ($('#page_template').val() == 'page-home.php') {
		$('.cmb2-id--guru-page-home-slider').show()
	} else {
		$('.cmb2-id--guru-page-home-slider').hide()
	}
	if ($('#page_template').val() == 'page-subscriptions.php') {
		$('.cmb2-id--guru-page-secondary').show()
	} else {
		$('.cmb2-id--guru-page-secondary').hide()
	}
}