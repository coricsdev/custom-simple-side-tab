<?php
/*
Plugin Name: Custom Simple Side Tab
Plugin URI: #
Description: Display a side tab that you can easily link to any page. Customize the tab text, font and colors. It's that simple. That's Simple Side Tab.
Version: 1.2.1
Author: coricsdev
Author URI: #
License: GPLv2
*/



// Hook will fire upon activation - we are using it to set default option values
register_activation_hook( __FILE__, 'rum_sst_activate_plugin' );




// Add options and populate default values on first load
function rum_sst_activate_plugin() {

	// populate plugin options array
	$rum_sst_plugin_options = array(
		'text_for_tab'     => 'SIMPLE SIDE TAB',
		'font_family'      => 'Tahoma, sans-serif',
		'font_weight_bold' => '1',
		'text_shadow'      => '0',
		'tab_url'          => 'http://rumspeed.com',
		'pixels_from_top'  => '350',
		'text_color'       => '#FFFFFF',
		'tab_color'        => '#A0244E',
		'hover_color'      => '#A4A4A4',
		'target_blank'     => '0',
		'left_right'	   => 'left',
		'text_for_tab2'     => 'SIMPLE SIDE TAB',
		'font_family2'      => 'Tahoma, sans-serif',
		'font_weight_bold2' => '1',
		'text_shadow2'      => '0',
		'tab_url2'          => 'http://rumspeed.com',
		'pixels_from_top2'  => '350',
		'text_color2'       => '#FFFFFF',
		'tab_color2'        => '#A0244E',
		'hover_color2'      => '#A4A4A4',
		'target_blank2'     => '0',
		'left_right2'	   => 'left'
		);

	// create field in WP_options to store all plugin data in one field
	add_option( 'rum_sst_plugin_options', $rum_sst_plugin_options );

}




// Fire off hooks in the admin
function rum_sst_admin_settings() {

	if ( is_admin() ){ // admin actions and filters

		// Hook for adding admin menu
		add_action( 'admin_menu', 'rum_sst_admin_menu' );

		// Hook for registering plugin option settings
		add_action( 'admin_init', 'rum_sst_settings_api_init');

		// Hook to fire farbtastic includes for using built in WordPress color picker functionality
		add_action('admin_enqueue_scripts', 'rum_sst_farbtastic_script');

		// Display the 'Settings' link in the plugin row on the installed plugins list page
		add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'rum_sst_admin_plugin_actions', -10);

	}
}
add_action( 'init', 'rum_sst_admin_settings' );




// non-admin enqueues, actions, and filters (public display of the tab)
function rum_sst_display_tab() {

	if ( is_admin() ) { // return without running if we are in the admin
		return;
	}


	// set the value for the tab display to true
	// this can only be changed by the 'rum_sst_plugin_display_tab' filter)
	$rum_sst_display_tab = true;


	// apply filter for the display of the tab so it can be turned on and off conditionally
	$rum_sst_display_tab = apply_filters( 'rum_sst_plugin_display_tab', $rum_sst_display_tab );


	// do not display the tab if the value has been turned off
	if ( $rum_sst_display_tab == false ) {
		return;
	}


	// get the current page url
	$rum_current_page_url 			= rum_get_full_url();


	// get the tab url from the plugin option variable array
	$rum_sst_plugin_option_array	= get_option( 'rum_sst_plugin_options' );
	$rum_sst_tab_url				= $rum_sst_plugin_option_array[ 'tab_url' ];


	// compare the page url and the option tab - don't render the tab if the values are the same
	if ( $rum_sst_tab_url != $rum_current_page_url ) {

		// hook to get option values and dynamically render css to support the tab classes
		add_action( 'wp_head', 'rum_sst_custom_css_hook' );

		// hook to get option values and write the div for the Simple Side Tab to display
		add_action( 'wp_footer', 'rum_sst_body_tag_html' );
	}
}
add_action( 'wp', 'rum_sst_display_tab' );




// get the complete url for the current page
function rum_get_full_url() {

	// wrap contents within isset(); these variables are not available when using WP-CLI
	// GitHub issue: https://github.com/rumspeed/simple-side-tab/issues/10
	// WP Repo support: https://wordpress.org/support/topic/php-notices-undefined-index-server_port-and-server_name?replies=1#post-7623551
	if(isset($_SERVER["SERVER_NAME"])) {
		$s 			= empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$sp 		= strtolower($_SERVER["SERVER_PROTOCOL"]);
		$protocol 	= substr($sp, 0, strpos($sp, "/")) . $s;

		return $protocol . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}
}




