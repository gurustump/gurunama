<?php
/*
 * CUSTOM POST TYPE TEMPLATE
 *
 * This is the custom post type post template. If you edit the post type name, you've got
 * to change the name of this template to reflect that name change.
 *
 * For Example, if your custom post type is "register_post_type( 'bookmarks')",
 * then your single template should be single-bookmarks.php
 *
 * Be aware that you should rename 'custom_cat' and 'custom_tag' to the appropiate custom
 * category and taxonomy slugs, or this template will not finish to load properly.
 *
 * For more info: http://codex.wordpress.org/Post_Type_Templates
*/
?>

<?php get_header(); ?>
			<div id="content">
				<div id="inner-content" class="wrap cf">
					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
							<?php $hasContentSecondary = is_active_sidebar('sidebar1'); ?>
							<section class="entry-content<?php echo $hasContentSecondary ? ' has-content-secondary':''; ?>">
								<div class="content-primary">
									<header class="article-header">
										<h1 class="single-title custom-post-type-title"><?php the_title(); ?></h1>
										<h2 class="author">by <?php the_author_meta('display_name'); ?></h2>
									</header>
									<h3>Logline</h3>
									<div class="info-box"><?php echo get_post_meta(get_the_ID(),'_guru_screenplay_logline',true); ?></div>
									<h3>Synopsis</h3>
									<div class="info-box"><?php the_content(); ?></div>
									<?php $tags = get_the_terms(get_the_ID(),'screenplay_tag');
									if ($tags) { ?>
									<h3>Genre</h3>
									<div class="info-box">
										<?php foreach($tags as $key => $tag) {
											end($tags);
											echo $tag->name.($key === key($tags) ? '':', ');
										} ?>
									</div>
									<?php } ?>
									<?php if (user_is_admin()
									|| check_subscription_level('professional')
									|| get_the_author_meta('ID') == get_current_user_id() ) { ?>
									<h3>Screenplay</h3>
									<div class="info-box">
										<?php echo do_shortcode('[pdf-embedder url="'.get_post_meta(get_the_ID(),'_guru_screenplay_file',true).'"]'); ?>
									</div>
									<?php } ?>
								</div>
								<?php if ($hasContentSecondary) { ?>
								<div class="content-secondary">
									<?php if (is_active_sidebar('sidebar1')) { 
										get_sidebar();
									} ?>
								</div>
								<?php } ?>
							</section> <!-- end article section -->
						</article>
						<?php endwhile; endif; ?>
					</main>
				</div>
			</div>
<?php get_footer(); ?>
