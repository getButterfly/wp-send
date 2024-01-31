<?php
/*
Plugin Name: WP Send
Plugin URI: https://getbutterfly.com/product/wp-send/
Description: WP Send is the simplest way to send your files around the world. Share large files with configurable filesizes and expiry dates.
Author: Ciprian Popescu
Author URI: https://getbutterfly.com/
Version: 1.0.5
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wp-send

WP Send
Copyright (C) 2011-2023 Ciprian Popescu (getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('WPSEND_VERSION', '1.0.5');
define('WPSEND_CPT', 'transfer');



// Plugin initialization
function wpsend_init() {
	load_plugin_textdomain('wp-send', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'wpsend_init');

include plugin_dir_path(__FILE__) . '/includes/cpt-send.php';
include plugin_dir_path(__FILE__) . '/includes/functions.php';
include plugin_dir_path(__FILE__) . '/includes/page-options.php';



/**
 * Email debugging and shortcode workarounds
 */
$wpsend_shortcode_fix = (string) get_option('wpsend_shortcode_fix');
$wpsend_html_fix = (string) get_option('wpsend_html_fix');

if ($wpsend_shortcode_fix === 'on') {
    add_action('init', 'wpsend_shortcode_fix', 12);
}
if ($wpsend_html_fix === 'on') {
    add_filter('wp_mail_content_type', 'wpsend_set_content_type');
}



function wpsend_install() {
    // Default options
    add_option('wpsend_label_name_own', 'Your name');
    add_option('wpsend_label_email_own', 'Your email address');
    add_option('wpsend_label_email_friend', 'Your friend email address');
    add_option('wpsend_label_message', 'Email message');
    add_option('wpsend_label_cc', 'Send a copy to self');
    add_option('wpsend_label_success', 'Email sent successfully!');
    add_option('wpsend_label_submit', 'Send Files');

    add_option('wpsend_link_anchor', 'Click to see your files!');
    add_option('wpsend_redirection', 0);
    add_option('wpsend_page_thankyou', 0);

    add_option('wpsend_noreply', '');

    // Email settings
    add_option('wpsend_email_title', 'New File!');

    // Members-only settings
    add_option('wpsend_restrictions', 0);
    add_option('wpsend_restrictions_message', 'This section is restricted to members only!');

    // Send all transfers to a universal email address
    add_option('wpsend_send_behaviour', 1);
    add_option('wpsend_hardcoded_email', '');

    add_option('wpsend_shortcode_fix', 'off');
    add_option('wpsend_html_fix', 'off');
    add_option('wpsend_allow_cc', 'off');

    add_option('wpsend_use_akismet', 'false');

    add_option('wpsend_dropbox_api', '');
    add_option('wpsend_dropbox_enable', 0);
}

register_activation_hook(__FILE__, 'wpsend_install');



function wpsend_get_attachments($ecid) {
    /**
     * Get all post attachments
     */
    $output = '';

    $args = [
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => $ecid,
        'orderby' => 'post__in',
        'order' => 'ASC'
    ];

    $attachments = get_posts($args);

    if ($attachments) {
        foreach ($attachments as $a) {
            $output .= '<div>';
                $file = wp_get_attachment_url($a->ID);

                $output .= '<a href="' . esc_url_raw($file) . '">' . esc_url_raw($file) . '</a>';
            $output .= '</div>';
        }
    }

    return $output;
}



