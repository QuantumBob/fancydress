<?php

function fill_query_vars($array)
{
    $keys = array(
        'original_url',
        'product_affiliate_link',
        'product_last_scraped',
        'product_title',
        'product_id',
        'shop_title',
        'shop_id',
        'auto_images',
        'primary_image',
    );

    foreach ($keys as $key)
    {
        if (!isset($array[$key])) {
            $array[$key] = '';
        }
    }

    $array_keys = array(
        'product_variations',
        'product_features',
        'product_image_urls',
        'extra_features',
        'product_image_ids',
    );

    foreach ($array_keys as $key)
    {
        if (!isset($array[$key])) {
            $array[$key] = array();
        }
    }
    return $array;
}

/**
 * logs an array to the log file
 *
 * @param array $array the array to log
 */
function log_array($array)
{
    file_put_contents('D:\Coding\code\Web Projects\fancydress\log.txt',
                      implode("\n",
                              $array));
}

/**
 * Removes characters from the url that might interfere with Wordpress
 *
 * @param strin $url the url string to clean
 */
function cleanup_url($url)
{

    for ($i = 0; $i <= 45; ++$i)
    {
        $url = str_replace(chr($i),
                               "",
                               $url);
    }

    for ($i = 123; $i <= 127; ++$i)
    {
        $url = str_replace(chr(127),
                               "",
                               $url);
    }
    return $url;
}

function strip_non_unicode($str)
{

    for ($i = 0; $i <= 31; ++$i)
    {
        $str = str_replace(chr($i),
                               "",
                               $str);
    }

    $str = str_replace(chr(127),
                           "",
                           $str);

    if (0 === strpos(bin2hex($str),
                             'efbbbf')) {
        $str = substr($str,
                      3);
    }
    return $str;
}

function json_decode_nice($json, $assoc = FALSE)
{

    $json = str_replace(array(
        "\n",
        "\r"),
                        "",
                        $json);
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/',
                         '$1"$3":',
                         $json);
    $json = preg_replace('/(,)\s*}$/',
                         '}',
                         $json);
    return json_decode($json,
                       $assoc);
}

function get_any_tag($x, $tag, &$results)
{

    $count = 1;

    foreach ($x->query("//" . $tag) as $node)
    {
        $key = 'amazon_' . $node->nodeName . '_' . strval($count);
        if ($node->nodeValue !== '') {
            $results [$key] = $node->nodeValue;
            $count ++;
        }
    }
}

function get_meta_id($post_id, $meta_key)
{
    global $wpdb;
    $result = $wpdb->get_results($wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
                                                $post_id,
                                                $meta_key));
    if ($result [0]->meta_id != '') {
        return $result [0]->meta_id;
    }

    return false;
}

function get_complete_meta($post_id, $meta_key)
{
    global $wpdb;
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
                                                 $post_id,
                                                 $meta_key));
    if ($results != '') {
        return $results;
    }

    return false;
}

function image_url_to_post_id($url)
{
    global $wpdb;

    $dir  = wp_get_upload_dir();
    $path = basename($url);

    if (0 === strpos($path,
                     $dir ['baseurl'] . '/')) {
        $path = substr($path,
                       strlen($dir ['baseurl'] . '/'));
    }

    $sql     = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s",
                              $path);
    $post_id = $wpdb->get_var($sql);

    return $post_id;
}

function get_primary_image_id($post_id)
{
    global $wpdb;
    $meta_key = 'primary_image';

    $result = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
                                                $post_id,
                                                $meta_key));
    if ($result [0]->meta_value != '') {
        return $result [0]->meta_value;
    }

    return false;
}

function upload_image_to_wp_media($url)
{
    if ($url === null) {
        return -1;
    }

    $upload_dir = wp_upload_dir();

    $filename = basename($url);
    $filename = cleanup_url($filename);

    if (wp_mkdir_p($upload_dir ['path'])) {
        $file = $upload_dir ['path'] . '/' . $filename;
    }
    else {
        $file = $upload_dir ['basedir'] . '/' . $filename;
    }

    if (file_exists($file)) {
        $result = image_url_to_post_id($file);
        return $result;
    }


    $image_data = file_get_contents($url);

    file_put_contents($file,
                      $image_data);

    $wp_filetype = wp_check_filetype($filename,
                                     null);

    $attachment = array(
        'post_mime_type' => $wp_filetype ['type'],
        'post_title'     => sanitize_file_name($filename),
        'post_content'   => '',
        'post_status'    => 'inherit');

    $attach_id   = wp_insert_attachment($attachment,
                                        $file);
    require_once (ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id,
                                                   $file);
    wp_update_attachment_metadata($attach_id,
                                  $attach_data);

    // if ( wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $fullsizepath ) ) )
    //       return true;

    return $attach_id;
}

/**
 * Used by ajax JavaScript call
 * Do not delete!
 */
function remove_image()
{
    $post_id  = intval(sanitize_text_field(filter_input(INPUT_POST,
                                                        'post_id')));
    $meta_key = sanitize_text_field(filter_input(INPUT_POST,
                                                 'meta_key'));
    $image_ID = intval(sanitize_text_field(filter_input(INPUT_POST,
                                                        'image_id')));

    if ($meta_key === 'featured_image') {

        if ($post_id != 0) {
            delete_post_meta($post_id,
                             $meta_key);
        }
    }
    elseif ($meta_key === 'secondary_images') {

        if ($image_ID !== "") {

            $secondary_images_json  = get_post_meta($post_id,
                                                    $meta_key,
                                                    true);
            $secondary_images_array = json_decode($secondary_images_json,
                                                  true);

            $key = array_search($image_ID,
                                $secondary_images_array,
                                true);

            unset($secondary_images_array[$key]);

            $secondary_images_json = json_encode($secondary_images_array,
                                                 JSON_FORCE_OBJECT);

            update_post_meta($post_id,
                             $meta_key,
                             $secondary_images_json);
        }
    }
}
add_action('wp_ajax_remove_image',
           'remove_image');

function get_post_id_from_url()
{
    $pattern = '/(?!post=)\d+/';
    $url     = filter_input(INPUT_SERVER,
                            'HTTP_REFERER');
    $matches = null;
    $count   = preg_match($pattern,
                          $url,
                          $matches);

    if ($count === 0) {
        return;
    }
    $post_id = intval($matches[0]);
    return $post_id;
}

function get_original_urls()
{
    global $wpdb;
    $meta_key = 'product_url';
    $results  = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s",
                                                  $meta_key));
    $urls     = [];
    foreach ($results as $array)
    {
        $urls [] = $array->meta_value;
    }
    if ($urls != '') {
        return $urls;
    }

    return [];
}

function get_original_url($post_id)
{
    global $wpdb;
    $meta_key = 'original_url';
    $results  = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
                                                  $post_id,
                                                  $meta_key));
    $url      = '';
    foreach ($results as $array)
    {
        $url = $array->meta_value;
    }
    if ($url != '') {
        return $url;
    }

    return false;
}
