<?php
/*
Plugin Name: OX Gut
Plugin URI: http://k2.oterox.science/ox-gut
Description: A custom WordPress plugin for OX Gut functionality.
Version: 1.0
Author: Javier Otero
Author URI: http://k3.oterox.science
Text Domain: ox-gut
Domain Path: /languages
License: GPL2
*/


class OX_Gut {
    public function __construct() {
        add_action('admin_menu', array($this, 'OX_Gut_admin_menu'));
        add_action('admin_init', array($this, 'OX_Gut_admin_init'));
        add_filter('the_content', array($this, 'ox_gut_display_options'));
        add_action('init', array($this, 'ox_gut_load_textdomain'));
    }

    function ox_gut_load_textdomain() {
        load_plugin_textdomain('ox-gut', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    function ox_gut_display_options($content){
        if(is_main_query() && is_single() && get_option('ox_gut_wordcount', '0' ) ){
            return $this->ox_gut_render_html($content);
        }

        return $content;
    }

    function ox_gut_render_html($content){
        $wordcount = str_word_count(strip_tags($content));
        $headline = esc_html(get_option('ox_gut_headline', ''));
        $location = esc_html(get_option('OX_Gut_location', '0'));

        if($location == '0'){
            $content = "<h2>$headline</h2>" . "<p>" . esc_html__('Word count','ox-gut') .": $wordcount</p>" . $content;
        } else {
            $content = $content . "<h2>$headline</h2>" . "<p>Word count: $wordcount</p>";
        }

        return $content;
    }

    function OX_Gut_admin_init() {
        add_settings_section('ox-gut-settings-section', null, null, 'ox-gut-settings');

        add_settings_field('OX_Gut_location', 'Display location', array($this, 'OX_Gut_location_html'), 'ox-gut-settings', 'ox-gut-settings-section');
        register_setting('ox-gut-settings-group', 'OX_Gut_location', array('sanitize_callback' => array( $this, 'sanitize_location'), 'default' => '0'));

        add_settings_field( 'ox_gut_headline', 'Headline', array($this, 'ox_gut_headline_html'), 'ox-gut-settings', 'ox-gut-settings-section');
        register_setting( 'ox-gut-settings-group', 'ox_gut_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));

        add_settings_field( 'ox_gut_wordcount', 'Wordcount', array($this, 'ox_gut_wordcount_html'), 'ox-gut-settings', 'ox-gut-settings-section');
        register_setting( 'ox-gut-settings-group', 'ox_gut_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
    }

    function sanitize_location($input) {
        if ($input == '0' || $input == '1') {
            return $input;
        } else {
            add_settings_error('OX_Gut_location', 'OX_Gut_location_error', 'Location must be either 0 or 1');
            return get_option('OX_Gut_location');
        }
    }

    function OX_Gut_location_html() {
        $location = get_option('OX_Gut_location');
        ?>
        <select name="OX_Gut_location">
            <option value="0" <?php selected($location, '0'); ?>>Top</option>
            <option value="1" <?php selected($location, '1'); ?>>Bottom</option>
        </select>
        <?php
    }

    function ox_gut_headline_html() {
        $headline = get_option('ox_gut_headline');
        ?>
        <input type="text" name="ox_gut_headline" value="<?php echo $headline; ?>" />
        <?php
    }   

    function ox_gut_wordcount_html() {
        $wordcount = get_option('ox_gut_wordcount');
        ?>
        <input type="checkbox" name="ox_gut_wordcount" value="1" <?php checked($wordcount, 1); ?> />
        <?php
    }   

    function OX_Gut_admin_menu() {
        add_options_page('OX Gut', __('OX Gut', 'ox-gut'), 'manage_options', 'ox-gut-settings', array($this, 'OX_Gut_settings_page'));
        add_menu_page('OX Gut', 'OX Gut', 'manage_options', 'ox-gut-menu', array($this, 'OX_Gut_settings_page'));
    }
    
    function OX_Gut_settings_page() {
        ?>
        <div class="wrap">
            <h2>OX Gut</h2>
            <p>Settings for OX Gut</p>
            <form method="post" action="options.php">
                <?php settings_fields('ox-gut-settings-group'); ?>
                <?php do_settings_sections('ox-gut-settings'); ?>
                <?php submit_button(); ?>
        </div>
        <?php
    }
}

$OX_Gut = new OX_Gut();

