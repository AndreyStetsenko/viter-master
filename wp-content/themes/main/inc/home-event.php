<section id='events' class="s-media">

    <div class="row section-header has-bottom-sep" data-aos="fade-up">
        <div class="col-full">
            <!-- <h3 class="subhead">What We Do</h3> -->
            <h1 class="display-2">Концерты</h1>
        </div>
    </div> <!-- end section-header -->

    <?php $query = new WP_Query(array(
        'post_type' => 'events',
    )) ?>

    <section class="slider">
      <?php while ($query->have_posts()): $query->the_post() ?>
          <?php include "event-card.php" ?>
      <?php endwhile ?>
    </section>
<?php wp_reset_query(); ?>
</section> <!-- end s-media -->
