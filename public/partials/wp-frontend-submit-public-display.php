<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://ptheme.com/
 * @since      1.0.0
 *
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/public/partials
 */

/*This file should primarily consist of HTML with a little bit of PHP.*/

function wfs_shortcode() {
	return wfs_html_tags();
}
add_shortcode( 'wp_frontend_submit', 'wfs_shortcode' );

function wfs_html_tags(){ ?>

	<div class="wfs-wrapper">

		<?php if ( ( get_option( 'wfs_guest_post' ) == '0' ) && ! is_user_logged_in() ) { // If current user is not logged in but logging in is required ?>
			<p class="upload-result">
				<span class="alert alert-warning"><?php printf( __( 'Please <a href="%s">login</a> to submit a post.', 'wp-frontend-submit' ), wp_login_url( get_permalink() ) );?></span>
			</p>
		<?php } else { ?>

			<form id="wfsposts_form" class="wfsposts_form" action="<?php echo home_url('/') ?>" method="POST" enctype="multipart/form-data">

				<?php if ( is_user_logged_in() ) {
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID; ?>
					<input type="hidden" name="wfs_userID" id="wfs_userID" value="<?php echo $user_id;?>" />
				<?php } else { ?>
					<p class="upload-input">
					<label>Your Name</label>
					<input class="form-control" name="wfs_userName" id="wfs_userName" type="text" value="<?php echo isset($_POST['wfs_userName']) ? $_POST['wfs_userName'] : '' ?>"/>
					<span class="wfs-desc"><?php _e( 'Please enter your name.', 'wp-frontend-submit');?></span>
				</p>

				<p class="upload-input">
					<label>Your Email</label>
					<input class="form-control" name="wfs_userEmail" id="wfs_userEmail" type="email" value="<?php echo isset($_POST['wfs_userEmail']) ? $_POST['wfs_userEmail'] : '' ?> "/>
					<span class="wfs-desc"><?php _e( 'Please enter your email address that can be reached.', 'wp-frontend-submit');?></span>
				</p>
				<?php }?>
				
				<p class="upload-input">
					<label>Post Title</label>
					<input class="form-control" name="wfs_post_title" id="wfs_post_title" type="text" value="<?php echo isset($_POST['wfs_post_title']) ? $_POST['wfs_post_title'] : '' ?>"/>
					<span class="wfs-desc"><?php _e( 'Enter a good title that describes what you are publishing. Be creative!', 'wp-frontend-submit');?></span>
				</p>

				<p class="upload-input">
					<label>Post Tags</label>
					<input class="form-control" name="wfs_post_tags" id="wfs_post_tags" type="text" value="<?php echo isset($_POST['wfs_post_tags']) ? $_POST['wfs_post_tags'] : '' ?> "/>
					<span class="wfs-desc"><?php _e( 'Enter a comma seperated list of tags that perfectly describe your post. e.g. funny, man, public', 'wp-frontend-submit');?></span>
				</p>

				<p class="upload-input">
					<label>Post Source</label>
					<input class="form-control" name="wfs_post_source" id="wfs_post_source" type="text" value="<?php echo isset($_POST['wfs_post_source']) ? $_POST['wfs_post_source'] : '' ?> "/>
					<span class="wfs-desc"><?php _e( 'Content copyright, source, or credits. You can use hyperlinks here.', 'wp-frontend-submit');?></span>
				</p>

				<p class="upload-input">
					<label>Post Category</label>
					<?php 
					wp_dropdown_categories(array(
						'orderby'            => 'NAME', 
						'hide_empty'         => 0, 
						'selected'           => (isset($_POST['wfs_post_category']) ? $_POST['wfs_post_category'] : ''),
						'hierarchical'       => 1, 
						'name'               => 'wfs_post_category',
						'id'                 => 'wfs_post_category',
						'class'              => 'form-control',
						'hide_if_empty'      => false,
					));?>
				</p>

				<div class="upload-content">
					<label>Post Content</label>
					<?php wp_editor( (isset($_POST['wfs_post_content']) ? stripslashes($_POST['wfs_post_content']) : '') , 'wfs_post_content', 
						array(
							'media_buttons' => false,
							'teeny' => true,
							'textarea_rows' => get_option('default_post_edit_rows', 5),
						)
					) ?>
				</div>

				<div class="upload-images" data-limit="<?php echo get_option('wfs_images_limit', '3');?>">
					<label>Add Images</label>
					<a href="#" class="btn btn-default img_add_file"><?php _e('Upload Image','wp-frontend-submit'); ?></a>
					<a href="#" class="btn btn-default img_add_url"><?php _e('Add Image URL','wp-frontend-submit'); ?></a>

					<p class="upload-image">
						<input type="file" name="wfs_post_files" id="wfs_post_files" accept="image/*" />
						<a href="#" class="btn btn-default img_add_upload" type="submit"><?php _e('Upload','wp-frontend-submit'); ?></a>
						<?php wp_nonce_field( 'wfs_post_upload_form', 'wfs_post_upload_local_nonce' ); ?>
					</p>

					<p class="upload-url">
						<input type="text" name="img_url" id="img_url" placeholder="<?php _e('Enter URL of image here...','wp-frontend-submit'); ?>" />
						<a href="#" class="btn btn-default img_add"><?php _e('Upload','wp-frontend-submit'); ?></a>
						<?php wp_nonce_field( 'wfs_post_upload_form', 'wfs_post_upload_url_nonce' ); ?>
					</p>
					
				</div>

				<ol class="upload-images-lib images-preview">
					<li></li>
				</ol>

				<div class="wfs_post_submit">
					<a class="btn btn-info btn-lg" type="submit">
						<?php _e('Submit Post', 'bluthemes'); ?>
					</a>
				</div>
				
				<?php wp_nonce_field('wfs_post_upload_form', 'wfs_post_upload_form_nonce'); ?>
			</form>

			<p class="upload-result">
				<!-- result of upload goes here -->
			</p>
		<?php } ?>	
	</div>
	
<?php }