function wpsend_display_form($atts) {
    $attributes = shortcode_atts([
        'limit' => 16,
        'expire' => 7
    ], $atts);

    wpsend_impression(get_the_ID());

    $wpsend_label_submit = get_option('wpsend_label_submit');

    $wpsend_send_behaviour = get_option('wpsend_send_behaviour');
    $wpsend_link_anchor = get_option('wpsend_link_anchor');

    $wpsend_redirection = get_option('wpsend_redirection');
    $wpsend_page_thankyou = get_option('wpsend_page_thankyou');

    $wpsend_email_title = get_option('wpsend_email_title');

    // Email Designer
    $wpsend_template = '';

    if ((int) get_option('wpsend_reusable_block_id') > 0) {
        $wpSendReusableBlockId = (int) get_option('wpsend_reusable_block_id');

        $wpSendReusableBlockObject = get_post($wpSendReusableBlockId);

        $wpsend_template = apply_filters('the_content', $wpSendReusableBlockObject->post_content);
    }

    // Send routine
    if (isset($_POST['wpsend_submit'])) {
        if ((int) $wpsend_send_behaviour === 1) {
            $wpsend_to = sanitize_email($_POST['wpsend_to']);
        } else if ((int) $wpsend_send_behaviour === 0) {
            $wpsend_to = sanitize_email(get_option('wpsend_hardcoded_email'));
        }

        // Check if <Mail From> fields are filled in
        $wpsend_from = sanitize_text_field($_POST['wpsend_from']);
        $wpsend_from_email = sanitize_email($_POST['wpsend_from_email']);
        $wpsend_email_message = wpautop(stripslashes($_POST['wpsend_message']));

        $wpsend_referrer = esc_url($_POST['wpsend_referrer']);

        // Designer
        $wpsend_template = str_replace('[name]', $wpsend_from, $wpsend_template);
        $wpsend_template = str_replace('[email]', $wpsend_from_email, $wpsend_template);
        $wpsend_template = str_replace('[transfer-message]', $wpsend_email_message, $wpsend_template);
        $wpsend_template = str_replace('[transfer-referrer]', $wpsend_referrer, $wpsend_template);

        $subject = sanitize_text_field($wpsend_email_title);
        $subject = str_replace('[name]', $wpsend_from, $subject);
        $subject = str_replace('[email]', $wpsend_from_email, $subject);

        $headers[] = "Content-Type: text/html;";

		// Akismet
		$content['comment_author'] = $wpsend_from;
		$content['comment_author_email'] = $wpsend_from_email;
		$content['comment_author_url'] = home_url();
		$content['comment_content'] = $wpsend_email_message;

		if (wpsend_checkSpam($content)) {
			echo '<p><strong>' . esc_html__('Akismet prevented sending of this email and marked it as spam!', 'wp-send') . '</strong></p>';
		} else {
            /**
             * Create transfer object (custom post type)
             */
            $wpsend_post = [
                'post_title' => esc_html__('Transfer', 'wp-send') . ' (' . date('Y/m/d H:i:s') . ')',
                'post_content' => $wpsend_template,
                'post_status' => 'private',
                'post_type' => WPSEND_CPT,
                'post_author' => 1
            ];

            // Insert the email into the database
            $wpsend_id = wp_insert_post($wpsend_post);
    
            if (isset($_POST['wpsend_pick_me'])) {
                // Add featured image to post
                $wpSendPicked = (int) $_POST['wpsend_pick_me'];
            }

            add_post_meta($wpsend_id, 'wpsend_name', $wpsend_from, true);
            add_post_meta($wpsend_id, 'wpsend_email_sender', $wpsend_from_email, true);
            add_post_meta($wpsend_id, 'wpsend_email_recipient', $wpsend_to, true);
            if (isset($_POST['wpsend_allow_cc'])) {
                add_post_meta($wpsend_id, 'wpsend_email_cc', $_POST['wpsend_allow_cc'], true);
            }

            // Expiry Date
            add_post_meta($wpsend_id, 'wpsend_expiry_date', $_POST['wpsend_expiry_date']);

            // Multiple File Upload
            if (!empty($_FILES['upload_attachment']['name'][0])) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';

                $files = $_FILES['upload_attachment'];
                $count = 0;

                foreach ($files['name'] as $count => $value) {
                    if ($files['name'][$count]) {
                        $file = [
                            'name'     => $files['name'][$count],
                            'type'     => $files['type'][$count],
                            'tmp_name' => $files['tmp_name'][$count],
                            'error'    => $files['error'][$count],
                            'size'     => $files['size'][$count]
                        ];

                        $upload_overrides = ['test_form' => false];
                        $upload = wp_handle_upload($file, $upload_overrides);

                        // $filename should be the path to a file in the upload directory
                        $filename = $upload['file'];

                        // The ID of the post this attachment is for
                        $parent_post_id = $wpsend_id;

                        // Check the type of file (we'll use this as the 'post_mime_type')
                        $filetype = wp_check_filetype(basename($filename), null);

                        // Get the path to the upload directory
                        $wp_upload_dir = wp_upload_dir();

                        // Prepare an array of post data for the attachment
                        $attachment = [
                            'guid'           => $wp_upload_dir['url'] . '/' . basename($filename), 
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        ];

                        // Insert the attachment
                        $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);

                        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it
                        require_once ABSPATH . 'wp-admin/includes/image.php';

                        // Generate the metadata for the attachment, and update the database record
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    }

                    $count++;
                }
            }

            // Update transfer post with the file attachments
            if ((string) $_POST['selected-file'] !== '') {
                $dropbox_selected_files_array = explode(',', $_POST['selected-file']);

                foreach ($dropbox_selected_files_array as $dropbox_file) {
                    $wpsend_template .= '<div><a href="' . $dropbox_file . '">' . $dropbox_file . '</a></div>';
                }
            }
            $wpsend_template .= wpsend_get_attachments($wpsend_id);

            $updated_transfer = [
                'ID' => $wpsend_id,
                'post_content' => $wpsend_template
            ];

            wp_update_post($updated_transfer);



            // Mail sending
            wp_mail($wpsend_to, $subject, $wpsend_template, $headers);

            if (isset($_POST['wpsend_allow_cc'])) {
                wp_mail($wpsend_from_email, $subject, $wpsend_template, $headers);
            }


            // Redirection
            if ((int) $wpsend_redirection === 1 && (int) $wpsend_page_thankyou > 0) {
                wpsend_conversion(get_the_ID());
                echo '<meta http-equiv="refresh" content="0;url=' . get_permalink($wpsend_page_thankyou) . '">';
                exit;
            }

            $wpsend_label_success = get_option('wpsend_label_success');
            echo '<p class="wpsend-confirmation has-black-color has-light-green-cyan-background-color has-text-color has-background">' . esc_html($wpsend_label_success) . '</p>';
            wpsend_conversion(get_the_ID());
        }
    }

    /**
     * Display transfer form
     */
	$output = '<div class="wpsend-container">';
		$output .= '<form action="#" method="post" enctype="multipart/form-data">';

            $today = date('Y-m-d');
            $expiry_days = $attributes['expire'];
            $expiry_date = date('Y-m-d', strtotime($today . " + $expiry_days days"));

            if ((int) get_option('wpsend_dropbox_enable') === 1) {
                $output .= '<script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="' . get_option('wpsend_dropbox_api') . '"></script>
                <p id="droptarget"></p>
                <input type="hidden" id="selected-file" name="selected-file">';
            }

            $limit = $attributes['limit'];
            $limit_bytes = (int) $limit * 1024 * 1024;


            $output .= '<input type="file" id="file" name="upload_attachment[]" class="files" size="50" multiple>';
            //

            $output .= '<p><small>' . sprintf(__('The filesize limit is <b>%d</b>MB.', 'wp-send'), $limit) . '</small></p>';
            $output .= '<div class="wp-send-error" id="wp-send-filesize-error">
                <p><small>' . sprintf(__('The selected files are too big! The filesize limit is <b>%d</b>MB.', 'wp-send'), $limit) . '</small></p>
            </div>';

            $output .= '<p>
                <label for="wpsend_from">' . get_option('wpsend_label_name_own') . '</label><br>
                <input type="text" id="wpsend_from" name="wpsend_from" size="30" required>
            </p>
            <p>
                <label for="wpsend_from_email">' . get_option('wpsend_label_email_own') . '</label><br>
                <input type="email" id="wpsend_from_email" name="wpsend_from_email" size="30" required>
            </p>';

            if ((int) $wpsend_send_behaviour === 1) {
                $output .= '<p>
                    <label for="wpsend_to">' . get_option('wpsend_label_email_friend') . '</label><br>
                    <input type="email" id="wpsend_to" name="wpsend_to" size="30" required>
                </p>';
            }

            $output .= '<p><label for="wpsend_message">' . get_option('wpsend_label_message') . '</label><br><textarea id="wpsend_message" name="wpsend_message" rows="6" cols="60"></textarea></p>';

            if (get_option('wpsend_allow_cc') === 'on') {
                $output .= '<p><input type="checkbox" name="wpsend_allow_cc" id="wpsend_allow_cc"> <label for="wpsend_allow_cc">' . get_option('wpsend_label_cc') . '</label></p>';
            }

            $output .= '<p><small>' . sprintf(__('The file will expire in <b>%s</b> days, on %s.', 'wp-send'), $expiry_days, $expiry_date) . '</small></p>';

            $output .= '<p>
                <input type="hidden" name="wpsend_referrer" value="' . get_permalink() . '">
                <input type="hidden" name="wpsend_expiry_date" value="' . $expiry_date . '">
                <input type="submit" id="wpsend_submit" name="wpsend_submit" value="' . $wpsend_label_submit . '">
            </p>
        </form>
    </div>';

    if ((int) get_option('wpsend_restrictions') === 0 || (int) get_option('wpsend_restrictions') === 1 && is_user_logged_in()) {
        return $output;
    } else if ((int) get_option('wpsend_restrictions') === 1 && !is_user_logged_in()) {
        $output = get_option('wpsend_restrictions_message');
    }

    return $output;
}

