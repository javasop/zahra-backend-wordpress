<?php
/*
Plugin Name: Easy Admin Color Schemes
Plugin URI: http://JamesDimick.com/easy-admin-color-schemes
Description: Easy Admin Color Schemes allows users to easily customize the look and feel of the administration interface for WordPress.
Version: 4.2
Author: James Dimick
Author URI: http://JamesDimick.com
Text Domain: easy-admin-color-schemes
Domain Path: /includes/lang/
License: GPLv3 or later

	Copyright (C) 2011 James Dimick <mail@jamesdimick.com> - JamesDimick.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

# Don't ever load the plugin file directly
if(!defined('ABSPATH')) die('-1');

# Only continue if we're on the admin side
if(!is_admin()) return;

# Define some useful constants for use throughout the plugin
define('EACS_VERSION', '4.2');
define('EACS_DEBUG', false); # Set to true if you are having issues with the plugin
define('EACS_SLUG', 'admincolorscheme');
define('EACS_TEXT_DOMAIN', 'easy-admin-color-schemes');
define('EACS_INCLUDES_URL', plugin_dir_url(__FILE__) . 'includes/');
define('EACS_CURRENT_PAGE', eacs_get_current_plugin_page());
define('EACS_OPT_DEFAULT', eacs_get_option_default());
define('EACS_OPT_FORCED', eacs_get_option_forced());
define('EACS_OPT_SHOW_AUTHOR_SUPPORT_MSG', eacs_get_option_showauthorsupportmsg());
define('EACS_CAP_CREATE_SCHEMES', 'create_' . EACS_SLUG . 's');
define('EACS_CAP_EDIT_SCHEME', 'edit_' . EACS_SLUG);
define('EACS_CAP_EDIT_SCHEMES', 'edit_' . EACS_SLUG . 's');
define('EACS_CAP_EDIT_OTHERS_SCHEMES', 'edit_others_' . EACS_SLUG . 's');
define('EACS_CAP_DELETE_SCHEME', 'delete_' . EACS_SLUG);
define('EACS_CAP_DELETE_SCHEMES', 'delete_' . EACS_SLUG . 's');
define('EACS_CAP_DELETE_OTHERS_SCHEMES', 'delete_others_' . EACS_SLUG . 's');
define('EACS_CAP_IMPORT_SCHEMES', 'import_' . EACS_SLUG . 's');
define('EACS_CAP_EXPORT_SCHEME', 'export_' . EACS_SLUG);
define('EACS_CAP_EXPORT_SCHEMES', 'export_' . EACS_SLUG . 's');
define('EACS_CAP_EXPORT_OTHERS_SCHEMES', 'export_others_' . EACS_SLUG . 's');
define('EACS_CAP_MANAGE_SETTINGS', 'manage_' . EACS_SLUG . 's_settings');

# Hook the plugin into WordPress
register_activation_hook(__FILE__, 'eacs_activate');
add_action('init', 'eacs_init');

# Give all the plugin capabilities to all admins and setup the default schemes when the plugin is activated
function eacs_activate() {
	global $wpdb;

	if(is_object($role = get_role('administrator'))) {
		if(!$role->has_cap(EACS_CAP_CREATE_SCHEMES)) $role->add_cap(EACS_CAP_CREATE_SCHEMES);
		if(!$role->has_cap(EACS_CAP_EDIT_SCHEMES)) $role->add_cap(EACS_CAP_EDIT_SCHEMES);
		if(!$role->has_cap(EACS_CAP_EDIT_OTHERS_SCHEMES)) $role->add_cap(EACS_CAP_EDIT_OTHERS_SCHEMES);
		if(!$role->has_cap(EACS_CAP_DELETE_SCHEMES)) $role->add_cap(EACS_CAP_DELETE_SCHEMES);
		if(!$role->has_cap(EACS_CAP_DELETE_OTHERS_SCHEMES)) $role->add_cap(EACS_CAP_DELETE_OTHERS_SCHEMES);
		if(!$role->has_cap(EACS_CAP_IMPORT_SCHEMES)) $role->add_cap(EACS_CAP_IMPORT_SCHEMES);
		if(!$role->has_cap(EACS_CAP_EXPORT_SCHEMES)) $role->add_cap(EACS_CAP_EXPORT_SCHEMES);
		if(!$role->has_cap(EACS_CAP_EXPORT_OTHERS_SCHEMES)) $role->add_cap(EACS_CAP_EXPORT_OTHERS_SCHEMES);
		if(!$role->has_cap(EACS_CAP_MANAGE_SETTINGS)) $role->add_cap(EACS_CAP_MANAGE_SETTINGS);
	}

	$slug = EACS_SLUG;
	if(@is_readable($dsfile = plugin_dir_path(__FILE__) . 'includes/php/default-schemes.php')) include_once($dsfile);
}

# Initialize the plugin and hook up the various plugin functions
function eacs_init() {
	load_plugin_textdomain(EACS_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/includes/lang');

	register_post_type(EACS_SLUG, array(
		'label' => eacs__('Admin Color Schemes'),
		'labels' => array(
			'name' => eacs_x('Admin Color Schemes', 'general name'),
			'singular_name' => eacs_x('Admin Color Scheme', 'singular name'),
			'add_new' => eacs_x('Add New', 'add new item'),
			'add_new_item' => eacs__('Add New Admin Color Scheme'),
			'edit_item' => eacs__('Edit Admin Color Scheme'),
			'new_item' => eacs__('New Admin Color Scheme'),
			'view_item' => eacs__('View Admin Color Scheme'),
			'search_items' => eacs__('Search Admin Color Schemes'),
			'not_found' => eacs__('No admin color schemes found.'),
			'not_found_in_trash' => eacs__('No admin color schemes found in Trash.'),
			'all_items' => eacs__('All Admin Color Schemes')
		),
		'public' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 100,
		'menu_icon' => EACS_INCLUDES_URL . 'img/icon16.png',
		'capability_type' => EACS_SLUG,
		'capabilities' => array(
			'publish_posts' => EACS_CAP_CREATE_SCHEMES,
			'edit_post' => EACS_CAP_EDIT_SCHEME,
			'edit_posts' => EACS_CAP_EDIT_SCHEMES,
			'edit_others_posts' => EACS_CAP_EDIT_OTHERS_SCHEMES,
			'delete_post' => EACS_CAP_DELETE_SCHEME,
			'delete_posts' => EACS_CAP_DELETE_SCHEMES,
			'delete_others_posts' => EACS_CAP_DELETE_OTHERS_SCHEMES,
			'read' => '',
			'read_post' => '',
			'read_private_posts' => '',
			'edit_private_posts' => '',
			'edit_published_posts' => '',
			'delete_private_posts' => '',
			'delete_published_posts' => '',
			'export_scheme' => EACS_CAP_EXPORT_SCHEME,
			'export_schemes' => EACS_CAP_EXPORT_SCHEMES,
			'export_others_schemes' => EACS_CAP_EXPORT_OTHERS_SCHEMES
		),
		'hierarchical' => false,
		'supports' => array('title', 'editor', 'author', 'excerpt'),
		'register_meta_box_cb' => 'eacs_post_meta_boxes',
		'taxonomies' => array(),
		'permalink_epmask' => '',
		'has_archive' => false,
		'rewrite' => false,
		'query_var' => false,
		'can_export' => false,
		'show_in_nav_menus' => false
	));

	add_action('admin_init', 'eacs_admin_init');
	add_action('admin_init', 'eacs_do_forced_scheme');
	add_action('admin_enqueue_scripts', 'eacs_admin_enqueue_scripts');
	add_action('admin_menu', 'eacs_admin_menu');
	add_action('user_register', 'eacs_do_default_scheme', 99999);
	add_action('admin_notices', 'eacs_admin_messages');
	remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
	add_action('admin_color_scheme_picker', 'eacs_profile_page_scheme_picker');
	add_filter('map_meta_cap', 'eacs_map_meta_cap', 10, 4);

	switch(EACS_CURRENT_PAGE) {
		case 'add':
		case 'edit':
			if(EACS_CURRENT_PAGE == 'edit') {
				add_action('admin_action_eacs-css', 'eacs_scheme_output_action');
				add_action('admin_action_eacs-activate', 'eacs_scheme_activate_action');
			}

			add_action('in_admin_footer', 'eacs_remove_unneeded_admin_footer_stuff', 1);
			add_action('edit_form_after_title', 'eacs_edit_form_after_title');
			add_action('edit_form_after_editor', 'eacs_edit_form_after_editor');
			add_filter('default_hidden_meta_boxes', 'eacs_default_hidden_meta_boxes', 10, 2);
			add_filter('enter_title_here', 'eacs_enter_title_here');
			add_filter('the_editor', 'eacs_the_editor');
			add_filter('wp_insert_post_data', 'eacs_wp_insert_post_data', 99999, 2);
			add_filter('post_updated_messages', 'eacs_post_updated_messages', 99999);
			break;

		case 'view':
			add_action('manage_' . EACS_SLUG . '_posts_custom_column', 'eacs_custom_columns');
			add_filter('views_edit-' . EACS_SLUG, 'eacs_views');
			add_filter('bulk_actions-edit-' . EACS_SLUG, 'eacs_bulk_actions');
			add_filter('manage_edit-' . EACS_SLUG . '_columns', 'eacs_edit_columns');
			add_filter('manage_edit-' . EACS_SLUG . '_sortable_columns', 'eacs_sortable_columns');
			add_filter('pre_get_posts', 'eacs_pre_get_posts');
			add_filter('post_updated_messages', 'eacs_post_updated_messages', 99999);
			break;

		case 'import':
			add_action('load-' . EACS_SLUG . '_page_eacs-import', 'eacs_do_import');
			break;

		case 'export':
			add_action('admin_init', 'eacs_before_export_page_load');
			break;
	}
}

# Add any color schemes managed by this plugin and setup the plugin options fields
function eacs_admin_init() {
	foreach(get_posts(array('post_type' => EACS_SLUG, 'numberposts' => -1, 'posts_per_page' => -1, 'nopaging' => true)) as $scheme) {
		wp_admin_css_color(
			sanitize_title("eacs-{$scheme->ID}"),
			_draft_or_post_title($scheme->ID),
			admin_url("post.php?post={$scheme->ID}&action=eacs-css"),
			eacs_return_valid_colors_array($scheme->ID)
		);
	}

	register_setting('eacs_options', 'eacs_default', 'eacs_validate_option_default');
	register_setting('eacs_options', 'eacs_forced', 'eacs_validate_option_forced');
	register_setting('eacs_options', 'eacs_showauthorsupportmsg', 'eacs_validate_option_showauthorsupportmsg');
}

# Setup the admin menu entries for this plugin
function eacs_admin_menu() {
	$parent = 'edit.php?post_type=' . EACS_SLUG;

	add_submenu_page($parent, eacs__('Import Admin Color Scheme'), eacs__('Import'), EACS_CAP_IMPORT_SCHEMES, 'eacs-import', 'eacs_output_import_admin_page');
	add_submenu_page($parent, eacs__('Export Admin Color Scheme'), eacs__('Export'), EACS_CAP_EXPORT_SCHEMES, 'eacs-export', 'eacs_output_export_admin_page');
	add_submenu_page($parent, eacs__('Admin Color Schemes Settings'), eacs__('Settings'), EACS_CAP_MANAGE_SETTINGS, 'eacs-settings', 'eacs_output_settings_admin_page');

	if(current_user_can(EACS_CAP_EXPORT_SCHEMES)) add_filter('parent_file', 'eacs_export_admin_menu_parent_file');
}

# Enqueue the scripts needed by the plugin
function eacs_admin_enqueue_scripts() {
	$min = EACS_DEBUG === true ? '' : '.min';

	wp_enqueue_style('eacs-global', EACS_INCLUDES_URL . 'css/global' . $min . '.css', array(), EACS_VERSION);

	switch(EACS_CURRENT_PAGE) {
		case 'add':
		case 'edit':
			wp_enqueue_style('farbtastic');
			wp_enqueue_style('editor-buttons');
			wp_enqueue_style('eacs-addedit-screen', EACS_INCLUDES_URL . 'css/addedit' . $min . '.css', array(), EACS_VERSION);
			wp_enqueue_script('farbtastic');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('eacs-addedit-screen', EACS_INCLUDES_URL . 'js/addedit' . $min . '.js', array('jquery', 'farbtastic'), EACS_VERSION);
			wp_localize_script('eacs-addedit-screen', 'eacsL10n', array(
				'adminBaseUrl' => admin_url(),
				'insertShortcodeTitle' => eacs__('Click or drag & drop to insert this shortcode into the CSS textarea')
			));
			break;

		case 'view':
			wp_enqueue_style('thickbox');
			wp_enqueue_style('eacs-viewall-screen', EACS_INCLUDES_URL . 'css/viewall' . $min . '.css', array(), EACS_VERSION);
			wp_enqueue_script('thickbox');
			wp_enqueue_script('eacs-viewall-screen', EACS_INCLUDES_URL . 'js/viewall' . $min . '.js', array('jquery', 'thickbox'), EACS_VERSION);
			wp_localize_script('eacs-viewall-screen', 'eacsL10n', array(
				'adminBaseUrl' => admin_url(),
				'previewSchemeTitle' => eacs__('Preview this admin color scheme'),
				'previewSchemeText' => eacs__('Preview'),
				'previewQuote' => eacs__('Preview &ldquo;'),
				'exportQuote' => eacs__('Export &ldquo;'),
				'endQuote' => eacs__('&rdquo;'),
				'selectExportMethod' => eacs__('Select the export method'),
				'cancel' => eacs__('Cancel')
			));
			break;

		case 'export':
			wp_enqueue_style('eacs-export-screen', EACS_INCLUDES_URL . 'css/export' . $min . '.css', array(), EACS_VERSION);
			break;
	}
}

# Properly map the custom capabilities for the plugin
function eacs_map_meta_cap($caps, $cap, $user_id, $args) {
	if(in_array($cap, array(EACS_CAP_EDIT_SCHEME, EACS_CAP_DELETE_SCHEME, EACS_CAP_EXPORT_SCHEME))) {
		$post = get_post($args[0]);
		$caps = array();
	}

	switch($cap) {
		case EACS_CAP_EDIT_SCHEME:
			$caps[] = $user_id == $post->post_author ? EACS_CAP_EDIT_SCHEMES : EACS_CAP_EDIT_OTHERS_SCHEMES;
			break;

		case EACS_CAP_DELETE_SCHEME:
			$caps[] = $user_id == $post->post_author ? EACS_CAP_DELETE_SCHEMES : EACS_CAP_DELETE_OTHERS_SCHEMES;
			break;

		case EACS_CAP_EXPORT_SCHEME:
			$caps[] = $user_id == $post->post_author ? EACS_CAP_EXPORT_SCHEMES : EACS_CAP_EXPORT_OTHERS_SCHEMES;
			break;
	}

	return $caps;
}

# Setup any notices that might need to be displayed to the user
function eacs_admin_messages() {
	if(EACS_CURRENT_PAGE !== false) {
		if(EACS_OPT_SHOW_AUTHOR_SUPPORT_MSG) {
			?><div id="eacs-author-support-link" class="updated"><p>
				<strong><?php eacs_e('Like this plugin? <a href="http://jamesdimick.com/support-my-work">Support the author</a> to help guarantee future development. All contributions are welcome!') ?></strong>
				<small><em><?php printf(eacs__('Disable this message on <a href="%s">the settings page</a>.'), esc_url(admin_url(sprintf('edit.php?post_type=%s&page=eacs-settings#support-the-author-msg', EACS_SLUG)))) ?></em></small>
				<br class="clear" />
			</p></div><?php
		}

		if(isset($_GET['message']) && is_numeric($_GET['message'])) {
			$err = false;

			switch((int)$_GET['message']) {
				case 1:
					$msg = eacs__('Admin color scheme successfully activated for your account.');
					break;

				case 2:
					$msg = eacs__('Admin color scheme successfully imported.');
					break;

				case 3:
					$msg = eacs__('Admin color scheme import failed.');
					$err = true;
					break;

				case 4:
					$msg = eacs__('Invalid EACS XML file selected for import.');
					$err = true;
					break;

				case 5:
					$msg = eacs__('Server config error. (PHP SimpleXML extension not available)');
					$err = true;
					break;
			}

			if(isset($msg)) add_settings_error('eacs_errors', 'eacs', $msg, ($err?'error':'updated'));
		}

		if(isset($_GET['settings-updated']) && !count(get_settings_errors('eacs_errors'))) {
			add_settings_error('eacs_errors', 'eacs', eacs__('Settings saved.'), 'updated');
		}

		settings_errors('eacs_errors');
	}
}

# We call this to output the contents of a color scheme
function eacs_scheme_output_action() {
	if(EACS_CURRENT_PAGE == 'edit' && isset($_GET['post']) && 0 < ($id = (int)$_GET['post']) && is_object($post=&get_post($id)) && $post->post_type == EACS_SLUG) {
		header('Content-type: text/css');
		exit(str_replace(array('[primarycolor1]', '[primarycolor2]', '[primarycolor3]', '[primarycolor4]'), eacs_return_valid_colors_array($post->ID), $post->post_content));
	}
}

# We call this to activate a scheme on a specific user account
function eacs_scheme_activate_action() {
	global $_wp_admin_css_colors;

	if(EACS_CURRENT_PAGE == 'edit' && isset($_GET['post']) && 0 < ($id = (int)$_GET['post']) && is_object($post=&get_post($id)) && $post->post_type == EACS_SLUG && isset($_wp_admin_css_colors[sanitize_title("eacs-{$post->ID}")])) {
		check_admin_referer(sprintf('eacsactivate-%s_%d', $post->post_type, $post->ID));
		update_user_option(get_current_user_id(), 'admin_color', sanitize_title("eacs-{$post->ID}"), true);
		wp_redirect(admin_url(sprintf('edit.php?post_type=%s&message=1', $post->post_type)));
		exit;
	}
}

# Customize the add/edit scheme page name placeholder
function eacs_enter_title_here($string) {
	global $current_screen;

	if(isset($current_screen->post_type) && $current_screen->post_type == EACS_SLUG) {
		$string = eacs__('Enter name here');
	}

	return $string;
}

# Customize the add/edit scheme page CSS textarea
function eacs_the_editor($html) {
	global $current_screen;

	if(isset($current_screen->post_type) && $current_screen->post_type == EACS_SLUG) {
		$html = str_replace('<textarea', sprintf('<label for="content" id="content-prompt-text" class="hide-if-no-js">%s</label><textarea spellcheck="false" wrap="off"', eacs__('Enter CSS here')), $html);
	}

	return $html;
}

# Add our custom post meta boxes to the add/edit scheme page
function eacs_post_meta_boxes() {
	remove_meta_box('slugdiv', EACS_SLUG, 'normal');
	add_meta_box('submitdiv', eacs__('Save'), 'eacs_post_save_box', EACS_SLUG, 'side', 'high');
	add_meta_box('eacs-primarycolors', eacs__('Primary Colors'), 'eacs_scheme_colors_box', EACS_SLUG, 'side', 'high');
	add_meta_box('postexcerpt', eacs__('Description'), 'eacs_scheme_desc_box', EACS_SLUG, 'side');
	add_meta_box('authordiv', eacs__('Author'), 'post_author_meta_box', EACS_SLUG, 'side');
	add_meta_box('eacs-preview', eacs__('Preview'), 'eacs_scheme_preview_box', EACS_SLUG, 'normal');
}

# Set which meta boxes are hidden by default on the add/edit scheme page
function eacs_default_hidden_meta_boxes($hidden) {
	global $current_screen;

	if(isset($current_screen->post_type) && $current_screen->post_type == EACS_SLUG) {
		if(false != ($key = array_search('postexcerpt', $hidden))) unset($hidden[$key]);
		if(!in_array('authordiv', $hidden)) $hidden[] = 'authordiv';
	}

	return $hidden;
}

# The save meta box for the add/edit scheme page
function eacs_post_save_box() {
	global $post, $current_screen;

	?><div class="submitbox" id="submitpost">
		<div id="major-publishing-actions"><?php
			if(isset($current_screen->action) && $current_screen->action != 'add' && isset($post->ID) && $post->ID != 0) {
				?><div id="delete-action">
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID) ?>"><?php eacs_e('Move to Trash') ?></a>
				</div><?php
			}

			?><div id="publishing-action">
				<img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')) ?>" id="ajax-loading" style="visibility:hidden" />
				<input name="original_publish" type="hidden" id="original_publish" value="<?php eacs_e('Save') ?>" />
				<input name="publish" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php eacs_e('Save') ?>" />
			</div>

			<div class="clear"></div>
		</div>
	</div><?php
}

# The primary colors meta box for the add/edit scheme page
function eacs_scheme_colors_box() {
	global $post;
	$colors = eacs_return_valid_colors_array();
	$title = eacs__('Choose a color');

	?><p>
		<input type="text" name="_eacs_colors[]" id="priclr1" value="<?php echo $colors[0] ?>" size="7" maxlength="7" title="<?php echo $title ?>" class="code" />
		<input type="text" name="_eacs_colors[]" id="priclr2" value="<?php echo $colors[1] ?>" size="7" maxlength="7" title="<?php echo $title ?>" class="code" />
		<input type="text" name="_eacs_colors[]" id="priclr3" value="<?php echo $colors[2] ?>" size="7" maxlength="7" title="<?php echo $title ?>" class="code" />
		<input type="text" name="_eacs_colors[]" id="priclr4" value="<?php echo $colors[3] ?>" size="7" maxlength="7" title="<?php echo $title ?>" class="code" />
	</p>

	<p><?php eacs_e('The primary colors are displayed along with an admin color scheme as a hint to users as to what the scheme looks like.') ?></p>
	<hr/>
	<p><?php eacs_e('You can also use the primary colors inside the scheme CSS via the following shortcodes:') ?></p>
	<p id="shortcodes"><code>[primarycolor1]</code><code>[primarycolor2]</code><br/><code>[primarycolor3]</code><code>[primarycolor4]</code></p>
	<p><?php eacs_e('The shortcodes will be replaced by the actual CSS colors inside the scheme CSS.') ?></p><?php
}

# The description meta box for the add/edit scheme page
function eacs_scheme_desc_box() {
	global $post;

	?><label class="screen-reader-text" for="excerpt"><?php eacs_e('Description') ?></label>
	<textarea rows="5" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt ?></textarea>
	<p><?php eacs_e('Short description of this admin color scheme. <em class="nonessential">(Optional)</em>') ?></p><?php
}

# The preview meta box for the add/edit scheme page
function eacs_scheme_preview_box() {
	?><p id="previewjsnote"><strong><?php eacs_e('JavaScript must be enabled in your browser for the preview to work.') ?></strong></p><?php
}

# When a scheme is inserted into the database update the primary colors meta field
function eacs_wp_insert_post_data($data, $postarr) {
	if($postarr['post_type'] == EACS_SLUG && $postarr['post_status'] == 'publish' && isset($_POST['_eacs_colors'])) {
		$colors = array_fill(0, 4, '#ffffff');

		if(isset($postarr['_eacs_colors']) && is_array($c=$postarr['_eacs_colors'])) {
			foreach($colors as $key=>$val) {
				if(isset($c[$key]) && '' != ($clr=trim($c[$key])) && preg_match('/^#[a-f0-9]{6}$/i', $clr)) {
					$colors[$key] = $clr;
				}
			}
		}

		update_post_meta($postarr['ID'], '_eacs_colors', $colors);
	}

	return $data;
}

# Unset the "editor" post type feature right before the post content input area is output so it will not be output. We will do our own magic instead
function eacs_edit_form_after_title() {
	global $_wp_post_type_features;
	unset($_wp_post_type_features[EACS_SLUG]['editor']);
}

# Re-add the "editor" post type feature that we unset before and then output our own custom content input area
function eacs_edit_form_after_editor() {
	global $_wp_post_type_features, $post;
	$_wp_post_type_features[EACS_SLUG]['editor'] = true;

	?><div id="postdivrich" class="postarea edit-form-section">
		<?php wp_editor($post->post_content, 'content', array('media_buttons' => false, 'tinymce' => false, 'quicktags' => false, 'editor_height' => 360)); ?>
	</div><?php
}

# Remove some unneeded things from the footer on the add/edit scheme page
function eacs_remove_unneeded_admin_footer_stuff() {
	remove_action('admin_print_footer_scripts', array('_WP_Editors', 'editor_js'), 50);
	remove_action('admin_footer', array('_WP_Editors', 'enqueue_scripts'), 1);
	remove_action('admin_footer', 'wp_print_media_templates');

	wp_dequeue_script('autosave');
	wp_dequeue_script('schedule');
	wp_dequeue_script('media-editor');
}

# Setup the sort order on the view all schemes page
function eacs_pre_get_posts($query) {
	if($query->get('post_type') == EACS_SLUG && $query->get('orderby') == '') {
		$query->set('orderby', 'title');
		$query->set('order', 'ASC');
	}
}

# Add custom messages to be displayed on the add/edit and view all schemes pages when a scheme is saved
function eacs_post_updated_messages($messages) {
	$messages[EACS_SLUG] = array_fill(1, 10, eacs__('Admin color scheme saved.'));
	return $messages;
}

# Set the allowed views for schemes on the view all schemes page
function eacs_views($views) {
	return array_intersect_key($views, array('all' => null, 'mine' => null, 'trash' => null));
}

# Set the allowed bulk actions for schemes on the view all schemes page
function eacs_bulk_actions($actions) {
	return array_intersect_key($actions, array('trash' => null, 'untrash' => null, 'delete' => null));
}

# Set the visible table columns on the view all schemes page
function eacs_edit_columns($columns) {
	return array(
		'cb' => '<input type="checkbox" />',
		'name' => eacs__('Name'),
		'description' => eacs__('Description'),
		'author' => eacs__('Author'),
		'dated' => eacs__('Date')
	);
}

# Set which table columns can be sorted on the view all schemes page
function eacs_sortable_columns($columns) {
	$columns['name'] = array('title', true);
	$columns['dated'] = 'date';
	return $columns;
}

# This is called when the table columns on the view all schemes page are output
function eacs_custom_columns($column) {
	global $post;

	switch ($column) {
		case 'name':
			$scheme_name = sanitize_title("eacs-{$post->ID}");
			$pto = get_post_type_object($post->post_type);
			$can_edit = current_user_can($pto->cap->edit_post, $post->ID);
			$edit_link = esc_url(get_edit_post_link($post->ID));
			$is_trash = $post->post_status == 'trash';
			$is_active = $scheme_name == trim(get_user_option('admin_color'));
			$is_default = $scheme_name == EACS_OPT_DEFAULT;
			$is_forced = $scheme_name == EACS_OPT_FORCED;

			?><strong>
				<span class="eacs-schemes-list-colors"><?php
					foreach(eacs_return_valid_colors_array() as $color) echo "<span style=\"background-color:$color\">&zwj;</span>";
				?></span><?php

				if($can_edit && !$is_trash)
					printf('<a href="%s" title="%s" class="row-title">', $edit_link, esc_attr(eacs__('Edit this admin color scheme')));

				echo _draft_or_post_title();

				if($can_edit && !$is_trash) echo '</a>';
			?></strong>

			<?php if(!$is_trash && ($is_active || $is_default || $is_forced)) {
				$tags = array();
				$url = admin_url('edit.php?post_type=' . EACS_SLUG . '&page=eacs-settings');
				$can_manage_settings = current_user_can(EACS_CAP_MANAGE_SETTINGS);

				if($is_active)
					$tags[] = sprintf('<span title="%s">%s</span>', esc_attr(eacs__('This admin color scheme is currently active for your account')), esc_attr(eacs__('[Active]')));

				if($is_default)
					$tags[] = sprintf('<%1$s%2$s title="%3$s">%4$s</%1$s>', ($can_manage_settings ? 'a' : 'span'), ($can_manage_settings ? ' href="' . $url . '#eacs_default"' : ''), esc_attr(eacs__('This admin color scheme is currently the site-wide default')), esc_attr(eacs__('[Default]')));

				if($is_forced)
					$tags[] = sprintf('<%1$s%2$s title="%3$s">%4$s</%1$s>', ($can_manage_settings ? 'a' : 'span'), ($can_manage_settings ? ' href="' . $url . '#eacs_forced"' : ''), esc_attr(eacs__('This admin color scheme is currently being forced on all users site-wide')), esc_attr(eacs__('[Forced]')));

				printf('<small>%s</small>', implode('&nbsp;', $tags));
			} ?>

			<div class="row-actions<?php echo !$is_trash?' can-preview':'' ?>"><?php
				$actions = array();

				if(!$is_trash) {
					if(EACS_OPT_FORCED == '' && !$is_active)
						$actions['activate'] = sprintf('<a href="%s" title="%s">%s</a>', esc_url(wp_nonce_url(admin_url(sprintf($pto->_edit_link . '&action=eacs-activate', $post->ID)), sprintf('eacsactivate-%s_%d', $post->post_type, $post->ID))), esc_attr(eacs__('Activate this admin color scheme for your account')), eacs__('Activate'));

					if($can_edit)
						$actions['edit'] = sprintf('<a href="%s" title="%s">%s</a>', $edit_link, esc_attr(eacs__('Edit this admin color scheme')), eacs__('Edit'));

					if(current_user_can($pto->cap->export_scheme, $post->ID))
						$actions['export'] = sprintf('<a href="%s" title="%s">%s</a>', esc_url(admin_url(sprintf('edit.php?post_type=%s&post=%d&page=eacs-export', EACS_SLUG, $post->ID))), esc_attr(eacs__('Export this admin color scheme')), eacs__('Export'));
				}

				if(current_user_can($pto->cap->delete_post, $post->ID)) {
					if($is_trash)
						$actions['untrash'] = sprintf('<a href="%s" title="%s">%s</a>', esc_url(wp_nonce_url(admin_url(sprintf($pto->_edit_link . '&action=untrash', $post->ID)), sprintf('untrash-%s_%d', $post->post_type, $post->ID))), esc_attr(eacs__('Restore this admin color scheme from the Trash')), eacs__('Restore'));
					elseif(EMPTY_TRASH_DAYS)
						$actions['trash'] = sprintf('<a href="%s" title="%s" class="submitdelete">%s</a>', esc_url(get_delete_post_link($post->ID)), esc_attr(eacs__('Move this admin color scheme to the Trash')), eacs__('Trash'));

					if($is_trash || !EMPTY_TRASH_DAYS)
						$actions['delete'] = sprintf('<a href="%s" title="%s" class="submitdelete">%s</a>', esc_url(get_delete_post_link($post->ID, '', true)), esc_attr(eacs__('Delete this admin color scheme permanently')), eacs__('Delete Permanently'));
				}

				if(!empty($actions)) {
					$last = end((array_keys($actions)));
					foreach($actions as $act=>$lnk) echo sprintf('<span class="%s">%s</span>', $act, $lnk.($act==$last?'':' | '));
				}
			?></div><?php
			break;

		case 'description':
			echo esc_html(convert_chars(wptexturize($post->post_excerpt)));
			break;

		case 'dated':
			$time = get_post_modified_time('G', true, $post);
			printf('<abbr title="%s">%s</abbr><br />%s', esc_attr(get_the_modified_time(eacs__('Y/m/d g:i:s A'))), (($time_diff=time()-$time) > 0 && $time_diff < 24*60*60 ? sprintf(eacs__('%s ago'), human_time_diff($time)) : mysql2date(eacs__('Y/m/d'), $post->post_modified)), eacs__('Last Modified'));
			break;
	}
}

# Outputs the scheme import page
function eacs_output_import_admin_page() {
	$bytes = apply_filters('import_upload_size_limit', wp_max_upload_size());

	?><div class="wrap">
		<?php screen_icon() ?>
		<h2><?php eacs_e('Import Admin Color Scheme') ?></h2>

		<div class="narrow">
			<p><?php eacs_e('Choose a EACS XML file to upload, then click Upload File and Import.') ?></p>

			<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($_SERVER['REQUEST_URI'], 'eacs-import-upload')) ?>">
				<p>
					<label for="upload"><?php eacs_e('Choose a file from your computer:') ?></label> (<?php printf(eacs__('Maximum size: %s'), wp_convert_bytes_to_hr($bytes)) ?>)
					<input type="hidden" name="max_file_size" value="<?php echo $bytes ?>" />
					<input type="file" id="upload" name="import" size="25" />
				</p>

				<?php submit_button(eacs__('Upload File and Import'), 'secondary') ?>
			</form>
		</div>
	</div><?php
}

# This actually does the import of the scheme the user uploads
function eacs_do_import() {
	if(!current_user_can(EACS_CAP_IMPORT_SCHEMES)) return;

	if(isset($_FILES['import']['size']) && $_FILES['import']['size'] > 0 && @file_exists($_FILES['import']['tmp_name'])) {
		$url = admin_url('edit.php?post_type=' . EACS_SLUG);

		if(function_exists('simplexml_load_file')) {

			$errors = libxml_use_internal_errors(true);
			$xml = simplexml_load_file($_FILES['import']['tmp_name'], 'SimpleXMLElement', LIBXML_NOCDATA);
			libxml_use_internal_errors($errors);

			if($xml !== false && is_object($xml) && isset($xml->easy_admin_color_scheme) && is_object($p = $xml->easy_admin_color_scheme) && isset($p->name) && '' != ($name = trim((string)$p->name)) && isset($p->css) && '' != ($css = trim((string)$p->css))) {
				function unescape($str) {
					return str_replace(array('\r\n', '\n', '\t') , array("\n", "\n", "\t"), $str);
				}

				if( 0 < ($id = wp_insert_post(array('post_type' => EACS_SLUG, 'post_status' => 'publish', 'post_title' => $name, 'post_excerpt' => (isset($p->desc) ? trim(unescape((string)$p->desc)) : ''), 'post_content' => unescape($css))))) {
					$colors = array_fill(0, 4, '#ffffff');

					if(isset($p->colors) && is_object($c = $p->colors)) {
						foreach($colors as $key => $val) {
							$color = 'color' . ($key + 1);
							if(isset($c->$color) && '' != ($clr = trim((string)$c->$color)) && preg_match('/^#[a-f0-9]{6}$/i', $clr))
								$colors[$key] = $clr;
						}
					}

					update_post_meta($id, '_eacs_colors', $colors);

					$url .= '&message=2';
				} else {
					$url .= '&page=eacs-import&message=3';
				}
			} else {
				$url .= '&page=eacs-import&message=4';
			}
		} else {
			$url .= '&page=eacs-import&message=5';
		}

		wp_redirect($url);
		exit;
	}
}

# Outputs the scheme export page
function eacs_output_export_admin_page() {
	global $post;

	?><div class="wrap">
		<?php screen_icon() ?>
		<h2><?php eacs_e('Export Admin Color Scheme') ?></h2>
		<p><?php printf(eacs__('Select how you would like to export the &ldquo;<strong>%s</strong>&rdquo; admin color scheme.'), _draft_or_post_title()) ?></p>

		<form action="" method="post" id="export-filters">
			<ul>
				<li>
					<input type="radio" name="method" id="method1" value="xml" checked="checked" />
					<label for="method1">
						<strong><?php eacs_e('EACS XML File') ?></strong><br/>
						<span class="description"><?php eacs_e('This method exports the admin color scheme as a special XML file which allows it to be imported back into the Easy Admin Color Schemes plugin (e.g., on another WordPress site).') ?></span>
					</label>
				</li>

				<li>
					<input type="radio" name="method" id="method2" value="php" />
					<label for="method2">
						<strong><?php eacs_e('WordPress Plugin') ?></strong><br/>
						<span class="description"><?php eacs_e('This method exports the admin color scheme as its own WordPress plugin so it can be used by itself on any WordPress site (i.e., without requiring the Easy Admin Color Schemes plugin).') ?></span>
					</label>
				</li>
			</ul>

			<?php submit_button(eacs__('Download Export File'), 'primary') ?>
		</form>
	</div><?php
}

# Make sure its a valid scheme they're trying to export and call do_export() if needed
function eacs_before_export_page_load() {
	if(isset($_GET['post']) && 0 < ($id=(int)trim($_GET['post']))) {
		global $post;
		$post = get_post($id);

		if($post->post_type == EACS_SLUG) {
			if(current_user_can(EACS_CAP_EXPORT_SCHEME, $id)) {
				if($post->post_status != 'trash') {
					if(isset($_POST['method']) && ('xml' == ($method=strtolower($_POST['method'])) || $method == 'php')) {
						eacs_do_export($method);
						exit;
					} else {
						return;
					}
				} else {
					wp_die(eacs__('You can&rsquo;t export this item because it is in the Trash. Please restore it and try again.'));
				}
			} else {
				wp_die(eacs__('You are not allowed to export this item.'));
			}
		} else {
			wp_die(eacs__('You must specify a valid item to be exported!'));
		}
	} else {
		wp_die(eacs__('You must specify a valid item to be exported!'));
	}
}

# This actually does the export of the scheme the user picks
function eacs_do_export($method = false) {
	global $post;

	if(($method == 'xml' || $method == 'php') && isset($post->ID)) {
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=easy_admin_color_schemes_export.' . $method);
		header(sprintf('Content-Type: %s; charset=%s', ($method == 'xml' ? 'text/xml' : 'application/x-httpd-php'), get_option('blog_charset')), true);

		$colors = eacs_return_valid_colors_array();

		if($method == 'xml') {
			function escape_xml_cdata($string) {
				return str_replace(array("\r\n", "\n", "\t"), array("\n", '\n', '\t'), !seems_utf8($string = trim(apply_filters('the_content_export', $string))) ? utf8_encode($string) : $string) . (substr($string, -1) == ']' ? ' ' : '');
			}

			printf(
				'<?xml version="1.0" encoding="UTF-8"?>%11$s<easy_admin_color_schemes_export version="1.0">%11$s%12$s<easy_admin_color_scheme>%11$s%12$s%12$s<name>%1$s</name>%11$s%12$s%12$s<desc><![CDATA[%2$s]]></desc>%11$s%12$s%12$s<colors>%11$s%12$s%12$s%12$s<color1>%3$s</color1>%11$s%12$s%12$s%12$s<color2>%4$s</color2>%11$s%12$s%12$s%12$s<color3>%5$s</color3>%11$s%12$s%12$s%12$s<color4>%6$s</color4>%11$s%12$s%12$s</colors>%11$s%12$s%12$s<css><![CDATA[%7$s]]></css>%11$s%12$s%12$s<author>%8$s</author>%11$s%12$s%12$s<date>%11$s%12$s%12$s%12$s<created>%9$s</created>%11$s%12$s%12$s%12$s<modified>%10$s</modified>%11$s%12$s%12$s</date>%11$s%12$s</easy_admin_color_scheme>%11$s</easy_admin_color_schemes_export>',
				apply_filters('the_title_rss', trim(_draft_or_post_title())),
				escape_xml_cdata(trim($post->post_excerpt)),
				trim($colors[0]),
				trim($colors[1]),
				trim($colors[2]),
				trim($colors[3]),
				escape_xml_cdata(trim($post->post_content)),
				trim(get_the_author_meta('login', $post->post_author)),
				trim($post->post_date),
				trim($post->post_modified),
				PHP_EOL,
				"\t"
			);
		} elseif($method == 'php') {
			printf(
				'<?php%8$s/*%8$sPlugin Name: Admin Color Scheme: %2$s%8$sDescription: This plugin adds the <strong>%2$s</strong> admin color scheme.&emsp;<em>&mdash; This plugin was generated by the <a href="http://JamesDimick.com/easy-admin-color-schemes">Easy Admin Color Schemes</a> WordPress plugin.</em>%8$s*/%8$s%8$sfunction eacs_%1$s_css() {%8$s%9$s?>%8$s%8$s%7$s%8$s%8$s%9$s<?php%8$s}%8$s%8$sadd_action(\'admin_init\', create_function(\'\',\'wp_admin_css_color("EACS-%1$s","%2$s",admin_url("admin.php?action=EACS-%1$s"),array("%3$s","%4$s","%5$s","%6$s"));\'));%8$sadd_action(\'admin_action_EACS-%1$s\', create_function(\'\',\'header("Content-type: text/css");eacs_%1$s_css();exit;\'));%8$s?>',
				rand(10000, 99999),
				addslashes(trim(_draft_or_post_title())),
				addslashes(trim($colors[0])),
				addslashes(trim($colors[1])),
				addslashes(trim($colors[2])),
				addslashes(trim($colors[3])),
				str_replace(array('[primarycolor1]', '[primarycolor2]', '[primarycolor3]', '[primarycolor4]'), eacs_return_valid_colors_array($post->ID), trim($post->post_content)),
				PHP_EOL,
				"\t"
			);
		}

		exit;
	}
}

