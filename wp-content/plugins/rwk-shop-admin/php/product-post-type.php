<?php

function create_product_posttype()
{
    $labels = array(
        'name'               => __('Products',
                                   'Post Type General Name',
                                   'text_domain'),
        'singular_name'      => __('Product',
                                   'Post Type Singular Name',
                                   'text_domain'),
        'menu_name'          => __('Products',
                                   'text_domain'),
        'parent_item_colon'  => __('Parent Product',
                                   'text_domain'),
        'all_items'          => __('All Products',
                                   'text_domain'),
        'view_item'          => __('View Product',
                                   'text_domain'),
        'add_new_item'       => __('Add New Product',
                                   'text_domain'),
        'add_new'            => __('Add New',
                                   'text_domain'),
        'edit_item'          => __('Edit Product',
                                   'text_domain'),
        'update_item'        => __('Update Product',
                                   'text_domain'),
        'search_item'        => __('Search Products',
                                   'text_domain'),
        'not_found'          => __('Not Found',
                                   'text_domain'),
        'not_found_in_trash' => __('Not Found in Trash',
                                   'text_domain')
    );

    $supports   = array('title', 'editor', 'author', 'exerpt', 'thumbnail', 'comments', 'revisions', 'page-attributes');
    $taxonomies = array('category', 'post_tag');

    $args = array(
        'label'               => __('products'),
        'description'         => __('Products for the Shop'),
        'labels'              => $labels,
        'supports'            => $supports,
        'taxonomies'          => $taxonomies,
        'hierarchical'        => true, // false for post, true for page
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post'
    );

    register_post_type('rwk_product',
                       $args);
}
add_action('init',
           'create_product_posttype',
           0);

//function shop_featured_image()
//{
//    add_theme_support('post-thumbnails',
//                      array('post', 'page', 'product'));
//}
//add_action('after_setup_theme',
//           'shop_featured_image');
