<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
get_header();
?>

<div class="container pt-5 pb-5">

    <div class="row">
        <div class="col-12">
            <?php if (have_posts()) : ?>
                <h1 class="page-title">
                    <?php
                    printf(__('Search Results for: %s',
                              'rwk_shop'),
                              '<span>' . get_search_query() . '</span>');
                    ?>
                </h1>
            <?php else : ?>
                <h1 class="page-title">
                    <?php
                    _e('Nothing Found',
                       'rwk_shop');
                    ?>
                </h1>
            <?php endif; ?>
        </div><!-- .page-header -->
    </div>

    <div id="primary" class="row">
        <?php
        if (have_posts()) : while (have_posts()) : the_post();

                get_template_part('template-parts/product',
                                  'one-of-many');
            endwhile;

            the_posts_pagination(
                    array(
                        'prev_text'          => '<i id="header-search-icon" class="fas fa-search"></i><span class="screen-reader-text">' . __('Previous page',
                                                                                                                                              'rwk_shop') . '</span>',
                        'next_text'          => '<span class="screen-reader-text">' . __('Next page',
                                                                                         'rwk_shop') . '</span><i id="header-search-icon" class="fas fa-search"></i>',
                        'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page',
                                                                                                  'rwk_shop') . ' </span>',
                    )
            );

        else :
            ?>

            <p><?php
                _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.',
                   'rwk_shop');
                ?></p>
            <?php
            get_search_form();

        endif;
        ?>
    </div><!-- #primary -->
</div><!-- .wrap -->

<?php
get_footer();
