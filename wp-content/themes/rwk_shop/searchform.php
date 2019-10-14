<?php
/**
 * Template for displaying search forms in rwk_shop
 */
?>

<?php
$unique_id = esc_attr(wp_unique_id('search-form-'));
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo $unique_id; ?>">
        <span class="screen-reader-text"><?php
            echo _x('Search for:',
                    'label');
            ?>
        </span>
    </label>
    <input type="search" id="<?php echo $unique_id; ?>" class="search-field" placeholder="<?php
    echo esc_attr_x('Search &hellip;',
                    'placeholder');
    ?>" value="<?php echo get_search_query(); ?>" name="s" />
    <button type="submit" class="search-submit">
        <i id="header-search-icon" class="fas fa-search"></i>
        <span class="screen-reader-text"><?php
            echo _x('Search',
                    'submit button');
            ?>
        </span>
    </button>
</form>
