<?php

/**
 * Plugin Name:       Liquid Edge Login Page
 * Plugin URI:        https://liquidedge.co.nz/
 * Description:       Change logo, URL, accent color and background image of wp-login.php
 * Version:           1.02
 * Requires at least: 5.2
 * Requires PHP:      5.3
 * Author:            Brad Tipper
 * Author URI:        https://liquidedge.co.nz/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       led-login-page
 */

defined( 'ABSPATH' ) or die( 'Yikes...' );


add_action( 'admin_enqueue_scripts', 'led_add_color_picker' );
function led_add_color_picker( $hook ) {
    if( is_admin() ) { 
		wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'colorpicker-handle', plugins_url( 'js/colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
    }
}


add_action( 'admin_menu', 'led_add_admin_menu' );
add_action( 'admin_init', 'led_settings_init' );


function led_add_admin_menu(  ) { 
	add_options_page( 'Login Page', 'Login Page', 'manage_options', 'login_page', 'led_options_page' );
}


function led_settings_init(  ) { 

	register_setting( 'pluginPage', 'led_settings' );

	add_settings_section( 
		'led_pluginPage_section', 
		__( 'Customize the login page display below.', 'wordpress' ), 
		'led_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'led_login_logo', 
		__( 'Logo', 'wordpress' ), 
		'led_login_logo_render', 
		'pluginPage', 
		'led_pluginPage_section' 
	);

	add_settings_field( 
		'led_login_accent', 
		__( 'Accent Color', 'wordpress' ), 
		'led_login_accent_render', 
		'pluginPage', 
		'led_pluginPage_section' 
	);
	
	add_settings_field( 
		'led_login_color', 
		__( 'Background Color', 'wordpress' ), 
		'led_login_color_render', 
		'pluginPage', 
		'led_pluginPage_section' 
	);
	
	add_settings_field( 
		'led_login_image', 
		__( 'Background Image', 'wordpress' ), 
		'led_login_image_render', 
		'pluginPage', 
		'led_pluginPage_section' 
	);

	add_settings_field( 
		'led_login_background_contrasting_color', 
		__( 'Background Contrasting Color', 'wordpress' ), 
		'led_login_background_contrasting_color_render', 
		'pluginPage', 
		'led_pluginPage_section' 
	);

}

function led_login_logo_render(  ) { 
	$options = get_option( 'led_settings' );
	?>
	<input type='text' name='led_settings[led_login_logo]' value='<?php echo $options['led_login_logo']; ?>'>
	<?php
}

function led_login_accent_render(  ) { 
	$options = get_option( 'led_settings' );
	?>
	<input type='text' class="color-picker" name='led_settings[led_login_accent]' value='<?php echo $options['led_login_accent']; ?>'>
	<?php
}

function led_login_color_render(  ) { 
	$options = get_option( 'led_settings' );
	?>
	<input type='text' class="color-picker" name='led_settings[led_login_color]' value='<?php echo $options['led_login_color']; ?>'>
	<?php
}

function led_login_image_render(  ) { 
	$options = get_option( 'led_settings' );
	?>
	<input type='text' name='led_settings[led_login_image]' value='<?php echo $options['led_login_image']; ?>'>
	<?php
}

function led_login_background_contrasting_color_render(  ) { 
	$options = get_option( 'led_settings' );
	?>
	<input type='text' class="color-picker" name='led_settings[led_login_background_contrasting_color]' value='<?php echo $options['led_login_background_contrasting_color']; ?>'>
	This colour should contrast well with whatever you have set for the background.
	<?php
}

function led_settings_section_callback(  ) { 
	echo __( 'Note: Setting a background image will override the background color. The accent color is applied to alerts, input highlights and buttons.', 'wordpress' );
}

function led_options_page(  ) { 
		?>
		<form action='options.php' method='post'>
			<h2>Login Page</h2>
			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>
		</form>
		<?php
}

function led_login_styling() {

	$options = get_option( 'led_settings' );
	$logo = $options['led_login_logo'];
	$accent = $options['led_login_accent'];
	$color = $options['led_login_color'];
	$image = $options['led_login_image'];
	$contrasting_color = $options['led_login_background_contrasting_color'];

	$login_styles = '<style type="text/css">';

	// Add Logo
	if ( $logo != '' ) {
		$login_styles .= '
			#login h1 a, .login h1 a {
				background-image: url("'.$logo.'");
				height:100px;
				width:300px;
				background-size: contain;
				background-repeat: no-repeat;
				padding-bottom: 10px;
			}
		';
	};

	// Add background
	if ( $image != '' ) {
		$login_styles .= '
			body {
				background-image: url("'.$image.'")!important;
				background-size:cover!important;
				background-repeat:no-repeat!important;
				background-position:center!important;
			}
		';
	} elseif ( $color != '' ) {
		$login_styles .= '
			body {
				background-color: '.$color.'!important;
			}
		';
	}

	// Accent Color
	if ( $accent != '' ) {
		$login_styles .= '
			.wp-core-ui .button-primary {
				background:'.$accent.'!important;
				border-color:'.$accent.'!important;
			}
			.wp-core-ui .button-secondary {
				color:'.$accent.'!important;
			}
			.login #login_error, .login .message, .login .success {
				border-left-color:'.$accent.'!important;
			}
			input[type=checkbox]:focus, input[type=color]:focus, input[type=date]:focus, input[type=datetime-local]:focus, input[type=datetime]:focus, input[type=email]:focus, input[type=month]:focus, input[type=number]:focus, input[type=password]:focus, input[type=radio]:focus, input[type=search]:focus, input[type=tel]:focus, input[type=text]:focus, input[type=time]:focus, input[type=url]:focus, input[type=week]:focus, select:focus, textarea:focus {
				border-color:'.$accent.'!important;
				box-shadow: 0 0 0 1px '.$accent.'!important;
			}
		';
	};

	// Background contrasting color Color
	if ( $contrasting_color != '' ) {
		$login_styles .= '

			.wp-core-ui #nav a,
			.wp-core-ui #backtoblog a,
			.wp-core-ui .privacy-policy-page-link a {
				color:'.$contrasting_color.'!important;
			}

		';
	};

	$login_styles .= '</style>';

	echo $login_styles;
}
add_action( 'login_enqueue_scripts', 'led_login_styling' );

function led_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'led_login_logo_url' );
 
function led_login_logo_url_title() {
    return get_option('blogname');
}
add_filter( 'login_headertitle', 'led_login_logo_url_title' );