# Makes sure the proper menu item is selected when accessing the export scheme page directly
function eacs_export_admin_menu_parent_file($parent) {
	global $submenu;

	$p = 'edit.php?post_type=' . EACS_SLUG;

	if($p == $parent && isset($_GET['page']) && $_GET['page'] == 'eacs-export') {
		global $submenu_file;
		$submenu_file = 'edit.php?post_type=' . EACS_SLUG;
	}

	foreach($submenu[$p] as $key => $val) {
		if(array_search('eacs-export', $val)) {
			unset($submenu[$p][$key]);
			break;
		}
	}

	return $parent;
}

# Outputs the plugin settings page
function eacs_output_settings_admin_page() {
	global $_wp_admin_css_colors;

	$cols = array(); foreach($_wp_admin_css_colors as $scheme => $details) $cols[$scheme] = strtolower($details->name);
	array_multisort($cols, SORT_ASC, SORT_STRING, $_wp_admin_css_colors);

	?><div class="wrap">
		<?php screen_icon() ?>
		<h2><?php eacs_e('Admin Color Schemes Settings') ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields('eacs_options') ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="eacs_default"><?php eacs_e('Default Admin Color Scheme') ?></label></th>

					<td>
						<select name="eacs_default" id="eacs_default"><?php
							foreach($_wp_admin_css_colors as $color => $color_info) {
								echo '<option value="' . $color . '"' . (EACS_OPT_DEFAULT == $color ? ' selected="selected"' : '') . '>' . $color_info->name . ($color == 'fresh' ? eacs__(' (WordPress Default)') : '') . '</option>';
							}
						?></select>

						<span class="description"><?php eacs_e('This is the site-wide default admin color scheme all new users will receive.') ?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="eacs_forced"><?php eacs_e('Forced Admin Color Scheme') ?></label></th>

					<td>
						<select name="eacs_forced" id="eacs_forced">
							<option value="_"<?php echo EACS_OPT_FORCED == '' || !isset($_wp_admin_css_colors[EACS_OPT_FORCED]) ? ' selected="selected"' : '' ?>><?php eacs_e('&mdash; None &mdash;') ?></option><?php

							foreach($_wp_admin_css_colors as $color => $color_info) {
								echo '<option value="' . $color . '"' . (EACS_OPT_FORCED == $color ? ' selected="selected"' : '') . '>' . $color_info->name . '</option>';
							}

						?></select>

						<span class="description"><?php eacs_e('This admin color scheme is forced on all users site-wide.') ?></span>
					</td>
				</tr>

				<tr valign="top"><td colspan="2"><hr style="opacity:.2;background-color:#000;height:1px;border:0" /></td></tr>

				<tr valign="top" id="support-the-author-msg">
					<th scope="row"><?php eacs_e('Plugin Author Support Notice') ?></th>

					<td>
						<label for="eacs_showauthorsupportmsg">
							<input type="checkbox" name="eacs_showauthorsupportmsg" id="eacs_showauthorsupportmsg" value="1" <?php checked('1', EACS_OPT_SHOW_AUTHOR_SUPPORT_MSG) ?> />
							<?php eacs_e('Show the &ldquo;<em>Support the Author</em>&rdquo; notice on all plugin pages') ?>
						</label>
					</td>
				</tr>
			</table>

			<?php submit_button(eacs__('Save Changes'), 'primary') ?>
		</form>
	</div><?php
}

