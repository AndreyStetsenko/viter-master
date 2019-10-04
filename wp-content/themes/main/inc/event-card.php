<?php $future_dates = get_future_dates_by_show_id(get_the_ID()); ?>
<?php $nearest_date_time = get_nearest_date_by_show_id(get_the_ID()); ?>
<div data-aos="fade-up">
  <div class="slider-card">
    <div class="slider-card_left">
      <div class="slider-card_left-date">
        <?php console_log($nearest_date_time) ?>
        <span class="left-date_top"><?php echo date_i18n('d', strtotime($nearest_date_time)) ?></span>
        <span class="left-date_bot"><?php echo date_i18n('F', strtotime($nearest_date_time)) ?></span>
      </div>
    </div>
    <div class="slider-card_right">
      <div class="slider-card_right-cont">
        <div class="right-cont-left">
          <span class="slider-title"><?php the_title() ?></span>
          <?php $city = wp_get_post_terms(get_the_ID(), 'city')[0]->name ?>
          <span class="slider-geo"><?php echo $city ?></span>
          <span class="slider-main"><?php the_field('event-place') ?></span>
        </div>
        <div class="right-cont-right">
          <span class="slider-price"><?php the_field('event-price') ?> грн</span>
          <button class="btn btn--event" onclick="window.open('<?= getEventUrl($future_dates[0]['show_dates__buy']) ?>')">Билеты</button>
        </div>
      </div>
    </div>
  </div>
</div>
