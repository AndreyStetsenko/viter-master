<?php
/*
    Template Name: Blog
*/
?>
<?php get_header(); ?>

<div class="news_page_arh">

  <div class="row clients-outer" data-aos="fade-up">
    <div class="news_page_arh-title">
      <h2>Новости</h2>
      <?php if ( function_exists( 'dimox_breadcrumbs' ) ) dimox_breadcrumbs(); ?>
    </div>
    <ul class="music-card">
      <?php if( have_posts() ){ while( have_posts() ){ the_post(); ?>
        <li <?php post_class(); ?> id="post-<?php the_ID(); ?>">
          <a href="<?php the_permalink(); ?>" class="transition">
            <?php the_post_thumbnail(); ?>
            <div class="music-card_cont">
              <h2><?php the_title(); ?></h2>
              <p><?php
  $text = strip_tags( get_the_content() );
  echo mb_substr( $text, 0, 152 );
  ?></p>
            </div>
          </a>
        </li>
        <?php }} ?>
    </ul>
    <!-- <button class="btn btn--secondary" onclick="location.href = 'news.html'">Больше</button> -->
  </div> <!-- end clients-outer -->
</div>

<?php get_footer(); ?>
