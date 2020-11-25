<?php
/*
Author: Eddie Machado
URL: http://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, etc.
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/bones.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
require_once( 'library/admin.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {

  //Allow editor style.
  add_editor_style( get_stylesheet_directory_uri() . '/library/css/editor-style.css' );

  // let's get language support going, if you need it
  load_theme_textdomain( 'bonestheme', get_template_directory() . '/library/translation' );

  // USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
  require_once( 'library/custom-post-type.php' );

  // launching operation cleanup
  add_action( 'init', 'bones_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'bones_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'bones_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  bones_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'bones_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'bones_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 1920;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'extra-small', 200, 200 );
add_image_size( 'small', 400, 400 );
add_image_size( 'extra-large', 1920, 1920 );
add_image_size( 'movie-thumb', 400, 225, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 100 sized image,
we would use the function:
<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'bones-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'bones_custom_image_sizes' );

function bones_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'extra-large' => __('Extra Large'),
        'extra-small' => __('Extra Small'),
        'small' => __('Small'),
        'movie-thumb' => __('Movie (16:9) Thumbnail'),
    ) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* THEME CUSTOMIZE *********************/

/* 
  A good tutorial for creating your own Sections, Controls and Settings:
  http://code.tutsplus.com/series/a-guide-to-the-wordpress-theme-customizer--wp-33722
  
  Good articles on modifying the default options:
  http://natko.com/changing-default-wordpress-theme-customization-api-sections/
  http://code.tutsplus.com/tutorials/digging-into-the-theme-customizer-components--wp-27162
  
  To do:
  - Create a js for the postmessage transport method
  - Create some sanitize functions to sanitize inputs
  - Create some boilerplate Sections, Controls and Settings
*/

function bones_theme_customizer($wp_customize) {
  // $wp_customize calls go here.
  //
  // Uncomment the below lines to remove the default customize sections 

  // $wp_customize->remove_section('title_tagline');
  // $wp_customize->remove_section('colors');
  // $wp_customize->remove_section('background_image');
  // $wp_customize->remove_section('static_front_page');
  // $wp_customize->remove_section('nav');

  // Uncomment the below lines to remove the default controls
  // $wp_customize->remove_control('blogdescription');
  
  // Uncomment the following to change the default section titles
  // $wp_customize->get_section('colors')->title = __( 'Theme Colors' );
  // $wp_customize->get_section('background_image')->title = __( 'Images' );
}