// Include WordPress color picker functionality
function rum_sst_farbtastic_script($hook) {

	// only enqueue farbtastic on the plugin settings page
	if( $hook != 'settings_page_rum_simple_side_tab' ) 
		return;


	// load the style and script for farbtastic
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );
}




// action function to get option values and write the div for the Simple Side Tab to display
function rum_sst_body_tag_html() {

	// get plugin option array and store in a variable
	$rum_sst_plugin_option_array	= get_option( 'rum_sst_plugin_options' );

	// fetch individual values from the plugin option variable array
	$rum_sst_text_for_tab			= $rum_sst_plugin_option_array[ 'text_for_tab' ];
	$rum_sst_tab_url				= $rum_sst_plugin_option_array[ 'tab_url' ];
	$rum_sst_text_for_tab_2			= $rum_sst_plugin_option_array[ 'text_for_tab2' ];
	$rum_sst_tab_url_2				= $rum_sst_plugin_option_array[ 'tab_url2' ];


	// sanatize the output string
	$rum_sst_text_for_tab = esc_html( $rum_sst_text_for_tab );
	$rum_sst_text_for_tab_2 = esc_html( $rum_sst_text_for_tab_2);


	// apply filters for the tab text
	$rum_sst_text_for_tab = apply_filters( 'rum_sst_plugin_text_for_tab', $rum_sst_text_for_tab );
	$rum_sst_text_for_tab_2 = apply_filters( 'rum_sst_plugin_text_for_tab_2', $rum_sst_text_for_tab_2 );


	// this field was added after the initial release so it may not be set
	if ( isset($rum_sst_plugin_option_array[ 'target_blank' ] ) ) {
		$rum_sst_target_blank			= $rum_sst_plugin_option_array[ 'target_blank' ];
	} else {
		$rum_sst_target_blank			= '0';
	}

	if ( isset($rum_sst_plugin_option_array[ 'target_blank2' ] ) ) {
		$rum_sst_target_blank_2			= $rum_sst_plugin_option_array[ 'target_blank2' ];
	} else {
		$rum_sst_target_blank_2			= '0';
	}
	// this field was added after the initial release so it may not be set
	if ( isset($rum_sst_plugin_option_array[ 'left_right' ] ) ) {
		$rum_sst_left_right			= $rum_sst_plugin_option_array[ 'left_right' ];
	} else {
		$rum_sst_left_right			= 'left';
	}
	if ( isset($rum_sst_plugin_option_array[ 'left_right2' ] ) ) {
		$rum_sst_left_right_2		= $rum_sst_plugin_option_array[ 'left_right2' ];
	} else {
		$rum_sst_left_right_2			= 'left';
	}
	// set the page target
	if ($rum_sst_target_blank == '1') {
		$rum_sst_target_blank = ' target="_blank"';
	} else {
		$rum_sst_target_blank = '';
	}
	if ($rum_sst_target_blank_2 == '1') {
		$rum_sst_target_blank_2 = ' target="_blank"';
	} else {
		$rum_sst_target_blank_2 = '';
	}
	// set side of page for tab
	if ($rum_sst_left_right == 'right') {
		$rum_sst_left_right_location = 'rum_sst_right';
	} else {
		$rum_sst_left_right_location = 'rum_sst_left';
	}
	if ($rum_sst_left_right_2 == 'right') {
		$rum_sst_left_right_location_2 = 'rum_sst_right';
	} else {
		$rum_sst_left_right_location_2 = 'rum_sst_left';
	}
	
	

	if(preg_match('/(?i)msie [7-8]/',$_SERVER['HTTP_USER_AGENT'])) {

	    // if IE 7 or 8
	    // Write HTML to render tab
		echo '<a href="' . esc_url( $rum_sst_tab_url ) . '"' . $rum_sst_target_blank . '><div id="rum_sst_tab" class="rum_sst_contents less-ie-9 ' . $rum_sst_left_right_location . '">' . $rum_sst_text_for_tab . '</div></a>';
		echo '<a href="' . esc_url( $rum_sst_tab_url_2 ) . '"' . $rum_sst_target_blank_2 . ' id="rum_sst_tab_2" class="rum_sst_contents_2 ' . $rum_sst_left_right_location_2 . '">' . $rum_sst_text_for_tab_2 . '</a>';
	} else {

	   // if IE>8
	   // Write HTML to render tab
	   echo '<a href="' . esc_url( $rum_sst_tab_url ) . '"' . $rum_sst_target_blank . ' id="rum_sst_tab" class="rum_sst_contents ' . $rum_sst_left_right_location . '">' . $rum_sst_text_for_tab . '</a>';
	   echo '<a href="' . esc_url( $rum_sst_tab_url_2 ) . '"' . $rum_sst_target_blank_2 . ' id="rum_sst_tab_2" class="rum_sst_contents_2 ' . $rum_sst_left_right_location_2 . '">' . $rum_sst_text_for_tab_2 . '</a>';
	}

}




