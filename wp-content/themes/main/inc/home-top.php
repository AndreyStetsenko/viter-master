<?php
$image = get_field('img-head', 'option');
if( !empty($image) ): ?>
<section id="home" class="s-home target-section" data-parallax="scroll" data-image-src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" data-natural-width=3000 data-natural-height=2000 data-position-y=center>
<?php endif; ?>

<div class="dim"></div>
<div class="player" id="player">
   <div class="playback_wrapper">
      <div class="playback_blur"></div>
      <div class="playback_thumb" data-tilt></div>
      <div class="playback_info">
         <div class="title">Friday Comes</div>
         <div class="artist">Gena VITER</div>
      </div>
      <div class="playback_btn_wrapper">
         <i class="btn-prev fa fa-step-backward" aria-hidden="true"></i>
         <div class="btn-switch"><i class="btn-play fa fa-play" aria-hidden="true"></i><i class="btn-pause fa fa-pause" aria-hidden="true"></i></div>
         <i class="btn-next fa fa-step-forward" aria-hidden="true"></i>
      </div>
      <div class="playback_timeline">
         <div class="playback_timeline_start-time">00:31</div>
         <div class="playback_timeline_slider">
            <div class="slider_base"></div>
            <div class="slider_progress"></div>
            <div class="slider_handle"></div>
         </div>
         <div class="playback_timeline_end-time">03:11</div>
      </div>
   </div>
   <div class="list_wrapper">
      <ul class="list">
         <li class="list_item selected">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
         <li class="list_item">
            <div class="thumb"> </div>
            <div class="info">
               <div class="title">Friday Comes</div>
               <div class="artist">Gena VITER</div>
            </div>
         </li>
      </ul>
   </div>
</div>


<!-- ===== END PLAYER ===== -->

<div class="overlay"></div>
<div class="shadow-overlay"></div>

<div class="home-content">

    <div class="row home-content__main">

        <h3>Gena</h3>

        <h1>
            VITER
        </h1>

    </div>

    <div class="home-content__scroll">
        <a href="#media" class="scroll-link smoothscroll">
            <span>Моя музыка</span>
        </a>
    </div>

    <div class="home-content__line"></div>

</div> <!-- end home-content -->

<?php
  $rows = get_field('top-social', 'option');
  if($rows)
  {
    echo '<ul class="home-social">';
    foreach($rows as $row)
    {
      echo '<li>';
      echo '<a href="' . $row['link'] . '">';
      echo '<i class="fa fa-' . $row['icon'] . '" aria-hidden="true">';
      echo '</i>';
      echo '<span>' . $row['icon'] . '';
      echo '</span>';
      echo '</a>';
      echo '</li>';
    }
    echo '</ul>';
  }
  ?>
<!-- end home-social -->

</section> <!-- end s-home -->