add_action( 'customize_register', 'bones_theme_customizer' );

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'bonestheme' ),
		'description' => __( 'The first (primary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	/*
	to add more sidebars or widgetized areas, just copy
	and edit the above sidebar code. In order to call
	your new sidebar just use the following code:

	Just change the name to whatever your new
	sidebar's id is, for example:

	register_sidebar(array(
		'id' => 'sidebar2',
		'name' => __( 'Sidebar 2', 'bonestheme' ),
		'description' => __( 'The second (secondary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar2.php

	*/
} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function bones_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
		<article  class="cf">
			<header class="comment-author vcard">
				<?php
				/*
				  this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
				  echo get_avatar($comment,$size='32',$default='<path_to_url>' );
				*/
				?>
				<?php // custom gravatar call ?>
				<?php
				  // create variable
				  $bgauthemail = get_comment_author_email();
				?>
				<img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=40" class="load-gravatar avatar avatar-48 photo" height="40" width="40" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
				<?php // end custom gravatar call ?>
				<?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'bonestheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'bonestheme' ),'  ','') ) ?>
				<time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'bonestheme' )); ?> </a></time>

			</header>
			<?php if ($comment->comment_approved == '0') : ?>
			<div class="alert alert-info">
			  <p><?php _e( 'Your comment is awaiting moderation.', 'bonestheme' ) ?></p>
			</div>
			<?php endif; ?>
			<section class="comment_content cf">
				<?php comment_text() ?>
			</section>
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</article>
	<?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!

function bones_fonts() {
	wp_enqueue_style('googleFonts', '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic');
}
add_action('wp_enqueue_scripts', 'bones_fonts');

function echoSVG($svg) {
	$svgs = array(
		/*'btnPlay' => 				file_get_contents('images/btn-play.svg'),
		'icDonate' => 				file_get_contents('images/ic-donate.svg'),
		'icExclamation' =>			file_get_contents('images/ic-exclamation.svg'),*/
		'icFacebook' =>				file_get_contents(get_template_directory_uri().'/library/images/ic-facebook.svg'),
		'icInstagram' =>			file_get_contents(get_template_directory_uri().'/library/images/ic-instagram.svg'),
		/*'icLinkedin' =>			file_get_contents('images/ic-linkedin.svg'),
		'icMovie' =>				file_get_contents('images/ic-movie.svg'),
		'icMovieAdd' =>				file_get_contents('images/ic-movie-add.svg'),
		'icPhoto' =>				file_get_contents('images/ic-photo.svg'),*/
		'icPinterest' =>			file_get_contents(get_template_directory_uri().'/library/images/ic-pinterest.svg'),
		'icTwitter' =>				file_get_contents(get_template_directory_uri().'/library/images/ic-twitter.svg'),
		'icYoutube' =>				file_get_contents(get_template_directory_uri().'/library/images/ic-youtube.svg'),
		/*'icUpload' => 			file_get_contents('images/ic-upload.svg'),
		'icUploadSimple' =>			file_get_contents('images/ic-upload-simple.svg'),
		'icVimeo' =>				file_get_contents('images/ic-vimeo.svg'),
		'loading' =>				file_get_contents('images/loading.svg'),
		'nextArrow' => 				file_get_contents('images/next-arrow.svg'),
		'nextChevron' => 			file_get_contents('images/next-chevron.svg'),
		'icEdit' => 				file_get_contents('images/ic-edit.svg'),
		'icRotateClock' => 			file_get_contents('images/ic-rotate-clockwise.svg'),
		'icRotateCounterClock' =>	file_get_contents('images/ic-rotate-counterclockwise.svg'),
		'icSave' =>					file_get_contents('images/ic-save.svg'),
		'icCancel' =>				file_get_contents('images/ic-cancel.svg'),*/
	);
	echo $svgs[$svg];
}
// Youtube functions
function getYoutubeIDfromURL($url) {
	preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
	return $match[1];
}
function getYoutubePoster($youtubeID,$max) {
	return '<img src="https://i3.ytimg.com/vi/'.$youtubeID.'/'.($max ? 'maxresdefault' : 'hqdefault').'.jpg" />';
}
function createYoutubePlayer($youtubeID) {
	return '<div class="video-container"><iframe src="https://www.youtube.com/embed/'.$youtubeID.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
}

// Front end posting functions

// update post meta function
function __update_post_meta( $post_id, $field_name, $value = '' )
{
	if ( empty( $value ) OR ! $value ) {
		delete_post_meta( $post_id, $field_name );
	} elseif ( ! get_post_meta( $post_id, $field_name ) ) {
		add_post_meta( $post_id, $field_name, $value );
	} else {
		update_post_meta( $post_id, $field_name, $value );
	}
}
// upload user file
function upload_user_file($file = array(), $parent_ID) {
	require_once(ABSPATH. 'wp-admin/includes/admin.php'); 
	$file_return = wp_handle_upload($file, array('test_form' => false));
	if(isset($file_return['error']) || isset($file_return['upload_error_handler'])){
		return false;
	} else {
		$filename = $file_return['url'];
		$attachment = array(
			'post_mime_type' => $file_return['type'],
			'post_content' => '',
			'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_status' => 'inherit',
			'guid' => $file_return['url']
		);
		$attachment_id = wp_insert_attachment( $attachment, $filename );
		require_once(ABSPATH. 'wp-admin/includes/image.php');
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );
		update_post_meta($parent_ID,'_guru_screenplay_file', $filename);
	}
	return $file_return;
}

