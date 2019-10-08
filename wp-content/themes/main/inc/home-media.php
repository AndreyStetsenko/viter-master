<section id='media' class="s-news">

    <div class="intro-wrap">

        <div class="row section-header has-bottom-sep light-sep" data-aos="fade-up">
            <div class="col-full">
                <!-- <h3 class="subhead">Recent Works</h3> -->
                <h1 class="display-2 display-2--light">#Media</h1>
            </div>
        </div> <!-- end section-header -->

    </div> <!-- end intro-wrap -->

    <div class="row works-content">
        <div class="col-full masonry-wrap">
            <?php
              $rows = get_field('media_popup', 'option');
              if($rows)
              {
                echo '<div class="masonry">';
                foreach($rows as $row)
                {
                  echo '<div class="item">';
                  echo '<a class="popup-youtube" href="' . $row['media_popup-video'] . '">';
                  echo '<img src="' . $row['media_popup-photo'] . '">';
                  echo '<i class="fa fa-play" aria-hidden="true">';
                  echo '</i>';
                  echo '</a>';
                  echo '</div>';
                }
                echo '</div>';
              }
            ?>
        </div> <!-- end col-full -->
    </div> <!-- end news-content -->

</section> <!-- end s-news -->
