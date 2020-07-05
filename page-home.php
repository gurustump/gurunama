<?php
/*
 Template Name: Home Page
*/
?>

<?php get_header(); ?>
			<div id="content">
				<div id="inner-content" class="wrap cf">
					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
							<?php $hasContentSecondary = is_active_sidebar('sidebar1'); ?>
							<section class="entry-content<?php echo $hasContentSecondary ? ' has-content-secondary':''; ?>" itemprop="articleBody">
								<div class="home-slider page-slider slider SLIDER">
									<div class="slide slide-smiling-man">
										<img src="<?php echo get_template_directory_uri(); ?>/library/images/home-slider/bw-smiling-man.jpg">
										<div class="slide-content">
											<p>Protecting the Authentic Voice of Native Americans in Film, TV and New Media</p>
										</div>
									</div>
									<div class="slide slide-woman">
										<img src="<?php echo get_template_directory_uri(); ?>/library/images/home-slider/bw-woman.jpg">
										<div class="slide-content">
											<p>Providing genuine Native American talent to the entertainment industry</p>
										</div>
									</div>
									<div class="slide slide-man">
										<img src="<?php echo get_template_directory_uri(); ?>/library/images/home-slider/bw-ian.jpg">
										<div class="slide-content">
											<p>Working with the Motion Picture Association to offer Native American representation to Studios and Networks</p>
											<img src="<?php echo get_template_directory_uri(); ?>/library/images/home-slider/mpa-logo.png" alt="MPA">
										</div>
									</div>
								</div>
								<div class="content-primary">
								<?php the_content(); ?>
								</div>
								<?php /* if ($hasContentSecondary) { ?>
								<div class="content-secondary">
									<?php if (is_active_sidebar('sidebar1')) { 
										get_sidebar();
									} ?>
								</div>
								<?php } */ ?>
							</section>
						</article>
						<?php endwhile; endif; ?>
					</main>
				</div>
			</div>
<?php get_footer(); ?>
