<?php

function enqueue_secondary_image_scripts($page)
{
    if ($page == 'post.php' || $page === 'post-new.php') {
        wp_enqueue_script('rwk_secondary_images_script',
                          PLUGIN_URL . 'metaboxes/image_metaboxes/secondary_images_metabox_script.js',
                          array('jquery'),
                          null);
        wp_localize_script('rwk_secondary_images_script',
                           'ajax_object',
                           array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
add_action('admin_enqueue_scripts',
           'enqueue_secondary_image_scripts');

// Add fields to the edit product page
function create_secondary_image_metabox()
{
    add_meta_box('secondary-images-meta',
                 'Images',
                 'rwk_secondary_images_metabox',
                 'rwk_product',
                 'side',
                 'low');
}
add_action('admin_init',
           'create_secondary_image_metabox');

function rwk_secondary_images_metabox($post)
{

    $html[] = '<div>';
    $html[] = '<a href="#" id ="rwk_upload_secondary_button" class="button">Add image</a>';
    $html[] = '<input id="secondary_images" type="hidden" name="secondary_images" value=""/>';
    $html[] = '<div class="container mt-2">';
    $html[] = '<div class="row image-tiles">';

    $html[] = build_image_grid($post);

    $html[] = '</div>';
    $html[] = '</div>';
    $html[] = '</div>';

    echo empty($html) ? '' : implode(' ',
                                     $html);
}

function build_image_grid($post)
{
    $meta_key        = 'product_details';
    $image_size      = 'thumbnail'; //'full'; // it would be better to use thumbnail size here (150x150 or so)
    $product_details = get_post_meta($post->ID,
                                     $meta_key,
                                     true);

    if (isset($product_details['product_images'])) {
        $images_array = $product_details['product_images'];
    }
    else {
        $images_array = [];
    }

//    if (empty($images_array) || $images_array === false || $images_array === 'null') {
//        $images_array = [];
//    }

    if (isset($product_details['primary_image'])) {
        $primary_image = $product_details['primary_image'];
    }
    else {
        $primary_image = '';
    }

    foreach ($images_array as $ID)
    {
        $image_attributes = wp_get_attachment_image_src($ID,
                                                        $image_size);

        if ($image_attributes) {

            $image   = 'class="rwk_sec_image"><img src="' . $image_attributes[0] . '" style="max-width:95%; display:block;" />';
            $checked = $primary_image == $ID ? 'checked' : '';

            $html [] = '<div class = "col-6">';
            $html [] = '<div class="radio mt-2">';
            $html [] = '<label class="primary_image_label" for="primary_image_' . $ID . '">';
            $html [] = 'Primary Image';
            $html [] = '</label>';
            $html [] = '<input type="radio" id="primary_image_' . $ID . '" name="primary_image" value="' . $ID . '" ' . $checked . '/>';
            $html [] = '</div>';
            $html [] = '<a href = "#" id = "rwk_secondary_image_button"' . $image . '</a>';
            $html [] = '<a href="#" data-image-id="' . $ID . '" data-image-key="secondary_images" id="rwk_remove_image_button" style="display:inline-block;">Remove image</a>';
            $html[]  = '</div>';
        }
    }

    return empty($html) ? '' : implode(' ',
                                       $html);
}

function save_secondary_image_details($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    $meta_key = 'secondary_images';

    $new_image_ids = sanitize_text_field(filter_input(INPUT_POST,
                                                      $meta_key));
    if ($new_image_ids != "") {

        $new_images_array = explode(',',
                                    $new_image_ids);

        $images_array = get_post_meta($post_id,
                                      $meta_key,
                                      true);

        if (empty($images_array) || $images_array === 'null') { // no images added yet
            $images_array = [];
        }

        foreach ($new_images_array as $image_id)
        {
            if (!in_array($image_id,
                          $images_array)) {
                $images_array[] = intval($image_id);
            }
        }

        update_post_meta($post_id,
                         $meta_key,
                         $images_array);
    }

    $meta_key         = 'primary_image';
    $primary_image_id = sanitize_text_field(filter_input(INPUT_POST,
                                                         $meta_key));
    if ($primary_image_id != "") {
        update_post_meta($post_id,
                         $meta_key,
                         $primary_image_id);
    }
}
//add_action('save_post_product',
//           'save_secondary_image_details',
//           10,
//           1);