$screenwriter_level_limits = array(
	'writer' => 1,
	'writer_basic' => 1,
	'writer_plus' => 2,
	'writer_pro' => 4,
	'writer_pro_unlimited' => INF,
);

// check if current user is admin
function user_is_admin($userID = false) {
	$user_roles = $userID ? get_userdata($userID)->roles : wp_get_current_user()->roles;
	return in_array('administrator', $user_roles);
}
function current_user_owns_post($post) {
	return $post->post_author == get_current_user_ID();
}
function get_subscription_level_array($userID = false) {
	$userID = $userID ? $userID : get_current_user_id();
	$user_level_ID_list = get_user_meta($userID,'ihc_user_levels');
	$user_level_IDs = explode(',',$user_level_ID_list[0]);
	return $user_level_IDs;
}
	
// check Ultimate Membership Pro subscription level
function get_subscription_level($userID = false) {
	$user_level_IDs = get_subscription_level_array($userID);
	if ($user_level_IDs) {
		$user_level_names = array();
		foreach($user_level_IDs as $id) {
			$this_level = ihc_get_level_by_id($id);
			array_push($user_level_names, $this_level['name']);
		}
		return $user_level_names;
	} else {
		return false;
	}
}
function check_subscription_level($subscriptionLevelSlug) {
	$user_level_IDs = get_subscription_level_array();
	if ($user_level_IDs) {
		foreach($user_level_IDs as $id) {
			$this_level = ihc_get_level_by_id($id);
			if ($this_level['name'] == $subscriptionLevelSlug) {
				return true;
			}
		}
	} else {
		return false;
	}
}
function is_screenwriter($userID = false) {
	global $screenwriter_level_limits;
	$subscription_level = get_subscription_level($userID);
	if ($subscription_level) {
		foreach($screenwriter_level_limits as $key => $limit) {
			if (in_array($key,$subscription_level)) {
				return $key;
			}
		}
	}
	return user_is_admin($userID);
}
// check if the user's Ultimate Membership Pro subscription has not yet reached the limit for how many screenplays can be uploaded
function can_upload_screenplay($userID = false) {
	global $screenwriter_level_limits;
	$subscription_level = get_subscription_level($userID);
	if ($subscription_level) {
		$user_screenplay_upload_limit = 0;
		foreach($subscription_level as $level) {
			if ($user_screenplay_upload_limit < $screenwriter_level_limits[$level]) {
				$user_screenplay_upload_limit = $screenwriter_level_limits[$level];
			}
		}
		$screenplays = get_posts(array(
			'author'=> get_current_user_ID(),
			'posts_per_page'=>-1,
			'post_type'=>'screenplay',
		));
		$screenplay_count = count($screenplays);
		if ($screenplay_count < $user_screenplay_upload_limit) {
			return true;
		}
	}
	return user_is_admin();
}
function assemble_screenplay_table_row($screenplay) {
	$edit_page = get_page_by_path('script-edit');
	$html = '';
	if (is_screenwriter($screenplay->post_author)) {
		$html .= '<tr>';
		$html .= '<td><a href="'.get_permalink($screenplay->ID).'">'.$screenplay->post_title.'</a></td>';
		$html .= '<td class="inessential">'.get_userdata($screenplay->post_author)->display_name.'</td>';
		$html .= '<td class="inessential">'.get_post_meta($screenplay->ID,'_guru_screenplay_logline',true).'</td>';
		$html .= '<td>'.((is_screenwriter() && current_user_owns_post($screenplay)) ? '<a class="ic-edit" href="'.add_query_arg('post',$screenplay->ID,get_permalink($edit_page->ID)).'">Edit</a>' : '').'</td>';
		$html .= '<td>'.((is_screenwriter() && current_user_owns_post($screenplay)) ? '<a class="ic-delete DELETE_SCREENPLAY" data_screenplay_title="'.$screenplay->post_title.'" href="'.get_delete_post_link($screenplay->ID).'">Delete</a>' : '').'</td>';
		$html .= '</tr>';
	}
	return $html;
}
// returns an array of the IDs of the subscriptions to which the current user is not subscribed
function get_available_subscriptions() {
	$levels = get_option('ihc_levels');
	$userSubscriptions = get_subscription_level_array();
	foreach($userSubscriptions as $subscription) {
		unset($levels[$subscription]);
	}
	return $levels;
}

