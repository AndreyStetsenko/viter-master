<section id='news' class="s-about">

    <div class="row section-header has-bottom-sep" data-aos="fade-up">
        <div class="col-full">
            <h1 class="display-1 display-1--light">#News</h1>
        </div>
    </div> <!-- end section-header -->

    <div class="row clients-outer" data-aos="fade-up">
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
      <a class="btn btn--secondary" <?php the_category('bez-rubriki'); ?>
    </div> <!-- end clients-outer -->

    <div class="about__line"></div>

</section> <!-- end s-about -->
