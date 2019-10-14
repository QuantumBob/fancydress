<?php
include 'amazon_html.php';

/**
 * Enqueues the scripts needed by the amazon metabox
 *
 * @param none $name this is it
 */
function enqueue_amazon_scripts($page)
{
    if ($page == 'post.php' || $page === 'post-new.php') {
        wp_enqueue_script('rwk_amazon_script',
                          PLUGIN_URL . 'metaboxes/rwk_amazon/amazon_metabox_script.js',
                          array('jquery'),
                          null);
        wp_localize_script('rwk_amazon_script',
                           'ajax_object',
                           array('ajaxurl' => admin_url('admin-ajax.php'), 'we_value' => 1234));
    }
}
add_action('admin_enqueue_scripts',
           'enqueue_amazon_scripts');

// Add fields to the edit product page
function add_amazon_metabox()
{
    // id, title, callback, screen, context, priority, callback_args
    add_meta_box('amazon-meta',
                 'Scrape Amazon',
                 'create_amazon_metabox',
                 'rwk_product',
                 'normal',
                 'high');
}
add_action('admin_init',
           'add_amazon_metabox');

function create_amazon_metabox($post)
{
    $original_url           = '';
    $product_affiliate_link = '';
    $product_last_scraped   = '';
    $product_title          = '';
    $product_id             = '';
    $product_variations     = [];
    $product_features       = [];
    $product_image_urls     = [];

    $shop_title     = get_the_title($post);
    $shop_id        = '';
    $extra_features = [];
    $auto_images    = "";

    $meta_key = 'product_details';
    $details  = get_post_meta($post->ID,
                              $meta_key,
                              true);

    if ($details !== "" && $details !== "[]") {

        $original_url           = $details ['original_url'];
        $product_affiliate_link = $details['product_affiliate_link'];
        $product_last_scraped   = $details ['product_last_scraped'];
        $product_title          = $details ['product_title'];
        $product_id             = $details ['product_id'];

        foreach ($details as $key => $detail)
        {
            if ($key === 'product_variations') {
                $product_variations = $detail;
            }
            if (stripos($key,
                        'product_feature_') !== false) {
                $product_features [] = $detail;
            }
            if (stripos($key,
                        'shop_feature_') !== false) {
                $extra_features [] = $detail;
            }
            if (stripos($key,
                        'product_image_url_') !== false) {
                $product_image_urls [] = $detail;
            }
        }
    }
    else {
        $shop_title  = "";
        $auto_images = "checked";
    }
    product_header($original_url,
                   $post,
                   $auto_images,
                   $product_last_scraped,
                   $product_affiliate_link);


    echo '<div class="container mt-2">';

    product_titles_and_ids($product_title,
                           $product_id,
                           $shop_title,
                           $shop_id);
    product_variations_container($product_variations);
    echo '<div class="row">';

    product_features_container($product_features);

    product_shop_features_container($extra_features);

    echo '</div>';

    product_image_url_container($product_image_urls);

    echo '</div>';
}

