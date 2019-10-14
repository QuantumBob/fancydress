jQuery(function ($) {
// Check Post exists event
    {
        $('body').on('click', '#product_scrape_button', function (e) {
            e.preventDefault();
            var button = $(this);
            button.text("Scraping...");

            var original_url = $('#original_url').val();
            var post_id = $('#post_id').val();
            var auto_images = $('#auto_upload_to_post').val();
            var data = {
                'action': 'scrape_product_page',
                'original_url': original_url,
                'post_id': post_id,
                'auto_upload_to_post': auto_images
            };
            jQuery.post(ajax_object.ajaxurl, data, function (response) {

                button.text("Scrape this URL");

                if (response === undefined || response.length === 0) {
                    $("#original_url").val("No URL enterd");
                    return false;
                }
                $("#original_url").val(response.original_url);
                if (response.product_last_scraped !== false) {

                    $("#product_last_scraped").val(response.product_last_scraped);
                    $("#product_title").val(response.product_title);
                    $("#product_id").val(response.product_id);
                    $("#shop_title").val(response.shop_title);
                    $("#product_affiliate_link").val(response.product_affiliate_link);

                    var secondary_images = $('#secondary_images');
                    var current_ids = secondary_images.val();

                    var image_ids = response.product_image_ids;

                    if (current_ids === "") {
                        secondary_images.val(image_ids);
                    } else {
                        secondary_images.val(current_ids + ',' + image_ids);
                    }

                    var variations = [];
                    variations = response.product_variations;
                    var variation_html = "";

                    $("#product_variations_container").empty();
                    variation_html += '<span>Amazon Variations</span>';

                    for (var i = 0; i < variations.length; i++)
                    {
                        if ('price_low' in variations[i]) {
                            var price_low = variations[i]['price_low'];
                            var price_high = variations[i]['price_high'];

                            variation_html += '<div class="col-4 mt-0 mb-3">';
                            variation_html += '<div class="align-inputs mt-0 mb-3">';
                            variation_html += '<label class="mb-0">Price Low</label>';
                            variation_html += '<input name="product_variations[' + i + '][price_low]" value="' + price_low + '"/>';
                            variation_html += '</div>';
                            variation_html += '<div class="align-inputs mt-0 mb-3">';
                            variation_html += '<label class="mb-0">Price High</label>';
                            variation_html += '<input name="product_variations[' + i + '][price_high]" value="' + price_high + '"/>';
                            variation_html += '</div>';
                            variation_html += '</div>';
                        } else {
                            var size = variations[i]['size'];
                            var price = variations[i]['price'];

                            variation_html += '<div class="col-4 mt-0 mb-3">';
                            variation_html += '<div class="align-inputs mt-0 mb-3">';
                            variation_html += '<label class="mb-0">Size</label>';
                            variation_html += '<input name="product_variations[' + i + '][size]" value="' + size + '"/>';
                            variation_html += '</div>';
                            variation_html += '<div class="align-inputs mt-0 mb-3">';
                            variation_html += '<label class="mb-0">Price</label>';
                            variation_html += '<input name="product_variations[' + i + '][price]" value="' + price + '"/>';
                            variation_html += '</div>';
                            variation_html += '</div>';
                        }
                    }

                    $("#product_variations_container").append(variation_html);

                    var features = response.product_features;
                    var features_html = "";

                    $("#product_features_container").empty();
                    features_html += '<span>Amazon Features</span>';

                    for (var i = 0; i < features.length; i++)
                    {
                        var item = features[i];
                        //var key = i + 1;
                        var key = i;
                        features_html += '<div class="mb-1">';
                        features_html += item;
                        features_html += '<input type="hidden" name="product_feature_' + key + '" value="' + item + '" />';
                        features_html += '</div>';
                    }

                    $("#product_features_container").append(features_html);

                    var image_urls = response.product_img_urls;
                    var image_urls_html = "";

                    $("#product_image_urls_container").empty();
                    image_urls_html += '<span>Amazon Images</span>';

                    for (var i = 0; i < image_urls.length; i++)
                    {
                        var item = image_urls[i];
                        var key = i + 1;
                        image_urls_html += "<div>";
                        image_urls_html += item;
                        image_urls_html += '<input type="hidden" name="product_image_url_' + key + '" value="' + item + '" />';
                        image_urls_html += "</div>";
                    }

                    $("#product_image_urls_container").append(image_urls_html);
                }
            }, "json");
        });
    }
    {
        $('body').on('click', '#add_shop_feature_button', function (e) {
            e.preventDefault();

            var count = $("div #shop_features_container textarea").last().length;
            var textarea = '<textarea cols="100" rows="1" wrap="hard" name="shop_feature_' + count + '"></textarea>';
            $("#add_shop_feature_button").before(textarea);


        });
    }
    {
        $('body').on('click', '#refresh_product_images_button', function (e) {
            e.preventDefault();
            var button = $(this);
            button.text("Refreshing...");


            var post_id = $('#post_id').val();

            var data = {
                'action': 'refresh_product_images',
                'post_id': post_id,
            };
            jQuery.post(ajax_object.ajaxurl, data, function (response) {
                button.text("Refresh Product Images");

                var secondary_images = $('#secondary_images');
                var current_ids = secondary_images.val();
                var image_ids = response.product_image_ids;

                if (current_ids === "") {
                    secondary_images.val(image_ids);
                } else {
                    secondary_images.val(current_ids + ',' + image_ids);
                }

                var image_urls = response.product_img_urls;
                var image_urls_html = "";

                $("#product_image_urls_container").empty();
                image_urls_html += '<span>Amazon Images</span>';

                for (var i = 0; i < image_urls.length; i++)
                {
                    var item = image_urls[i];
                    var key = i + 1;
                    image_urls_html += '<div>';
                    image_urls_html += item;
                    image_urls_html += '<input type="hidden" name="product_image_url_' + key + '" value="' + item + '" />';
                    image_urls_html += '</div>';
                }

                image_urls_html += '<div>';
                image_urls_html += '<a href="#" id="refresh_product_images_button" class="button ml-2">Refresh Product Images</a>';
                image_urls_html += '</div>';
                $("#product_image_urls_container").append(image_urls_html);

            });
        });
    }
});