// SHORTCODES
// root path shortcode
function root_path_shortcode() {
	$url = home_url('/');
	return esc_url($url);
}
add_shortcode('root_path', 'root_path_shortcode');

// Video embed shortcode: adds container for variable width and alignment
function youtube_embed_shortcode($atts) {
	$a = shortcode_atts( array(
		'url' => '',
		'width' => '',
		'align' => 'alignnone'
	), $atts);
	return '<div class="video-outer-wrapper'.($a['align'] ?  ' '.$a['align'] : '').'"'.($a['width'] ?  ' style="width:'.$a['width'].'"' : '').'>'.createYoutubePlayer(getYoutubeIDfromURL($a['url'])).'</div>';
}
add_shortcode('youtube','youtube_embed_shortcode');

// Button shortcode
function button_shortcode($atts) {
	$a = shortcode_atts( array(
		'label' => '',
		'url' => '',
		'color' => '',
		'size' => '',
		'align' => 'none',
		'target' => ''
	), $atts);
	return '<a class="align'.$a['align'].' btn'.($a['color'] ? ' btn-'.$a['color'] : '').($a['size'] ? ' btn-'.$a['size'] : '').'"'.($a['target'] ? ' target="'.$a['target'].'"' : '').' href="'.$a['url'].'">'.$a['label'].'</a>';
	//return '<a class="btn" href="'.$a['url'].'">'.$a['label'].'</a>';
}
add_shortcode('button','button_shortcode');

// Display list of user's screenplays shortcode
function display_screenplay_list_shortcode($atts) {
	$a = shortcode_atts( array(
		'show_all' => 0,
	), $atts);
	$edit_page = get_page_by_path('script-edit');
	$show_all = check_subscription_level('professional') ? true : $a['show_all'];
	$screenplays = get_posts(array(
		'author'=> $show_all ? false : get_current_user_ID(),
		'posts_per_page'=>-1,
		'post_type'=>'screenplay',
	));
	if (count($screenplays) > 0) {
		$html = '<table class="screenplay-list"><tr>';
		$html .= '<th>Title</th>';
		$html .= '<th class="inessential">Author</th>';
		$html .= '<th class="inessential">Logline</th>';
		if (is_screenwriter()) {
			$html .= '<th>Edit Details</th>';
			$html .= '<th>Delete</th>';
		}
		$html .= '</tr>';
		foreach($screenplays as $screenplay) {
			if (current_user_owns_post($screenplay)) {
				$html .= assemble_screenplay_table_row($screenplay);
			}
		}
		foreach($screenplays as $screenplay) {
			if (!current_user_owns_post($screenplay)) {
				$html .= assemble_screenplay_table_row($screenplay);
			}
		}
		$html .= '</table>';
	} else {
		$html = "<p>You don't have any scripts yet.</p>";
	}
	if (can_upload_screenplay()) {
		$html .= '<div class="actions"><a href="'.get_permalink($edit_page->ID).'" class="btn-red">Add a Script</a></div>';
	} else {
		$html .= '<p><a href="'.get_permalink(get_page_by_path('my-account')).'/?ihc_ap_menu=subscription">Upgrade</a> to upload another script to nama.media.</p>';
	}
	$html .= '<pre style="display:none;">';
	$html .= 'subscription level: '.print_r(get_subscription_level(get_current_user_ID()),true)."\n";
	$html .= 'subscription level writer: '.check_subscription_level('writer')."\n";
	$html .= 'subscription level writer_basic: '.check_subscription_level('writer_basic')."\n";
	$html .= 'subscription level writer_plus: '.check_subscription_level('writer_plus')."\n";
	$html .= 'subscription level writer_pro: '.check_subscription_level('writer_pro')."\n";
	$html .= 'subscription level writer_pro_unlimited: '.check_subscription_level('writer_pro_unlimited')."\n";
	$html .= 'subscription level professional: '.check_subscription_level('professional')."\n";
	$html .= '</pre>';
	return $html;
}
add_shortcode('display_scripts', 'display_screenplay_list_shortcode');