// action function to add a new submenu under Settings
function rum_sst_admin_menu() {

	// Add a new submenu under Settings
	add_options_page( 'Simple Side Tab Option Settings', 'Simple Side Tab', 'manage_options', 'rum_simple_side_tab', 'rum_sst_options_page' );
}




// Display and fill the form fields for the plugin admin page
function rum_sst_options_page() {


?>

	<div class="wrap">
	<?php screen_icon( 'plugins' ); ?>
	<h2>Simple Side Tab</h2>
	<p>Simple Side Tab was created to give you an easy option for adding a linkable tab to the side of your WordPress website. Use it to link to your contact/support/feedback page or use it to drive traffic to a new product you just released. It's quick and easy. <em>NOTE: This plugin requires the WP_footer() hook to be fired from your theme.</em></p>
	<form method="post" action="options.php">


<?php

	settings_fields( 'rum_sst_option_group' );
	do_settings_sections( 'rum_simple_side_tab' );

	// get plugin option array and store in a variable
	$rum_sst_plugin_option_array	= get_option( 'rum_sst_plugin_options' );

	// fetch individual values from the plugin option variable array
	$rum_sst_text_for_tab			= $rum_sst_plugin_option_array[ 'text_for_tab' ];
	$rum_sst_font_family			= $rum_sst_plugin_option_array[ 'font_family' ];
	$rum_sst_tab_url				= $rum_sst_plugin_option_array[ 'tab_url' ];
	$rum_sst_pixels_from_top		= $rum_sst_plugin_option_array[ 'pixels_from_top' ];
	$rum_sst_text_color				= $rum_sst_plugin_option_array[ 'text_color' ];
	$rum_sst_tab_color				= $rum_sst_plugin_option_array[ 'tab_color' ];
	$rum_sst_hover_color			= $rum_sst_plugin_option_array[ 'hover_color' ];
	//2
	$rum_sst_text_for_tab_2			= $rum_sst_plugin_option_array[ 'text_for_tab2' ];
	$rum_sst_font_family_2			= $rum_sst_plugin_option_array[ 'font_family2' ];
	$rum_sst_tab_url_2				= $rum_sst_plugin_option_array[ 'tab_url2' ];
	$rum_sst_pixels_from_top_2		= $rum_sst_plugin_option_array[ 'pixels_from_top2' ];
	$rum_sst_text_color_2			= $rum_sst_plugin_option_array[ 'text_color2' ];
	$rum_sst_tab_color_2			= $rum_sst_plugin_option_array[ 'tab_color2' ];
	$rum_sst_hover_color_2			= $rum_sst_plugin_option_array[ 'hover_color2' ];


	// set a default value if the option is not set
	if ( isset($rum_sst_plugin_option_array[ 'text_shadow' ] ) ) {
		$rum_sst_text_shadow			= $rum_sst_plugin_option_array[ 'text_shadow' ];
	} else {
		$rum_sst_text_shadow			= 0;
	}


	if ( isset($rum_sst_plugin_option_array[ 'text_shadow2' ] ) ) {
		$rum_sst_text_shadow_2			= $rum_sst_plugin_option_array[ 'text_shadow2' ];
	} else {
		$rum_sst_text_shadow_2			= 0;
	}

	// set a default value if the option is not set
	if ( isset($rum_sst_plugin_option_array[ 'font_weight_bold' ] ) ) {
		$rum_sst_font_weight_bold		= $rum_sst_plugin_option_array[ 'font_weight_bold' ];
	} else {
		$rum_sst_font_weight_bold		= 0;
	}

	if ( isset($rum_sst_plugin_option_array[ 'font_weight_bold2' ] ) ) {
		$rum_sst_font_weight_bold_2		= $rum_sst_plugin_option_array[ 'font_weight_bold2' ];
	} else {
		$rum_sst_font_weight_bold_2		= 0;
	}

	// this field was added after the initial release so it may not be set
	if ( isset($rum_sst_plugin_option_array[ 'target_blank' ] ) ) {
		$rum_sst_target_blank			= $rum_sst_plugin_option_array[ 'target_blank' ];
	} else {
		$rum_sst_target_blank			= '0';
	}

	if ( isset($rum_sst_plugin_option_array[ 'target_blank2' ] ) ) {
		$rum_sst_target_blank_2			= $rum_sst_plugin_option_array[ 'target_blank2' ];
	} else {
		$rum_sst_target_blank_2			= '0';
	}


	// this field was added after the initial release so it may not be set
	if ( isset($rum_sst_plugin_option_array[ 'left_right' ] ) ) {
		$rum_sst_left_right			= $rum_sst_plugin_option_array[ 'left_right' ];
	} else {
		$rum_sst_left_right			= 'left';
	}


	if ( isset($rum_sst_plugin_option_array[ 'left_right2' ] ) ) {
		$rum_sst_left_right_2			= $rum_sst_plugin_option_array[ 'left_right2' ];
	} else {
		$rum_sst_left_right_2			= 'left';
	}


?>



	<script type="text/javascript">

		jQuery(document).ready(function() {
			jQuery('#colorpicker1').hide();
			jQuery('#colorpicker1').farbtastic("#color1");
			jQuery("#color1").click(function(){jQuery('#colorpicker1').slideToggle()});
		});

		jQuery(document).ready(function() {
			jQuery('#colorpicker2').hide();
			jQuery('#colorpicker2').farbtastic("#color2");
			jQuery("#color2").click(function(){jQuery('#colorpicker2').slideToggle()});
		});

		jQuery(document).ready(function() {
			jQuery('#colorpicker3').hide();
			jQuery('#colorpicker3').farbtastic("#color3");
			jQuery("#color3").click(function(){jQuery('#colorpicker3').slideToggle()});
		});

		jQuery(document).ready(function() {
			jQuery('#colorpicker1_2').hide();
			jQuery('#colorpicker1_2').farbtastic("#color1_2");
			jQuery("#color1_2").click(function(){jQuery('#colorpicker1_2').slideToggle()});
		});

		jQuery(document).ready(function() {
			jQuery('#colorpicker2_2').hide();
			jQuery('#colorpicker2_2').farbtastic("#color2_2");
			jQuery("#color2_2").click(function(){jQuery('#colorpicker2_2').slideToggle()});
		});

		jQuery(document).ready(function() {
			jQuery('#colorpicker3_2').hide();
			jQuery('#colorpicker3_2').farbtastic("#color3_2");
			jQuery("#color3_2").click(function(){jQuery('#colorpicker3_2').slideToggle()});
		});

	</script>


	<table class="widefat">

		<tr valign="top">
		<th scope="row" width="230"><label for="rum_sst_text_for_tab">Text for tab</label></th>
		<td width="525"><input maxlength="30" size="25" type="text" name="rum_sst_plugin_options[text_for_tab]" value="<?php echo esc_html( $rum_sst_text_for_tab ); ?>" /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_tab_font">Select font</label></th>
		<td>
			<select name="rum_sst_plugin_options[font_family]">
				<option value="Arial, sans-serif"									<?php selected( $rum_sst_font_family, 'Arial, sans-serif' );									?>	>ARIAL</option>
				<option value="Georgia, serif"										<?php selected( $rum_sst_font_family, 'Georgia, serif' );										?>	>GEORGIA</option>
				<option value='"Helvetica Neue", Helvetica, sans-serif'				<?php selected( $rum_sst_font_family, '"Helvetica Neue", Helvetica, sans-serif' );				?>	>HELVETICA NEUE / HELVETICA</option>
				<option value='"Lucida Sans Unicode", "Lucida Grande", sans-serif'	<?php selected( $rum_sst_font_family, '"Lucida Sans Unicode", "Lucida Grande", sans-serif' );	?>	>LUCIDA</option>
				<option value="Tahoma, sans-serif"									<?php selected( $rum_sst_font_family, 'Tahoma, sans-serif' );									?>	>TAHOMA</option>
				<option value='"Trebuchet MS", sans-serif'							<?php selected( $rum_sst_font_family, '"Trebuchet MS", sans-serif' );							?>	>TREBUCHET MS</option>
				<option value="Verdana, sans-serif"									<?php selected( $rum_sst_font_family, 'Verdana, sans-serif' );									?>	>VERDANA</option>
			</select>
		</td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_font_weight_bold">Bold text</label></th>
		<td><input name="rum_sst_plugin_options[font_weight_bold]" type="checkbox" value="1" <?php checked( '1', $rum_sst_font_weight_bold ); ?> /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_text_shadow">Drop shadow on hover</label></th>
		<td><input name="rum_sst_plugin_options[text_shadow]" type="checkbox" value="1" <?php checked( '1', $rum_sst_text_shadow ); ?> /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_target_blank">Open link in new window</label></th>
		<td><input name="rum_sst_plugin_options[target_blank]" type="checkbox" value="1" <?php checked( '1', $rum_sst_target_blank ); ?> /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_tab_url">Tab URL</label></th>
		<td><input size="45" type="text" name="rum_sst_plugin_options[tab_url]" value="<?php echo esc_url( $rum_sst_tab_url ); ?>" /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_left_right">Show tab on left or right</label></th>
		<td>
			<input name="rum_sst_plugin_options[left_right]" type="radio" value="left" <?php checked( 'left', $rum_sst_left_right ); ?> /> Left
			<input name="rum_sst_plugin_options[left_right]" type="radio" value="right" <?php checked( 'right', $rum_sst_left_right ); ?> /> Right
		</td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_pixels_from_top">Position from top (px)</label></th>
		<td><input maxlength="4" size="4" type="text" name="rum_sst_plugin_options[pixels_from_top]" value="<?php echo sanitize_text_field( $rum_sst_pixels_from_top ); ?>" /></td>
		</tr>		
		
	</table>

<BR>

	<table class="widefat" border="1">

		<tr valign="top">
			<th scope="row" colspan="2" width="33%"><strong>Colors:</strong> Click on each field to display the color picker. Click again to close it.</th>
			<td width="33%" rowspan="4">
				<div id="colorpicker1"></div>
				<div id="colorpicker2"></div>
				<div id="colorpicker3"></div>
			</td>
		</tr>


		<tr valign="top">
			<th scope="row"><label for="rum_sst_text_color">Text color</label></th>
			<td width="33%"><input type="text" maxlength="7" size="6" value="<?php echo esc_attr( $rum_sst_text_color ); ?>" name="rum_sst_plugin_options[text_color]" id="color1" /></td>
		</tr>


		<tr valign="top">
			<th scope="row"><label for="rum_sst_tab_color">Tab color</label></th>
			<td width="33%"><input type="text" maxlength="7" size="6" value="<?php echo esc_attr( $rum_sst_tab_color ); ?>" name="rum_sst_plugin_options[tab_color]" id="color2" /></td>
		</tr>


		<tr valign="top">
			<th scope="row"><label for="rum_sst_hover_color">Tab hover color</label></th>
			<td width="33%"><input type="text" maxlength="7" size="6" value="<?php echo esc_attr( $rum_sst_hover_color ); ?>" name="rum_sst_plugin_options[hover_color]" id="color3" /></td>
		</tr>

		<tr valign="top">
			<td colspan="3">&nbsp;</td>
		</tr>

	</table>

<!------------------------2nd TAB---------------->

<BR>
	<table class="widefat">

		<tr valign="top">
		<th scope="row" width="230"><label for="rum_sst_text_for_tab_2">Text for tab</label></th>
		<td width="525"><input maxlength="30" size="25" type="text" name="rum_sst_plugin_options[text_for_tab2]" value="<?php echo esc_html( $rum_sst_text_for_tab_2 ); ?>" /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_tab_font_2">Select font</label></th>
		<td>
			<select name="rum_sst_plugin_options[font_family2]">
				<option value="Arial, sans-serif"									<?php selected( $rum_sst_font_family_2, 'Arial, sans-serif' );									?>	>ARIAL</option>
				<option value="Georgia, serif"										<?php selected( $rum_sst_font_family_2, 'Georgia, serif' );										?>	>GEORGIA</option>
				<option value='"Helvetica Neue", Helvetica, sans-serif'				<?php selected( $rum_sst_font_family_2, '"Helvetica Neue", Helvetica, sans-serif' );				?>	>HELVETICA NEUE / HELVETICA</option>
				<option value='"Lucida Sans Unicode", "Lucida Grande", sans-serif'	<?php selected( $rum_sst_font_family_2, '"Lucida Sans Unicode", "Lucida Grande", sans-serif' );	?>	>LUCIDA</option>
				<option value="Tahoma, sans-serif"									<?php selected( $rum_sst_font_family_2, 'Tahoma, sans-serif' );									?>	>TAHOMA</option>
				<option value='"Trebuchet MS", sans-serif'							<?php selected( $rum_sst_font_family_2, '"Trebuchet MS", sans-serif' );							?>	>TREBUCHET MS</option>
				<option value="Verdana, sans-serif"									<?php selected( $rum_sst_font_family_2, 'Verdana, sans-serif' );									?>	>VERDANA</option>
			</select>
		</td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_font_weight_bold_2">Bold text</label></th>
		<td><input name="rum_sst_plugin_options[font_weight_bold2]" type="checkbox" value="1" <?php checked( '1', $rum_sst_font_weight_bold_2 ); ?> /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_text_shadow_2">Drop shadow on hover</label></th>
		<td><input name="rum_sst_plugin_options[text_shadow2]" type="checkbox" value="1" <?php checked( '1', $rum_sst_text_shadow_2 ); ?> /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_target_blank_2">Open link in new window</label></th>
		<td><input name="rum_sst_plugin_options[target_blank2]" type="checkbox" value="1" <?php checked( '1', $rum_sst_target_blank_2 ); ?> /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_tab_url_2">Tab URL</label></th>
		<td><input size="45" type="text" name="rum_sst_plugin_options[tab_url2]" value="<?php echo esc_url( $rum_sst_tab_url_2 ); ?>" /></td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_left_right_2">Show tab on left or right</label></th>
		<td>
			<input name="rum_sst_plugin_options[left_right2]" type="radio" value="left" <?php checked( 'left', $rum_sst_left_right_2 ); ?> /> Left
			<input name="rum_sst_plugin_options[left_right2]" type="radio" value="right" <?php checked( 'right', $rum_sst_left_right_2 ); ?> /> Right
		</td>
		</tr>


		<tr valign="top">
		<th scope="row"><label for="rum_sst_pixels_from_top_2">Position from top (px)</label></th>
		<td><input maxlength="4" size="4" type="text" name="rum_sst_plugin_options[pixels_from_top2]" value="<?php echo sanitize_text_field( $rum_sst_pixels_from_top_2 ); ?>" /></td>
		</tr>		
		
	</table>

<BR>

	<table class="widefat" border="1">

		<tr valign="top">
			<th scope="row" colspan="2" width="33%"><strong>Colors:</strong> Click on each field to display the color picker. Click again to close it.</th>
			<td width="33%" rowspan="4">
				<div id="colorpicker1_2"></div>
				<div id="colorpicker2_2"></div>
				<div id="colorpicker3_2"></div>
			</td>
		</tr>


		<tr valign="top">
			<th scope="row"><label for="rum_sst_text_color_2">Text color</label></th>
			<td width="33%"><input type="text" maxlength="7" size="6" value="<?php echo esc_attr( $rum_sst_text_color_2 ); ?>" name="rum_sst_plugin_options[text_color2]" id="color1_2" /></td>
		</tr>


		<tr valign="top">
			<th scope="row"><label for="rum_sst_tab_color_2">Tab color</label></th>
			<td width="33%"><input type="text" maxlength="7" size="6" value="<?php echo esc_attr( $rum_sst_tab_color_2 ); ?>" name="rum_sst_plugin_options[tab_color2]" id="color2_2" /></td>
		</tr>


		<tr valign="top">
			<th scope="row"><label for="rum_sst_hover_color_2">Tab hover color</label></th>
			<td width="33%"><input type="text" maxlength="7" size="6" value="<?php echo esc_attr( $rum_sst_hover_color_2 ); ?>" name="rum_sst_plugin_options[hover_color2]" id="color3_2" /></td>
		</tr>

		<tr valign="top">
			<td colspan="3">&nbsp;</td>
		</tr>

	</table>

	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>



<?php
	echo '</form>';
	echo '</div>';
}




