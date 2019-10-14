<!DOCTYPE html>
<html>
    <head>
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <?php
        $test = 1;
        ?>

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top ">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar" aria-controls="collapsingNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsingNavbar">
                <a class="navbar-brand" href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'top-menu',
                    'container'      => 'false',
                    'menu_class'     => 'navbar-nav mr-auto mt-2 mt-lg-0',
                    'fallback_cb'    => '__return_false',
                    'walker'         => new bootstrap_4_walker_nav_menu()
                ));
                ?>
                <?php
                get_search_form();
                ?>

            </div>
        </nav>

