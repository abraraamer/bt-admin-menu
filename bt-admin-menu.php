<?php

/**
 * Plugin Name: Admin Menu Scroll
 * Version: 1.0
 * Description: Scrollable Admin Menu
 * Author: Bent Tech
 * Text Domain: bt-admin-menu
 * Domain Path: /languages/
 * License: GPL v2
 */
if (is_admin()) {
    DEFINE('BT_ADMIN_MENU_PATH', plugin_dir_path(__FILE__));
    DEFINE('BT_ADMIN_MENU_URL', plugin_dir_url(__FILE__));
    DEFINE('BT_ADMIN_MENU_TEXT_DOMAIN', 'bt-admin-menu');
    DEFINE('BT_ADMIN_MENU_SLUG', 'bt-menu-scroll-settings');
    DEFINE('BT_ADMIN_MENU_OPTIONS_KEY', 'bt_admin_menu_settings');
    DEFINE('BT_ADMIN_MENU_PLUGIN_DIR_NAME', basename(__DIR__));
    DEFINE('BT_ADMIN_MENU_DEFAULT_COLOR', '#2271b1');
    DEFINE('BT_ADMIN_MENU_DEFAULT_COLOR_KEY', 'bt-admin-menu-hover-color');

    add_filter('admin_init', ['BT_Admin_Menu', 'bt_register_settings']);

    add_action('admin_menu', ['BT_Admin_Menu', 'bt_menu_page']);
    add_action('admin_enqueue_scripts', ['BT_Admin_Menu', 'bt_menu_add_css'], 9999);
    add_filter('menu_order', ['BT_Admin_Menu', 'bt_menu_order'], 9999);
    add_filter('admin_init', ['BT_Admin_Menu', 'bt_register_settings']);

    global $bt_functions;
    $bt_functions = [];

    function bt_admin_menu_language_init() {
        load_plugin_textdomain(BT_ADMIN_MENU_TEXT_DOMAIN, false, BT_ADMIN_MENU_PLUGIN_DIR_NAME . '/languages');
    }

    add_action('init', 'bt_admin_menu_language_init');

    class BT_Admin_Menu {

        public static $sections = [
            'settings' => [
                'title' => '',
                'desc' => '',
                'fields' => [
                    'sticky_menus' => ['type' => 'radio', 'title' => 'Sticky fields', 'default' => 1, 'desc' => 'Sticks important items to the top'],
                    'menu_color' => ['type' => 'color', 'title' => 'Menu Color', 'default' => BT_ADMIN_MENU_DEFAULT_COLOR, 'desc' => 'Active and sub menu color'],
                ]
            ]
        ];

        static function bt_menu_add_css() {
            $key = 'bt-admin-menu';
            $time = filemtime(BT_ADMIN_MENU_PATH . '/css/' . $key . '.css');
            $main_css = 'bt-admin-menu';
            wp_enqueue_style(
                    $main_css,
                    BT_ADMIN_MENU_URL . 'css/' . $key . '.css', [], $time
            );
            //menu color
            $options = get_option(BT_ADMIN_MENU_OPTIONS_KEY, []);
            $menu_color = isset($options['menu_color']) ? $options['menu_color'] : BT_ADMIN_MENU_DEFAULT_COLOR;
            if (!empty($menu_color)) {
                $data = '#adminmenu{'
                        . '--' . BT_ADMIN_MENU_DEFAULT_COLOR_KEY . ': ' . $menu_color . ''
                        . '}';
                wp_add_inline_style($main_css, $data);
            }


            $time2 = filemtime(BT_ADMIN_MENU_PATH . '/js/' . $key . '.js');
            wp_enqueue_script(
                    'bt-admin-menu',
                    BT_ADMIN_MENU_URL . 'js/' . $key . '.js', ['jquery'], $time2
            );
        }

        static function bt_menu_order($menu_ord) {

            if (!$menu_ord) {
                return true;
            }
            $options = get_option(BT_ADMIN_MENU_OPTIONS_KEY, []);
            $sticky = isset($options['sticky_menus']) ? $options['sticky_menus'] : 1;
            if (!$sticky) {
                return [];
            }
            if ($sticky) {
                $array = array(
                    'index.php', // this represents the dashboard link
                    'edit.php?post_type=page', // this is the default Page menu
                    'edit.php', // this is the default POST admin menu 
                    'upload.php',
                    'plugins.php',
                    'options-general.php',
                );
                if (is_plugin_active('woocommerce/woocommerce.php')) {
                    $array[] = 'wc-admin&path=/wc-pay-welcome-page';
                    $array[] = 'edit.php?post_type=product';
                    $array[] = 'woocommerce';
                }
                if (is_plugin_active('elementor/elementor.php')) {
                    $array[] = 'elementor';
                    $array[] = 'elementor-pro';
                }
                return $array;
            }
        }

        static function bt_menu_settings() {
            include_once BT_ADMIN_MENU_PATH . '/settings/settings.php';
        }

        static function bt_menu_page() {
            add_submenu_page(
                    'options-general.php',
                    __('Admin Menu Scroll', BT_ADMIN_MENU_TEXT_DOMAIN),
                    __('Admin Menu Scroll', BT_ADMIN_MENU_TEXT_DOMAIN),
                    'manage_options',
                    BT_ADMIN_MENU_SLUG,
                    ['BT_Admin_Menu', 'bt_menu_settings'],
            );
        }

        static function bt_fields($args) {
            list ($field, $type, $default) = $args; 
            
            $options = get_option(BT_ADMIN_MENU_OPTIONS_KEY, []);
            $value = isset($options[$field]) ? $options[$field] : $default;
            switch ($type) {
                case 'color':

                    echo '<div>' .
                    '<input type="color" name="' . BT_ADMIN_MENU_OPTIONS_KEY . '[' . $field . ']" value="' . $value . '"  />' .
                    '</div>' .
                    '<div>';
                    break;
                case 'radio';

                    echo '<div>' .
                    '<input type="radio" name="' . BT_ADMIN_MENU_OPTIONS_KEY . '[' . $field . ']" value="1" ' . ($value ? 'checked' : '') . ' />' .
                    '<label>' . __('Yes', BT_ADMIN_MENU_TEXT_DOMAIN) . '</label>' .
                    '</div>' .
                    '<div>' .
                    '<input type="radio" name="' . BT_ADMIN_MENU_OPTIONS_KEY . '[' . $field . ']" value="0"  ' . ($value ? '' : 'checked') . ' />' .
                    '<label>' . __('No', BT_ADMIN_MENU_TEXT_DOMAIN) . '</label>' .
                    '</div>';
                    break;
            }
        }

        static function bt_register_settings() {
            register_setting(BT_ADMIN_MENU_OPTIONS_KEY, BT_ADMIN_MENU_OPTIONS_KEY);

            foreach (self::$sections as $section_id => $section_options) {
                $title = isset($section_options['title']) ? $section_options['title'] : '';
                $description = !empty($section_options['desc']) ? '<br><small style="font-weight: normal;">' . __($section_options['desc'], BT_ADMIN_MENU_TEXT_DOMAIN) . '</small>' : '';
                add_settings_section(
                        'bt_' . $section_id . '_section', //$id 
                        $title . $description, //$title
                        '',
                        BT_ADMIN_MENU_SLUG, //The slug-name of the settings page on which to show the section
                );
                foreach ($section_options['fields'] as $key => $field_options) {
                    if (!isset($field_options['type']))
                        continue;

                    $description = !empty($field_options['desc']) ? '<br><small style="font-weight: normal;">' . __($field_options['desc'], BT_ADMIN_MENU_TEXT_DOMAIN) . '</small>' : '';
                    $default =  isset($field_options['default']) ? $field_options['default'] : '';
                    $type = $field_options['type'];
                    add_settings_field(
                            $key, //field id
                            __($field_options['title'], BT_ADMIN_MENU_TEXT_DOMAIN) . $description,
                            ['BT_Admin_Menu', 'bt_fields'],
                            BT_ADMIN_MENU_SLUG,
                            'bt_settings_section',
                            [$key, $type, $default], //id, type, and default
                    );
                }
            }
        }

    }

}

