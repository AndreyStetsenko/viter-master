<section id='about' class="s-media s-media1">

    <div class="row section-header has-bottom-sep" data-aos="fade-up" style="max-width: 1200px; margin-bottom: 17rem;">
        <div class="col-full col-fulld" style="text-align: left;padding-left: 9.8rem;">
            <!-- <h3 class="subhead">What We Do</h3> -->
            <h1 class="display-2" style="font-size: 36px;">Обо мне</h1>
        </div>
    </div> <!-- end section-header -->
    <div class="row services-list block-1-2 block-tab-full">
    <?php
      $rows = get_field('about-artist', 'option');
      if($rows)
      {
        foreach($rows as $row)
        {
          echo '<div class="col-block service-item" data-aos="fade-up">';
          echo '<div class="service-text">';
          echo '<h3 class="h2">' . $row['about-artist_title'] .'</h3>';
          echo '<p>' . $row['about-artist_more'] . '</p>';
          echo '</div>';
          echo '</div>';
        }
      }
     ?>
     </div>

    <div class="viter_body_netam" data-aos="fade-up">
      <?php
      $image = get_field('about_img_center', 'option');
      if( !empty($image) ): ?>
      <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
      <?php endif; ?>
    </div>
</section> <!-- end s-media -->
