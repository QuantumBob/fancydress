<?php
get_header();
?>

<div class="container pt-5 pb-5">
    <div class="row">
        <div class="col-12 text-center">
            <?
            $test = 'stop';
            ?>
            <h1><?php the_title(); ?></h1>
            <input id="post_id" type="hidden" name="post_id" value="<?= the_ID(); ?>"/>
        </div>
    </div>
    <div class = "row">
        <?php
        if (have_posts()) : while (have_posts()) : the_post();

                $post_id = get_the_ID();
                $product = the_product($post_id);

                echo '<div class = "col-6" >';
                the_image_carousel($post_id);
                echo '</div>';

                echo '<div class="col-6">';
                the_content();
                the_features();
                the_prices($product['product_variations']);
                echo '</div>';
            endwhile;
        endif;
        ?>
    </div>
</div>


<?php
get_footer();