// Ajax handler function
function scrape_product_page()
{
    $original_url = filter_input(INPUT_POST,
                                 'original_url');
    $pos          = stripos($original_url,
                            '/ref');
    if ($pos !== false) {
        $original_url = substr($original_url,
                               0,
                               $pos);
    }
    $post_id = filter_input(INPUT_POST,
                            'post_id');

    $scrape_data = [];

    $dom                     = new DOMDocument ();
    $dom->preserveWhiteSpace = false;

    if ($original_url === '') {
        header('Content-Type: application/json');
        echo json_encode([]);

        wp_die();
    }
    elseif ($original_url === 'nun') {
        $result                       = $dom->loadHTMLFile('D:\Coding\code\Web Projects\fancydress\nun.html');
        $scrape_data ['original_url'] = 'D:\Coding\code\Web Projects\fancydress\nun.html';
    }
    elseif ($original_url === 'bird') {
        $result                       = $dom->loadHTMLFile('D:\Coding\code\Web Projects\fancydress\bird.html');
        $scrape_data ['original_url'] = 'D:\Coding\code\Web Projects\fancydress\bird.html';
    }
    elseif ($original_url === 'stop') {
        return;
    }
    else {
        // if we have already scraped this url do nothing, maybe message!
        // stop scraping a new product if it was already in database
        $urls = get_original_urls();

        if ($urls !== '') {
            $i = in_array($original_url,
                          $urls);
            if ($i) {
                $scrape_data                          = [];
                $scrape_data ['original_url']         = 'This url has already been scraped';
                $scrape_data ['product_last_scraped'] = false;
                echo json_encode($scrape_data);

                wp_die();
            }
        }

        $original_url                 = '' ? get_original_url($post_id) : $original_url;
        $page                         = file_get_contents($original_url);
        $result                       = $dom->loadHTML($page);
        $scrape_data ['original_url'] = $original_url;
    }

    $scrape_data ['shop_title'] = get_the_title($post_id);
    if ($scrape_data ['shop_title'] === 'Auto Draft') {
        $scrape_data ['shop_title'] = '';
    }

    $scrape_data ['product_last_scraped'] = current_time('mysql');
    $x                                    = new DOMXPath($dom);

    if ($result === true) {

        $scrape_data ['product_img_urls'] = scrape_image_urls($x);

        $scrape_data ['product_title'] = scrape_product_title($x);

        $scrape_data ['product_variations'] = scrape_variations($x);

        $scrape_data ['product_features'] = scrape_features($x);

        // $size_price_prime = get_size_price_prime($x);

        $scrape_data ['product_id'] = scrape_asin($x);
    }

    log_array($scrape_data);

    $auto_update_images = sanitize_text_field(filter_input(INPUT_POST,
                                                           'auto_upload_to_post'));
    if ($auto_update_images === 'on') {
        $attachment_ids = [];
        foreach ($scrape_data ['product_img_urls'] as $url)
        {
            $id = upload_image_to_wp_media($url);
            if ($id !== 0) {
                $attachment_ids [] = $id;
            }
        }
        $scrape_data ['product_image_ids'] = $attachment_ids;
    }

    header('Content-Type: application/json');
    echo json_encode($scrape_data);

    wp_die();
}
add_action('wp_ajax_scrape_product_page',
           'scrape_product_page');

/**
 * Scrapes the images from the url
 *
 * @param none
 */
function refresh_product_images()
{
    $scrape_data = [];

    $dom                     = new DOMDocument ();
    $dom->preserveWhiteSpace = false;

    $post_id = filter_input(INPUT_POST,
                            'post_id');

    $original_url = get_original_url($post_id);

    $page                         = file_get_contents($original_url);
    $result                       = $dom->loadHTML($page);
    $scrape_data ['original_url'] = $original_url;
    $x                            = new DOMXPath($dom);

    if ($result === true) {
        $scrape_data ['product_img_urls'] = scrape_image_urls($x);
    }

    $attachment_ids = [];
    foreach ($scrape_data ['product_img_urls'] as $url)
    {
        $id = upload_image_to_wp_media($url);
        if ($id !== 0) {
            $attachment_ids [] = $id;
        }
    }
    $scrape_data ['product_image_ids'] = $attachment_ids;


    header('Content-Type: application/json');
    echo json_encode($scrape_data);

    wp_die();
}
add_action('wp_ajax_refresh_product_images',
           'refresh_product_images');