# This is called when the settings page is submitted to make sure the default scheme option is valid
function eacs_validate_option_default($input) {
	global $_wp_admin_css_colors;

	if('' != ($i = sanitize_title(trim($input))) && isset($_wp_admin_css_colors[$i])) {
		$out = $i;
	} else {
		add_settings_error('eacs_errors', 'eacs_options_default_invalid', eacs__('Invalid admin color scheme selected.'));
		$out = EACS_OPT_DEFAULT;
	}

	return $out;
}

# This is called when the settings page is submitted to make sure the forced scheme option is valid
function eacs_validate_option_forced($input) {
	global $_wp_admin_css_colors;

	$in = sanitize_title(trim($input));

	if($in == '_') {
		$out = '';
	} elseif($in != '' && isset($_wp_admin_css_colors[$in])) {
		$out = $in;
	} else {
		add_settings_error('eacs_errors', 'eacs_options_forced_invalid', eacs__('Invalid admin color scheme selected.'));
		$out = EACS_OPT_FORCED;
	}

	return $out;
}

# This is called when the settings page is submitted to make sure the support the author option is valid
function eacs_validate_option_showauthorsupportmsg($input) {
	if('' == ($in=trim($input)) || $in == '1') {
		$out = $in;
	} else {
		add_settings_error('eacs_errors', 'eacs_options_showauthorsupportmsg_invalid', eacs__('Support the Author option value invalid.'));
		$out = '1';
	}

	return $out;
}