// Display available subscriptions (subscriptions other than the ones the user already has)
function display_available_subscriptions() {
	$levels = get_option('ihc_levels');
	$userSubscriptions = get_subscription_level_array();
	foreach($userSubscriptions as $subscription) {
		unset($levels[$subscription]);
	}
	$html = '';
	if (count($levels) > 0) {
		$html .= '<h2>Available Upgrades</h2>';
		$html .= '<div class="available-subscriptions">';
		foreach($levels as $key => $level) {
			$html .= do_shortcode('[ihc-select-level id='.$key.']');
		}
		$html .= '</div>';
	}
	//return print_r($userSubscriptions,true);
	//return print_r($levels,true);
	return $html;
}
add_shortcode('available_subscriptions','display_available_subscriptions');

// filtering gallery shortcode
add_shortcode('gallery', 'gurustump_gallery_shortcode');

/**
 * The Gallery shortcode.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @param array $attr {
 *     Attributes of the gallery shortcode.
 *
 *     @type string $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
 *     @type string $orderby    The field to use when ordering the images. Default 'menu_order ID'.
 *                              Accepts any valid SQL ORDERBY statement.
 *     @type int    $id         Post ID.
 *     @type string $itemtag    HTML tag to use for each image in the gallery.
 *                              Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
 *     @type string $icontag    HTML tag to use for each image's icon.
 *                              Default 'dt', or 'div' when the theme registers HTML5 gallery support.
 *     @type string $captiontag HTML tag to use for each image's caption.
 *                              Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
 *     @type int    $columns    Number of columns of images to display. Default 3.
 *     @type string $size       Size of the images to display. Default 'thumbnail'.
 *     @type string $ids        A comma-separated list of IDs of attachments to display. Default empty.
 *     @type string $include    A comma-separated list of IDs of attachments to include. Default empty.
 *     @type string $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
 *     @type string $link       What to link each image to. Default empty (links to the attachment page).
 *                              Accepts 'file', 'none'.
 * }
 * @return string HTML content to display gallery.
 */
function gurustump_gallery_shortcode( $attr ) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	/**
	 * Filter the default gallery shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating
	 * the default gallery template.
	 *
	 * @since 2.5.0
	 *
	 * @see gallery_shortcode()
	 *
	 * @param string $output The gallery output. Default empty.
	 * @param array  $attr   Attributes of the gallery shortcode.
	 */
	$output = apply_filters( 'post_gallery', '', $attr );
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	$html5 = current_theme_supports( 'html5', 'gallery' );
	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'li'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery'));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'dl';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'dd';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'dt';

	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 */
	if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
	}

	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div class='thumb-index'>\n\t<div class='thumb-index-inner'>\n\t\t<ul id='$selector' class='thumb-index-list gallery GALLERY galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

	/**
	 * Filter the default gallery shortcode CSS styles.
	 *
	 * @since 2.5.0
	 *
	 * @param string $gallery_style Default gallery shortcode CSS styles.
	 * @param string $gallery_div   Opening HTML div container for the gallery shortcode output.
	 */
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		if ( ! empty( $link ) && 'file' === $link )
			$image_output = wp_get_attachment_link( $id, $size, false, false );
		elseif ( ! empty( $link ) && 'none' === $link )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
			$image_output = wp_get_attachment_link( $id, $size, true, false );

		$image_meta  = wp_get_attachment_metadata( $id );
		$xl_image = wp_get_attachment_image_src($id,'extra-large');

		$output .= "<{$itemtag} class='gallery-item GALLERY_ITEM'>";
		$output .= "
			$image_output
			<input class='IMG_SRC' type='hidden' value='{$xl_image[0]}' />
			<input class='IMG_WIDTH' type='hidden' value='{$xl_image[1]}' />
			<input class='IMG_HEIGHT' type='hidden' value='{$xl_image[2]}' />
			<input class='IMG_RESIZED' type='hidden' value='{$xl_image[3]}' />";
			$itemContent = "<span class='item-content'><span class='view-item'>View Image</span>";
				if ( $captiontag && trim($attachment->post_excerpt) ) {
					$itemContent .= "
						<{$captiontag} class='wp-caption-text gallery-caption'>
						" . wptexturize($attachment->post_excerpt) . "
						</{$captiontag}>";
				}
			$itemContent .= "</span>"; // end item-content
			$output .= $itemContent;
			
		$output .= "</{$itemtag}>";
		if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
			$output .= '<br style="clear: both" />';
		}
	}

	if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		$output .= "
			<br style='clear: both' />";
	}

	$output .= "
		</ul>\n\t</div>\n</div>\n";

	return $output;
} // end gurustump_gallery_shortcode