// Use Settings API to whitelist options
function rum_sst_settings_api_init() {

	register_setting( 'rum_sst_option_group', 'rum_sst_plugin_options' );
}




// Build array of links for rendering in installed plugins list
function rum_sst_admin_plugin_actions($links) {

	$links[] = '<a href="options-general.php?page=rum_simple_side_tab">'.__('Settings').'</a>';
	return $links;
}




// This function runs all the css and dynamic css elements for displaying the simple side tab
function rum_sst_custom_css_hook() {

	// get plugin option array and store in a variable
	$rum_sst_plugin_option_array	= get_option( 'rum_sst_plugin_options' );

	// fetch individual values from the plugin option variable array
	$rum_sst_font_family			= $rum_sst_plugin_option_array[ 'font_family' ];
	$rum_sst_pixels_from_top		= $rum_sst_plugin_option_array[ 'pixels_from_top' ];
	$rum_sst_text_color				= $rum_sst_plugin_option_array[ 'text_color' ];
	$rum_sst_tab_color				= $rum_sst_plugin_option_array[ 'tab_color' ];
	$rum_sst_hover_color			= $rum_sst_plugin_option_array[ 'hover_color' ];
	$rum_sst_left_right				= $rum_sst_plugin_option_array[ 'left_right' ];

	//2
	$rum_sst_font_family_2			= $rum_sst_plugin_option_array[ 'font_family2' ];
	$rum_sst_pixels_from_top_2		= $rum_sst_plugin_option_array[ 'pixels_from_top2' ];
	$rum_sst_text_color_2			= $rum_sst_plugin_option_array[ 'text_color2' ];
	$rum_sst_tab_color_2			= $rum_sst_plugin_option_array[ 'tab_color2' ];
	$rum_sst_hover_color_2			= $rum_sst_plugin_option_array[ 'hover_color2' ];
	$rum_sst_left_right_2			= $rum_sst_plugin_option_array[ 'left_right2' ];


	// set a default value if the option is not set
	if ( isset($rum_sst_plugin_option_array[ 'text_shadow' ] ) ) {
		$rum_sst_text_shadow			= $rum_sst_plugin_option_array[ 'text_shadow' ];
	} else {
		$rum_sst_text_shadow			= 0;
	}

	if ( isset($rum_sst_plugin_option_array[ 'text_shadow2' ] ) ) {
		$rum_sst_text_shadow_2			= $rum_sst_plugin_option_array[ 'text_shadow2' ];
	} else {
		$rum_sst_text_shadow_2			= 0;
	}

	// set a default value if the option is not set
	if ( isset($rum_sst_plugin_option_array[ 'font_weight_bold' ] ) ) {
		$rum_sst_font_weight_bold		= $rum_sst_plugin_option_array[ 'font_weight_bold' ];
	} else {
		$rum_sst_font_weight_bold		= 0;
	}

	if ( isset($rum_sst_plugin_option_array[ 'font_weight_bold2' ] ) ) {
		$rum_sst_font_weight_bold_2		= $rum_sst_plugin_option_array[ 'font_weight_bold2' ];
	} else {
		$rum_sst_font_weight_bold_2		= 0;
	}
?>

<style type='text/css'>
/* Begin Simple Side Tab Styles*/
#rum_sst_tab {
	font-family:<?php echo $rum_sst_font_family; ?>;
	top:<?php echo $rum_sst_pixels_from_top; ?>px;
	background-color:<?php echo $rum_sst_tab_color; ?>;
	color:<?php echo $rum_sst_text_color; ?>;
	border-style:solid;
	border-width:0px;
}

