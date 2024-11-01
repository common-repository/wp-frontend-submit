<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ptheme.com/
 * @since      1.0.0
 *
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Frontend_Submit
 * @subpackage Wp_Frontend_Submit/public
 * @author     Leo <newbiesup@gmail.com>
 */
class Wp_Frontend_Submit_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Frontend_Submit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Frontend_Submit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-frontend-submit-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'dashicons' ); // enqueue Dashicons styles

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Frontend_Submit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Frontend_Submit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-frontend-submit-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ptajax', array( 
			'ajaxurl' 				=> admin_url( 'admin-ajax.php' ),
			'noImageURLError' 		=> __('Please enter a valid image URL!', 'wp-frontend-submit'),
			'ImageLimiteURLError' 	=> sprintf( __('You are only allowed to upload up to %s images!', 'wp-frontend-submit'), get_option('wfs_images_limit', '3') ),
			'noImageUploadError' 	=> __('No image is selected!', 'wp-frontend-submit'),
			'noTitleError' 			=> __('You must provide a title. What are you publishing?', 'wp-frontend-submit'),
			'noImageError' 			=> __('Please add at least one image to your post.', 'wp-frontend-submit'),
			'ImageSuccess' 			=> __('Your image has been added successfully', 'wp-frontend-submit'),
			'UserNameError' 		=> __('Please enter your Name!', 'wp-frontend-submit'),
			'UserEmailError' 		=> __('Please enter a valid Email!', 'wp-frontend-submit'),
		));

	}

	public function wfs_upload_image_from_url() {
		$data = array();

		if( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'wfs_post_upload_form' ) ){
			// process upload
			if (is_numeric($_POST['id'])) {
				// do nothing, attachment ID is ready
				$attachment_id = $_POST['id'];
			} else {
				// get attachment ID
				$attachment_id = $this->wfs_attach_image($_POST['id']);
			}

			// continue on success
			if ( is_numeric($attachment_id) ) {

				$img_full = wp_get_attachment_image_src( $attachment_id, 'full' );
				$img_thumb = wp_get_attachment_image_src( $attachment_id, 'add-post-thumb' );
				$data['status'] = true;
				$data['message'] = 
					'<li id="attachment-'.$attachment_id.'">
						<img src="'.$img_thumb[0].'" alt="" />
						<a href="#" class="remove_image" title="'.__('Remove Image','wp-frontend-submit').'" data-id="'.$attachment_id.'"><i class="dashicons dashicons-no"></i></a>
						<div class="check boxmark" tabindex="0" title="'.__('Set as featured','wp-frontend-submit').'">
							<span class="dashicons dashicons-yes"></span>
						</div>
					</li>';

			} else {

				$data['status'] = false;
				$data['message'] = __('An error has occured. Your image was not added.','wp-frontend-submit');

			}
		} else {
			$data['status'] = false;
			$data['message'] = __('An error has occured. Your image was not added.','wp-frontend-submit');
		}

		echo json_encode($data);
		die();
	}

	public function wfs_upload_image_from_local() {
		$data = array();
		$data['status'] = false;
		$data['message'] = '';
		$attachment_ids = array();

		if( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'wfs_post_upload_form' ) ){
			

			if ( empty($_FILES['files']) ) {

				$data['status'] = false;
				$data['message'] = __('Please select an image to upload!','wp-frontend-submit');

			} else {
				$files = $this->reArrayFiles($_FILES['files']);
				$i = 0;

				foreach( $files as $file ){
					if( is_array($file) ){
						$attachment_id = $this->upload_user_file( $file, false );
						
						if ( is_numeric($attachment_id) ) {

							$img_full = wp_get_attachment_image_src( $attachment_id, 'full' );
							$img_thumb = wp_get_attachment_image_src( $attachment_id, 'add-post-thumb' );
							$data['status'] = true;
							$data['message'] = 
								'<li id="attachment-'.$attachment_id.'">
									<img src="'.$img_thumb[0].'" alt="" />
									<a class="remove_image" title="'.__('Remove Image','wp-frontend-submit').'" data-id="'.$attachment_id.'"><i class="dashicons dashicons-no"></i></a>
									<div class="check boxmark" tabindex="0" title="'.__('Set as featured','wp-frontend-submit').'">
										<span class="dashicons dashicons-yes"></span>
									</div>
								</li>';

							$attachment_ids[] = $attachment_id;

						}
					}
					$i++;
				}

				if( ! $attachment_ids ){
					$data['status'] = false;
					$data['message'] = __('An error has occured. Your image was not added.','wp-frontend-submit');
				}
			}
		} else {
			$data['status'] = false;
			$data['message'] = __('An error has occured. Your image was not added.','wp-frontend-submit');
		}

		echo json_encode($data);
		die();
	}

	public function wfs_remove_uploaded_image() {
		$data = array();

		if ( isset($_POST['id']) ){
			if ( false === wp_delete_attachment( $_POST['id'] ) ) {
				$data['status'] = false;
				$data['message'] = __('Your image was not removed. Please try again.','wp-frontend-submit');
			} else {
				$data['status'] = true;
				$data['message'] = __('Your image is removed.','wp-frontend-submit');
			} 
		} else {
			$data['status'] = false;
			$data['message'] = __('Somthing\'s wrong. Please try again.','wp-frontend-submit');
		}
		echo json_encode($data);
		die();
	}

	public function wfs_upload_post() {
		$data = array();

		if( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'wfs_post_upload_form' ) ){

			// $current_user = wp_get_current_user();

			// Get clean input variables
			$title 			= wp_strip_all_tags( $_POST['wfs_post_title'] );
			$tags 			= sanitize_text_field( $_POST['wfs_post_tags'] );
			$content 		= $_POST['wfs_post_content'];
			$category 		= intval( $_POST['wfs_post_category'] );
			$imgs 			= $_POST['imgs'];
			$img_featured 	= $_POST['img_featured'];
			// $userID  		= $_POST['userID'];
			$userName  		= isset($_POST['userName']) ? wp_strip_all_tags( $_POST['userName'] ) : false;
			$userEmail  	= isset($_POST['userEmail']) ? sanitize_text_field( $_POST['userEmail'] ) : false;
			$images_limit   = absint( get_option('wfs_images_limit', '3') );

			// Validate require fields
			if ( ( ! is_user_logged_in() ) && ! $userName ) {
				$data['error'] = __('Please enter your Name!','wp-frontend-submit');
			} elseif ( ( ! is_user_logged_in() ) && ! is_email( $userEmail ) ) {
				$data['error'] = __('Please enter a valid Email!','wp-frontend-submit');
			} elseif ( ! $imgs ) {
				$data['error'] = __('Please add at least one image to your post.','wp-frontend-submit');
			// } elseif (!$img_featured) {
			// 	$data['error'] = __('You must select your primary or featured image before publishing.','wp-frontend-submit');
			} elseif (! $title) {
				$data['error'] = __('You must provide a title. What are you publishing?','wp-frontend-submit');
			} else {
				// quick tweaking of image/featured image
				$img_array = explode(',', $imgs);

				if ( count($img_array) > $images_limit) {
					$data['error'] = sprintf( __('You are only allowed to upload up to %s images!','wp-frontend-submit'), get_option('wfs_images_limit', '3') );
				} else {

					if ( (count($img_array) == 1) || empty($img_featured) ) { // 1 image or no featured image set
						$img_featured = $img_array[0];
					} else {
						$img_featured = $_POST['img_featured'];
					}

					$cats = explode(',', $category);

					$post_args = array(
						'post_title'	=> wp_strip_all_tags($title),
						'post_content'	=> wp_filter_post_kses($content),
						'tags_input'	=> $tags,
						'post_category'	=> $cats,
						'post_status'	=> get_option('wfs_post_status','pending')
					);

					/*if ( $userID ) {
						$post_args[ 'post_author' ] = $userID;
					}*/
				
					// Insert post
					$post_id = wp_insert_post( $post_args );

					if ( $post_id ) {
						
						set_post_thumbnail( $post_id, $img_featured ); // Set featured image
						update_post_meta( $post_id, 'wfs_gallery', $imgs ); // Add a custom field to store image IDs

						if ( $userName ) {
							update_post_meta( $post_id, 'wfs_userName', $userName );
						}
						if ( $userEmail ) {
							update_post_meta( $post_id, 'wfs_userEmail', $userEmail );
						}

						if ( get_post_status ( $post_id ) == 'publish' ) {
							$data['success'] = __('Your post has been published successfully.','wp-frontend-submit');
						} elseif ( get_post_status ( $post_id ) == 'pending' ) {
							$data['success'] = __('Your post has been uploaded. It will be published after being reviewed by site admin.','wp-frontend-submit');
						}

					} else {
						$data['error'] = __('Post submission failed. Please try later.','wp-frontend-submit');
					}
				}
			}

			// attach images to post

			
		} else {
			$data['error'] = __('Nonce validation failed.','wp-frontend-submit');
		}
		echo json_encode($data);
		die();
	}

	private function wfs_attach_image($file, $post_id = null, $desc = null) {
	    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	    if ( ! empty($file) ) {
	        // Download file to temp location
	        $tmp = download_url( $file );
	        // Set variables for storage
	        // fix file filename for query strings
	        preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches);
	        $file_array['name'] = basename($matches[0]);
	        $file_array['tmp_name'] = $tmp;
	        // If error storing temporarily, unlink
	        if ( is_wp_error( $tmp ) ) {
	            @unlink($file_array['tmp_name']);
	            $file_array['tmp_name'] = '';
	        }
	        // do the validation and storage stuff
	        $id = media_handle_sideload( $file_array, $post_id, $desc );
	        // If error storing permanently, unlink
	        if ( is_wp_error($id) ) {@unlink($file_array['tmp_name']);}
			return $id;
	    }
	}


	private function upload_user_file( $file = array(), $title = false ) {

		require_once ABSPATH.'wp-admin/includes/admin.php';

		$file_return = wp_handle_upload($file, array('test_form' => false));

		if(isset($file_return['error']) || isset($file_return['upload_error_handler'])){

			return false;

		}else{

			$filename = $file_return['file'];

			$attachment = array(
				'post_mime_type' => $file_return['type'],
				'post_content' => '',
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'guid' => $file_return['url']
			);

			if($title){
				$attachment['post_title'] = $title;
			}

			$attachment_id = wp_insert_attachment( $attachment, $filename );

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );

			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			if( 0 < intval( $attachment_id ) ) {
				return $attachment_id;
			}
		}

		return false;
	}


	private function reArrayFiles(&$file_post) {

	    $file_ary = array();
	    $file_count = count($file_post['name']);
	    $file_keys = array_keys($file_post);

	    for ($i=0; $i<$file_count; $i++) {
	        foreach ($file_keys as $key) {
	            $file_ary[$i][$key] = $file_post[$key][$i];
	        }
	    }

	    return $file_ary;
	}

}
