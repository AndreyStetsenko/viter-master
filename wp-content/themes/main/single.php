<?php get_header(); ?>

   <div id="main" class="news-block">
     <div class="news_page_arh-title">
       <h2><?php the_title(); ?></h2>
       <?php if ( function_exists( 'dimox_breadcrumbs' ) ) dimox_breadcrumbs(); ?>
     </div>

      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
         <h2><a href="<?php the_permalink() ?>"></a></h2>
         <div class="entry">
            <?php the_content(); ?>
         </div>
      <?php endwhile; endif; ?>

   </div><!-- main -->

<?php get_footer(); ?>