#rum_sst_tab_2 {
	font-family:<?php echo $rum_sst_font_family_2; ?>;
	top:<?php echo $rum_sst_pixels_from_top_2; ?>px;
	background-color:<?php echo $rum_sst_tab_color_2; ?>;
	color:<?php echo $rum_sst_text_color_2; ?>;
	border-style:solid;
	border-width:0px;
}

#rum_sst_tab:hover {
	background-color: <?php echo $rum_sst_hover_color; ?>;
	<?php
	if ( $rum_sst_text_shadow == '1' ) {

		if ( $rum_sst_left_right == 'left' ) {

		  echo '	-moz-box-shadow:    -3px 3px 5px 2px #ccc;' . "\n";
		  echo '	-webkit-box-shadow: -3px 3px 5px 2px #ccc;' . "\n";
		  echo '	box-shadow:         -3px 3px 5px 2px #ccc;' . "\n";
		} else {

		  echo '	-moz-box-shadow:    -3px -3px 5px 2px #ccc;' . "\n";
		  echo '	-webkit-box-shadow: -3px -3px 5px 2px #ccc;' . "\n";
		  echo '	box-shadow:         -3px -3px 5px 2px #ccc;' . "\n";			
		}
	}
	?>
}

#rum_sst_tab_2:hover {
	background-color: <?php echo $rum_sst_hover_color_2; ?>;
	<?php
	
	if ( $rum_sst_text_shadow_2 == '1' ) {

		if ( $rum_sst_left_right_2 == 'left' ) {

		  echo '	-moz-box-shadow:    -3px 3px 5px 2px #ccc;' . "\n";
		  echo '	-webkit-box-shadow: -3px 3px 5px 2px #ccc;' . "\n";
		  echo '	box-shadow:         -3px 3px 5px 2px #ccc;' . "\n";
		} else {

		  echo '	-moz-box-shadow:    -3px -3px 5px 2px #ccc;' . "\n";
		  echo '	-webkit-box-shadow: -3px -3px 5px 2px #ccc;' . "\n";
		  echo '	box-shadow:         -3px -3px 5px 2px #ccc;' . "\n";			
		}
	}
	?>
}


