<?php

function lo_plugin_activate()
{
	global $wpdb;
	global $lo_version;

	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix . "lo_optin_forms` (
		`id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		`blog_id` INTEGER(11) NOT NULL,
		`form_name` VARCHAR(255) NOT NULL,
		`full_form_content` TEXT,
		`email_only_form_content` TEXT,
		`subscribe_only_form_content` TEXT,
		PRIMARY KEY (`id`) COMMENT ''
		) ENGINE=InnoDB CHECKSUM=0 DELAY_KEY_WRITE=0 PACK_KEYS=0 AUTO_INCREMENT=0 AVG_ROW_LENGTH=0 MIN_ROWS=0 MAX_ROWS=0 ROW_FORMAT=DEFAULT KEY_BLOCK_SIZE=0";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_site_option( "lo_version", $lo_version );

}

function lo_initialize()
{
	/**
	register_post_type($GLOBALS['LO_CUSTOM_POST_TYPE'], $GLOBALS['LO_CUSTOM_POST_TYPE_ARGS']);
	foreach ($GLOBALS['LO_TAXONOMIES'] as $taxonomy_name => $taxonomy_info) {
		register_taxonomy($taxonomy_name, $taxonomy_info['object_type'], $taxonomy_info['args']);
	}
	flush_rewrite_rules(false);
	**/

	add_action('admin_menu','lo_admin_menu');
}

function lo_init_scripts()
{
	global $wp_scripts,$wp_styles;

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-ui-widget');

	wp_enqueue_style('jquery-ui-theme',LO_PLUGIN_DIR_URL.'/jqueryui/themes/'.JQUERY_UI_THEME.'/jquery-ui-1.8.14.custom.css' );
	//wp_enqueue_script('ds-frontend-scripts',LO_PLUGIN_DIR_URL.'/frontend/js/scripts.js',array(),false,true );

	$data = array(
	);
	wp_localize_script( 'ds-frontend-scripts', 'lo_frontend_scripts_data', $data );

	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
}

function lo_optin_form_shortcode($atts) {
	global $wpdb;
	extract(shortcode_atts(array(
		'id' => 0,
		'type' => ''
	), $atts));

	$form_data = $wpdb->get_row($wpdb->prepare("SELECT `".$wpdb->escape($type)."` FROM `".$wpdb->prefix . "lo_optin_forms` WHERE `id`=%d AND `blog_id`=".get_current_blog_id(),$id),ARRAY_A);

	ob_start();

	if(is_array($form_data) && array_key_exists($type,$form_data) && $form_data[$type] != '')
	{
		echo do_shortcode(stripslashes($form_data[$type]));
	}

	$output = ob_get_contents();
	ob_end_clean();
	return do_shortcode($output);
}

function lo_post_edit_form_tag( ) {
    echo ' enctype="multipart/form-data"';
}

function lo_admin_menu()
{
	add_menu_page(__('LeadOutcome', 'lo'), __('LeadOutcome', 'lo'), 'administrator', 'leadoutcom_main', 'lo_admin_main',plugins_url('leadoutcome/frontend/img/lo_icon.png'));
	$act_optin_form_page = add_submenu_page( 'leadoutcom_main' , __('Opt-In Forms', 'lo'), __('Opt-In Forms', 'lo'), 'administrator', 'leadoutcome_optin_forms', 'lo_admin_optin_forms');
	$act_lead_track_convert_options_page = add_submenu_page( 'leadoutcom_main' , __('Lead Tracking / Conversions', 'lo'), __('Lead Tracking / Conversions', 'lo'), 'administrator', 'leadoutcome_lead_track_convert_options', 'lo_admin_lead_track_convert_options');
}

function lo_admin_main()
{
	include_once(LO_PLUGIN_DIR.'/views/admin/admin_main.php');
}

function lo_admin_lead_track_convert_options()
{
	global $wpdb;

	$posts_update_success = false;
	$posts_update_success = false;
	$perform_update = false;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'update')
	{
		if(!current_user_can('manage_options'))
		{
			wp_die( __('You do not have priviledges to access this page','lo'), 'Access Denied!', array('back_link' => true) );
		}

		if ( !empty($_REQUEST['lononce']) && wp_verify_nonce($_REQUEST['lononce'],'lo_lead_track_convert_options_update') && $_REQUEST['action'] == 'update' )
		{
			$perform_update = true;
			$posts_update_success = update_site_option( 'lo_lead_track_convert_posts', ($_POST['lead_track_convert_posts'] == '1' ? '1' : '0') );
			$pages_update_success = update_site_option( 'lo_lead_track_convert_pages', ($_POST['lead_track_convert_pages'] == '1' ? '1' : '0') );
		}
	}
	$lo_lead_track_convert_posts = get_site_option('lo_lead_track_convert_posts',1);
	$lo_lead_track_convert_pages = get_site_option('lo_lead_track_convert_pages',1);
	include_once( LO_PLUGIN_DIR . '/views/admin/form_edit_lead_track_convert_options.php' );
}

function lo_admin_optin_forms()
{
	global $wpdb;

	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'add')
	{
		$perform_create = false;
		$create_success = false;

		if ( !empty($_REQUEST['lononce']) && wp_verify_nonce($_REQUEST['lononce'],'lo_optin_form_add') && $_REQUEST['action'] == 'add' )
		{
			$perform_create = true;
			$wpdb->insert( 
				$wpdb->prefix . 'lo_optin_forms', 
				array( 
					'blog_id' => get_current_blog_id()
					,'form_name' => $_REQUEST['form_name'] 
					,'full_form_content' => $_REQUEST['full_form_content']
					,'email_only_form_content' => $_REQUEST['email_only_form_content']
					,'subscribe_only_form_content' => $_REQUEST['subscribe_only_form_content']
				), 
				array( 
					'%d',
					'%s',
					'%s',
					'%s',
					'%s'
				) 
			);

			$insert_id = $wpdb->insert_id;

			if($insert_id || $insert_id > 0)
			{
				$_REQUEST['form_id'] = $insert_id;
				$create_success = true;
			}
		}

		$form_data = $wpdb->get_row('SELECT * FROM `'.$wpdb->prefix . 'lo_optin_forms` WHERE `id`='.$wpdb->escape($_REQUEST['form_id']).' AND `blog_id`='.get_current_blog_id(),ARRAY_A);

		if($create_success || $create_success > 0)
		{
			include_once( LO_PLUGIN_DIR . '/views/admin/form_edit_optin_form.php' );
		}
		else
		{
			include_once( LO_PLUGIN_DIR . '/views/admin/form_add_optin_form.php' );
		}

	}
	elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['form_id']) && $_REQUEST['form_id'] > 0)
	{
		if(!current_user_can('manage_options'))
		{
			wp_die( __('You do not have priviledges to access this page','lo'), 'Access Denied!', array('back_link' => true) );
		}

		$perform_update = false;
		$update_success = false;
		if ( !empty($_REQUEST['lononce']) && wp_verify_nonce($_REQUEST['lononce'],'lo_optin_form_edit') && $_REQUEST['action'] == 'edit' )
		{
			$perform_update = true;
			$update_success = $wpdb->update( $wpdb->prefix . 'lo_optin_forms'
					, array(
						'form_name' => $_POST['form_name']
						,'full_form_content' => $_POST['full_form_content']
						,'email_only_form_content' => $_POST['email_only_form_content']
						,'subscribe_only_form_content' => $_POST['subscribe_only_form_content']
					)
					, array( 'id' => $wpdb->escape($_POST['form_id']),'blog_id'=>get_current_blog_id() ) );
		}
		$form_data = $wpdb->get_row('SELECT * FROM `'.$wpdb->prefix . 'lo_optin_forms` WHERE `id`='.$wpdb->escape($_REQUEST['form_id']).' AND `blog_id`='.get_current_blog_id(),ARRAY_A);
		include_once( LO_PLUGIN_DIR . '/views/admin/form_edit_optin_form.php' );
	}
	else
	{
		if(current_user_can('manage_options'))
		{
			if(!class_exists('WP_List_Table')){
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			}
			if(!class_exists('LeadOutcome_OptinForms_List_Table')){
				require_once( LO_PLUGIN_DIR . '/includes/optin_forms_list_table.php' );
			}

			$LeadOutcome_OptinForms_List_Table = new LeadOutcome_OptinForms_List_Table($restrict_to_my_clubs);
			$LeadOutcome_OptinForms_List_Table->prepare_items();

			include_once(LO_PLUGIN_DIR.'/views/admin/admin_optin_forms.php');
		}
		else
		{
			wp_die( __('You do not have priviledges to access this page','lo'), 'Access Denied!', array('back_link' => true) );
		}
	}
}

function leadoutcome_optin_forms_edit()
{
}

function lo_delete_optin_form($ids)
{
	global $wpdb;

	if(!current_user_can('manage_options'))
	{
		wp_die( __('You do not have priviledges to access this page','lo'), 'Access Denied!', array('back_link' => true) );

	}

	for($a=0;$a<count($ids);$a++)
	{
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix . "lo_optin_forms` WHERE `id` = %d AND `blog_id` = %d",$ids[$a],get_current_blog_id()));
	}
	?>
	<script>location.replace('<?php echo $_REQUEST['_wp_http_referer'].'&deleted=1' ?>');</script>
	<?php
	exit;
}

