<?php
/*
 Template Name: Edit Script
*/
?>
<?php 
$screenplayTitle = $_POST['screenplayTitle'];
$screenplaySynopsis = $_POST['screenplaySynopsis'];
$screenplayLogline = $_POST['screenplayLogline'];
$screenplayFile = $_POST['screenplayFile'];
$screenplayType = $_POST['screenplayType'];
$screenplayTribalAffiliation = $_POST['screenplayTribalAffiliation'];
$screenplayTribe = $_POST['screenplayTribe'];
$screenplayUnion = $_POST['screenplayUnion'];
$screenplayTags = $_POST['screenplayTag'];
$queryID = $_POST['editid'];
$submit = $_POST['submit'];

$hasUploadErrors = false;
$titleError = '';
$loglineError = '';
$synopsisError = '';
$fileError = '';
$returnMsg = '';

if ($queryID) {
	$thisPost = get_post($queryID);
}
global $user_ID;
if ($_GET['post']) {
	$page_title = 'Update Script';
	$queryID = $_GET['post'];
	$thisPost = get_post($queryID);
	$is_valid_screenplay_ID = $thisPost && $thisPost->post_type == 'screenplay';
	$new_screenplay_ID = $queryID;
}
if (isset($submit) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' )) {
	if ( ($screenplayTitle || ($queryID && ! empty(trim(get_the_title($queryID)))) )
	&& ($screenplaySynopsis || ($queryID && ! empty(trim(get_the_content($queryID)))) )
	&& ($screenplayLogline || ($queryID && ! empty(trim(get_post_meta($queryID,'_guru_screenplay_logline',true)))) )
	&& ($_FILES['screenplayFile']['size'] != 0 || ($queryID && ! empty(trim(get_post_meta($queryID,'_guru_screenplay_file',true)))) )
	&& (!$queryID || ($queryID && current_user_owns_post($thisPost)))
	) { // if there are no validation errors
		$new_screenplay = array(
			'post_title' => wp_strip_all_tags($screenplayTitle),
			'post_content' => $screenplaySynopsis,
			'post_status' => 'publish',
			'post_author' => $user_ID,
			'post_type' => 'screenplay',
			'post_category' => array(0),
		);

		if ($queryID) {
			$new_screenplay['ID'] = $queryID;
			wp_update_post($new_screenplay);
			$returnMsg = 'Your Script was Updated.';
			$new_screenplay_ID = $queryID;
		} else {
			$queryID = $new_screenplay_ID = wp_insert_post($new_screenplay);
			$thisPost = get_post($queryID);
			$page_title = 'Update Script';	
			$returnMsg = 'Your New Script was Successfully Uploaded.';
		}
		__update_post_meta($new_screenplay_ID, '_guru_screenplay_logline', $screenplayLogline);
		__update_post_meta($new_screenplay_ID, '_guru_screenplay_script_type', $screenplayType);
		__update_post_meta($new_screenplay_ID, '_guru_screenplay_tribal_affiliation', $screenplayTribalAffiliation);
		__update_post_meta($new_screenplay_ID, '_guru_screenplay_tribe', $screenplayTribe);
		__update_post_meta($new_screenplay_ID, '_guru_screenplay_union', $screenplayUnion);
		
		if (!empty($screenplayTags)) {
			wp_set_post_terms($new_screenplay_ID,$screenplayTags,'screenplay_tag');
		}
		
		if (!empty($_FILES)) {
			$file = $_FILES['screenplayFile'];
			$file_return = upload_user_file($file,$new_screenplay_ID);
		}
		
	} else { // handle errors with upload
		$hasUploadErrors = true;
		if ($queryID && !current_user_owns_post($thisPost)) {
			$pageError = 'You do not have permission to update this screenplay';
		}
		if (!$screenplayTitle && empty(trim(get_the_title($queryID))) ) {
			$titleError = 'Please enter a title for this screenplay';
		}
		if (!$screenplaySynopsis && empty(trim(get_the_content($queryID))) ) {
			$loglineError = 'Please enter a synopsis for this screenplay';
		}
		if (!$screenplayLogline && empty(trim(get_post_meta($queryID,'_guru_screenplay_logline',true))) ) {
			$synopsisError = 'Please enter a logline for this screenplay';
		}
		if (!$_FILES['screenplayFile']['size'] != 0 && empty(trim(get_post_meta($queryID,'_guru_screenplay_file',true))) ) {
			$fileError = 'Please select a PDF file of this screenplay to upload';
		}
	}
} else if (!$_GET['post']) {
	$page_title = 'Upload Script';
}
if ($new_screenplay_ID) {
	$currentScreenplayArray = array(
		'title' => get_the_title($new_screenplay_ID),
		'logline' => get_post_meta($new_screenplay_ID,'_guru_screenplay_logline',true),
		'synopsis' => get_the_content(false,false,$new_screenplay_ID),
		'file' => get_post_meta($new_screenplay_ID,'_guru_screenplay_file',true),
		'script_type' => get_post_meta($new_screenplay_ID,'_guru_screenplay_script_type',true),
		'tribal_affiliation' => get_post_meta($new_screenplay_ID,'_guru_screenplay_tribal_affiliation',true),
		'union' => get_post_meta($new_screenplay_ID,'_guru_screenplay_union',true),
	);
}
?>
<?php get_header(); ?>
			<div id="content">
				<div id="inner-content" class="wrap cf">
					<main id="main" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
							<header class="article-header">
								<h1 class="page-title"><?php echo $page_title; ?></h1>
							</header>
							<?php $hasContentSecondary = is_active_sidebar('sidebar1'); ?>
							<section class="entry-content<?php echo $hasContentSecondary ? ' has-content-secondary':''; ?>" itemprop="articleBody">
								<div class="content-primary">
									<?php the_content(); ?>
									<?php /*
									<pre>$queryID: 
									<?php echo $queryID; ?>
									</pre>
									<pre>$thisPost: 
									<?php print_r($thisPost); ?>
									</pre>
									<pre>$userID: 
									<?php echo $user_ID; ?>
									</pre>
									<pre>Is the boolean working? 
									<?php echo ($queryID && current_user_owns_post($thisPost)) ? 'yes':'no'; ?>
									</pre>
									*/ ?>
									<?php if ((!$queryID && can_upload_screenplay()) || ($queryID && current_user_owns_post($thisPost))) { ?>
									<form action="" enctype="multipart/form-data" id="screenplayUploadForm" method="POST">
										<?php if ($hasUploadErrors) { ?>
										<p class="error">There was a problem with your screenplay submission<?php echo $pageError ? $pageError : ''; ?></p>
										<?php } ?>
										<?php if ($returnMsg) { ?>
										<p class="notice"><?php echo $returnMsg; ?></p>
										<?php } ?>
										<fieldset>
											<label for="screenplayTitle" class="required">Script Title</label>
											<?php if ($hasUploadErrors && $titleError) { 
												echo '<div class="error">'.$titleError.'</div>';
											} ?>
											<input type="text" name="screenplayTitle" id="screenplayTitle" required value="<?php echo $currentScreenplayArray['title']; ?>" />
										</fieldset>
										<fieldset class="logline-container">
											<label for="screenplayLogline" class="required">Logline</label>
											<?php if ($hasUploadErrors && $loglineError) { 
												echo '<div class="error">'.$loglineError.'</div>';
											} ?>
											<textarea name="screenplayLogline" id="screenplayLogline" required><?php echo $currentScreenplayArray['logline']; ?></textarea>
										</fieldset>
										<fieldset>
											<label for="screenplaySynopsis" class="required">Synopsis</label>
											<?php if ($hasUploadErrors && $synopsisError) { 
												echo '<div class="error">'.$synopsisError.'</div>';
											} ?>
											<textarea name="screenplaySynopsis" id="screenplaySynopsis" required><?php echo $currentScreenplayArray['synopsis']; ?></textarea>
										</fieldset>
										<fieldset>
											<label for="screenplayType" class="required">Type</label>
											<select name="screenplayType" id="screenplayType" required>
												<?php foreach(script_type_options() as $key => $option) {
													echo '<option value="'.$key.'"'.($key == $currentScreenplayArray['script_type'] ? ' selected':'').'>'.$option.'</option>';
												} ?>
											</select>
										</fieldset>
										<fieldset>
											<label>Genre</label>
											<div class="field tagging-js TAGGING_JS" data-tags-input-name="screenplayTag"><?php
												$tags = get_the_terms($queryID,'screenplay_tag');
												if ($tags) {
													foreach($tags as $key => $tag) {
														end($tags);
														echo $tag->name.($key === key($tags) ? '':', ');
													}
												}
											?></div>
										</fieldset>
										<fieldset class="upload-file-container">
											<?php if ($queryID && ! empty(trim(get_post_meta($queryID,'_guru_screenplay_file',true)))) { ?>
											<p class="note">Click file name to replace:</p>
											<?php } ?>
											<?php if ($hasUploadErrors && $fileError) { 
												echo '<p class="error">'.$fileError.'</p>';
											} ?>
											<label class="btn-blue btn-upload<?php echo ($queryID && ! empty(trim(get_post_meta($queryID,'_guru_screenplay_file',true))) ) ? ' file-selected' : '';?>" for="screenplayFile"><?php echo ($queryID && $currentScreenplayArray['file']) ? basename($currentScreenplayArray['file']) : 'Select a Script to Upload';?></label>
											<input type="file" name="screenplayFile" id="screenplayFile"<?php echo ($queryID && ! empty(trim(get_post_meta($queryID,'_guru_screenplay_file',true)))) ? '' : ' required';?> accept=".pdf"  />
										</fieldset>
										<fieldset class="submit-container">
											<?php if ($queryID && current_user_owns_post(get_post($queryID))) { ?>
												<input type="hidden" name="editid" id="editid" value="<?php echo $queryID; ?>" />
											<?php } ?>
											<input type="hidden" name="submit" id="submit" value="true" />
											<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
											<button class="btn-red" type="submit"><?php echo $queryID ? 'Update' : 'Add'; ?> Script</button>
										</fieldset>
									</form>
									<?php } else if ($queryID && !current_user_owns_post($thisPost)) { ?>
									<p>You do not have permission to edit this content.</p>
									<?php } else { ?>
									<p>Want to upload a screenplay? Please <?php echo is_user_logged_in() ? '<a href="'.get_permalink(get_page_by_path('my-account')).'/?ihc_ap_menu=subscription">upgrade</a>' : '<a href="'.get_permalink(get_page_by_path('member-login')).'">login to</a>'; ?> your account.
									<?php } ?>
								</div>
								<?php if ($hasContentSecondary) { ?>
								<div class="content-secondary">
									<?php if (is_active_sidebar('sidebar1')) { 
										get_sidebar();
									} ?>
								</div>
								<?php } ?>
							</section>
						</article>
						<?php endwhile; endif; ?>
					</main>
				</div>
			</div>
<?php get_footer(); ?>
