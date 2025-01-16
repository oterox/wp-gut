<?php
/*
Plugin Name: OX Gut
Plugin URI: http://k2.oterox.science/ox-gut
Description: A custom WordPress plugin for OX Gut functionality.
Version: 1.0
Author: Javier Otero
Author URI: http://k3.oterox.science
License: GPL2
*/


class OX_Gut {
    public function __construct() {
        add_action('admin_menu', array($this, 'OX_Gut_admin_menu'));
        add_action('admin_init', array($this, 'OX_Gut_admin_init'));
    }

    function OX_Gut_admin_init() {
        add_settings_section('ox-gut-settings-section', null, null, 'ox-gut-settings');
        add_settings_field('OX_Gut_location', 'Display location', array($this, 'OX_Gut_location_html'), 'ox-gut-settings', 'ox-gut-settings-section');
        register_setting('ox-gut-settings-group', 'OX_Gut_location', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
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

    function OX_Gut_admin_menu() {
        add_options_page('OX Gut', 'OX Gut', 'manage_options', 'ox-gut-settings', array($this, 'OX_Gut_settings_page'));
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

