jQuery(document).ready(function ($) {

    $('body').on('click', '#rwk_single_image_button', function (e) {
        e.preventDefault();
        var button = $(this);

        var post_id = $('#post_id').val();
        var image_id = $('img').attr('id');

        var data = {
            'action': 'get_next_image',
            'post_id': post_id,
            'image_id': image_id
        };
        jQuery.post(ajax_object.ajaxurl, data, function (response) {
            var image_src = response.image_src;
            var image_id = response.image_id;
            var html1 = 'class="rwk_sec_image"><img id="' + image_id + '" src="' + image_src + '" style="max-width:95%; display:block;" />';
            var image = '<a href = "#" id = "rwk_single_image_button" ' + html1 + '</a>';
            $("#rwk_single_image_button").html(image);
        }, "json");
    });
});








