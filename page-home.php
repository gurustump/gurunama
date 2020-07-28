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
									<?php $slides = get_post_meta(get_the_ID(),'_guru_page_home_slider',true);
									foreach($slides as $slide) { ?>
									<div class="slide">
										<?php $link_array = $slide['url'];
										$imgSrcMeta = wp_get_attachment_image_src($slide['image_id'], 'extra-large');
										/*echo '<pre>';
										print_r($slide);
										echo '</pre>';*/
										$imgSrc = $imgSrcMeta[0];
										echo $link_array ? '<a href="'.$link_array['url'].'"'.($link_array['target'] ? ' target="_blank"':'').'>' : ''; ?>
											<img class="bg" src="<?php echo $imgSrc; ?>">
											<span class="slide-content"<?php echo ($slide['text_width'] || $slide['text_left'] || $slide['text_size']) ? ' style="'.($slide['text_width'] ? 'width:'.$slide['text_width'].'%;':'').($slide['text_left'] ? 'left:'.$slide['text_left'].'%;':'').($slide['text_size'] ? 'font-size:.'.$slide['text_size'].'em;':'').'"':''; ?>>
												<?php echo wpautop($slide['text']); ?>
											</span>
										<?php echo $link_array ? '</a>' : ''; ?>
									</div>
									<?php } ?>
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
