<?php

function enqueue_primary_image_scripts($page)
{
    if ($page == 'post.php' || $page === 'post-new.php')
    {
        wp_enqueue_script('rwk_primary_image_script',
                          PLUGIN_URL . 'js/primary_image_metabox_script.js',
                          array('jquery'),
                          null);
        wp_localize_script('rwk_primary_image_script',
                           'ajax_object',
                           array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}
add_action('admin_enqueue_scripts',
           'enqueue_primary_image_scripts');

// Add fields to the edit product page
function create_primary_image_metabox()
{
    add_meta_box('primary-image-meta',
                 'Primary Image',
                 'rwk_primary_image_metabox',
                 'product',
                 'side',
                 'low');
}
add_action('admin_init',
           'create_primary_image_metabox');

function rwk_primary_image_metabox($post)
{
    $meta_key = 'featured_image';
    $value    = get_post_meta($post->ID,
                              $meta_key,
                              true);
    if ($value === "")
    {
        $value = -1;
    }
    $image_size = 'thumbnail'; //'full'; // it would be better to use thumbnail size here (150x150 or so)

    $image_attributes = wp_get_attachment_image_src($value,
                                                    $image_size);

    // if we come to this page and there are images already selected we use this (not the javascript)
    if ($image_attributes)
    {

        // $image_attributes[0] - image URL
        // $image_attributes[1] - image width
        // $image_attributes[2] - image height

        $image   = 'class=""><img src="' . $image_attributes[0] . '" style="max-width:95%; display:block;" />';
        $display = 'inline-block';
    }
    else
    {
        $image   = ' class="button">Upload image';
        $display = 'none'; // display state ot the "Remove image" button
    }

    $html[] = '<div>';
    $html[] = '<a href="#" id ="rwk_upload_image_button"' . $image . '</a>';
    $html[] = '<input type="hidden" name="' . $meta_key . '" id="' . $meta_key . '" value="' . esc_attr($value) . '">';
    $html[] = '<a href="#" data-image-key="featured_image" id="rwk_remove_image_button" style="display:inline-block; display:' . $display . '">Remove image</a>';
    $html[] = '</div>';

    echo empty($html) ? '' : implode(' ',
                                     $html);
}

function save_primary_image_details($post_ID)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    {
        return $post_ID;
    }

    $meta_key   = 'featured_image';
    $POST_value = sanitize_text_field(filter_input(INPUT_POST,
                                                   $meta_key));
    if ($POST_value != "")
    {
        update_post_meta($post_ID,
                         $meta_key,
                         $POST_value);
    }
}
add_action('save_post_product',
           'save_primary_image_details',
           10,
           1);