/***************************************************************************
****************************************************************************
 Site Reviews 
***************************************************************************/

add_action('admin_init', function () {
    add_post_type_support('site-review', 'custom-fields');
});
/**
 * Hides the submission form after a review has been submitted
 * Paste this in your active theme's functions.php file
 *
 * @param string $script
 * @return string
 */
add_filter('site-reviews/enqueue/public/inline-script', function ($script) {
	return $script."
	document.addEventListener('site-reviews/after/submission', function (ev) {
		if (false !== ev.detail.errors) return;
		ev.detail.form.classList.add('glsr-hide-form');
		ev.detail.form.insertAdjacentHTML('afterend', '<p>' + ev.detail.message + '</p>');
	});";
});
/**
 * Hides the submission form from registered users who have already submitted a review
 * Paste this in your active theme's functions.php file
 *
 * @param string $template
 * @return string
 */
add_filter('site-reviews/rendered/template/reviews-form', function ($template) {
	return glsr('Modules\ReviewLimits')->hasReachedLimit(array('assign_to' => get_the_ID()))
		? sprintf('<p>%s</p>', __('You have already submitted a review for this script.'))
		: $template;
});

//
// ADDING CUSTOM FIELDS TO THE SUBMISSION FORM IS NOT ACTIVELY SUPPORTED!
// However, if you would like to try and do this on your own, this should help get you started.
//