# Enforces the default scheme option (if set) when a new user is registered
function eacs_do_default_scheme($user_id) {
	global $_wp_admin_css_colors;

	if(EACS_OPT_DEFAULT != '' && isset($_wp_admin_css_colors[EACS_OPT_DEFAULT])) {
		update_user_option($user_id, 'admin_color', EACS_OPT_DEFAULT, true);
	}
}

# Enforces the forced scheme option (if set) when any admin page is loaded
function eacs_do_forced_scheme() {
	global $_wp_admin_css_colors;

	if(EACS_OPT_FORCED != '' && isset($_wp_admin_css_colors[EACS_OPT_FORCED])) {
		update_user_option(get_current_user_id(), 'admin_color', EACS_OPT_FORCED, true);
	}
}

# Replace the default profile page scheme picker with a custom one
function eacs_profile_page_scheme_picker() {
	global $_wp_admin_css_colors, $user_id;

	?><fieldset>
		<legend class="screen-reader-text"><span><?php eacs_e('Admin Color Scheme') ?></span></legend><?php

		$current = EACS_OPT_FORCED != '' && isset($_wp_admin_css_colors[EACS_OPT_FORCED]) ? EACS_OPT_FORCED : ('' != ($opt=trim(get_user_option('admin_color', $user_id))) ? $opt : (isset($_wp_admin_css_colors[EACS_OPT_DEFAULT]) ? EACS_OPT_DEFAULT : 'fresh'));

		foreach($_wp_admin_css_colors as $color => $color_info) {
			$disabled = EACS_OPT_FORCED != '' && isset($_wp_admin_css_colors[EACS_OPT_FORCED]) && EACS_OPT_FORCED != $color;

			?><div class="color-option"<?php echo $disabled?' style="opacity:0.5"':'' ?>>
				<input name="admin_color" id="admin_color_<?php echo $color ?>" type="radio" value="<?php echo esc_attr($color) ?>" class="tog" <?php checked($color, $current); echo $disabled?' disabled="disabled"':'' ?> />

				<table class="color-palette">
					<tr><?php foreach($color_info->colors as $html_color) echo "<td style=\"background-color:$html_color\">&nbsp;</td>"; ?></tr>
				</table>

				<label for="admin_color_<?php echo $color ?>"><?php echo $color_info->name ?></label>
			</div><?php
		}
	?></fieldset><?php
}

