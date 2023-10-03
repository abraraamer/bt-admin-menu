<?php

/**
 * Plugin Name: Admin Menu Scroll
 * Version: 1.0
 * Plugin URI: https://codecanyon.net/user/bent-tech33
 * Description: Scrollable Admin Menu
 * Author: Bent Tech
 * Author URI: https://codecanyon.net/user/bent-tech33
 * Text Domain: bt-admin-menu
 * Domain Path: /languages/
 * License: GPL v2
 */
if (is_admin()) {
    DEFINE('BT_ADMIN_MENU_PATH', plugin_dir_path(__FILE__));
    DEFINE('BT_ADMIN_MENU_URL', plugin_dir_url(__FILE__));

    function bt_menu_add_css() {
        $key = 'bt-admin-menu';
        $time = filemtime(BT_ADMIN_MENU_PATH . '/css/' . $key . '.css');
        wp_enqueue_style(
                'bt-admin-menu',
                BT_ADMIN_MENU_URL . 'css/' . $key . '.css', [], $time
        );
        $color = get_theme_mod('my-custom-color', 'blue'); //E.g. #FF0000
        $custom_css = "
                .mycolor{
                        background: {$color};
                }";
        wp_add_inline_style($key, $custom_css);
        
        $time2 = filemtime(BT_ADMIN_MENU_PATH . '/js/' . $key . '.js');
        wp_enqueue_script(
                'bt-admin-menu',
                BT_ADMIN_MENU_URL . 'js/' . $key . '.js', ['jquery'], $time2
        );
    }

    add_action('admin_enqueue_scripts', 'bt_menu_add_css', 9999);
}

