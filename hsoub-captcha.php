<?php
/*
Plugin Name: Hsoub CAPTCHA
Plugin URI: http://web2dev.me/labs/hsoub-captcha/
Description: a simple comment CAPTCHA protection based on Hsoub CAPTCHA API (support arabic & english)
Version: 1.0
Author: web2dev (Mohamed)
Author URI: http://web2dev.me
*/

// include options page
include('options-panel.php');

// load translations
function hsoub_captcha_i18n(){
	load_plugin_textdomain( 'hsoub-captcha', false , dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

// Setup some values just after the plugin activation
function hcaptcha_activation(){

	// Set default values
	$options = array( 'hcaptcha_lang' => 'en' , 'hcaptcha_background' => 'ffffff' , 'hcaptcha_border' => 'dddddd');

	foreach($options as $option_name => $default_value ):

		if ( !get_option( $option_name ) ) update_option( $option_name , $default_value );
				
	endforeach;
}

// Test the submited captcha
function test_captcha($comment){

	// if user not logged in & the api key exist & check if the captcha field exist
	if ( !is_user_logged_in() && '' != get_option('hcaptcha_api_key')):
		// if the captcha field empty
		if ( empty( $_POST['hcaptcha_input'] ) ):

			wp_die( __('Please enter the verification code (CAPTCHA).' , 'hsoub-captcha') ) ;

		endif;

		// Check API : http://captcha.hsoub.com/developers
		$response = wp_remote_retrieve_body( wp_remote_get("http://captcha.hsoub.com/api/{$_POST['hcaptcha_language']}/verify?key={$_POST['hcaptcha_key']}&input={$_POST['hcaptcha_input']}&challenge={$_POST['hcaptcha_challenge']}") );

		if ( $response == 'false' ):

			wp_die( __('the verification code (CAPTCHA) wrong.' , 'hsoub-captcha') );

		endif;


	endif;

	return $comment;
}

// Add the captcha field into the comment form
function captcha_field( $content ){
	// if user not logged in & the api key exist
	if ( !is_user_logged_in() && '' != get_option('hcaptcha_api_key') ):
		ob_start();
	?>

	<div id="hcaptcha">
            <script type="text/javascript"><!--
                hcaptcha_options = {language: '<?php echo get_option( 'hcaptcha_lang' ) ?>', key: '<?php echo get_option( 'hcaptcha_api_key' ) ?>', background: '#<?php echo get_option( 'hcaptcha_background' ) ?>', border: '#<?php echo get_option( 'hcaptcha_border' ) ?>'}; 
            //--></script>
            <script type="text/javascript" src="https://captcha.hsoub.com/hcaptcha.js"></script> 		
	</div>  
	          
	<?php

		$captcha = ob_get_contents();
		ob_end_clean();
		//return the default form with captcha field
		return $content . $captcha;

	else:
		// return the default form
		return $content;

	endif;
}
// Register some hooks (plugin activation, init)
register_activation_hook( __FILE__ , 'hcaptcha_activation' );
add_action('init','hsoub_captcha_i18n');

// Add some filters (add captcha field, test captcha)
if (isset($_POST['hcaptcha_input'])) add_filter( 'preprocess_comment' , 'test_captcha' );
add_filter( 'comment_form_field_comment' , 'captcha_field' );
