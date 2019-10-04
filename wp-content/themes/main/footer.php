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

          <form name="contactForm" id="contactForm" method="post" action="" novalidate="novalidate">
              <fieldset>

              <div class="form-field">
                  <input name="contactName" type="text" id="contactName" placeholder="Ваше имя" value="" minlength="2" required="" aria-required="true" class="full-width">
              </div>
              <div class="form-field">
                  <input name="contactEmail" type="email" id="contactEmail" placeholder="Ваш Email" value="" required="" aria-required="true" class="full-width">
              </div>
              <div class="form-field">
                  <input name="contactSubject" type="text" id="contactSubject" placeholder="Тема" value="" class="full-width">
              </div>
              <div class="form-field">
                  <textarea name="contactMessage" id="contactMessage" placeholder="Ваше сообщение" rows="10" cols="50" required="" aria-required="true" class="full-width"></textarea>
              </div>
              <div class="form-field">
                  <button class="full-width btn--primary">Отправить</button>
                  <div class="submit-loader">
                      <div class="text-loader">Отправка...</div>
                      <div class="s-loader">
                          <div class="bounce1"></div>
                          <div class="bounce2"></div>
                          <div class="bounce3"></div>
                      </div>
                  </div>
              </div>

              </fieldset>
          </form>

          <!-- contact-warning -->
          <div class="message-warning">
              Что-то пошло не так. Пожалуйста, попробуйте еще раз.
          </div>

          <!-- contact-success -->
          <div class="message-success">
              Ваше сообщение было отправлено, спасибо!<br>
          </div>

      </div> <!-- end contact-primary -->

      <div class="contact-secondary">
          <div class="contact-info">

              <h3 class="h6 hide-on-fullwidth">Контактная Информация</h3>

              <div class="cinfo">
                  <h5>Где меня найти</h5>
                  <p>
                      Киев, Хрещатик 22<br>
                      02002 Украина
                  </p>
              </div>

              <div class="cinfo">
                  <h5>Напишите нам</h5>
                  <p>
                      contact.viter@gmail.com<br>
                      info.viter@gmail.com
                  </p>
              </div>

              <div class="cinfo">
                  <h5>Позвоните нам</h5>
                  <p>
                      Рабочий: +380 99 123 1234<br>
                      Мобильный: +380 99 123 1234<br>
                      Офис: +380 44 123 1234
                  </p>
              </div>

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
$(".slider").slick({
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
