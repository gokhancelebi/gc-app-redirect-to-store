<?php
/*
 * Plugin Name: GC App Redirect to Store
 * Plugin URI: https://gokhancelebi.net
 * Description: Redirects to the app store when the user is on mobile.
 *
 */

# create app url edit page for admin
add_action('admin_menu', 'gc_app_redirect_to_store_menu');
function gc_app_redirect_to_store_menu() {
    add_options_page('GC App Redirect to Store', 'GC App Redirect to Store', 'manage_options', 'gc-app-redirect-to-store', 'gc_app_redirect_to_store_options');
}

# add link settings page for android and ios store urls
function gc_app_redirect_to_store_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    if (isset($_POST['gc_app_redirect_to_store_submit'])) {
        update_option('gc_app_redirect_to_store_android_url', $_POST['gc_app_redirect_to_store_android_url']);
        update_option('gc_app_redirect_to_store_ios_url', $_POST['gc_app_redirect_to_store_ios_url']);
    }
    echo '<div class="wrap">';
    echo '<h2>GC App Redirect to Store</h2>';
    # show app redirect url
    echo '<p>App Redirect URL: <a href="' . get_site_url() . '/app-redirect" target="_blank">' . get_site_url() . '/app-redirect</a></p>';
    echo '<form method="post" action="">';
    settings_fields('gc_app_redirect_to_store_options');
    do_settings_sections('gc_app_redirect_to_store');
    echo '<table class="form-table">';
    echo '<tr valign="top">';
    echo '<th scope="row">Android Store URL</th>';
    echo '<td><input type="text" name="gc_app_redirect_to_store_android_url" value="' . get_option('gc_app_redirect_to_store_android_url') . '" /></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">iOS Store URL</th>';
    echo '<td><input type="text" name="gc_app_redirect_to_store_ios_url" value="' . get_option('gc_app_redirect_to_store_ios_url') . '" /></td>';
    echo '</tr>';
    echo '</table>';
    submit_button("Save", "primary", "gc_app_redirect_to_store_submit");
    echo '</form>';
    echo '</div>';
}

# add public redirect page with custom url
add_action('init', 'gc_app_redirect_to_store_redirect');
function gc_app_redirect_to_store_redirect() {
    if (get_option('gc_app_redirect_to_store_android_url') && get_option('gc_app_redirect_to_store_ios_url')) {
        add_rewrite_rule('^app-redirect/?$', 'index.php?gc_app_redirect_to_store=1', 'top');
        add_rewrite_tag('%gc_app_redirect_to_store%', '([^&]+)');
        add_action('template_redirect', 'gc_app_redirect_to_store_template_redirect');
    }
}

# redirect to store url
function gc_app_redirect_to_store_template_redirect() {
    global $wp_query;
    if ($wp_query->get('gc_app_redirect_to_store')) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/android/i', $user_agent)) {
            wp_redirect(get_option('gc_app_redirect_to_store_android_url'));
        } else if (preg_match('/(ipod|iphone|ipad)/i', $user_agent)) {
            wp_redirect(get_option('gc_app_redirect_to_store_ios_url'));
        } else {
            wp_redirect(home_url());
        }
        exit;
    }
}

# example url: https://siteurl.net/app

# when plugin is activated, add rewrite rules
register_activation_hook(__FILE__, 'gc_app_redirect_to_store_rewrite_flush');
function gc_app_redirect_to_store_rewrite_flush() {
    gc_app_redirect_to_store_redirect();
    flush_rewrite_rules();
}