/**
 * Modifies the submission form fields
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/forms/submission-form', function ($config) {
	$config['overall_rating'] = [
		'label' => __('Overall Rating', 'your_theme_domain'),
		'options' => [
			'' => __('', 'your_theme_domain'),
			'0' => __('0', 'your_theme_domain'),
			'1' => __('1', 'your_theme_domain'),
			'2' => __('2', 'your_theme_domain'),
			'3' => __('3', 'your_theme_domain'),
			'4' => __('4', 'your_theme_domain'),
			'5' => __('5', 'your_theme_domain'),
			'6' => __('6', 'your_theme_domain'),
			'7' => __('7', 'your_theme_domain'),
			'8' => __('8', 'your_theme_domain'),
			'9' => __('9', 'your_theme_domain'),
			'10' => __('10', 'your_theme_domain'),
		],
		'required' => true,
		'type' => 'select',
	];
	$config['premise'] = [
		'label' => __('Premise', 'your_theme_domain'),
		'options' => [
			'' => __('', 'your_theme_domain'),
			'0' => __('0', 'your_theme_domain'),
			'1' => __('1', 'your_theme_domain'),
			'2' => __('2', 'your_theme_domain'),
			'3' => __('3', 'your_theme_domain'),
			'4' => __('4', 'your_theme_domain'),
			'5' => __('5', 'your_theme_domain'),
			'6' => __('6', 'your_theme_domain'),
			'7' => __('7', 'your_theme_domain'),
			'8' => __('8', 'your_theme_domain'),
			'9' => __('9', 'your_theme_domain'),
			'10' => __('10', 'your_theme_domain'),
		],
		'required' => true,
		'type' => 'select',
	];
	$config['plot'] = [
		'label' => __('Plot', 'your_theme_domain'),
		'options' => [
			'' => __('', 'your_theme_domain'),
			'0' => __('0', 'your_theme_domain'),
			'1' => __('1', 'your_theme_domain'),
			'2' => __('2', 'your_theme_domain'),
			'3' => __('3', 'your_theme_domain'),
			'4' => __('4', 'your_theme_domain'),
			'5' => __('5', 'your_theme_domain'),
			'6' => __('6', 'your_theme_domain'),
			'7' => __('7', 'your_theme_domain'),
			'8' => __('8', 'your_theme_domain'),
			'9' => __('9', 'your_theme_domain'),
			'10' => __('10', 'your_theme_domain'),
		],
		'required' => true,
		'type' => 'select',
	];
	$config['character'] = [
		'label' => __('Character', 'your_theme_domain'),
		'options' => [
			'' => __('', 'your_theme_domain'),
			'0' => __('0', 'your_theme_domain'),
			'1' => __('1', 'your_theme_domain'),
			'2' => __('2', 'your_theme_domain'),
			'3' => __('3', 'your_theme_domain'),
			'4' => __('4', 'your_theme_domain'),
			'5' => __('5', 'your_theme_domain'),
			'6' => __('6', 'your_theme_domain'),
			'7' => __('7', 'your_theme_domain'),
			'8' => __('8', 'your_theme_domain'),
			'9' => __('9', 'your_theme_domain'),
			'10' => __('10', 'your_theme_domain'),
		],
		'required' => true,
		'type' => 'select',
	];
	$config['dialogue'] = [
		'label' => __('Dialogue', 'your_theme_domain'),
		'options' => [
			'' => __('', 'your_theme_domain'),
			'0' => __('0', 'your_theme_domain'),
			'1' => __('1', 'your_theme_domain'),
			'2' => __('2', 'your_theme_domain'),
			'3' => __('3', 'your_theme_domain'),
			'4' => __('4', 'your_theme_domain'),
			'5' => __('5', 'your_theme_domain'),
			'6' => __('6', 'your_theme_domain'),
			'7' => __('7', 'your_theme_domain'),
			'8' => __('8', 'your_theme_domain'),
			'9' => __('9', 'your_theme_domain'),
			'10' => __('10', 'your_theme_domain'),
		],
		'required' => true,
		'type' => 'select',
	];
	$config['setting'] = [
		'label' => __('Setting', 'your_theme_domain'),
		'options' => [
			'' => __('', 'your_theme_domain'),
			'0' => __('0', 'your_theme_domain'),
			'1' => __('1', 'your_theme_domain'),
			'2' => __('2', 'your_theme_domain'),
			'3' => __('3', 'your_theme_domain'),
			'4' => __('4', 'your_theme_domain'),
			'5' => __('5', 'your_theme_domain'),
			'6' => __('6', 'your_theme_domain'),
			'7' => __('7', 'your_theme_domain'),
			'8' => __('8', 'your_theme_domain'),
			'9' => __('9', 'your_theme_domain'),
			'10' => __('10', 'your_theme_domain'),
		],
		'required' => true,
		'type' => 'select',
	];
	$config['era'] = [
		'label' => __('Era', 'your_theme_domain'),
		'placeholder' => __('Modern Era, Period', 'your_theme_domain'),
		'type' => 'text',
	];
	$config['locations'] = [
		'label' => __('Locations', 'your_theme_domain'),
		'placeholder' => __('', 'your_theme_domain'),
		'type' => 'text',
	];
	$config['logline'] = [
		'label' => __('Logline', 'your_theme_domain'),
		'placeholder' => __('', 'your_theme_domain'),
		'type' => 'textarea',
	];
	$config['strengths'] = [
		'label' => __('Strengths', 'your_theme_domain'),
		'placeholder' => __('', 'your_theme_domain'),
		'type' => 'textarea',
	];
	$config['weaknesses'] = [
		'label' => __('Weaknesses', 'your_theme_domain'),
		'placeholder' => __('', 'your_theme_domain'),
		'type' => 'textarea',
	];
	$config['prospects'] = [
		'label' => __('Prospects', 'your_theme_domain'),
		'placeholder' => __('', 'your_theme_domain'),
		'type' => 'textarea',
	];
	return $config;
});
 
/**
 * Customises the order of the fields used in the Site Reviews submission form.
 * @param array $order
 * @return array
 */