add_shortcode('send', 'wpsend_display_form');

add_action('wp_enqueue_scripts', 'wpsend_enqueue_scripts');
function wpsend_enqueue_scripts() {
    wp_enqueue_style('wpsend', plugins_url('css/vintage.css', __FILE__));

    wp_enqueue_script('wpsend', plugins_url('js/vintage.js', __FILE__), [], WPSEND_VERSION, true);
}

// Displays options menu
function wpsend_add_option_page() {
    add_submenu_page('edit.php?post_type=' . WPSEND_CPT, esc_html__('WP Send Settings', 'wp-send'), esc_html__('WP Send Settings', 'wp-send'), 'manage_options', 'wpsend_options_page', 'wpsend_options_page');
}

add_action('admin_menu', 'wpsend_add_option_page');

// custom settings link inside Plugins section
function wpsend_settings_link($links) { 
	$settings_link = '<a href="' . admin_url('edit.php?post_type=' . WPSEND_CPT . '&page=wpsend_options_page') . '">' . esc_html__('Settings', 'wp-send') . '</a>'; 
	array_unshift($links, $settings_link); 

    return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpsend_settings_link');






function wpsend_delete_expired_files_daily() {
    if (!wp_next_scheduled('delete_expired_files')) {
        wp_schedule_event(time(), 'daily', 'delete_expired_files');
    }
}

add_action('wp', 'wpsend_delete_expired_files_daily');

function wpsend_delete_expired_files_callback() {
    $args = [
        'post_type' => WPSEND_CPT,
        'post_status' => ['publish', 'private', 'draft', 'pending'],
        'posts_per_page' => -1
    ];

    $files = new WP_Query($args);

    if ($files->have_posts()) {
        while ($files->have_posts()) {
            $files->the_post();

            $expiration_date = get_post_meta(get_the_ID(), 'wpsend_expiry_date', true);
            $expiration_date_time = strtotime($expiration_date);

            if ($expiration_date_time <= time()) {
                wp_delete_post(get_the_ID(), true);
            }
        }
    }
}

add_action('delete_expired_files', 'wpsend_delete_expired_files_callback');
