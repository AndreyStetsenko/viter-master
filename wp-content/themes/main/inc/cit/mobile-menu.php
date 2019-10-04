<div class="mobile-menu-overlay"></div>
<div class="mobile-menu d-flex flex-column">
    <div class="mobile-menu__top">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="/" class="logo">
                    <img src="<?= theme_dir('img/logo.svg') ?>" alt="Logo">
                </a>
            </div>
            <div class="col-auto ml-auto">
                <?php echo qtranxf_generateLanguageSelectCode('text'); ?>
            </div>
        </div>
        <div class="mobile-menu__user">
            <?php icon('user') ?>
            <a href="<?php the_field('lk-link', 'options') ?>"><?php _e('Особистий кабінет', 'main') ?></a>
        </div>
    </div>
    <?php $cities = get_terms(array('taxonomy' => 'city')) ?>
    <?php $mobile_cats = get_terms(array('taxonomy' => 'showType')) ?>
    <ul class="mobile-menu__nav flex-fill">
        <li class="menu-item-has-children mobile-menu__type-1">
            <a href="#"><?php _e('Місто', 'main') ?> <span
                        class="float-right mobile-current-city"><?= $nameTaxonomy ?></span></a>
            <ul class="sub-menu">
                <?php foreach ($cities as $city): ?>
                    <li><a data-id="<?= $city->term_id ?>" class="mobile-citylink"
                           href="<?php echo get_term_link($city) ?>"><?php echo $city->name ?></a></li>
                <?php endforeach ?>
            </ul>
        </li>
        <?php if (0): ?>
            <li class="menu-item-has-children mobile-menu__type-2">
                <a href="#"><?php _e('Категорії', 'main') ?></a>
                <ul class="sub-menu">
                    <?php foreach ($mobile_cats as $category): ?>
                        <li><a href="<?php echo get_term_link($category) ?>"><?php echo $category->name ?></a></li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endif ?>
        <?php mobileMenu() ?>
    </ul>
</div>
