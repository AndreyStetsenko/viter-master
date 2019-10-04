<?php get_header(); ?>

<div class="container py-4">
    <div class="mb-3">
        <h1 class="mt-0 mb-1">
            <?php _e('Пошук за запитом', 'main') ?> <span class="text-danger">"<?php the_search_query() ?>"</span></h1>
    </div>

    <?php if (have_posts()): ?>
        <div class="afisha">
            <div class="row">
                <?php while (have_posts()): the_post() ?>
                    <?php include "inc/event-card.php" ?>
                <?php endwhile ?>
            </div>
        </div>
    <?php else: ?>
        <p><?php _e('По вашому запиту нічого не знайдено', 'main') ?></p>
    <?php endif ?>

    <div class="text-center mt-4">
        <?php wp_pagenavi(); ?>
    </div>

</div>

<?php get_footer(); ?>
