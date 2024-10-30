<?php

// Menu creation callback
add_action('admin_menu' , 'hcaptcha_create_menu');
// Admin init callback
add_action( 'admin_init' , 'hsoub_captcha_init' );

function hsoub_captcha_init() {
    /* Register color picker script */
    wp_register_script( 'color-picker' , plugins_url( 'color-picker/js/colorpicker.js' , __FILE__ ) );  
    /* Register color picker style */
    wp_register_style( 'color-picker' , plugins_url( 'color-picker/css/colorpicker.css' , __FILE__ ) );
}

function hcaptcha_create_menu() {

	//Top-level menu
	$page = add_menu_page(__('Hsoub CAPTCHA settings' , 'hsoub-captcha') ,__('Hsoub CAPTCHA' , 'hsoub-captcha') , 'administrator' , __FILE__ , 'hcaptcha_settings' , plugins_url( 'images/icon.png' , __FILE__ ) );
    // Call required styles & scripts
    add_action('admin_print_styles-' . $page, 'hsoub_captcha_required_files');
	//Register settings
	add_action( 'admin_init' , 'register_hcaptcha_settings' );
}

function hsoub_captcha_required_files() {
    // Include required styles & scripts into head tag
    wp_enqueue_script( 'color-picker' );
    wp_enqueue_style( 'color-picker' );
}


function register_hcaptcha_settings() {
	//register our settings
	register_setting( 'hcaptcha-settings-group' , 'hcaptcha_api_key' );
	register_setting( 'hcaptcha-settings-group' , 'hcaptcha_lang' );
	register_setting( 'hcaptcha-settings-group' , 'hcaptcha_background' );
	register_setting( 'hcaptcha-settings-group' , 'hcaptcha_border' );
}

function hcaptcha_settings() {
?>
<div class="wrap">
<h2><?php _e("Hsoub CAPTCHA settings" , 'hsoub-captcha') ?></h2>
<style type="text/css">
.field {min-width: 300px;padding: 5px;text-transform: uppercase;}
.link {text-decoration: none;color: #32A2D9;}
</style>
<form method="post" action="options.php">

    <?php settings_fields( 'hcaptcha-settings-group' ) ?>
    <?php do_settings_sections( 'hcaptcha_settings' ) ?>

    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e("API key" , 'hsoub-captcha') ?></th>
        <td>
        	<input class="field" type="text" name="hcaptcha_api_key" value="<?php echo get_option('hcaptcha_api_key'); ?>" />
        	<br />
        	<a class="link" target="_blank" href="http://captcha.hsoub.com/signup"><?php _e("Get your API key" , 'hsoub-captcha') ?></a>
        </td>
        </tr>
         
        <tr valign="top">
        <th scope="row"><?php _e("Primary language" , 'hsoub-captcha') ?></th>
        <td>
        	<select class="field" name="hcaptcha_lang">
                <?php
                // Available captcha languages
                    $langs = array(__('Arabic' , 'hsoub-captcha') => 'ar',
                                   __('English' , 'hsoub-captcha') => 'en'
                            );
                ?>
                <?php foreach( $langs as $lang_name => $lang_key ): ?>
                <option value="<?php echo $lang_key ?>" <?php echo ( get_option( 'hcaptcha_lang' ) == $lang_key ) ? 'selected' : '' ?>><?php echo $lang_name ?></option>
                <?php endforeach; ?>
        	</select></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e("Background color" , 'hsoub-captcha') ?></th>
        <td>
            <input class="field" maxlength="6" type="text" id="hcaptcha_background" name="hcaptcha_background" value="<?php echo get_option('hcaptcha_background'); ?>" />
            <br /><small><?php _e("HEX (without #)" , 'hsoub-captcha') ?></small>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e("Border color" , 'hsoub-captcha') ?></th>
        <td>
            <input class="field" maxlength="6" type="text" id="hcaptcha_border" name="hcaptcha_border" value="<?php echo get_option('hcaptcha_border'); ?>" />
            <br /><small><?php _e("HEX (without #)" , 'hsoub-captcha') ?></small>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e("Demo" , 'hsoub-captcha') ?></th>
        <td>
            <script type="text/javascript"><!--
                hcaptcha_options = {language: '<?php echo get_option('hcaptcha_lang'); ?>', key: '<?php echo get_option('hcaptcha_api_key'); ?>', background: '#<?php echo get_option('hcaptcha_background'); ?>', border: '#<?php echo get_option('hcaptcha_border'); ?>'}; 
            //--></script>
            <script type="text/javascript" src="https://captcha.hsoub.com/hcaptcha.js"></script>           
        </td>
        </tr>

        <script type="text/javascript">
            jQuery(document).ready(function(){

                jQuery('#hcaptcha_border, #hcaptcha_background').focus( function(){

                    var self = jQuery( this );

                    self.ColorPicker( {

                        color: '#' + self.val(),

                        onSubmit: function(hsb , hex , rgb , el) {
                            self.val( hex );
                            jQuery( el ).ColorPickerHide();
                        },

                        onBeforeShow: function () {
                            jQuery( this ).ColorPickerSetColor( this.value );
                        },

                        onChange: function (hsb , hex , rgb , el) {
                            self.val( hex );
                        }
                                              
                    } )

                    .bind( 'keyup' , function(){
                        jQuery( this ).ColorPickerSetColor( this.value );
                    } );                    

                } ); 
                              
            } );
        </script>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e("Save settings" , 'hsoub-captcha') ?>" />
    </p>

</form>
</div>
<?php } ?>