function lo_wp_footer() {
	global $wpdb,$post;

	$show_lead_track_convert_code = false;
	if ( !is_admin() && !is_feed() && !is_robots() && !is_trackback() && isset($post->ID) ) {


		$lo_lead_track_convert_posts = get_site_option('lo_lead_track_convert_posts',1);
		$lo_lead_track_convert_pages = get_site_option('lo_lead_track_convert_pages',1);

		$current_post_type = get_post_type( $post->ID );
		$lo_this_page_visited_title = get_the_title( $post->ID );

		if($current_post_type == 'post' && $lo_lead_track_convert_posts == 1)
		{
			$show_lead_track_convert_code = true;
			$lo_lead_track_convert_activity = 'Visited Post';
		}
		if($current_post_type == 'page' && $lo_lead_track_convert_pages == 1)
		{
			$show_lead_track_convert_code = true;
			$lo_lead_track_convert_activity = 'Visited Page';
		}
	}

	if($show_lead_track_convert_code)
	{
		include_once(LO_PLUGIN_DIR.'/views/frontend/lead_track_convert_code.php');
	}
}

function lo_messages($text,$type='message')
{
	if($type=='error')
	{
		echo '<div class="error"><p>'.$text.'</p></div>';
	}
	elseif($type=='message')
	{
		echo '<div id="message" class="updated below-h2"><p>'.$text.'</p></div>';
	}
}
