<div class="c-head-dop">
    <div class="header-cont">
        <div class="hcc hd-left hd-left-logo">
            <div class="burgerbut">
                <div class="burger burger-1">
                    <a href="#" class="mobile_menu">
                        <i class="fa fa-bars"></i>
                    </a>
                </div>
            </div>
            <a class="logomenu" href="/"><img
                        src="<?php echo theme_dir('/img/logo-ivent.svg'); ?>" alt="citrus event"></a>
        </div>
        <div class="hcc hd-center">
            <div class="d3">
                <?php get_search_form() ?>
            </div>
        </div>
        <div class="hcc hd-right">
            <nav class="hdl-nav">
                <?php

                $categories = get_terms([
                    'taxonomy' => 'showType',
                ]);

                if (get_queried_object()->taxonomy === 'showType') {
                    $nameTaxonomy = get_queried_object()->name ? : __('Всі типи', 'main');
                } else {
                    $nameTaxonomy = __('Всі типи', 'main');
                }
                ?>
                <ul class="hdl-topmenu">
                    <li><a href="#" class="hdlp-a"><?=$nameTaxonomy;?>
                            <img src="<?php echo theme_dir('/img/arrow-gray.png') ?>"
                                 class="hclp-arrow" alt=""></a>
                        <i class="triangle"></i>
                        <ul class="hdl-submenu">
                            <?php foreach ($categories as $category): ?>
                                <li><a href="<?php echo get_term_link($category) ?>"><?php echo $category->name ?></a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                </ul>
            </nav>
            <div class="user-actions">
                <div class="user-actions_profile">
                    <div href="#" class="auth">
                        <div class="usercart"></div>
                        <div class="name">
                            <a href="<?php the_field('lk-link', 'options') ?>"
                               class="link-to"><?php _e('Особистий кабінет') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
