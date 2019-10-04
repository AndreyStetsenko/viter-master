<?php $slider = get_field('home-slider') ?>

<?php if ($slider): ?>
    <div class="home-slider">
        <?php foreach ($slider as $slide): ?>
            <div class="home-slider__item">
                <a href="<?php echo $slide['home-slider__link'] ?>" class="home-slider__link"></a>
                <img src="<?php echo wp_get_attachment_image_src($slide['home-slider__image'],
                    'slider-1')[0] ?>" alt="image">
            </div>
        <?php endforeach; ?>
    </div>
    <div class="container">
        <div class="home-slider-indicator">
            <div class="row no-gutters align-items-center">
                <div class="col">
                    <div class="home-slider-dots"></div>
                </div>
                <div class="col-auto">
                    <div class="home-slider-count"></div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
