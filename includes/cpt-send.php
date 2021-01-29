<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Register Transfer CPT
 */
function wpsend_transfer_cpt() {
	$labels = [
		'name'                  => _x('Transfers', 'Post Type General Name', 'wp-send'),
		'singular_name'         => _x('Transfer', 'Post Type Singular Name', 'wp-send'),
		'menu_name'             => __('WP Send', 'wp-send'),
		'name_admin_bar'        => __('Transfer', 'wp-send'),
		'archives'              => __('Transfer Archives', 'wp-send'),
		'attributes'            => __('Transfer Attributes', 'wp-send'),
		'parent_item_colon'     => __('Parent Transfer:', 'wp-send'),
		'all_items'             => __('All Transfers', 'wp-send'),
		'add_new_item'          => __('Add New Transfer', 'wp-send'),
		'add_new'               => __('Add New', 'wp-send'),
		'new_item'              => __('New Transfer', 'wp-send'),
		'edit_item'             => __('Edit Transfer', 'wp-send'),
		'update_item'           => __('Update Transfer', 'wp-send'),
		'view_item'             => __('View Transfer', 'wp-send'),
		'view_items'            => __('View Transfers', 'wp-send'),
		'search_items'          => __('Search Transfer', 'wp-send'),
		'not_found'             => __('Not found', 'wp-send'),
		'not_found_in_trash'    => __('Not found in Trash', 'wp-send'),
		'featured_image'        => __('Featured Image', 'wp-send'),
		'set_featured_image'    => __('Set featured image', 'wp-send'),
		'remove_featured_image' => __('Remove featured image', 'wp-send'),
		'use_featured_image'    => __('Use as featured image', 'wp-send'),
		'insert_into_item'      => __('Insert into Transfer', 'wp-send'),
		'uploaded_to_this_item' => __('Uploaded to this Transfer', 'wp-send'),
		'items_list'            => __('Transfers list', 'wp-send'),
		'items_list_navigation' => __('Transfers list navigation', 'wp-send'),
		'filter_items_list'     => __('Filter Transfers list', 'wp-send')
	];

    $args = [
		'label'                 => __('Transfer', 'wp-send'),
		'description'           => __('A Transfer', 'wp-send'),
		'labels'                => $labels,
		'supports'              => ['title', 'editor', 'author', 'custom-fields'],
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-airplane',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => false,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => false,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'capabilities' => [
			'create_posts' => 'do_not_allow', // Removes support for the "Add New" function
		],
		'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
	];

    register_post_type(WPSEND_CPT, $args);
}

add_action('init', 'wpsend_transfer_cpt', 0);



/**
 * Custom thumbnail and video columns
 */
function wpsend_admin_columns($defaults) {
    $defaults['wpsend_expiry_date'] = 'Expiry Date';

    return $defaults;
}

function wpsend_admin_custom_columns($column_name, $id) {
    if ($column_name === 'wpsend_expiry_date') {
        echo 'Expires<br>' . date_i18n(get_option('date_format'), strtotime(get_post_meta($id, 'wpsend_expiry_date', true)));
    }
}

add_filter('manage_' . WPSEND_CPT . '_posts_columns', 'wpsend_admin_columns', 5);
add_action('manage_' . WPSEND_CPT . '_posts_custom_column', 'wpsend_admin_custom_columns', 5, 2);
