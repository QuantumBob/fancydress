jQuery(function ($) {
// Remove image event
    {
        $('body').on('click', '#rwk_remove_image_button', function (e) {
            e.preventDefault();
           
            var image_id = $(this).attr("data-image-id");
            var meta_key = $(this).attr("data-image-key");
            var post_id = $('#post_id').val();
            var data = {
                'action': 'remove_image',
                'image_id': image_id,
                'meta_key': meta_key,
                'post_id': post_id
            };
            jQuery.post(ajax_object.ajaxurl, data, function (response) {
            });
            $(this).hide();
            if (meta_key === 'featured_image') {
                $(this).prev().val("").prev().addClass('button').html('Upload image');
            }
            else if (meta_key === 'secondary_images') {
                $(this).prev().hide().prev().hide();
            }
            return false;
        });
    }

}); // end jQuery(function($) {