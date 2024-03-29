<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_sticky() && is_home() && !is_paged()) {
            printf('<span class="sticky-post">%s</span>',
                   _x('Featured',
                      'post',
                      'rwk_shop'));
        }
        the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">',
                          esc_url(get_permalink())),
                                  '</a></h2>');
        ?>
    </header><!-- .entry-header -->

    <?php get_primary_image($post); ?>



</article><!-- #post-<?php the_ID(); ?> -->






