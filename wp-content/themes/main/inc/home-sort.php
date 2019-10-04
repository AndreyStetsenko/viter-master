<div class="container">
    <?php $default_city = get_term_by('name', 'Київ', 'city') ?>
    <div class="filter" id="filter"
         data-showtype-id="<?php echo get_queried_object()->term_id ?>"
         data-default-city="<?= $default_city->term_id ?>"
         data-empty-text="<?php _e('Вибачте, на обрану дату подій немає', 'main') ?>">
        <div class="row no-gutters">
            <div class="col-4 col-lg-24 mb-2">
                <button data-type="0" class="button-filter button-filter-left active"><?php _e('Будь-яка дата',
                        'main') ?></button>
            </div>
            <div class="col-4 col-lg-24">
                <button data-type="today" class="button-filter"><?php _e('Сьогодні', 'main') ?></button>
            </div>
            <div class="col-4 col-lg-24">
                <button data-type="weekend" class="button-filter button-filter-right"><?php _e('На вихідних',
                        'main') ?></button>
            </div>
            <div class="col mb-2 d-lg-none">
                <?php $mobile_cats = get_terms(array('taxonomy' => 'showType')) ?>
                <div class="dropdown-3">
                    <div class="dropdown-3__selected"><?php _e('Категорії', 'main') ?></div>
                    <ul class="dropdown-3__list">
                        <li class="dropdown-3__item"><?php _e('Знайти зал', 'main') ?></li>
                        <?php foreach ($mobile_cats as $category): ?>
                            <li class="dropdown-3__item"><a href="<?php echo get_term_link($category) ?>"><?php echo $category->name ?></a></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-24 mb-2">
                <div class="dropdown-3">
                    <div class="dropdown-3__selected"><?php _e('Знайти зал', 'main') ?></div>
                    <?php $sites = get_terms([
                        'taxonomy' => 'site',
                    ]);
                    ?>
                    <ul class="dropdown-3__list">
                        <li class="dropdown-3__item" data-id="0"><?php _e('Знайти зал', 'main') ?></li>
                        <?php foreach ($sites as $site): ?>
                            <li class="dropdown-3__item" data-id="<?= $site->term_id ?>"><?= $site->name ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-24">
                <div class="input-datepicker">
                    <input placeholder="Вибрати дату" autocomplete="off" type="text" id="date_pick">
                </div>
            </div>
        </div>
    </div>
</div>
