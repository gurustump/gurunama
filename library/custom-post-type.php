<?php

// Flush rewrite rules for custom post types
add_action( 'after_switch_theme', 'bones_flush_rewrite_rules' );

// Flush your rewrite rules
function bones_flush_rewrite_rules() {
	flush_rewrite_rules();
}

function screenplay_custom_type() { 
	register_post_type( 'screenplay', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		array( 'labels' => array(
			'name' => __( 'Screenplays', 'bonestheme' ),
			'singular_name' => __( 'Screenplay', 'bonestheme' ),
			'all_items' => __( 'All Screenplays', 'bonestheme' ),
			'add_new' => __( 'Add New', 'bonestheme' ),
			'add_new_item' => __( 'Add New Screenplay', 'bonestheme' ),
			'edit' => __( 'Edit', 'bonestheme' ),
			'edit_item' => __( 'Edit Screenplay', 'bonestheme' ),
			'new_item' => __( 'New Screenplay', 'bonestheme' ),
			'view_item' => __( 'View Screenplay', 'bonestheme' ),
			'search_items' => __( 'Search Screenplay', 'bonestheme' ),
			'not_found' =>  __( 'Nothing found in the Database.', 'bonestheme' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'bonestheme' ),
			'parent_item_colon' => ''
			), 
			'description' => __( 'Scripts for television and film uploaded by our users', 'bonestheme' ), /* Screenplay Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => 'dashicons-media-text',
			'rewrite'	=> array( 'slug' => 'screenplay', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'screenplay', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky')
		) /* end of options */
	); /* end of register post type */
	
}

// adding the function to the Wordpress init
add_action( 'init', 'screenplay_custom_type');
	

register_taxonomy( 'screenplay_tag', 
	array('screenplay'), 
	array('hierarchical' => false,    /* if this is false, it acts like tags */
		'labels' => array(
			'name' => __( 'Screenplay Tags', 'bonestheme' ),
			'singular_name' => __( 'Screenplay Tag', 'bonestheme' ),
			'search_items' =>  __( 'Search Screenplay Tags', 'bonestheme' ),
			'all_items' => __( 'All Screenplay Tags', 'bonestheme' ),
			'parent_item' => __( 'Parent Screenplay Tag', 'bonestheme' ),
			'parent_item_colon' => __( 'Parent Screenplay Tag:', 'bonestheme' ),
			'edit_item' => __( 'Edit Screenplay Tag', 'bonestheme' ),
			'update_item' => __( 'Update Screenplay Tag', 'bonestheme' ),
			'add_new_item' => __( 'Add New Screenplay Tag', 'bonestheme' ),
			'new_item_name' => __( 'New Screenplay Tag Name', 'bonestheme' )
		),
		'show_admin_column' => true,
		'show_ui' => true,
		'query_var' => true,
	)
);
?>
