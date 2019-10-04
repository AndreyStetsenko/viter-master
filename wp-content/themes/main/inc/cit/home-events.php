<?php $query = new WP_Query(array(
    'post_type' => 'events',
)) ?>
<div class="tickets">
    <div class="container">
        <div class="afisha">
            <div class="row ajax-tickets" style="opacity: 0">
                <?php while ($query->have_posts()): $query->the_post() ?>
                    <?php include "ticket-item.php" ?>
                <?php endwhile ?>
            </div>
        </div>

        <div class="text-center mt-2 mb-6">
            <a href="#" class="button-2 button-loadmore"><?php _e('Показати ще', 'main') ?></a>
        </div>
    </div>
</div>
<?php wp_reset_query(); ?>
