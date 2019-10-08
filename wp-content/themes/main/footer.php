<footer id="contact">

  <div class="overlay"></div>
  <!-- <div class="contact__line"></div> -->

  <div class="row section-header" data-aos="fade-up">
      <div class="col-full">
          <!-- <h3 class="subhead">Contact Us</h3> -->
          <h1 class="display-2 display-2--light">Связаться со мной</h1>
      </div>
  </div>

  <div class="row contact-content" data-aos="fade-up">

      <div class="contact-primary">

          <h3 class="h6">Отправить сообщение</h3>

          <?php echo do_shortcode( '[contact-form-7 id="14510" title="Form footer"]' ); ?>

      </div> <!-- end contact-primary -->

      <div class="contact-secondary">
          <div class="contact-info">

              <h3 class="h6 hide-on-fullwidth">Контактная Информация</h3>

              <?php
                $rows = get_field('contacts_footer', 'option');
                if($rows)
                {
                	foreach($rows as $row)
                	{
                    echo '<div class="cinfo">';
                		echo '<h5>' . $row['contacts_footer_title'] . '</h5>';
                    echo '<p>' . $row['contacts_footer_inf1'] . '<br>';
                    echo '' . $row['contacts_footer_inf2'] . '<br>';
                    echo '' . $row['contacts_footer_inf3'] . '<br>';
                    echo '' . $row['contacts_footer_inf4'] . '</p>';
                    echo '</div>';
                	}
                }
                ?>

              <?php
                $rows = get_field('top-social', 'option');
                if($rows)
                {
                  echo '<ul class="contact-social">';
                  foreach($rows as $row)
                  {
                    echo '<li>';
                    echo '<a href="' . $row['link'] . '">';
                    echo '<i class="fa fa-' . $row['icon'] . '" aria-hidden="true">';
                    echo '</i>';
                    echo '</a>';
                    echo '</li>';
                  }
                  echo '</ul>';
                }
                ?>

          </div> <!-- end contact-info -->
      </div> <!-- end contact-secondary -->

  </div> <!-- end contact-content -->

    <div class="row footer-bottom">

        <div class="col-twelve">
            <div class="copyright">
                <span>© Gena VITER 2019</span>
                <span>Website Development by <a href="https://www.facebook.com/stetsenko.freelance" target="_blank">Andrew Stetsenko</a></span>
            </div>

            <div class="go-top">
                <a class="smoothscroll" title="Back to Top" href="#top"><i class="icon-arrow-up" aria-hidden="true"></i></a>
            </div>
        </div>

    </div> <!-- end footer-bottom -->

</footer>
<?php wp_footer(); ?>

<script type="text/javascript">
$(".sli1der").slick({
infinite: false,
dots: false,
autoplay: false,
vertical: true,
verticalSwiping: true,
focusOnSelect: true,
slidesToShow: 2,
slidesToScroll: 1
});
iFrameResize({
    log: false,
    heightCalculationMethod: 'max'
}, '#auto-iframe');
</script>
</body>
</html>
