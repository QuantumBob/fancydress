<?php

function product_posts_columns($columns)
{
    $columns = array(
        'cb'         => '<input type="checkbox" />',
        'title'      => 'Product Title',
        'exerpt'     => 'Exerpt',
        'amazon_url' => 'Amazon Url',
//        'description'  => 'Description',
//        'colour'       => 'Colour',
//        'product_cats' => 'Product Cats'
    );

    return $columns;
}
add_filter('manage_rwk_product_posts_columns',
           'product_posts_columns');

function product_posts_custom_column($column, $post_id)
{
    switch ($column)
    {

        case 'exerpt' :
            echo get_the_excerpt($post_id);
            break;

        case 'amazon_url' :
            echo get_post_meta($post_id,
                               'original_url',
                               true);
            break;
    }
}
add_action('manage_rwk_product_posts_custom_column',
           'product_posts_custom_column',
           10,
           2);