function scrape_variations($x)
{

    $query          = "//div[@id='variation_size_name']//span[starts-with(@class, 'a-size-base')]"; // this is the size
    $node_list_size = $x->query($query);
    $count          = 0;
    $variants       = [];
    $price_low      = 1000;
    $price_high     = 0;

    foreach ($node_list_size as $node_size)
    {
        $query           = "parent::div/following-sibling::div[1]//span[starts-with(@id, 'size_name_')]"; // this is the price
        $node_list_price = $x->query($query,
                                     $node_size);

        if ($node_size->nodeValue !== '') {

            // $variants[$size_key] = []; // trim($node_price->nodeValue);
            foreach ($node_list_price as $node_price)
            {
                $size  = trim($node_size->nodeValue);
                $price = trim($node_price->nodeValue);

                $price = str_replace("£",
                                     "",
                                     $price);

                if ($price < $price_low) {
                    $price_low = $price;
                }
                if ($price > $price_high) {
                    $price_high = $price;
                }
                array(
                    'size'  => $size,
                    'price' => $price);
            }
            $count ++;
        }
    }

    if (empty($variants)) {
        $query     = "//div[@id='unifiedPrice_feature_div']//span[@id='priceblock_ourprice']"; // this is for single price items
        $node_list = $x->query($query);
        foreach ($node_list as $node)
        {
            $price = trim($node->nodeValue);
            $price = str_replace("£",
                                 "",
                                 $price);
        }
        $variants [] = array(
            'size'  => 'One Size',
            'price' => $price);
        $price_low   = $price;
        $price_high  = $price;
    }

    $variants [] = array(
        'price_low'  => $price_low,
        'price_high' => $price_high);


    return $variants;
}

function scrape_features($x)
{
    $query     = "//div[@id='feature-bullets']//span[@class='a-list-item']";
    $node_list = $x->query($query);
    $features  = [];
    $count     = 0;
    foreach ($node_list as $node)
    {
        $features [] = trim($node->nodeValue);
        $count ++;
    }

    if (empty($features)) {
        $features [] = 'No features found';
    }
    return $features;
}

function scrape_size_price_prime($x)
{

    // class a-icon-prime when logged in
    $query     = "//span[@id='ourprice_shippingmessage']//i[@class='a-icon-prime']";
    $node_list = $x->query($query);
    foreach ($node_list as $node)
    {
        $key = 'amazon_prime';
        if ($node->nodeValue !== '') {
            $results [$key] = $node->nodeValue;
        }
    }
    return $results;
}

function scrape_asin($x)
{

    $id        = 'ASIN';
    $query     = "//input[@id='$id']";
    $node_list = $x->query($query);
    foreach ($node_list as $node)
    {
        if ($node->hasAttribute('value')) {
            $product_asin = $node->getAttribute('value');
        }
    }
    return $product_asin;
}

function scrape_product_title($x)
{
    $id        = 'productTitle';
    $query     = "//span[@id='$id']";
    $node_list = $x->query($query);

    foreach ($node_list as $node)
    {
        if ($node->nodeValue !== '') {
            $product_title = trim($node->nodeValue);
        }
    }
    return $product_title;
}

function scrape_image_urls($x)
{
    $images            = [];
    $scripts_node_list = $x->query("//div[@id='imageBlock']/following-sibling::script[1]");

    foreach ($scripts_node_list as $script)
    {
        $re      = '/= {(.*)};/s';
        $matches = [];
        $end     = preg_match($re,
                              $script->nodeValue,
                              $matches,
                              PREG_UNMATCHED_AS_NULL);
        // $results['img_url'] = $script->nodeValue;
        $str     = trim($matches [0],
                        ' =;');

        $str = str_replace('\'',
                           '"',
                           $str);

        $re  = '/\"airyConfig\" *:(.*)}}\"\)/s';
        $str = preg_replace($re,
                            '',
                            $str);

        $pos = strripos($str,
                        ',');
        $str = substr_replace($str,
                              '',
                              $pos,
                              1);

//        file_put_contents('D:\Coding\code\Web Projects\fancydress\imgs.txt',
//                          $str);
        $out = json_decode($str,
                           true);

        if ($out === false) {
            return [];
        }
        $image_array = $out ["colorImages"] ["initial"];
        foreach ($image_array as $images)
        {
            $results [] = $images ["hiRes"];
        }
    }
    return $results;
}

