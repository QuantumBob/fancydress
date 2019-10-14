<?php
if (!function_exists('get_recent_products')) :

    /**
     * Gets a group of products for the front page.
     */
    function get_recent_products()
    {
        $args = array(
            'numberposts' => '10',
            'post_status' => 'publish',
            'post_type'   => 'rwk_product',
        );

        $recent_posts = new WP_Query($args);
        wp_reset_query();

        if ($recent_posts->have_posts()) : while ($recent_posts->have_posts()) : $recent_posts->the_post();
//                set_query_var('column_class',
//                              'col-4');
//                get_template_part('template-parts/product',
//                                  'one-of-many');
                product_one_of_many(get_the_ID(),
                                    'col-4');
            endwhile;
        endif;
    }
endif;

if (!function_exists('product_one_of_many')) :

    function product_one_of_many($post_id, $column_class)
    {
        $product_details = get_post_meta($post_id,
                                         'product_details',
                                         true);

        $primary_image = $product_details['primary_image'];
        if ($primary_image === '' || $primary_image == null) {
            $primary_image = $product_details['product_images'];
//            if (empty($primary_image) || $primary_image === false || $primary_image === 'null') {
            if (!is_array($primary_image)) {
                $primary_image = [];
            }
            else {
                $primary_image = $primary_image[0];
            }
        }
        $image_size       = 'medium';
        $image_attributes = wp_get_attachment_image_src($primary_image,
                                                        $image_size);
        $primary_image    = $image_attributes[0];
        $permalink        = get_permalink($post_id);

        $index      = count($product_details['product_variations']) - 1;
        $price_low  = $product_details['product_variations'][$index]['price_low'];
        $price_high = $product_details['product_variations'][$index]['price_high'];
        if ($price_low === $price_high) {
            $price_range = 'Price - £' . $price_high;
        }
        elseif ($price_low <= $price_high) {
            $price_range = 'Prices from £' . $price_low . ' to £' . $price_high;
        }
        elseif ($price_low >= $price_high) {
            $price_range = 'Prices from £' . $price_high . ' to £' . $price_low;
        }
        else {
            $price_range = 'See Amazon for prices';
        }

        $title = the_title('',
                           '',
                           false);

        $html[] = '<div class="' . $column_class . ' text-center">';
        $html[] = '<a  href="' . $permalink . '">';
        $html[] = '<img class="mx-auto grid-img" src="' . $primary_image . '" style="display:block;" />';
        $html[] = '<span class="link-font" style="display:block;">' . $title . '</span>';
        $html[] = '<span class="link-font" style="display:block;">' . $price_range . '</span>';
        $html[] = '</a>';
        $html[] = '</div>';

        /*
          <div class="' . $column_class . ' text-center">
          <a  href="' . $permalink . '">
          <img class="mx-auto grid-img" src="' . $primary_image . '" style="display:block;" />
          </a>
          <a class="link-font" href="' . $permalink . '" style="display:block;">' . $title . '</a>';
          <a class="link-font" href="' . $permalink . '" style="display:block;">Price range :' . $price_range . '</a>
          <a class="link-font" href="' . get_affiliate_link() . '" style="display:block;">Amazon Link</a>
          </div>
         */

        echo empty($html) ? '' : implode('',
                                         $html);
    }
endif;

if (!function_exists('the_product')):

    function the_product($post_id)
    {
        $product = get_post_meta($post_id,
                                 'product_details',
                                 true);
        if (is_array($product)) {
            return $product;
        }
        return false;
    }
endif;

if (!function_exists('the_features')):

    function the_features()
    {

        echo '';
    }
endif;

if (!function_exists('the_prices')) :

    function the_prices($variations)
    {
        foreach ($variations as $variation)
        {
            if (!isset($variation['price_low'])) {
                $html [] = '<div class="col-4">';
                $html [] = '<div class="price">';
                $html [] = $variation['price'];
                $html [] = '</div>';
                $html [] = '<div class="price">';
                $html [] = $variation['size'];
                $html [] = '</div>';
                $html [] = '</div>';
            }
        }
        echo empty($html) ? '' : implode('',
                                         $html);
    }
endif;

if (!function_exists('the_price_range')) :

    function the_price_range()
    {

        echo '';
    }
endif;

if (!function_exists('the_affiliate_link')):

    function the_affiliate_link()
    {

        echo '';
    }
endif;

if (!function_exists('the_image_carousel')) :

    function the_image_carousel($post_id = null)
    {
        if ($post_id === null) {
            global $post;
            $post_id = $post - ID;
        }

        $product_details = get_post_meta($post_id,
                                         'product_details',
                                         true);

        $images     = [];
        $image_size = 'full';
        $image_ids  = $product_details['product_images'];


        if (!is_array($image_ids)) {
            $image_ids = [];
        }
        $key = 0;
        foreach ($image_ids as $image_id)
        {
            $image_id                     = $image_ids[$key];
            $image_attributes             = wp_get_attachment_image_src($image_id,
                                                                        $image_size);
            $images[$key]['image_src']    = $image_attributes[0];
            $images[$key]['image_id']     = $image_id;
            $images[$key]['aspect_ratio'] = $image_attributes[1] / $image_attributes[2];
            $key ++;
        }

        set_query_var('images',
                      $images);
        get_template_part('template-parts/product',
                          'carousel');
    }
endif;

function _rwk_end()
{

}
