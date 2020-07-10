<?php
/*
 Template Name: Subscriptions
*/
?>
<?php get_header(); ?>

			<div id="content">
				<div id="inner-content" class="wrap cf">
					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
							<header class="article-header">
								<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
								<?php /*<p class="byline vcard">
									<?php printf( __( 'Posted', 'bonestheme').' <time class="updated" datetime="%1$s" itemprop="datePublished">%2$s</time> '.__( 'by',  'bonestheme').' <span class="author">%3$s</span>', get_the_time('Y-m-j'), get_the_time(get_option('date_format')), get_the_author_link( get_the_author_meta( 'ID' ) )); ?>
								</p> */ ?>
							</header> <?php // end article header ?>
							<?php $hasContentSecondary = is_active_sidebar('sidebar1'); ?>
							<section class="entry-content<?php echo $hasContentSecondary ? ' has-content-secondary':''; ?>" itemprop="articleBody">
								<div class="content-primary">
									<?php the_content(); ?>
									<div class="available-subscriptions">
									<?php $levels = get_available_subscriptions();
									foreach($levels as $key => $level) {
										echo do_shortcode('[ihc-select-level id='.$key.']');
									} ?>
									</div>
								</div>
								<?php if ($hasContentSecondary) { ?>
								<div class="content-secondary">
									<?php if (is_active_sidebar('sidebar1')) { 
										get_sidebar();
									} ?>
								</div>
								<?php } ?>
							</section> <?php // end article section ?>
							<?php comments_template(); ?>
						</article>
						<?php endwhile; endif; ?>
					</main>
				</div>
			</div>
<?php get_footer(); ?>