.rum_sst_contents {
	position:fixed;
	margin:0;
	padding:15px 20px 15px 20px;
	text-decoration:none;
	text-align:center;
	font-size:15px;
	<?php
	if ( $rum_sst_font_weight_bold == '1' ) :
	  echo 'font-weight:bold;' . "\n";
	else :
	  echo 'font-weight:normal;' . "\n";
	endif;
	?>
	border-style:solid;
	display:block;
	z-index:100000;
}

.rum_sst_contents_2 {
	position:fixed;
	margin:0;
	padding:15px 20px 15px 20px;	
	text-decoration:none;
	text-align:center;
	font-size:15px;
	width: 133px;
	<?php
	if ( $rum_sst_font_weight_bold_2 == '1' ) :
	  echo 'font-weight:bold;' . "\n";
	else :
	  echo 'font-weight:normal;' . "\n";
	endif;
	?>
	border-style:solid;
	display:block;
	z-index:100000;
}

.rum_sst_left {
	left:-2px;
	cursor: pointer;
	-webkit-transform-origin:0 0;
	-moz-transform-origin:0 0;
	-o-transform-origin:0 0;
	-ms-transform-origin:0 0;
	-webkit-transform:rotate(270deg);
	-moz-transform:rotate(270deg);
	-ms-transform:rotate(270deg);
	-o-transform:rotate(270deg);
	transform:rotate(270deg);
	-moz-border-radius-bottomright:10px;
	border-bottom-right-radius:10px;
	-moz-border-radius-bottomleft:10px;
	border-bottom-left-radius:10px;
}

.rum_sst_right {
   	right:-1px;
	cursor: pointer;
	-webkit-transform-origin:100% 100%;
	-moz-transform-origin:100% 100%;
	-o-transform-origin:100% 100%;
	-ms-transform-origin:100% 100%;
	-webkit-transform:rotate(-90deg);
	-moz-transform:rotate(-90deg);
	-ms-transform:rotate(-90deg);
	-o-transform:rotate(-90deg);
	transform:rotate(-90deg);
	-moz-border-radius-topright:10px;
	border-top-right-radius:10px;
	-moz-border-radius-topleft:10px;
	border-top-left-radius:10px;
}

.rum_sst_right.less-ie-9 {
	right:-120px;
	filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1);
}

.rum_sst_left.less-ie-9 {
	filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}

/* End Simple Side Tab Styles*/

</style>

<?php
}
