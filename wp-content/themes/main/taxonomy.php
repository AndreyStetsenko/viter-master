<?php get_header() ?>
<?php include "inc/breadcrumbs.php" ?>
<div class="container">
    <h1 class="my-3"><?php single_term_title() ?></h1>
</div>
<?php include "inc/home-sort.php" ?>

<div class="container">
    <?php if (have_posts()): ?>
        <div class="afisha">
            <div class="row ajax-tickets" style="opacity: 0">
                <?php while (have_posts()): the_post() ?>
                    <?php include "inc/event-card.php" ?>
                <?php endwhile ?>
            </div>
        </div>

        <div class="text-center mt-2 mb-6">
            <a href="#" class="button-2 button-loadmore"><?php _e('Показати ще', 'main') ?></a>
        </div>
        <?php endif ?>
</div>

<?php get_footer() ?>
