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
									<?php 
									$authorID = get_the_author_meta('ID');
									$authorMeta = array();
									if (get_user_meta($authorID,'description',true)) {
										$authorMeta['bio'] = get_user_meta($authorID,'description',true);
									}
									if (get_user_meta($authorID,'tribe',true)) {
										$authorMeta['tribe'] = get_user_meta($authorID,'tribe',true);
									}
									if (get_user_meta($authorID,'tribal_affiliation',true)) {
										$authorMeta['tribal_affiliation'] = get_user_meta($authorID,'tribal_affiliation',true);
									}
									if (get_user_meta($authorID,'union',true)) {
										$authorMeta['union'] = get_user_meta($authorID,'union',true);
									}
									if (get_user_meta($authorID,'literary_rep_name',true)) {
										$authorMeta['literary_rep_name'] = get_user_meta($authorID,'literary_rep_name',true);
									}
									if (get_user_meta($authorID,'literary_rep_email',true)) {
										$authorMeta['literary_rep_email'] = get_user_meta($authorID,'literary_rep_email',true);
									}
									if (get_user_meta($authorID,'literary_rep_phone',true)) {
										$authorMeta['literary_rep_phone'] = get_user_meta($authorID,'literary_rep_phone',true);
									}
									if (get_user_meta($authorID,'literary_rep_website',true)) {
										$authorMeta['literary_rep_website'] = get_user_meta($authorID,'literary_rep_website',true);
									} ?>
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
									<?php if (!empty($authorMeta)) { ?>
										<h3>About this Writer</h3>
										<div class="about-writer info-box">
											<?php if ($authorMeta['bio']) { ?>
											<div class="bio">
												<h4>Biographical Sketch</h4>
												<div><?php echo $authorMeta['bio']; ?></div>
											</div>
											<?php } ?>
											<?php if ($authorMeta['tribe'] || $authorMeta['tribal_affiliation']) { ?>
											<div class="tribal">
												<h4>Tribal Affiliation</h4>
												<?php if ($authorMeta['tribe']) { ?>
												<div class="secondary-divider">
													<h5>Tribe</h5>
													<div><?php echo $authorMeta['tribe']; ?></div>
												</div>
												<?php } ?>
												<?php if ($authorMeta['tribal_affiliation']) { ?>
												<div class="secondary-divider">
													<h5>Tribal Affiliation Type</h5>
													<div><?php echo $authorMeta['tribal_affiliation']; ?></div>
												</div>
												<?php } ?>
											</div>
											<?php } ?>
											<?php if ($authorMeta['union']) { ?>
											<div class="union">
												<h4>Union Affiliation</h4>
												<div><?php echo $authorMeta['union']; ?></div>
											</div>
											<?php } ?>
											<?php if ($authorMeta['literary_rep_name'] || $authorMeta['literary_rep_email'] || $authorMeta['literary_rep_phone'] || $authorMeta['literary_rep_website']) { ?>
											<div class="representation">
												<h4>Literary Representation</h4>
												<?php if ($authorMeta['literary_rep_name']) { ?>
												<div class="secondary-divider">
													<h5>Literary Rep</h5>
													<div><?php echo $authorMeta['literary_rep_name']; ?></div>
												</div>
												<?php } ?>
												<?php if ($authorMeta['literary_rep_email']) { ?>
												<div class="secondary-divider">
													<h5>Email</h5>
													<div><?php echo $authorMeta['literary_rep_email']; ?></div>
												</div>
												<?php } ?>
												<?php if ($authorMeta['literary_rep_phone']) { ?>
												<div class="secondary-divider">
													<h5>Phone</h5>
													<div><?php echo $authorMeta['literary_rep_phone']; ?></div>
												</div>
												<?php } ?>
												<?php if ($authorMeta['literary_rep_website']) { ?>
												<div class="secondary-divider">
													<h5>Website</h5>
													<div><?php echo $authorMeta['literary_rep_website']; ?></div>
												</div>
												<?php } ?>
											</div>
											<?php } ?>
										</div>
									<?php } ?>
									<?php if (user_is_admin()
									|| check_subscription_level('professional')
									|| get_the_author_meta('ID') == get_current_user_id() ) { ?>
									<h3>Script</h3>
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
