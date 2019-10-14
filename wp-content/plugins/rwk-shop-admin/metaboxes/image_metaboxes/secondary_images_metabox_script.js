jQuery(function ($) {

    // Select/Upload secondary images event 
    {
        $('body').on('click', '#rwk_upload_secondary_button', function (e) {
            e.preventDefault();
            
            var secondary_images = $('#secondary_images');
            
            var custom_uploader = wp.media({
                title: 'Insert images',
                library: {
                    // uncomment the next line if you want to attach image to the current post
                    // uploadedTo: wp.media.view.settings.post.id,
                    type: 'image'
                },
                button: {
                    text: 'Use these images' // button label text
                },
                multiple: true // for multiple image selection set to true
            });
            custom_uploader.on('select', function () {
                var selections = custom_uploader.state().get('selection');
                var image_id_array = [];
                selections.each(function (selected) {

                    var urls = [];
                    var attachment = selected.toJSON();
                    image_id_array.push(attachment.id);
                    urls.push(attachment.url);
                    if ($("div .image-tiles div").last().length > 0) {
                        $("div .image-tiles div").last().after(new_secondary_image_html(attachment));
                    }
                    else{
                        $("div .image-tiles").append(new_secondary_image_html(attachment));
                    }
                });
                secondary_images.val(image_id_array.join(','));
            });
            custom_uploader.open();
        });
    }

    // Create html for secondary images
    {
        function new_secondary_image_html(attachment) {

            var image_html = '';
            image_html += '<div class = "col-6">';
            image_html += '<img class="true_pre_image rwk_sec_image" src="' + attachment.url + '" id="' + attachment.id + '" style="max-width:95%;display:block;" />';
//            image_html += '<input type="hidden" name="secondary_imgs" id="sec_image_' + attachment.id + '" value="' + attachment.id + '" />';
            image_html += '<a href="#"  data-image-id="' + attachment.id + '" data-image-key="secondary_images" id="rwk_remove_image_button" style="display:inline-block;">Remove image</a>';
            image_html += '</div>';
            return image_html;
        }
    }

}); // end jQuery(function($) {