add_filter('site-reviews/submission-form/order', function ($order) {
	// The $order array contains the field keys returned below.
	// Simply add any custom field keys that you have added and change them to the desired order.
	return [
		'overall_rating',
		'premise',
		'plot',
		'character',
		'dialogue',
		'setting',
		'era',
		'locations',
		'logline',
		'strengths',
		'weaknesses',
	];
});
 
/**
 * Modifies the submission form field rules
 * Paste this in your active theme's functions.php file.
 * @param array $rules
 * @return array
 */
/*add_filter('site-reviews/validation/rules', function ($rules) {
    $rules['opinion'] = 'required|max:100'; // maximum of 100 characters
    $rules['personnel_rating'] = 'required';
    $rules['service_rating'] = 'required';
    return $rules;
});*/

/**
 * Perform custom validation here if needed
 * Paste this in your active theme's functions.php file.
 * @param bool $isValid
 * @param array $requestData
 * @return bool|string
 */
add_filter('site-reviews/validate/custom', function ($isValid, $requestData) {
    // return true on success
    // or return false on failure
    // or return a custom error message string on failure
    return $isValid;
}, 10, 2);

/**
 * Triggered immediately after a review is saved
 * If you are uploading files, this is where you would save them to the database and attach them to the review
 * Paste this in your active theme's functions.php file.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 */
add_action('site-reviews/review/created', function ($review, $command) {
    // You may access the global $_POST and $_FILES here.
}, 10, 2);
 
/**
 * Displays custom fields in the Review's "Details" metabox
 * Paste this in your active theme's functions.php file.
 * @param array $metabox
 * @param \GeminiLabs\SiteReviews\Review $review
 * @return array
 */
add_filter('site-reviews/metabox/details', function ($metabox, $review) {
    foreach ($review->custom as $key => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $metabox[$key] = $value;
    }
    return $metabox;
}, 10, 2);

/**
 * Set the default values of the custom fields here
 * Paste this in your active theme's functions.php file.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @return \GeminiLabs\SiteReviews\Review
 */
add_filter('site-reviews/review/build/before', function ($review) {
    $review->custom = wp_parse_args($review->custom, [
		'overall_rating' => '',
		'premise' => '',
		'plot' => '',
		'character' => '',
		'dialogue' => '',
		'setting' => '',
    ]);
    return $review;
});

/**
 * Renders the custom review fields
 * Paste this in your active theme's functions.php file.
 * In order to display the rendered custom fields, you will need to use a custom "review.php" template
 * as shown in the plugin FAQ ("How do I change the order of the review fields?")
 * and you will need to add your custom template tags to it, for example: {{ name_of_your_custom_field }}
 * @param array $templateTags
 * @param \GeminiLabs\SiteReviews\Review $review
 * @return array
 */
add_filter('site-reviews/review/build/after', function ($templateTags, $review) {
	$ratingKeys = ['personnel_rating', 'service_rating']; // change these as needed
	foreach ($review->custom as $key => $value) {
    	if (in_array($key, $ratingKeys)) {
            $value = glsr_star_rating($value); // render the stars from the rating value
        }
        if (is_array($value)) {
            $list = array_reduce($value, function ($result, $item) {
                return $result.'<li>'.$item.'</li>';
            });
            $value = '<ul>'.$list.'</ul>';
        }
        $templateTags[$key] = '<div class="glsr-custom-'.$key.'">'.$value.'</div>';
    }
    return $templateTags;
}, 10, 2);

/* DON'T DELETE THIS CLOSING TAG */ ?>
