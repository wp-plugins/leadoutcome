<?php
add_action('init','lo_initialize');
add_action('wp_enqueue_scripts', 'lo_init_scripts');
add_action('admin_enqueue_scripts', 'lo_init_scripts');

add_action( 'post_edit_form_tag' , 'lo_post_edit_form_tag' );

add_action( 'wp_footer', 'lo_wp_footer' );

if(!is_admin())
{
	add_shortcode('leadoutcome_optin_form', 'lo_optin_form_shortcode');
}
