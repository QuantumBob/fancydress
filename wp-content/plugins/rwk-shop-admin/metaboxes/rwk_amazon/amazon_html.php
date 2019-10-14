<?php

function product_header($original_url, $post, $auto_images, $product_last_scraped, $product_affiliate_link)
{
    ?>
    <label class="mr-2">Amazon URL</label>
    <input name="original_url" id="original_url" value="<?= $original_url ?>" />
    <a href="#" id="product_scrape_button" class="button ml-2">Scrape this URL</a>
    <input type="hidden" name="post_id" id="post_id" value="<?= $post->ID ?>" />

    <div class="pt-1 mb-1">
        <input id="auto_upload_to_post" type="checkbox" name="auto_upload_to_post" <?= $auto_images; ?>>
        Add images to post automatically
        <label class="pt-1 pl-5">URL last scraped : </label>
        <input name="product_last_scraped" id="product_last_scraped" value="<?= $product_last_scraped ?>" />
    </div>

    <label class="mr-2">Affiliate Link</label>
    <input name="product_affiliate_link" id="product_affiliate_link" value="<?= $product_affiliate_link ?>" />
    <?
}

function product_titles_and_ids($product_title, $product_id, $shop_title, $shop_id)
{
    ?>
    <div class="row">
        <div class="col-6 text-center">Amazon</div>
        <div class="col-6 text-center">Shop</div>

        <div class="col-6 mt-1">
            <div class="align-inputs mt-0 mb-3">
                <label class="mb-0">Amazon Title</label>
                <input
                    name="product_title" id="product_title"
                    value="<?= $product_title ?>">
                <label class="mb-0">Amazon ID</label>
                <input name="product_id"
                       id="product_id" value="<?= $product_id ?>">
            </div>
        </div>
        <div class="col-6 mt-2">
            <div class="align-inputs mt-0 mb-3">
                <label class="mb-0">Shop Title</label>
                <input
                    name="shop_title" id="shop_title"
                    value="<?= $shop_title ?>">
                <label class="mb-0">Shop ID</label>
                <input name="shop_id"
                       id="shop_id" value="<?= $shop_id ?>">
            </div>
        </div>
    </div>
    <?
}

function product_variations_container($product_variations)
{
    ?>
    <div id="product_variations_container" class="row">
        <div class="col-12 text-center">Amazon Variations</div>
        <?
        $count = 0;
        $html  = [];
        foreach ($product_variations as $variation)
        {
            if (array_key_exists('price_low',
                                 $variation)) {
                $id      = 'product_price_low';
                $html [] = '<div id="product_price_range" class="col-4 mt-0 mb-3">';
                $html [] = '<div class="align-inputs mt-0 mb-3">';
                $html [] = '<label class="mb-0">Price Low</label>';
                $html [] = '<input name="product_variations[' . $count . '][price_low]" value="' . $variation ['price_low'] . '">';
                $html [] = '</div>';
                $html [] = '<div class="align-inputs mt-0 mb-3">';
                $html [] = '<label class="mb-0">Price High</label>';
                $html [] = '<input name="product_variations[' . $count . '][price_high]" value="' . $variation ['price_high'] . '">';
                $html [] = '</div>';
                $html [] = '<input type="hidden" name="product_price_range" value="product_price_range" />';
                $html [] = '</div>';
            }
            else {
                $id      = 'product_variation_' . $count;
                $html [] = '<div id="' . $id . '" class="col-4 mt-0 mb-3">';
                $html [] = '<div class="align-inputs mt-0 mb-3">';
                $html [] = '<label class="mb-0">Size</label>';
                $html [] = '<input name="product_variations[' . $count . '][size]" value="' . $variation ['size'] . '">';
                $html [] = '</div>';
                $html [] = '<div class="align-inputs mt-0 mb-3">';
                $html [] = '<label class="mb-0">Price</label>';
                $html [] = '<input name="product_variations[' . $count . '][price]" value="' . $variation ['price'] . '">';
                $html [] = '</div>';
                $html [] = '<input type="hidden" name="' . $id . '" value="' . $id . '" />';
                $html [] = '</div>';
            }
            $count ++;
        }

        echo empty($html) ? '' : implode(' ',
                                         $html);
        ?>

    </div>
    <?
}

function product_features_container($product_features)
{
    ?>
    <div class="col-6">
        <div id="product_features_container" class="mt-3 pt-2 pb-2">
            <div class="col-12 mb-2 text-center">Amazon Features</div>
    <?
    $count = 0;
    $html  = [];
    foreach ($product_features as $feature)
    {

        $html [] = '<div class="mb-1">';
        $html [] = $feature;
        $html [] = '<input type="hidden" name="product_feature_' . $count . '" value="' . $feature . '" />';
        $html [] = '</div>';
        $count ++;
    }
    echo empty($html) ? '' : implode(' ',
                                     $html);
    ?>
        </div>

    </div>

    <?
}

function product_shop_features_container($shop_features)
{
    ?>
    <div class="col-6">
        <div class="mt-3 pt-2 pb-2">
            <div class="col-12 mb-2 text-center">Shop Features</div>
            <div  id="shop_features_container" class="align-inputs mt-0 mb-3">

    <?
    $count = 0;
    $html  = [];
    if (empty($shop_features)) {
        $html [] = '<textarea cols="100" rows="1" wrap="hard" name="shop_feature_0"></textarea>';
    }
    else {
        foreach ($shop_features as $feature)
        {
            $html [] = '<textarea cols="100" rows="2" wrap="hard" name="shop_feature_' . $count . '">' . $feature . '</textarea>';
            $count ++;
        }
    }
    $html [] = '<a href="#" id="add_shop_feature_button" class="button ml-2">Add a shop feature</a>';
    echo empty($html) ? '' : implode(' ',
                                     $html);
    ?>
            </div>
        </div>
    </div>
                <?
            }

            function product_image_url_container($product_image_urls)
            {
                ?>
    <div class="row">
        <div id="product_image_urls_container" class="col-12 mt-3 pt-2 pb-2">
            <div class="col-12 mb-2 text-center">Amazon Images</div>
    <?
    $count = 0;
    $html  = [];
    foreach ($product_image_urls as $image_url)
    {

        $html [] = '<div>';
        $html [] = $image_url;
        $html [] = '<input type="hidden" name="product_image_url_' . $count . '" value="' . $image_url . '" />';
        $html [] = '</div>';
        $count ++;
    }
    echo empty($html) ? '' : implode(' ',
                                     $html);
    ?>
            <div class="mt-2">
                <a href="#" id="refresh_product_images_button" class="button ml-2">Refresh Amazon Images</a>
            </div>
        </div>
    </div>
    <?
}