# Returns a valid scheme primary colors array
function eacs_return_valid_colors_array($id = 0) {
	if((int)$id <= 0) {
		global $post;
		$id = isset($post->ID) && $post->ID > 0 ? $post->ID : 0;
	}

	$colors = get_post_meta((int)$id, '_eacs_colors', true);
	$blank = '#ffffff';

	if(is_array($colors) && !empty($colors)) {
		$pattern = '/^#[a-f0-9]{6}$/i';
		$colors[0] = isset($colors[0]) && preg_match($pattern, $colors[0]) ? $colors[0] : $blank;
		$colors[1] = isset($colors[1]) && preg_match($pattern, $colors[1]) ? $colors[1] : $blank;
		$colors[2] = isset($colors[2]) && preg_match($pattern, $colors[2]) ? $colors[2] : $blank;
		$colors[3] = isset($colors[3]) && preg_match($pattern, $colors[3]) ? $colors[3] : $blank;
	} else {
		$colors = array_fill(0, 4, $blank);
	}

	return $colors;
}

# Gets the default scheme plugin option
function eacs_get_option_default() {
	$default = trim(get_site_option('eacs_default', 'fresh'));
	return !empty($default) ? $default : 'fresh';
}

# Gets the forced scheme plugin option
function eacs_get_option_forced() {
	return trim(get_site_option('eacs_forced', ''));
}

