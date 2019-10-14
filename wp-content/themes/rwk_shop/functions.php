<?php
include 'php/menus.php';
include 'php/template-functions.php';
include 'php/taxonomies.php';
include 'php/image-functions.php';
include 'php/template-tags.php';

// disables gutenburg editor
/*
  add_filter('use_block_editor_for_post',
  '__return_false',
  10);
 */

add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        )
);

function load_stylesheets()
{
    wp_register_style('bootstrap',
                      get_template_directory_uri() . '/css/bootstrap.min.css',
                      array(),
                      false,
                      'all');
    wp_enqueue_style('bootstrap');

    wp_register_style('rwk_style',
                      get_template_directory_uri() . '/style.css',
                      array(),
                      false,
                      'all');
    wp_enqueue_style('rwk_style');
}
add_action('wp_enqueue_scripts',
           'load_stylesheets',
           11);

function load_javascript()
{
    wp_enqueue_script('jquery');

    wp_enqueue_script('bootstrap',
                      get_template_directory_uri() . '/js/bootstrap.bundle.min.js',
                      '',
                      null,
                      true);
    add_action('wp_enqueue_scripts',
               'bootstrap');

    wp_register_script('shop_js',
                       get_template_directory_uri() . '/js/scripts.js',
                       '',
                       null,
                       true);
    wp_enqueue_script('shop_js');

    wp_localize_script('shop_js',
                       'ajax_object',
                       array(
                'ajaxurl'  => admin_url('admin-ajax.php'),
                'we_value' => 1234));
}
add_action('wp_enqueue_scripts',
           'load_javascript');
