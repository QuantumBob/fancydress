<?php

function get_primary_image($post)
{
    if (!in_the_loop()) {
        $post = get_post($post);
    }
    $image_size = 'full'; // 'thumbnail'; it would be better to use thumbnail size here (150x150 or so)
    $meta_key   = 'primary_image';

    $primary_image = get_post_meta($post->ID,
                                   $meta_key,
                                   true);

    if (empty($primary_image)) {
        $meta_key      = 'secondary_images';
        $primary_image = get_post_meta($post->ID,
                                       $meta_key,
                                       true);
        if (empty($primary_image) || $primary_image === false || $primary_image === 'null') {
            return;
        }
        $primary_image = $primary_image[0];
    }

    $image_attributes = wp_get_attachment_image_src($primary_image,
                                                    $image_size);

    $image  = 'class="rwk_sec_image"><img id="' . $primary_image . '" src="' . $image_attributes[0] . '" style="max-width:95%; display:block;" />';
    $html[] = '<div>';
    $html[] = '<input id="secondary_images" type="hidden" name="secondary_images" value=""/>';
    $html[] = '<div class="container mt-2">';
    $html[] = '<div class="row image-tiles">';
    $html[] = '<div class = "col-6">';

    $html[] = '<a href = "#" id = "rwk_single_image_button"' . $image . '</a>';

    $html[] = '</div>';
    $html[] = '</div>';
    $html[] = '</div>';
    $html[] = '</div>';

    echo empty($html) ? '' : implode(' ',
                                     $html);
}
if (!function_exists('rwk_shop_post_thumbnail')) :

    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function rwk_shop_post_thumbnail()
    {
        if (is_singular()) :
            ?>

            <figure class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </figure><!-- .post-thumbnail -->

            <?php
        else :
            ?>

            <figure class="post-thumbnail">
                <a class="post-thumbnail-inner" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                    <?php the_post_thumbnail('post-thumbnail'); ?>
                </a>
            </figure>

        <?php
        endif; // End is_singular().
    }
endif;

function get_next_image()
{
    $post_id  = filter_input(INPUT_POST,
                             'post_id');
    $image_id = filter_input(INPUT_POST,
                             'image_id');

    $image_size = 'full';
    $meta_key   = 'secondary_images';
    $images     = get_post_meta($post_id,
                                $meta_key,
                                true);
    if (empty($images) || $images === false || $images === 'null') {
        return;
    }
    $image_data = [];

    $key      = array_search($image_id,
                             $images);
    $image_id = $images[0];

    if ($key !== false) {
        $key += 1;
        if (array_key_exists($key,
                             $images)) {
            $image_id = $images[$key];
        }
    }

    $image_attributes = wp_get_attachment_image_src($image_id,
                                                    $image_size);

    $image_data['image_src'] = $image_attributes[0];
    $image_data['image_id']  = $image_id;

    header('Content-Type: application/json');
    echo json_encode($image_data);
    wp_die();
}
add_action('wp_ajax_get_next_image',
           'get_next_image');
