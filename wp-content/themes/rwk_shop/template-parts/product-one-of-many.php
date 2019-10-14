<?php
$primary_image = get_post_meta($post->ID,
                               'primary_image',
                               true);
if ($primary_image === '')
{
    $primary_image = get_post_meta($post->ID,
                                   'secondary_images',
                                   true);
    if (empty($primary_image) || $primary_image === false || $primary_image === 'null')
    {
        $primary_image = [];
    }
    else
    {
        $primary_image = $primary_image[0];
    }
}
$image_size       = 'medium';
$image_attributes = wp_get_attachment_image_src($primary_image,
                                                $image_size);
$primary_image    = $image_attributes[0];
$permalink        = get_permalink($post->ID);

$price_range    = the_price_range();
$affiliate_link = the_affiliate_link();
?>

<div class="<?= $column_class ?> text-center">
    <a  href="<?= $permalink ?>">
        <img class="mx-auto grid-img" src=" <?= $primary_image; ?>" style="display:block;" />
    </a>
    <a class="link-font" href="<?= $permalink; ?>"><?= $post->post_title; ?></a>
<?= $price_range; ?>
<?= $affiliate_link ?>
</div>
