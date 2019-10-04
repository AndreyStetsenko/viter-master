<form class="header-search" role="search" method="get" action="<?= home_url('/') ?>">
    <input type="text" class="header-search__input"
           placeholder="<?php _e('Пошук...', 'main') ?>" name="s" value="<?= get_search_query() ?>"/>
    <button class="header-search__button">
        <?php icon('search') ?>
    </button>
</form>


