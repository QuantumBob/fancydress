<?php
get_header();
?>

<div class="container pt-5 pb-5">
    <div class="row">
        <div class="col-12">
            <h1>Selected Products</h1>
        </div>
    </div>

    <div class="row">
        <?
        get_recent_products();
        ?>
    </div>
</div>

<? get_footer(); ?>
