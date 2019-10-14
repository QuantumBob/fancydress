jQuery(function ($) {
// Select/Upload primary image event 
    {
        $('body').on('click', '#rwk_upload_image_button', function (e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: 'Insert image',
                library: {
                    // uncomment the next line if you want to attach image to the current post
                    // uploadedTo: wp.media.view.settings.post.id,
                    type: 'image'
                },
                button: {
                    text: 'Use this image' // button label text
                },
                multiple: false
            });
            // it also has "open" and "close" events
            custom_uploader.on('select', function () {
                // get selected image details from the modal dialog box
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                // we use this when adding new images to the page (not the php)
                $(button).removeClass('button');
                $(button).html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />');
                $(button).next().val(attachment.id).next().show();
            });
            custom_uploader.open();
        });
    }
}); // end jQuery(function($) {

