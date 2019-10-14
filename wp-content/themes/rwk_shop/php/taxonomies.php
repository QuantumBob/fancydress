<?php

function create_colours_hierarchical_taxonomy()
{

    $labels = array(
        'name' => _x('Colours', 'taxonomy general name'),
        'singular_name' => _x('Colour', 'taxonomy singular name'),
        'search_items' => __('Search Colours'),
        'all_items' => __('All Colours'),
        'parent_item' => __('Parent Colour'),
        'parent_item_colon' => __('Parent Colour:'),
        'edit_item' => __('Edit Colour'),
        'update_item' => __('Update Colour'),
        'add_new_item' => __('Add New Colour'),
        'new_item_name' => __('New Colour Name'),
        'menu_name' => __('Colours'),
    );
    
    register_taxonomy('colours', array('product'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'colour'),
    ));
    
    wp_insert_term('Red', 'colours');
    wp_insert_term('Green', 'colours');
    wp_insert_term('Blue', 'colours');
}
add_action('init', 'create_colours_hierarchical_taxonomy', 0);