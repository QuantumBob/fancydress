<?php
/**
 * Plugin Name:       RWK Shop Admin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Sets up the admin side of the shop including the database tables
 * Version:           0.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rob Kirk
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */
/*
 * Check for Existing Implementations #Check for Existing Implementations
 * PHP provides a number of functions to verify existence of variables, functions, classes and constants.
 * All of these will return true if the entity exists.

 * Variables: isset() (includes arrays, objects, etc.)
 * Functions: function_exists()
 * Classes: class_exists()
 * Constants: defined()
 *
 * Example
 * //Create a function called "wporg_get_foo" if it doesn't already exist
 * if ( !function_exists( 'wporg_get_foo' ) ) {
 *  function wporg_get_foo() {
 *      return get_option( 'wporg_option_foo' );
 *  }
 * }
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('PLUGIN_NAME',
       'rwk_shop_admin');
define('PLUGIN_URL',
       plugin_dir_url(__FILE__));

include 'php/rwk_settings.php';
include 'php/functions.php';
include 'php/product-post-type.php';
include 'php/admin-product-page.php';
include 'metaboxes/image_metaboxes/secondary-images-metabox.php';
include 'metaboxes/rwk_amazon/amazon-metabox.php';

register_activation_hook(__FILE__,
                         PLUGIN_NAME . '_activate');
register_deactivation_hook(__FILE__,
                           PLUGIN_NAME . '_deactivate');
register_uninstall_hook(__FILE__,
                        PLUGIN_NAME . '_uninstall');

function rwk_shop_admin_activate()
{
    global $wp_rewrite;

    //Write the rule
    $wp_rewrite->set_permalink_structure('/%postname%/');

    //Set the option
    update_option("rewrite_rules",
                  true);

    //Flush the rules and tell it to write htaccess
    $wp_rewrite->flush_rules(true);
}

function rwk_shop_admin_deactivate()
{
    unregister_post_type('rwk_product');
    flush_rewrite_rules();
}

function rwk_shop_admin_uninstall()
{
    /*
     *
      $option_name = 'wporg_option';

      delete_option($option_name);

      // for site options in Multisite
      delete_site_option($option_name);

      // drop a custom database table
      global $wpdb;
      $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mytable");
     *
     */
}

function rwk_add_plugin_page_settings_link($links)
{
//    $my_links[] = '<a href="' . admin_url( 'options-general.php?page=my-plugin' ) .'">' . __('Settings') . '</a>';
    $my_links[] = '<a href="' . admin_url('options-general.php?page=rwk_shop_admin_settings') . '">' . __('Settings') . '</a>';
    return array_merge($links,
                       $my_links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__),
                                                    'rwk_add_plugin_page_settings_link');

function load_admin_stylesheets($hook)
{
    if (!empty($hook) && ('post-new.php' === $hook || 'post.php' === $hook )) {

        wp_register_style('bootstrap',
                          PLUGIN_URL . '/css/bootstrap.min.css',
                          array(),
                          false,
                          'all');
        wp_enqueue_style('bootstrap');

        wp_register_style('style',
                          PLUGIN_URL . '/css/style.css',
                          array(),
                          false,
                          'all');
        wp_enqueue_style('style');
    }
}
add_action('admin_enqueue_scripts',
           'load_admin_stylesheets');

function enqueue_general_scripts()
{
    wp_enqueue_script('jquery');

    wp_enqueue_script('rwk_general_scripts',
                      PLUGIN_URL . 'js/general_scripts.js',
                      array('jquery'),
                      null);
//    wp_localize_script('rwk_general_scripts',
//                       'ajax_object',
//                       array('ajaxurl' => admin_url('admin-ajax.php'), 'we_value' => 1234));
}
add_action('admin_enqueue_scripts',
           'enqueue_general_scripts');

function enqueue_wp_media_files($page)
{
    if ($page == 'post.php' || $page === 'post-new.php') {
        // Enqueue WordPress media scripts
        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }

        // Enqueue custom script that will interact with wp.media
        wp_enqueue_script('rwk_remove_image_script',
                          PLUGIN_URL . 'js/remove_image_script.js',
                          array('jquery'),
                          null);

        wp_localize_script('rwk_remove_image_script',
                           'ajax_object',
                           array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
add_action('admin_enqueue_scripts',
           'enqueue_wp_media_files');
