<?php get_header() ?>
<?php include "inc/breadcrumbs.php" ?>
<?php
$future_dates = get_future_dates_by_show_id(get_the_ID());
$nearest_date_time = get_nearest_date_by_show_id(get_the_ID());
?>
<?php while (have_posts()): the_post() ?>

    <div class="container">
        <div class="event-top">
            <div class="row">

                <div class="col-md-auto">
                    <div class="event-poster">
                        <?php the_post_thumbnail('254x360') ?>
                        <?php $genres = get_the_terms(get_the_ID(), 'genre') ?>
                        <?php if (is_array($genres)): ?>
                            <div class="event-tags mt-2">
                                <?php foreach ($genres as $genre): ?>
                                    <a href="<?= get_term_link($genre->term_id) ?>">#<?= mb_strtolower($genre->name) ?></a>
                                <?php endforeach ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md">
                    <div class="event-card text-center text-md-left">
                        <div class="event-card-date">
                            <?php console_log($nearest_date_time) ?>
                            <?php echo date_i18n('d F Y H:i', strtotime($nearest_date_time)) ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 mr-auto">
                                <h1 class="event-title"><?php the_title() ?></h1>
                            </div>
                            <div class="col-lg-auto">
                                <div class="event-price"><?php the_field('event-price') ?>
                                    &nbsp;<?php _e('грн', 'main') ?></div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg mb-2">
                                <div class="d-inline-flex">
                                    <div class="d-none d-md-block event-loca-icon">
                                        <?php icon('loca') ?>
                                    </div>
                                    <div>
                                        <div class="event-city">
                                            <?php the_field('event-place') ?>
                                        </div>
                                        <div class="event-place">
                                            <?php the_field('event-address') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-auto">
                                    <a href="<?= getEventUrl($future_dates[0]['show_dates__buy']) ?>"
                                   class="button-1 event-button"><?php _e('Купити квиток', 'main') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-auto d-none d-lg-block">
                            <div class="event-left-text"><?php _e('До події лишилось', 'main') ?></div>
                            <?php $time_left = time_left($nearest_date_time); ?>
                            <div class="time-left">
                                <span><?php echo $time_left->days ?></span> <?php _e('днів', 'main') ?>
                                <span><?php echo $time_left->h ?></span> <?php _e('годин', 'main') ?>
                                <span><?php echo $time_left->i ?></span> <?php _e('хвилин', 'main') ?>
                            </div>
                        </div>
                        <?php if (0): ?>
                            <div class="col ml-md-auto col-md-auto text-center">
                                <div class="d-inline-flex flex-column flex-md-row event-social">
                                    <span class="mr-1"><?php _e('Поділитися в соц. мережах', 'main') ?>:</span>
                                    <div class="mt-1 mt-md-0">
                                        <a href="#" class="social-link">
                                            <img src="img/facebook.svg" alt="">
                                        </a>
                                        <a href="#" class="social-link">
                                            <img src="img/instagram.svg" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($future_dates): ?>
            <div class="event-dates style-box my-3">
                <h2 class="event-dates__title"><?php _e('Квитки на', 'main') ?> "<?php the_title() ?>"</h2>
                <?php foreach ($future_dates as $date): ?>
                    <div class="event-dates__item">
                        <div class="row align-items-center">
                            <div class="col-md">
                                <div class="event-dates__date">
                                    <?php icon('calendar', 'mr-1') ?>
                                    <?php echo date_i18n('d M., l', strtotime($date['show_dates__date'])) ?>
                                </div>
                                <?php icon('clock', 'mr-1') ?>
                                <?php echo $date['show_dates__time'] ?>
                                <?php icon('ticket', 'ml-4 mr-1') ?>
                                <?= $date['show_dates__price'] ?> грн
                            </div>
                            <div class="col-md-auto">
                                <a href="<?= getEventUrl($date['show_dates__buy']) ?>"
                                   class="button-1"><?php _e('Купити квиток', 'main') ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
        <article class="event-about style-box mb-5">
            <?php the_content() ?>
        </article>
    </div>

<?php endwhile ?>
<?php get_footer() ?>
