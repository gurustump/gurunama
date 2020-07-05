/*
 * Bones Scripts File
 * Author: Eddie Machado
 *
 * This file should contain any js scripts you want to add to the site.
 * Instead of calling it in the header or throwing it inside wp_head()
 * this file will be called automatically in the footer so as not to
 * slow the page load.
 *
 * There are a lot of example functions and tools in here. If you don't
 * need any of it, just remove it. They are meant to be helpers and are
 * not required. It's your world baby, you can do whatever you want.
*/


/*
 * Get Viewport Dimensions
 * returns object with viewport dimensions to match css in width and height properties
 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
*/
function updateViewportDimensions() {
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
	return { width:x,height:y };
}
// setting the viewport width
var viewport = updateViewportDimensions();


/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
*/
var waitForFinalEvent = (function () {
	var timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) { uniqueId = "Don't call this twice without a uniqueId"; }
		if (timers[uniqueId]) { clearTimeout (timers[uniqueId]); }
		timers[uniqueId] = setTimeout(callback, ms);
	};
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;


/*
 * Here's an example so you can see how we're using the above function
 *
 * This is commented out so it won't work, but you can copy it and
 * remove the comments.
 *
 *
 *
 * If we want to only do it on a certain page, we can setup checks so we do it
 * as efficient as possible.
 *
 * if( typeof is_home === "undefined" ) var is_home = $('body').hasClass('home');
 *
 * This once checks to see if you're on the home page based on the body class
 * We can then use that check to perform actions on the home page only
 *
 * When the window is resized, we perform this function
 * $(window).resize(function () {
 *
 *    // if we're on the home page, we wait the set amount (in function above) then fire the function
 *    if( is_home ) { waitForFinalEvent( function() {
 *
 *	// update the viewport, in case the window size has changed
 *	viewport = updateViewportDimensions();
 *
 *      // if we're above or equal to 768 fire this off
 *      if( viewport.width >= 768 ) {
 *        console.log('On home page and window sized to 768 width or more.');
 *      } else {
 *        // otherwise, let's do this instead
 *        console.log('Not on home page, or window sized to less than 768.');
 *      }
 *
 *    }, timeToWaitForLast, "your-function-identifier-string"); }
 * });
 *
 * Pretty cool huh? You can create functions like this to conditionally load
 * content and other stuff dependent on the viewport.
 * Remember that mobile devices and javascript aren't the best of friends.
 * Keep it light and always make sure the larger viewports are doing the heavy lifting.
 *
*/

/*
 * We're going to swap out the gravatars.
 * In the functions.php file, you can see we're not loading the gravatar
 * images on mobile to save bandwidth. Once we hit an acceptable viewport
 * then we can swap out those images since they are located in a data attribute.
*/
function loadGravatars() {
  // set the viewport using the function above
  viewport = updateViewportDimensions();
  // if the viewport is tablet or larger, we load in the gravatars
  if (viewport.width >= 768) {
  jQuery('.comment img[data-gravatar]').each(function(){
    jQuery(this).attr('src',jQuery(this).attr('data-gravatar'));
  });
	}
} // end function


/*
 * Put all your regular jQuery in here.
*/
jQuery(document).ready(function($) {
	loadGravatars();
	var win = $(window);
	var html = $('html');
	var body = $('body');
	
	// Check what page we're on
	if (typeof isHome === "undefined") var isHome = body.hasClass('home');
	if (typeof isScreenplayEdit === "undefined") var isScreenplayEdit = body.hasClass('page-template-page-edit-screenplay');
	
	win.resize(function() {
		waitForFinalEvent( function() {
			headerHeight();
			ovCheckHeight($('.OV.active'));
			if (isHome) {
				checkSponsorSliderWidth('resize');
			}
		}, timeToWaitForLast, 'resizeWindow');
	});
	
	win.scroll(function() {
		headerHeight();
	});
	
	// Control mobile main nav
	$('.TRIGGER_NAV').click(function(e) {
		e.preventDefault();
		$(this).add('.MAIN_NAV').toggleClass('active');
		html.toggleClass('mobile-nav-active');
	});
	
	function headerHeight() {
		var scrollTrigger = 0;
		if (win.scrollTop() > scrollTrigger) {
			html.addClass('scrolled');
		} else {
			html.removeClass('scrolled');
		}
	}
	
	$('.SLIDER').slick({
		autoplay:true,
		autoplaySpeed:10000,
		prevArrow:'<a class="slick-prev"></a>',
		nextArrow:'<a class="slick-next"></a>',
	});
	
	
	// Overlay functions
	function ovOpen(ov) {
		ov.addClass('active');
		ovCheckHeight(ov);
		$('html').addClass('ov-active');
	}
	function ovClose(ov) {
		ov.removeClass('active');
		if ($('.ov.active').length < 1) {
			$('html').removeClass('ov-active');
		}
	}
	function ovCheckHeight(ov) {
		var ovChild = ov.children().first();
		if (ovChild.outerHeight() > ov.height()) {
			ov.addClass('too-tall');
		} else {
			ov.removeClass('too-tall');
		}
	}
	$('.OV_CLOSE').click(function(e) {
		e.preventDefault();
		ovClose($(this).closest('.OV'));
	});
	
	$('.DELETE_SCREENPLAY').click(function(e) {
		if (!$(this).hasClass('delete-confirmed')) {
			e.preventDefault();
			var confirmRemovalOV = $('.OV_DELETE_SCREENPLAY')
			ovOpen(confirmRemovalOV);
			confirmRemovalOV.find('.SCREENPLAY_TITLE').html($(this).attr('data_screenplay_title'));
			confirmRemovalOV.find('.CONFIRM_REMOVE_SCREENPLAY').attr('href',$(this).attr('href'));
		}
	});
	
	// Screenplay Upload/Edit Page
	if (isScreenplayEdit) {
		$('input,textarea').on('change', function(e) {
			console.log('this happened');
			$(this).parents('fieldset').find('.error').addClass('resolved');
		});
		$('#screenplayFile').on('change', function(e) {
			var fileInput = $(e.target);
			var fileLabel = fileInput.prev('label');
			var fileLabelVal = fileLabel.html();
			var fileName = '';
			if (this.files && this.files.length > 1) {
				fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
			} else if (e.target.value) {
				console.log(e.target.value);
				fileName = e.target.value.split('\\').pop();
			}
			if (fileName) {
				fileLabel.html(fileName).addClass('file-selected');
			} else {
				fileLabel.html(fileLabelVal).removeClass('file-selected');
			}
		});
		console.log('working up to the tagging part');
		var taggingOptions = {
			'no-duplicate':true,
			'no-backspace':true,
			'no-spacebar':true,
			'edit-on-delete':false
		}
		$('.TAGGING_JS').tagging(taggingOptions);
		$('.type-zone').focus(function(e) {
			$(this).parents('.TAGGING_JS').addClass('active');
		})
		$('.type-zone').blur(function(e) {
			$(this).parents('.TAGGING_JS').removeClass('active');
		})
	}
}); /* end of as page load scripts */
