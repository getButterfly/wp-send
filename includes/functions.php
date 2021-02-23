<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function wpsend_shortcode_fix() {
    add_filter('the_content', 'do_shortcode', 9);
}

function wpsend_set_content_type($content_type) {
    return 'text/html';
}

function wpsend_checkSpam($content) {
	// Innocent until proven guilty
	$isSpam = false;
	$content = (array) $content;

	if (function_exists('akismet_init') && get_option('wpsend_use_akismet') == 'true') {
		$wpcom_api_key = get_option('wordpress_api_key');

		if(!empty($wpcom_api_key)) {
			// Set remaining required values for akismet api
			$content['user_ip'] = preg_replace('/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR']);
			$content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$content['referrer'] = $_SERVER['HTTP_REFERER'];
			$content['blog'] = get_option('home');

			if(empty($content['referrer'])) {
				$content['referrer'] = get_permalink();
			}

			$queryString = '';

			foreach($content as $key => $data) {
				if(!empty($data)) {
					$queryString .= $key . '=' . urlencode(stripslashes($data)) . '&';
				}
			}

			$response = Akismet::http_post($queryString, 'comment-check');

			if($response[1] == 'true') {
				update_option('akismet_spam_count', get_option('akismet_spam_count') + 1);
				$isSpam = TRUE;
			}
		}
	}
	return $isSpam;
}



function wpsend_impression($id, $count = true) {
    $impressionCount = get_post_meta($id, '_wpsend_impressions', true);

    if ($impressionCount == '') {
        $impressionCount = 0;
    }

    if ($count === true) {
        $impressionCount++;
        update_post_meta($id, '_wpsend_impressions', $impressionCount);
    }

    return $impressionCount;
}

function wpsend_conversion($id, $count = true) {
    $conversionCount = get_post_meta($id, '_wpsend_conversions', true);

    if ($conversionCount == '') {
        $conversionCount = 0;
    }

    if ($count === true) {
        $conversionCount++;
        update_post_meta($id, '_wpsend_conversions', $conversionCount);
    }

    return $conversionCount;
}

function wpsend_mail_from($mail_from_email) {
	$site_mail_from_email = sanitize_email(get_option('wpsend_noreply'));

	if (empty($site_mail_from_email)) {
		return $mail_from_email;
	} else {
		return $site_mail_from_email;
	}
}

add_filter('wp_mail_from', 'wpsend_mail_from', 1);



/**
 * Register meta box
 */
function wpsend_add_meta_box() {
	add_meta_box('wpsend_details_meta_box', __('Transfer Details', 'wp-send'), 'wpsend_details_metabox_callback', [WPSEND_CPT]);
}
add_action('add_meta_boxes', 'wpsend_add_meta_box');

function wpsend_details_metabox_callback($post) {
    $wpSendEmailRecipient = get_post_meta($post->ID, 'wpsend_email_recipient', true);
    $wpSendEmailSender = get_post_meta($post->ID, 'wpsend_email_sender', true);
    $wpSendName = get_post_meta($post->ID, 'wpsend_name', true);
    $wpSendExpiryDate = get_post_meta($post->ID, 'wpsend_expiry_date', true);
    ?>

    <p>
        <input type="text" class="large-text" value="<?php echo sanitize_text_field($wpSendName); ?>" readonly>
    </p>
    <p>
        <input type="email" class="large-text" value="<?php echo sanitize_email($wpSendEmailSender); ?>" readonly>
    </p>
    <p>
        <input type="email" class="large-text" value="<?php echo sanitize_email($wpSendEmailRecipient); ?>" readonly>
    </p>
    <p>
        <input type="text" class="large-text" value="<?php echo sanitize_text_field($wpSendExpiryDate); ?>" readonly>
    </p>

	<?php
}



/**
 * Delete all attached media when a transfer is permanently deleted
 */
function wpsend_delete_all_attached_media($post_id) {
    if (get_post_type($post_id) === WPSEND_CPT) {
        $attachments = get_attached_media('', $post_id);

        foreach ($attachments as $attachment) {
            wp_delete_attachment($attachment->ID, 'true');
        }
    }
}

add_action('before_delete_post', 'wpsend_delete_all_attached_media');



/**
 * TGM Plugin Activation
 */
function wpsend_register_required_plugins() {
	$plugins = [
		[
			'name' => 'GitHub Updater',
			'slug' => 'github-updater',
            'source' => 'https://github.com/afragen/github-updater/archive/master.zip',
            'external_url' => 'https://github.com/afragen/github-updater',
			'required' => true,
        ]
	];

	$config = [
		'id' => 'wp-send',
		'default_path' => '',
		'menu' => 'tgmpa-install-plugins',
		'parent_slug' => 'plugins.php',
		'capability' => 'manage_options',
		'has_notices' => true,
		'dismissable' => true,
		'dismiss_msg' => '',
		'is_automatic' => false,
		'message' => ''
	];

	tgmpa($plugins, $config);
}
