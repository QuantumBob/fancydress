<div class="carousel">
    <ul class="slides">

        <?php
        $length = count($images);
        $count  = 0;
        foreach ($images as $image):

            $count++;
            $next        = ($count === $length) ? 1 : $count + 1;
            $previous    = ($count === 1) ? $length : $count - 1;
            $checked     = ($count === 1) ? 'checked' : '';
            $orientation = ($image['aspect_ratio'] <= 1) ? 'portrait' : 'landscape';
            ?>
            <input type="radio" name="radio-buttons" id="img-<?= $count; ?>" <?= ' ' . $checked; ?> />
            <li class="slide-container">
                <div class="slide-image">
                    <img class="mx-auto <?= $orientation; ?> " src="<?= $image['image_src']; ?>" style="display:block;" />
                </div>
                <div class="carousel-controls">
                    <label for="img-<?= $previous; ?>" class="prev-slide"><span>&lsaquo;</span></label>
                    <label for="img-<?= $next; ?>" class="next-slide"><span>&rsaquo;</span></label>
                </div>
            </li>
            <?
        endforeach;
        ?>

        <div class="carousel-dots">
            <?
            for ($i = 1; $i <= $count; $i ++) :
                ?>
                <label for="img-<?= $i ?>" class="carousel-dot" id="img-dot-<?= $i; ?>"></label>
                <?
            endfor;
            ?>
        </div>
    </ul>
</div>