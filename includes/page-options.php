<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function wpsend_options_page() {
    if (isset($_POST['info_settings_update'])) {
        update_option('wpsend_use_akismet', sanitize_text_field($_POST['wpsend_use_akismet']));

        update_option('wpsend_dropbox_api', sanitize_text_field($_POST['wpsend_dropbox_api']));
        update_option('wpsend_dropbox_enable', (int) $_POST['wpsend_dropbox_enable']);

        update_option('wpsend_redirection', (int) $_POST['wpsend_redirection']);
        update_option('wpsend_page_thankyou', (int) $_POST['wpsend_page_thankyou']);

        update_option('wpsend_shortcode_fix', sanitize_text_field($_POST['wpsend_shortcode_fix']));
        update_option('wpsend_html_fix', sanitize_text_field($_POST['wpsend_html_fix']));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'wp-send') . '</p></div>';
    } else if (isset($_POST['info_payment_update'])) {
        update_option('wpsend_restrictions', sanitize_text_field($_POST['wpsend_restrictions']));
        update_option('wpsend_restrictions_message', esc_html(stripslashes_deep($_POST['wpsend_restrictions_message'])));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'wp-send') . '</p></div>';
    } else if (isset($_POST['info_designer_blocks_update'])) {
        update_option('wpsend_email_title', stripslashes($_POST['wpsend_email_title']));
        update_option('wpsend_reusable_block_id', (int) $_POST['wpsend_reusable_block_id']);

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'wp-send') . '</p></div>';
    } else if (isset($_POST['info_email_update'])) {
        update_option('wpsend_noreply', sanitize_email($_POST['wpsend_noreply']));

        update_option('wpsend_send_behaviour', (int) $_POST['wpsend_send_behaviour']);
        update_option('wpsend_hardcoded_email', sanitize_email($_POST['wpsend_hardcoded_email']));

        update_option('wpsend_allow_cc', sanitize_text_field($_POST["wpsend_allow_cc"]));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'wp-send') . '</p></div>';
    } else if (isset($_POST['info_labels_update'])) {
        update_option('wpsend_label_name_own', stripslashes(sanitize_text_field($_POST['wpsend_label_name_own'])));
        update_option('wpsend_label_email_own', stripslashes(sanitize_text_field($_POST['wpsend_label_email_own'])));
        update_option('wpsend_label_email_friend', stripslashes(sanitize_text_field($_POST['wpsend_label_email_friend'])));
        update_option('wpsend_label_message', stripslashes(sanitize_text_field($_POST['wpsend_label_message'])));
        update_option('wpsend_label_cc', stripslashes(sanitize_text_field($_POST['wpsend_label_cc'])));
        update_option('wpsend_label_success', sanitize_text_field($_POST['wpsend_label_success']));
        update_option('wpsend_label_submit', stripslashes(sanitize_text_field($_POST['wpsend_label_submit'])));
        update_option('wpsend_link_anchor', stripslashes(sanitize_text_field($_POST['wpsend_link_anchor'])));

        echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__('Options updated successfully!', 'wp-send') . '</p></div>';
    } else if (isset($_POST['info_debug_update'])) {
        $headers[] = "Content-Type: text/html;";

        if (!empty($_POST['wpsend_test_email']) && wp_mail($_POST['wpsend_test_email'], 'WP Send Test Email', 'Testing WP Send plugin...', $headers)) {
            echo '<div id="message" class="updated notice is-dismissible"><p>Email sent successfully. Check your inbox.</p></div>';
        } else {
            echo '<div id="message" class="updated notice notice-error is-dismissible"><p>Email not sent. Check your server configuration.</p></div>';
        }

        echo '<div id="message" class="updated notice is-dismissible"><p>Options updated successfully!</p></div>';
    }
    ?>
    <div class="wrap">
		<h2>WP Send <span class="dashicons dashicons-airplane"></span></h2>

		<?php
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'wpsend_dashboard';
		$tab = admin_url('edit.php?post_type=' . WPSEND_CPT . '&page=wpsend_options_page&tab=wpsend_');
		?>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo $tab; ?>dashboard" class="nav-tab <?php echo $active_tab === 'wpsend_dashboard' ? 'nav-tab-active' : ''; ?>"><?php _e('Dashboard', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>settings" class="nav-tab <?php echo $active_tab === 'wpsend_settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>designer_blocks" class="nav-tab <?php echo $active_tab === 'wpsend_designer_blocks' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Designer (Blocks)', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>email" class="nav-tab <?php echo $active_tab === 'wpsend_email' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Settings', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>members" class="nav-tab <?php echo $active_tab === 'wpsend_members' ? 'nav-tab-active' : ''; ?>"><?php _e('Restrictions', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>labels" class="nav-tab <?php echo $active_tab === 'wpsend_labels' ? 'nav-tab-active' : ''; ?>"><?php _e('Labels', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>diagnostics" class="nav-tab <?php echo $active_tab === 'wpsend_diagnostics' ? 'nav-tab-active' : ''; ?>"><?php _e('Diagnostics', 'wp-send'); ?></a>
			<a href="<?php echo $tab; ?>statistics" class="nav-tab <?php echo $active_tab === 'wpsend_statistics' ? 'nav-tab-active' : ''; ?>"><?php _e('Statistics', 'wp-send'); ?></a>
		</h2>
		<?php if ($active_tab === 'wpsend_dashboard') { ?>
            <h2>Thank you for using WP Send!</h2>

            <p>For support, feature requests and bug reporting, please visit the <a href="https://getbutterfly.com/wordpress-plugins/" rel="external noopener follow">official website</a>. <a href="https://getbutterfly.com/knowledge-base/" class="button button-secondary">WP Send Documentation</a></p>

            <p>
                You are using <b>WP Send</b> version <b><?php echo WPSEND_VERSION; ?></b> on PHP version <?php echo PHP_VERSION; ?>.
                <br>&copy;<?php echo date('Y'); ?> <a href="https://getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <small>Code wrangling since 2005</small>
            </p>

            <h3>Summary and usage examples</h3>
            <p>WP Send plugin uses one shortcode: <code>[send]</code>.</p>

            <p>Add the <code>[send limit="32" expire="7"]</code> shortcode to a post or a page, where <code>limit</code> is the filesize limit (in megabytes) and <code>expire</code> is the expiry/removal date (in days).</p>

        
            <h3>Licence Key</h3>

            <p>
                <code>15e852ee16f1167145a9d9067403277c3caea46e</code>
            </p>
            <p>
                <span class="dashicons dashicons-warning"></span> Use this licence key with your <b>GitHub Updater</b> plugin.
            </p>
            <hr>
            <p>
                <small>Your <b>WP Send</b> licence key is used to verify your support package, enable automatic updates and receive support.</small><br>
                <small>This plugin does not send any information, save for your unique API Key, to any third-party services.</small>
            </p>
		<?php } else if ($active_tab === 'wpsend_settings') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('WP Send General Settings', 'wp-send'); ?></h3>

    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="wpsend_use_akismet">Akismet Antispam</label></th>
    		                <td>
								<select name="wpsend_use_akismet" id="wpsend_use_akismet" class="regular-text">
									<option value="true"<?php if(get_option('wpsend_use_akismet') === 'true') echo ' selected'; ?>>Use Akismet (recommended)</option>
									<option value="false"<?php if(get_option('wpsend_use_akismet') === 'false') echo ' selected'; ?>>Do not use Akismet</option>
								</select>
    							<?php
    							if (function_exists('akismet_init')) {
    								$wpcom_api_key = get_option('wordpress_api_key');
    
    								if(!empty($wpcom_api_key)) {
    									echo '<p><small>Your Akismet plugin is installed and working properly. Your API key is <code>' . $wpcom_api_key . '</code>.</small></p>';
    								} else {
    									echo '<p><small>Your Akismet plugin is installed but no API key is present. Please fix it.</small></p>';
    								}
    							} else {
    								echo '<p><small>You need Akismet in order to use this feature. Please install/activate it.</small></p>';
    							}
    							?>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_user_enable">User Upload</label></th>
    		                <td>
                                <p>
                                    <input type="checkbox" name="wpsend_user_enable" value="1" checked disabled> <label>Enable user upload</label><br>
                                    <input type="checkbox" name="wpsend_dropbox_enable" value="1" <?php if ((int) get_option('wpsend_dropbox_enable') === 1) echo 'checked'; ?>> <label>Enable Dropbox upload</label>
                                </p>
                                <p>
                                    <input name="wpsend_dropbox_api" id="wpsend_dropbox_api" type="text" class="regular-text" value="<?php echo get_option('wpsend_dropbox_api'); ?>"> <label for="wpsend_dropbox_api">Dropbox API Key</label>
                                    <br><small>Allow users to send files from their Dropbox accounts. Requires an <a href="https://www.dropbox.com/developers/dropins/chooser/js" rel="external">API key</a>.</small>
                                </p>
    		                </td>
    		            </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_redirection">Redirection</label></th>
    		                <td>
								<select name="wpsend_redirection">
									<option value="0"<?php if ((int) get_option('wpsend_redirection') === 0) echo ' selected'; ?>>Do not redirect to another page</option>
									<option value="1"<?php if ((int) get_option('wpsend_redirection') === 1) echo ' selected'; ?>>Redirect to another page (see below)</option>
								</select>
                                <br>
								⌊ <?php
                                $wpsend_page_thankyou_id = (int) get_option('wpsend_page_thankyou');

                                wp_dropdown_pages([
                                    'name' => 'wpsend_page_thankyou',
                                    'selected' => $wpsend_page_thankyou_id,
                                    'show_option_none' => 'Select a page to redirect to...',
                                    'option_none_value' => 0
                                ]);
                                ?>
                                <br><small>Use these options to customize your success actions and/or redirect to a &quot;Thank You&quot; page.</small>
    		                </td>
    		            </tr>
                        <tr><td colspan="2"><hr></td></tr>
    		            <tr>
    		                <th scope="row"><label>Debugging<br><small>(developers only)</small></label></th>
    		                <td>
                                <p>
                                    <input name="wpsend_shortcode_fix" id="wpsend_shortcode_fix" type="checkbox"<?php if(get_option('wpsend_shortcode_fix') === 'on') echo ' checked'; ?>> <label for="wpsend_shortcode_fix">Apply content shortcode fix</label>
                                    <br><small>Only use this option if your WordPress version is old, or you have a buggy theme and the shortcode is not working.</small>
                                </p>
                                <p>
                                    <input name="wpsend_html_fix" id="wpsend_html_fix" type="checkbox"<?php if(get_option('wpsend_html_fix') === 'on') echo ' checked'; ?>> <label for="wpsend_html_fix">Apply HTML content type fix</label>
                                    <br><small>Only use this option if your emails are missing formatting and line breaks.</small>
                                </p>
    		                </td>
    		            </tr>
    		        </tbody>
    		    </table>

                <hr>
                <p><input type="submit" name="info_settings_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } else if ($active_tab === 'wpsend_members') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('Email Restrictions', 'wp-send'); ?></h3>
                <p>Restricting access to members only requires a user to be logged into your WordPress site.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="wpsend_restrictions">Member restrictions</label></th>
    		                <td>
								<select name="wpsend_restrictions">
									<option value="0"<?php if ((int) get_option('wpsend_restrictions') === 0) echo ' selected'; ?>>Do not restrict access to email form</option>
									<option value="1"<?php if ((int) get_option('wpsend_restrictions') === 1) echo ' selected'; ?>>Restrict access to members only</option>
								</select> <label for="wpsend_restrictions_message">Add a guest message below, if you restrict access to members only.</label>

								<?php wp_editor(get_option('wpsend_restrictions_message'), 'wpsend_restrictions_message', ['teeny' => true, 'textarea_rows' => 5, 'media_buttons' => false]); ?>
    		                </td>
    		            </tr>
    		        </tbody>
    		    </table>

                <hr>
				<p><input type="submit" name="info_payment_update" class="button button-primary" value="Save Changes"></p>
			</form>
        <?php } else if ($active_tab === 'wpsend_designer_blocks') { ?>
            <h3><?php _e('Email Designer (Blocks)', 'wp-send'); ?></h3>

            <p>Create your email template using a reusable block and the <b>Designer</b> tags below.</p>

            <p><b>Note:</b> Some of the block editor styles will not be available inside your email client (e.g. columns, buttons and so on).</p>

            <form method="post" action="">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="wpsend_email_title">Email Title/Subject</label></th>
                            <td>
                                <input name="wpsend_email_title" id="wpsend_email_title" type="text" class="regular-text" value="<?php echo get_option('wpsend_email_title'); ?>">
                                <br><small>This is the subject of the email.</small>
                                <br><small>Use <code class="codor">[name]</code> and <code class="codor">[email]</code> shortcodes to replace sender's name and email address (e.g. "You have received a file from [name]").</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wpsend_reusable_block_id">Email Template Reusable Block</label></th>
                            <td>
                                <?php
                                $wpSendReusableBlockId = get_option('wpsend_reusable_block_id');

                                $args = [
                                    'post_type' => 'wp_block',
                                    'posts_per_page' => -1,
                                    'order' => 'ASC',
                                    'orderby' => 'title'
                                ];
                                $wpBlockQuery = new WP_Query($args);
                                ?>

                                <select name="wpsend_reusable_block_id" id="wpsend_reusable_block_id">
                                    <option value="">Select a reusable block...</option>

                                    <?php
                                    if ($wpBlockQuery->have_posts()) {
                                        while ($wpBlockQuery->have_posts()) {
                                            $wpBlockQuery->the_post();

                                            $selected = ((int) $wpSendReusableBlockId === (int) get_the_ID()) ? 'selected' : '';
                                            echo '<option value="' . get_the_ID() . '" ' . $selected . '>' . get_the_title() . '</option>';
                                        }
                                    }
                                    ?>

                                </select>
                                <br><small><a href="<?php echo admin_url('edit.php?post_type=wp_block'); ?>">Select your email template reusable block or create one now</a>.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wpsend_template">Email Template</label></th>
                            <td>
                                <p>
                                    Use <code class="codor">[name]</code> and <code class="codor">[email]</code> Designer tags to replace sender's name and email address.<br>
                                    Use <code class="codor">[transfer-message]</code> Designer tag to include the transfer message.
                                </p>
                                <p>Check the <a href="https://getbutterfly.com/support/documentation/wp-send-documentation/#designer" rel="external">documentation section</a> for more template samples.</p>
				            </td>
				        </tr>
                    </tbody>
                </table>

                <hr>
    			<p><input type="submit" name="info_designer_blocks_update" class="button button-primary" value="Save Changes"></p>
            </form>
		<?php } else if ($active_tab === 'wpsend_email') { ?>
    		<form method="post" action="">
    			<h3 class="title"><?php _e('Email Settings', 'wp-send'); ?></h3>
                <p><b>Note:</b> To avoid your email adress being marked as spam, it is highly recommended that your "from" domain match your website. Some hosts may require that your "from" address be a legitimate address.</p>
                <p>Sometimes emails end up in your spam (or junk) folder. Sometimes they don't arrive at all. While the latter may indicate a server issue, the former may easily be fixed by setting up a dedicated email address (send@yourdomain.com or noreply@yourdomain.com).</p>

                <p>If your host blocks the <code>mail()</code> function, or if you notice errors or restrictions, configure your WordPress site to use SMTP. We recommend <a href="https://wordpress.org/plugins/post-smtp/" rel="external">Post SMTP Mailer/Email Log</a>.</p>
                <div class="postbox">
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="wpsend_noreply">Dedicated Email Address</label></th>
                                    <td>
                                        <input name="wpsend_noreply" id="wpsend_noreply" type="email" class="regular-text" value="<?php echo get_option('wpsend_noreply'); ?>">
                                        <br><small>Create a dedicated email address to use for sending emails and prevent your messages landing in Spam/Junk folders.<br>Use <code>noreply@yourdomain.com</code>, <code>send@yourdomain.com</code> or something similar.</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="wpsend_send_behaviour">Sending Behaviour</label></th>
    		                <td>
                                <select name="wpsend_send_behaviour" class="regular-text">
									<option value="1"<?php if ((int) get_option('wpsend_send_behaviour') === 1) echo ' selected'; ?>>Require recipient email address</option>
									<option value="0"<?php if ((int) get_option('wpsend_send_behaviour') === 0) echo ' selected'; ?>>Hide recipient and send all emails to the following email address</option>
								</select>
                                <br>&lfloor; <input name="wpsend_hardcoded_email" type="email" class="regular-text" value="<?php echo get_option('wpsend_hardcoded_email'); ?>">
								<br><small>If you want to send all emails to a universal email address, select the option above and fill in the email address.</small>
				            </td>
				        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_allow_cc">Carbon Copy (CC)</label></th>
    		                <td>
                                <select name="wpsend_allow_cc" id="wpsend_allow_cc" class="regular-text">
									<option value="on"<?php if(get_option('wpsend_allow_cc') === 'on') echo ' selected'; ?>>Allow sender to CC self</option>
									<option value="off"<?php if(get_option('wpsend_allow_cc') === 'off') echo ' selected'; ?>>Do not allow sender to CC self</option>
								</select>
								<br><small>Display a checkbox to allow the sender to CC self</small>
				            </td>
				        </tr>
				    </tbody>
				</table>

                <hr>
    			<p><input type="submit" name="info_email_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } else if ($active_tab === 'wpsend_labels') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('Labels', 'wp-send'); ?></h3>
    			<p>Use the labels to personalize or translate your email form.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_name_own">Your name<br><small>(input label)</small></label></th>
    		                <td>
                                <input name="wpsend_label_name_own" id="wpsend_label_name_own" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_name_own'); ?>">
                                <br><small>Default is "Your name"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_email_own">Your email address<br><small>(input label)</small></label></th>
    		                <td>
                                <input name="wpsend_label_email_own" id="wpsend_label_email_own" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_email_own'); ?>">
                                <br><small>Default is "Your email address"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_email_friend">Your friend's email address<br><small>(input label)</small></label></th>
    		                <td>
                                <input name="wpsend_label_email_friend" id="wpsend_label_email_friend" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_email_friend'); ?>">
                                <br><small>Default is "Your friend's email address"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_message">Email message<br><small>(textarea label)</small></label></th>
    		                <td>
                                <input name="wpsend_label_message" id="wpsend_label_message" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_message'); ?>">
                                <br><small>Default is "Email message"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_cc">Send a copy to self<br><small>(checkbox label)</small></label></th>
    		                <td>
                                <input name="wpsend_label_cc" id="wpsend_label_cc" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_cc'); ?>">
                                <br><small>Default is "Send a copy to self"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_success">Success message<br><small>(paragraph)</small></label></th>
    		                <td>
                                <input name="wpsend_label_success" id="wpsend_label_success" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_success'); ?>">
                                <br><small>Default is "Email sent successfully!"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_label_submit">Email submit<br><small>(button label)</small></label></th>
    		                <td>
                                <input id="wpsend_label_submit" name="wpsend_label_submit" type="text" class="regular-text" value="<?php echo get_option('wpsend_label_submit'); ?>">
                                <br><small>Default is "Send Email"</small>
                            </td>
                        </tr>
    		            <tr>
    		                <th scope="row"><label for="wpsend_link_anchor">Email link anchor<br><small>(link)</small></label></th>
    		                <td>
                                <input name="wpsend_link_anchor" name="wpsend_link_anchor" type="text" class="regular-text" value="<?php echo get_option('wpsend_link_anchor'); ?>">
                                <br><small>Default is "Click to see your files!"</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

				<hr>
				<p><input type="submit" name="info_labels_update" class="button button-primary" value="Save Changes"></p>
			</form>
		<?php } else if ($active_tab === 'wpsend_diagnostics') { ?>
			<form method="post" action="">
    			<h3 class="title"><?php _e('Diagnostics', 'wp-send'); ?></h3>
                <p>If your host blocks the <code>mail()</code> function, or if you notice errors or restrictions, configure your WordPress site to use SMTP. We recommend <a href="https://wordpress.org/plugins/post-smtp/" rel="external">Post SMTP Mailer/Email Log</a>.</p>
    		    <table class="form-table">
    		        <tbody>
    		            <tr>
    		                <th scope="row"><label for="wpsend_test_email"><?php _e('Test <code>wp_mail()</code> function', 'wp-send'); ?></label></th>
    		                <td>
                                <input name="wpsend_test_email" id="wpsend_test_email" type="email" class="regular-text" value="<?php echo get_option('admin_email'); ?>">
                                <br><small><?php _e('Use this address to send a test email message.', 'wp-send'); ?></small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="info_debug_update" class="button button-primary" value="<?php _e('Test/Save Changes', 'wp-send'); ?>"></p>
			</form>
		<?php } else if ($active_tab === 'wpsend_statistics') { ?>
			<h3 class="title"><?php _e('Statistics', 'wp-send'); ?></h3>
            <?php
            $args = [
                'post_type' => ['post', 'page', WPSEND_CPT],
                'post_status' => ['publish', 'private'],
                'posts_per_page' => 100,
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'meta_query' => [
                    [
                        'key' => '_wpsend_impressions',
                    ],
                ],
            ];
            $wpsend_query = new WP_Query($args);

            if ($wpsend_query->have_posts()) {
                echo '<table class="wp-list-table widefat striped posts">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Impressions<br><small>Pageviews</small></th>
                            <th scope="col">Conversions<br><small>Emails Sent</small></th>
                            <th scope="col">Conversion Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>';
                        while ($wpsend_query->have_posts()) {
                            $wpsend_query->the_post();

                            $impressions = (int) get_post_meta(get_the_ID(), '_wpsend_impressions', true);
                            $conversions = (int) get_post_meta(get_the_ID(), '_wpsend_conversions', true);

                            $conversionRate = ($impressions === 0 || $conversions === 0) ? 0 : number_format($conversions / $impressions * 100, 2);

                            echo '<tr>
                                <td>' . get_the_ID() . '</td>
                                <td><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></td>
                                <td>' . $impressions . '</td>
                                <td>' . $conversions . '</td>
                                <td>' . $conversionRate . '</td>
                            </tr>';
                        }
                    echo '</tbody>
                </table>';

                wp_reset_postdata();
            }
        }
        ?>
	</div>
<?php }