function save_product_details($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    $product_details_array = [];
    $count                 = 0;
    $meta_key              = 'product_details';

    foreach (array_keys($_POST) as $key)
    {
        if (stripos($key,
                    'product_') === 0) {
            if ($key === 'product_variations') {

                $variations = $_POST [$key];
                foreach ($variations as $variation)
                {
                    if (array_key_exists('price_low',
                                         $variation)) {
                        $product_details_array [$key] [$count] ['price_low']  = $variation['price_low'];
                        $product_details_array [$key] [$count] ['price_high'] = $variation['price_high'];
                    }
                    else {
                        $product_details_array [$key] [$count] = $variation;
                    }
                    $count ++;
                }
            }
            else if (stripos($key,
                             'product_image_url_') === 0) {
                $url                          = sanitize_text_field(filter_input(INPUT_POST,
                                                                                 $key));
                $product_details_array [$key] = str_replace('\\',
                                                            '\\\\',
                                                            $url);
            }
            else {
//                if ($key !== 'original_url') {
                $value                        = sanitize_text_field(filter_input(INPUT_POST,
                                                                                 $key));
                $product_details_array [$key] = str_replace('\\',
                                                            '\\\\',
                                                            $value);
//                }
            }
        }

        if (stripos($key,
                    'shop_') === 0) {
            $value = sanitize_text_field(filter_input(INPUT_POST,
                                                      $key));
            if ($value !== "") {
                $product_details_array [$key] = str_replace('\\',
                                                            '\\\\',
                                                            $value);
            }
        }
    }

    $product_affiliate_link = sanitize_text_field(filter_input(INPUT_POST,
                                                               'product_affiliate_link'));
    if ($product_affiliate_link != 0) {
        $product_details_array ['product_affiliate_link'] = $product_affiliate_link;
    }

    $shop_title = sanitize_text_field(filter_input(INPUT_POST,
                                                   'shop_title'));
    if ($shop_title != "") {
        $product_details_array ['shop_title'] = $shop_title;
    }

    $new_image_ids   = sanitize_text_field(filter_input(INPUT_POST,
                                                        'secondary_images'));
    $current_details = get_post_meta($post_id,
                                     $meta_key,
                                     true);

    $current_images_array = ($current_details === '') ? [] : $current_details['product_images'];

    if (empty($current_images_array) || $current_images_array === 'null') { // no images added yet
        $current_images_array = [];
    }

    $new_images_array = [];

    if ($new_image_ids != "") {
        $new_images_array = explode(',',
                                    $new_image_ids);

        foreach ($new_images_array as $image_id)
        {
            if (!in_array($image_id,
                          $current_images_array)) {
                $current_images_array[] = intval($image_id);
            }
        }
    }

    $product_details_array['product_images'] = $current_images_array;

    $primary_image_id = sanitize_text_field(filter_input(INPUT_POST,
                                                         'primary_image'));
    if ($primary_image_id != "") {
        $product_details_array['primary_image'] = $primary_image_id;
    }
    elseif (isset($current_images_array[0])) {
        $product_details_array['primary_image'] = $current_images_array[0];
    }
    else {
        $product_details_array['primary_image'] = '';
    }

    $original_url = sanitize_text_field(filter_input(INPUT_POST,
                                                     'original_url'));
    $original_url = str_replace('\\',
                                '\\\\',
                                $original_url);

    $product_details_array['original_url'] = $original_url;

    $filtered = array_filter($product_details_array);
    if (!empty($filtered)) {
        update_post_meta($post_id,
                         $meta_key,
                         $product_details_array);
    }

    $original_url = sanitize_text_field(filter_input(INPUT_POST,
                                                     'original_url'));
    $original_url = str_replace('\\',
                                '\\\\',
                                $original_url);

    if ($original_url != "") {
        update_post_meta($post_id,
                         'original_url',
                         $original_url);
    }
}
add_action('save_post_rwk_product',
           'save_product_details',
           10,
           1);