# Gets the option for whether to show the support the author message
function eacs_get_option_showauthorsupportmsg() {
	return false === ($val=get_site_option('eacs_showauthorsupportmsg')) || $val != '' ? '1' : '0';
}

# Gets the current plugin page (if any) the user is on
function eacs_get_current_plugin_page() {
	global $pagenow;
	$page = false;

	if(((isset($_REQUEST['post_type']) && trim($_REQUEST['post_type']) == EACS_SLUG) || (isset($_GET['post']) && 0 < ($id=(int)$_GET['post']) && is_object($post=&get_post($id)) && isset($post->post_type) && $post->post_type == EACS_SLUG)) && isset($pagenow) && !empty($pagenow)) {
		switch($pagenow) {
			case 'edit.php':
				if(isset($_GET['page']) && '' != ($p=trim($_GET['page']))) {
					switch($p) {
						case 'eacs-import':
							$page = 'import';
							break;

						case 'eacs-export':
							$page = 'export';
							break;

						case 'eacs-settings':
							$page = 'settings';
							break;
					}
				} else {
					$page = 'view';
				}
				break;

			case 'post.php':
				$page = 'edit';
				break;

			case 'post-new.php':
				$page = 'add';
				break;
		}
	}

	return $page;
}

# Custom versions of the translation functions with the text domain pre-filled
function eacs__($string) { return __($string, EACS_TEXT_DOMAIN); }
function eacs_e($string) { echo eacs__($string); }
function eacs_x($text, $context) { return _x($text, $context, EACS_TEXT_DOMAIN); }