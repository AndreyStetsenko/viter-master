<?php $future_dates = get_future_dates_by_show_id(get_the_ID()); ?>
<div class="col-6 col-md-4 col-lg-24">
    <div class="afisha-item">
        <div class="afisha-item__image">
            <a href="<?php the_permalink() ?>">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('254x360') ?>
                <?php else: ?>
                    <img src="<?= theme_dir('img/afisha-no-image.jpg') ?>" alt="">
                <?php endif ?>
            </a>
        </div>
        <div class="afisha-item__info">
            <div class="afisha-item__title two-rows">
                <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            </div>
            <div class="afisha-item__small nowrap">
                <?php icon('calendar', 'mr-1') ?>
                <?= get_custom_date(get_field('event-date')) ?>
            </div>
            <div class="afisha-item__small nowrap">
                <?php icon('clock', 'mr-1') ?><?php the_field('event-time') ?>, <?php echo date_i18n('l',
                    strtotime(get_field('event-date'))) ?>
            </div>
            <div class="afisha-item__small">
                <div class="d-flex">
                    <div>
                        <?php icon('loca', 'mr-1') ?>
                    </div>
                    <div class="two-rows"><?php the_field('event-place') ?></div>
                </div>
            </div>
            <?php $city = wp_get_post_terms(get_the_ID(), 'city')[0]->name ?>
            <div class="afisha-item__small nowrap">
                <?php icon('city', 'mr-1') ?>
                <?php echo $city ?>
            </div>
            <div class="nowrap mb-2">
                <?php icon('ticket', 'mr-1') ?>
                <b class="afisha-item__big"><?php the_field('event-price') ?> грн</b>
            </div>
            <a href="<?= getEventUrl($future_dates[0]['show_dates__buy']) ?>" class="button-1 d-block">
                <?php _e('Купити', 'main') ?>
                <span class="button-hide"><?php _e('Квиток', 'main') ?></span></a>
        </div>
    </div>
</div>
